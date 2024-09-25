<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;

class PermissionController extends Controller
{
    public function create(Request $request)
    {
        if(UserCan('permission.create')){
            return view('admin-panel.permission.create');
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function index(Request $request)
    {
        if(UserCan('permission.view')){
            return view('admin-panel.permission.index');
        }else{
            return view('admin-panel.error.401');
        }
    }

    public function store(Request $request)
    {

        if(UserCan('permission.create')){
            $request->validate([
                'name' => 'required|unique:permissions,name',
            ]);
            $permission = new permission;
            $permission->name = $request->name;
            $permission->group_name = $request->group_name;
            $permission->is_active = $request->status ?? 0;
            $permission->guard_name = "admin";
            if ($permission->save()) {
                return redirect()->route('permission.index')->with('success', 'permission created successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create permission. Please try again.');
            }
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function fetch(Request $request)
    {
        if(UserCan('permission.view')){
            $query = permission::Query();
            $limit = @$request->$limit ?? 10;
            $CurrentPage = @$request->page ?? 1;
            $offset = @($CurrentPage-1)*$limit ?? 10;
            if ($request->has('search') && !empty($request->search)) {
                $query->where('permissions.name', 'like', '%' . $request->search . '%');
            }
            if ($request->has('filter_btn') && !empty($request->filter_btn)) {
                if ($request->filter_btn == "total") {
                } elseif ($request->filter_btn == "active") {
                    $query->where('is_active', 1);
                } elseif ($request->filter_btn == "deactive") {
                    $query->where('is_active', 0);
                }
            }
            $totalPage = $query->count();
            $totalPage = ceil($totalPage / $limit);
            $permissions = $query->orderBy('permissions.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->select('permissions.name as name','permissions.created_at as created_at','permissions.id as id','permissions.id as id','permissions.is_active as is_active','permissions.group_name')
            ->get()
            ->toArray();
            return response()->json([
                'html' => view('admin-panel.permission.table.detail', ['permissions' => $permissions])->render(),
                'pagination' => view('admin-panel.permission.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
            ]);
        }else{
            return view('admin-panel.error.401');
        }

    }


    public function CountData(Request $request)
    {
        if(UserCan('permission.view')){
            $query = permission::query();
            $filters = [
                ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . permission::count()],
                ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . permission::where('is_active', 1)->count()],
                ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . permission::where('is_active', 0)->count()]
            ];
            $html = view('admin-panel.permission.table.countData', compact( 'filters'))->render();
            return response()->json(['html' => $html]);
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function deletepermission($id)
    {
        $permission = permission::find($id);
        if ($permission) {
            $permission->delete();
            return response()->json(['message' => 'permission deleted successfully']);
        } else {
            return response()->json(['message' => 'permission not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        if(UserCan('permission.edit')){
            $request->validate([
                'id' => 'required|integer|exists:permissions,id',
            ]);
            $permission = permission::find($request->id);
            $permission->is_active = !$permission->is_active;
            if ($permission->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'permission status updated successfully.',
                    'status' => $permission->is_active // Return the updated status
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => $permission->is_active, // Return the current status, which is the same as before the save attempt
                    'message' => 'Failed to update permission status.'
                ], 500);
            }
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function user(Request $request){
        if(UserCan('permission.edit')){
            $roles = Role::where('is_active',1)->get()->toArray();
            return view('admin-panel.permission.user.update-permission',compact('roles'));
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function get_user_permission (Request $request){

        if(UserCan('permission.edit')){
            if($request->roles == null ){
                $html =  "";
            }else{
                $AllPermissions = RolePermission::whereIn('role_id', $request->roles)->get()->toArray();
                $permissionIds = array_column($AllPermissions, 'permission_id');
                $permissions = Permission::whereIn('id', $permissionIds)->where('is_active', 1)->get();
                $groupedPermissions = $permissions->groupBy('group_name');
                $Permissions = $groupedPermissions->toArray();
                if ($request->user_id) {
                    $user = User::where('id', $request->user_id)->first();
                    $existingPermissions = $user ? explode(',', $user->permission_ids) : [];
                } else {
                    $existingPermissions = [];
                }
                $html =  view('admin-panel.permission.user.permissionList',compact('Permissions','existingPermissions'))->render();
            }
            return response()->json(['html' => $html]);
        }else{
            return view('admin-panel.error.401');
        }


    }

    public function SaveUserPermission(Request $request)
    {
        if(UserCan('permission.edit')){
            $AllPermissions = RolePermission::whereIn('role_id', $request->roles)->get()->toArray();
            $permissionIds = array_column($AllPermissions, 'permission_id');
            $permissions = Permission::whereIn('id', $permissionIds)->where('is_active', 1)->get();
            $groupedPermissions = $permissions->groupBy('group_name');
            $Permissions = $groupedPermissions->toArray();
            $existingPermissions  = [];
            $html =  view('admin-panel.permission.user.permissionList',compact('Permissions','existingPermissions'))->render();
            return response()->json(['html' => $html]);
        }else{
            return view('admin-panel.error.401');
        }

    }
}
