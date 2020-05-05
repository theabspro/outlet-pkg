<?php

namespace Abs\OutletPkg;

use Abs\OutletPkg\Shift;
use App\ActivityLog;
use App\Config;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class ShiftController extends Controller
{
    public function __construct() {
		$this->data['theme'] = config('custom.admin_theme');
	}

	public function getShiftFilter() {
		$this->data['extras'] = [
			'status' => [
				['id' => '', 'name' => 'Select Status'],
				['id' => '1', 'name' => 'Active'],
				['id' => '0', 'name' => 'Inactive'],
			],
		];
		return response()->json($this->data);
	}

	public function getShiftList(Request $request) {
		$shifts = Shift::withTrashed()
			->select([
				'shifts.id',
				'shifts.name',
				DB::raw('IF(shifts.deleted_at IS NULL, "Active","Inactive") as status'),
			])
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
			->where('shifts.company_id', Auth::user()->company_id)
		;

		return Datatables::of($shifts)
			
			->addColumn('status', function ($shift) {
				$status = $shift->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indigator ' . $status . '"></span>' . $shift->status;
			})
			->addColumn('action', function ($shift) {
				
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$action = '';
				if (Entrust::can('edit-shift')) {
					$action .= '<a href="#!/outlet-pkg/shift/edit/' . $shift->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				}
				if (Entrust::can('delete-shift')) {
					$action .= '<a href="javascript:;" data-toggle="modal" data-target="#shift-delete" onclick="angular.element(this).scope().deleteShift(' . $shift->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';
				}
				return $action;
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
		$this->data['action'] = $action;
		$this->data['shift'] = $shift;
		return response()->json($this->data);
	}

	public function saveShift(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 64 Charachers',
			];
			$validator = Validator::make($request->all(), [
				'name' => [
					'required:true',
					'min:3',
					'max:64',
					'unique:shifts,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$shift = new Shift;
				$shift->created_by_id = Auth::user()->id;
				$shift->created_at = Carbon::now();
				$shift->updated_at = NULL;
			} else {
				$shift = Shift::withTrashed()->find($request->id);
				$shift->updated_by_id = Auth::user()->id;
				$shift->updated_at = Carbon::now();
			}
			$shift->fill($request->all());
			$shift->company_id = Auth::user()->company_id;
			if ($request->status == 'Inactive') {
				$shift->deleted_at = Carbon::now();
				$shift->deleted_by_id = Auth::user()->id;
			} else {
				$shift->deleted_by_id = NULL;
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
}