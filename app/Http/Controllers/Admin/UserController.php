<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Role;
use Illuminate\Validation\Rule;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function create(Request $request)
    {
        if(UserCan('user.create')){
            $usertype = Auth::user()->roles_ids;
            $usertype = explode(',', $usertype);
            $roles = Role::whereIn('id', $usertype)->pluck('name')->toArray();

            if (in_array('Super Admin', $roles)) {
                $companies = Company::all()->toArray();
            } else {
                $company_id = Auth::user()->company_id;
                if ($company_id) {
                    $companies = Company::where('id', $company_id)->get()->toArray();
                } else {
                    return response()->json(['error' => 'You are not authorized to create a user'], 403);
                }
            }
            $roles = Role::where('is_active',1)->get()->toArray();
            return view('admin-panel.user.create',compact('roles','companies'));
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function index(Request $request)
    {
        if(UserCan('user.view')){
            return view('admin-panel.user.index');
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function store(Request $request)
    {
        if (UserCan('user.create')) {
            // Validate request data
            $validated = $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:20048', // Adjust as needed
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'role' => 'required|array|min:1',
                'company' => 'required',
            ]);

            DB::beginTransaction(); // Start a transaction

            try {
                // Handle file upload
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('admin_assets/images/profile_pictures'), $filename);
                }

                // Create new User
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->profile_picture = $filename ?? null;  // Handle missing file case
                $user->roles_ids = implode(',', $request->role);

                // Handle permissions
                $permissions = array_filter(array_keys($request->all()), 'is_numeric');
                $user->permission_ids = implode(',', $permissions);
                $user->company_id = $request->company;
                $user->is_active = $request->status ?? 0;

                // Save the user
                if (!$user->save()) {
                    throw new \Exception('Failed to create user.');
                }

                // Create wallet for the user's company
                WalletCreate($user->id, 'user',$request->company);


                // Attach roles to the user
                $user->roles()->attach($request->role);

                DB::commit(); // Commit transaction

                return redirect()->route('user.index')->with('success', 'User created successfully.');
            } catch (\Exception $e) {
                DB::rollBack(); // Roll back the transaction on error
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        } else {
            return view('admin-panel.error.401');
        }
    }


    public function fetchUsers(Request $request)
    {
        if(UserCan('user.view')){

            $usertype = Auth::user()->roles_ids;
            $usertype = explode(',', $usertype);
            $roles = Role::whereIn('id', $usertype)->pluck('name')->toArray();

            $query = User::leftJoin('users as userList', 'userList.id', 'users.created_by')
                ->leftJoin('roles', function($join) {
                    $join->on('roles.id', '=', 'users.roles_ids');
                });


            if (in_array('Super Admins', $roles)) {

            } else {
                $company_id = Auth::user()->company_id;
                if ($company_id) {
                    $query->where('users.company_id', $company_id);
                } else {
                    return response()->json([
                        'html' => "<center>No data Avaialble</center>",
                        'pagination' => "",
                    ]);
                }

            }

            $limit = $request->input('limit', 10);
            $CurrentPage = $request->input('page', 1);
            $offset = ($CurrentPage - 1) * $limit;

            if ($request->has('search') && !empty($request->search)) {
                $query->where(function($q) use ($request) {
                    $q->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('filter_btn') && !empty($request->filter_btn)) {
                if ($request->filter_btn == "active") {
                    $query->where('users.is_active', 1);
                } elseif ($request->filter_btn == "deactive") {
                    $query->where('users.is_active', 0);
                }
            }

            $totalPage = $query->count();
            $totalPage = ceil($totalPage / $limit);

            $users = $query->orderBy('users.id', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->select(
                    'users.name as name',
                    'users.created_at as created_at',
                    'users.id as id',
                    'users.is_active as is_active',
                    'users.email',
                    'userList.name as created_by',
                    'users.roles_ids',
                    'users.profile_picture'
                )
                ->get()
                ->map(function($user) {
                    // Convert comma-separated role IDs to an array
                    $roleIds = explode(',', $user->roles_ids);

                    // Fetch the role names from the roles table
                    $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();

                    // Join role names with commas
                    $user->roles_names = implode(', ', $roles);

                    return $user;
                })
                ->toArray();

            return response()->json([
                'html' => view('admin-panel.user.table.detail', ['users' => $users])->render(),
                'pagination' => view('admin-panel.user.table.pagination', [
                    'limit' => $limit,
                    'offset' => $offset,
                    'totalPage' => $totalPage,
                    'CurrentPage' => $CurrentPage
                ])->render()
            ]);
        }
        else{
            return view('admin-panel.error.401');
        }
    }


    public function CountData(Request $request)
    {
        if(UserCan('user.view')){
            $usertype = Auth::user()->roles_ids;
            $usertype = explode(',', $usertype);
            $roles = Role::whereIn('id', $usertype)->pluck('name')->toArray();
            if (in_array('Super Admins', $roles)) {
                $filters = [
                    ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . User::count()],
                    ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . User::where('is_active', 1)->count()],
                    ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . User::where('is_active', 0)->count()]
                ];
            } else {
                $company_id = Auth::user()->company_id;
                if ($company_id) {
                    $filters = [
                        ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . User::where('users.company_id', $company_id)->count()],
                        ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . User::where('users.company_id', $company_id)->where('is_active', 1)->count()],
                        ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . User::where('users.company_id', $company_id)->where('is_active', 0)->count()]
                    ];
                } else {
                    $html = "";
                    return response()->json(['html' => $html]);
                }
            }
            $query = User::query();
            $html = view('admin-panel.user.table.countData', compact( 'filters'))->render();
            return response()->json(['html' => $html]);
        }
        else{
            return view('admin-panel.error.401');
        }
    }

    public function deleteuser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'user deleted successfully']);
        } else {
            return response()->json(['message' => 'user not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        if(UserCan('user.edit')){
            $request->validate([
                'id' => 'required|integer|exists:users,id',
            ]);

            // Find the user and toggle its status
            $user = User::find($request->id);
            $user->is_active = !$user->is_active;

            // Save the updated user and respond accordingly
            if ($user->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'user status updated successfully.',
                    'status' => $user->is_active // Return the updated status
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => $user->is_active, // Return the current status, which is the same as before the save attempt
                    'message' => 'Failed to update user status.'
                ], 500);
            }
        }
        else{
            return view('admin-panel.error.401');
        }
    }

    public function edit(Request $request,$id){
        if(UserCan('user.edit')){
            $permissions = Permission::where('is_active', 1)->get();
            $groupedPermissions = $permissions->groupBy('group_name');
            $Permissions = $groupedPermissions->toArray();
            $user = User::where('id',$id)->first();
            $existingPermissions = RolePermission::where('role_id', $id)
            ->pluck('permission_id')
            ->toArray();
            $roles = Role::where('is_active',1)->get()->toArray();
            return view('admin-panel.user.edit',compact('Permissions','user','existingPermissions','roles'));
        }
        else{
            return view('admin-panel.error.401');
        }

    }


    public function update(Request $request, $id)
    {
        if(UserCan('user.edit')){
            $request->validate([
                'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // File is optional
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($id), // Ignore the current user's email
                ],
                'password' => 'nullable|min:8|confirmed', // Password is optional
                'role' => 'required|array|min:1',
            ]);

            $user = User::findOrFail($id);
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('admin_assets/images/profile_pictures'), $filename);
                $user->profile_picture = $filename;
            }

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->roles_ids = implode(',', $request->role);
            $user->permission_ids = implode(',', $request->permissions ?? []); // Handle case where permissions might be null
            $user->is_active = $request->status ?? 0;
            $permissions = array_filter(array_keys($request->all()), 'is_numeric');
            $user->permission_ids = implode(',', $permissions);
            // Save the user
            if ($user->save()) {
                $user->roles()->sync($request->role);
                return redirect()->route('user.index')->with('success', 'User updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update user. Please try again.');
            }
        }
        else{
            return view('admin-panel.error.401');
        }

        }
}
