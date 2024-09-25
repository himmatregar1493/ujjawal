<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Permission;
use App\Models\IntakePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class CompanyController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.company.create');
    }

    public function index(Request $request)
    {
        return view('admin-panel.company.index');
    }

    public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'name' => 'required|string|max:255|unique:companies,name',
        'email' => 'required|email|max:255',
    ]);

    DB::beginTransaction();
    try {
        // Create a new Company
        $company = new Company;
        $company->name = $request->name;
        $company->email = $request->email;
        $company->created_by = Auth::user()->id;
        $company->is_active = $request->status ?? 0;
        if ($company->save()) {
            WalletCreate($company->id, 'company');
        } else {
            throw new \Exception('Failed to save company.');
        }
        DB::commit();
        return redirect()->route('company.index')->with('success', 'Company created successfully.');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->withInput()->with('error', 'Failed to create company. Please try again.');
    }
}
    public function fetch(Request $request)
    {
        $query = Company::Query();

        $limit = @$request->$limit ?? 10;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('companies..name', 'like', '%' . $request->search . '%');
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

        $intakes = $query->orderBy('companies..id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('companies..name as name','companies..created_at as created_at','companies..id as id','companies..id as id','companies..is_active as is_active')
        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.company.table.detail', ['intakes' => $intakes])->render(),
            'pagination' => view('admin-panel.company.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = Company::query();


        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Company::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Company::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Company::where('is_active', 0)->count()]
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.company.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function deleteIntake($id)
    {
        $intake = Company::find($id);
        if ($intake) {
            $company->delete();
            return response()->json(['message' => 'Company deleted successfully']);
        } else {
            return response()->json(['message' => 'Company not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:intakes,id',
        ]);

        // Find the intake and toggle its status
        $intake = Company::find($request->id);
        $company->is_active = !$company->is_active;

        // Save the updated intake and respond accordingly
        if ($company->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Company status updated successfully.',
                'status' => $company->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $company->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update intake status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){
        $permissions = Permission::where('is_active', 1)->get();
        $groupedPermissions = $permissions->groupBy('group_name');
        $Permissions = $groupedPermissions->toArray();
        $intake = Company::where('id',$id)->first();
        $existingPermissions = IntakePermission::where('intake_id', $id)
        ->pluck('permission_id')
        ->toArray();

        return view('admin-panel.company.edit',compact('Permissions','intake','existingPermissions'));
    }

    public function update(Request $request, $id)
    {
        $numericKeys = array_filter(array_keys($request->all()), 'is_numeric');
        $existingPermissions = IntakePermission::where('intake_id', $id)->get();
        $existingPermissionIds = $existingPermissions->pluck('permission_id')->toArray();
        $permissionsToAdd = array_diff($numericKeys, $existingPermissionIds);
        $permissionsToRemove = array_diff($existingPermissionIds, $numericKeys);
        foreach ($permissionsToAdd as $permissionId) {
            IntakePermission::create([
                'intake_id' => $id,
                'permission_id' => $permissionId,
            ]);
        }

        foreach ($permissionsToRemove as $permissionId) {
            IntakePermission::where('intake_id', $id)
                        ->where('permission_id', $permissionId)
                        ->delete();
        }

        return redirect()->route('company.index')->with('success', 'Company permissions updated successfully.');
    }


}



