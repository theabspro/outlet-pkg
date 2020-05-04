<?php

namespace Abs\OutletPkg;
use App\Http\Controllers\Controller;
use App\Shift;
use Auth;
use Carbon\Carbon;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class ShiftController extends Controller {

	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}

	public function getShiftList(Request $request) {
		$shifts = Shift::withTrashed()

			->select([
				'shifts.id',
				'shifts.name',
				'shifts.code',

				DB::raw('IF(shifts.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('shifts.company_id', Auth::user()->company_id)

			->where(function ($query) use ($request) {
				if (!empty($request->name)) {
					$query->where('shifts.name', 'LIKE', '%' . $request->name . '%');
				}
			})
			->where(function ($query) use ($request) {
				if ($request->status == '1') {
					$query->whereNull('shifts.deleted_at');
				} else if ($request->status == '0') {
					$query->whereNotNull('shifts.deleted_at');
				}
			})
		;

		return Datatables::of($shifts)
			->rawColumns(['name', 'action'])
			->addColumn('name', function ($shift) {
				$status = $shift->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $shift->name;
			})
			->addColumn('action', function ($shift) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				if (Entrust::can('edit-shift')) {
					$output .= '<a href="#!/outlet-pkg/shift/edit/' . $shift->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				}
				if (Entrust::can('delete-shift')) {
					$output .= '<a href="javascript:;" data-toggle="modal" data-target="#shift-delete-modal" onclick="angular.element(this).scope().deleteShift(' . $shift->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';
				}
				return $output;
			})
			->make(true);
	}

	public function getShiftFormData(Request $request) {
		$id = $request->id;
		if (!$id) {
			$shift = new Shift;
			$action = 'Add';
		} else {
			$shift = Shift::withTrashed()->find($id);
			$action = 'Edit';
		}
		$this->data['success'] = true;
		$this->data['shift'] = $shift;
		$this->data['action'] = $action;
		return response()->json($this->data);
	}

	public function saveShift(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'code.required' => 'Short Name is Required',
				'code.unique' => 'Short Name is already taken',
				'code.min' => 'Short Name is Minimum 3 Charachers',
				'code.max' => 'Short Name is Maximum 32 Charachers',
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 191 Charachers',
			];
			$validator = Validator::make($request->all(), [
				'code' => [
					'required:true',
					'min:3',
					'max:32',
					'unique:shifts,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'required:true',
					'min:3',
					'max:191',
					'unique:shifts,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$shift = new Shift;
				$shift->company_id = Auth::user()->company_id;
			} else {
				$shift = Shift::withTrashed()->find($request->id);
			}
			$shift->fill($request->all());
			if ($request->status == 'Inactive') {
				$shift->deleted_at = Carbon::now();
			} else {
				$shift->deleted_at = NULL;
			}
			$shift->save();

			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Shift Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Shift Updated Successfully',
				]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function deleteShift(Request $request) {
		DB::beginTransaction();
		// dd($request->id);
		try {
			$shift = Shift::withTrashed()->where('id', $request->id)->forceDelete();
			if ($shift) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Shift Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}

	public function getShifts(Request $request) {
		$shifts = Shift::withTrashed()
			->with([
				'shifts',
				'shifts.user',
			])
			->select([
				'shifts.id',
				'shifts.name',
				'shifts.code',
				DB::raw('IF(shifts.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('shifts.company_id', Auth::user()->company_id)
			->get();

		return response()->json([
			'success' => true,
			'shifts' => $shifts,
		]);
	}
}