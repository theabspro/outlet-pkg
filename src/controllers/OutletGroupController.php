<?php

namespace Abs\OutletPkg;

use Abs\OutletPkg\OutletGroup;
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

class OutletGroupController extends Controller
{
    public function __construct() {
		$this->data['theme'] = config('custom.admin_theme');
	}

	public function getOutletGroupFilter() {
		$this->data['extras'] = [
			'status' => [
				['id' => '', 'name' => 'Select Status'],
				['id' => '1', 'name' => 'Active'],
				['id' => '0', 'name' => 'Inactive'],
			],
		];
		return response()->json($this->data);
	}

	public function getOutletGroupList(Request $request) {
		$outlet_groups = OutletGroup::withTrashed()
			->select([
				'outlet_groups.id',
				'outlet_groups.code',
				'outlet_groups.name',
				DB::raw('IF(outlet_groups.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where(function ($query) use ($request) {
				if (!empty($request->code)) {
					$query->where('outlet_groups.code', 'LIKE', '%' . $request->code . '%');
				}
			})
			->where(function ($query) use ($request) {
				if (!empty($request->name)) {
					$query->where('outlet_groups.name', 'LIKE', '%' . $request->name . '%');
				}
			})
			->where(function ($query) use ($request) {
				if ($request->status == '1') {
					$query->whereNull('outlet_groups.deleted_at');
				} else if ($request->status == '0') {
					$query->whereNotNull('outlet_groups.deleted_at');
				}
			})
			->where('outlet_groups.company_id', Auth::user()->company_id)
		;

		return Datatables::of($outlet_groups)
			
			->addColumn('status', function ($outlet_group) {
				$status = $outlet_group->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indigator ' . $status . '"></span>' . $outlet_group->status;
			})
			->addColumn('action', function ($outlet_group) {
				
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$action = '';
				if (Entrust::can('edit-outlet-group')) {
					$action .= '<a href="#!/outlet-pkg/outlet-group/edit/' . $outlet_group->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				}
				if (Entrust::can('delete-outlet-group')) {
					$action .= '<a href="javascript:;" data-toggle="modal" data-target="#delete_outlet_group" onclick="angular.element(this).scope().deleteOutletGroup(' . $outlet_group->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';
				}
				return $action;
			})
			->make(true);
	}

	public function getOutletGroupFormData(Request $request) {
		$id = $request->id;
		if (!$id) {
			$outlet_group = new OutletGroup;
			$action = 'Add';
		} else {
			$outlet_group = OutletGroup::withTrashed()->find($id);
			$action = 'Edit';
		}
		$this->data['success'] = true;
		$this->data['action'] = $action;
		$this->data['outlet_group'] = $outlet_group;
		return response()->json($this->data);
	}

	public function saveOutletGroup(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'code.required' => 'Code is Required',
				'code.unique' => 'Code is already taken',
				'code.min' => 'Code is Minimum 3 Charachers',
				'code.max' => 'Code is Maximum 191 Charachers',
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 191 Charachers',
			];
			$validator = Validator::make($request->all(), [
				'code' => [
					'required:true',
					'min:3',
					'max:191',
					'unique:outlet_groups,code,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'required:true',
					'min:3',
					'max:191',
					'unique:outlet_groups,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$outlet_group = new OutletGroup;
				$outlet_group->created_by_id = Auth::user()->id;
				$outlet_group->created_at = Carbon::now();
				$outlet_group->updated_at = NULL;
			} else {
				$outlet_group = OutletGroup::withTrashed()->find($request->id);
				$outlet_group->updated_by_id = Auth::user()->id;
				$outlet_group->updated_at = Carbon::now();
			}
			$outlet_group->fill($request->all());
			$outlet_group->company_id = Auth::user()->company_id;
			if ($request->status == 'Inactive') {
				$outlet_group->deleted_at = Carbon::now();
				$outlet_group->deleted_by_id = Auth::user()->id;
			} else {
				$outlet_group->deleted_by_id = NULL;
				$outlet_group->deleted_at = NULL;
			}
			$outlet_group->save();
			
			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Outlet Group Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Outlet Group Updated Successfully',
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

	public function deleteOutletGroup(Request $request) {
		DB::beginTransaction();
		try {
			$outlet_group = OutletGroup::withTrashed()->where('id', $request->id)->forceDelete();
			if ($outlet_group) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Outlet Group Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}
}
