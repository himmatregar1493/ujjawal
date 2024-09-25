<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
class RoleController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.role.create');
    }

    public function index(Request $request)
    {   
        return view('admin-panel.role.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
           
        ]);

        $role = new Role;
        $role->name = $request->name;
        $role->is_active = $request->status ?? 0;
        $role->guard_name = "admin";

        if ($role->save()) {
            return redirect()->route('role.index')->with('success', 'Role created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create role. Please try again.');
        }
    }

    public function fetchRoles(Request $request)
    {
        $query = Role::leftjoin('users','users.id','roles.created_by');
        
        $limit = @$request->$limit ?? 10;
        
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('roles.name', 'like', '%' . $request->search . '%');
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
        
        $roles = $query->orderBy('roles.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('roles.name as name','roles.created_at as created_at','roles.id as id','roles.id as id','roles.is_active as is_active','users.name as created_by')
        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.role.table.detail', ['roles' => $roles])->render(),
            'pagination' => view('admin-panel.role.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = Role::query();
        
        
        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Role::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Role::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Role::where('is_active', 0)->count()]
        ];
        

        // Render the HTML view with filtered counts
        $html = view('admin-panel.role.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            return response()->json(['message' => 'Role deleted successfully']);
        } else {
            return response()->json(['message' => 'Role not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:roles,id',
        ]);
    
        // Find the role and toggle its status
        $role = Role::find($request->id);
        $role->is_active = !$role->is_active;
    
        // Save the updated role and respond accordingly
        if ($role->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Role status updated successfully.',
                'status' => $role->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $role->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update role status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){
        $permissions = Permission::where('is_active', 1)->get();
        $groupedPermissions = $permissions->groupBy('group_name');
        $Permissions = $groupedPermissions->toArray();
        $role = Role::where('id',$id)->first();
        $existingPermissions = RolePermission::where('role_id', $id)
        ->pluck('permission_id')
        ->toArray();
        
        return view('admin-panel.role.edit',compact('Permissions','role','existingPermissions'));
    }

    public function update(Request $request, $id)
    {
        $numericKeys = array_filter(array_keys($request->all()), 'is_numeric');
        $existingPermissions = RolePermission::where('role_id', $id)->get();
        $existingPermissionIds = $existingPermissions->pluck('permission_id')->toArray();
        $permissionsToAdd = array_diff($numericKeys, $existingPermissionIds);
        $permissionsToRemove = array_diff($existingPermissionIds, $numericKeys);
        foreach ($permissionsToAdd as $permissionId) {
            RolePermission::create([
                'role_id' => $id,
                'permission_id' => $permissionId,
            ]);
        }

        foreach ($permissionsToRemove as $permissionId) {
            RolePermission::where('role_id', $id)
                        ->where('permission_id', $permissionId)
                        ->delete();
        }

        return redirect()->route('role.index')->with('success', 'Role permissions updated successfully.');
    }
}
