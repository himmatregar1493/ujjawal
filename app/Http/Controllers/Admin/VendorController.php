<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Permission;
use App\Models\IntakePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class VendorController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.vendor.create');
    }

    public function index(Request $request)
    {

        return view('admin-panel.vendor.index');
    }

    public function store(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'email' => 'nullable|email|max:255',
        ]);
        DB::beginTransaction(); // Start a transaction
        try {
            // Create new Vendor
            $vendor = new Vendor;
            $vendor->name = $request->name;
            $vendor->email = $request->email;
            $vendor->created_by = Auth::user()->id;
            $vendor->company_id = Auth::user()->company_id;
            $vendor->is_active = $request->status ?? 0;

            if (!$vendor->save()) {
                throw new \Exception('Failed to save vendor.');
            }

            WalletCreate($vendor->id, 'vendor');
            DB::commit();
            return redirect()->route('vendor.index')->with('success', 'Vendor created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function fetch(Request $request)
    {
        $query = Vendor::Query();

        $limit = @$request->$limit ?? 10;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('vendors.name', 'like', '%' . $request->search . '%');
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

        $vendors = $query->orderBy('companies..id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('companies..name as name','companies..created_at as created_at','companies..id as id','companies..id as id','companies..is_active as is_active')
        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.vendor.table.detail', ['vendors' => $vendors])->render(),
            'pagination' => view('admin-panel.vendor.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = Vendor::query();


        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Vendor::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Vendor::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Vendor::where('is_active', 0)->count()]
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.vendor.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function deleteIntake($id)
    {
        $intake = Vendor::find($id);
        if ($intake) {
            $vendor->delete();
            return response()->json(['message' => 'Vendor deleted successfully']);
        } else {
            return response()->json(['message' => 'Vendor not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:vendors,id',
        ]);

        // Find the intake and toggle its status
        $intake = Vendor::find($request->id);
        $vendor->is_active = !$vendor->is_active;

        // Save the updated intake and respond accordingly
        if ($vendor->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor status updated successfully.',
                'status' => $vendor->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $vendor->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update intake status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){
        $permissions = Permission::where('is_active', 1)->get();
        $groupedPermissions = $permissions->groupBy('group_name');
        $Permissions = $groupedPermissions->toArray();
        $intake = Vendor::where('id',$id)->first();
        $existingPermissions = IntakePermission::where('intake_id', $id)
        ->pluck('permission_id')
        ->toArray();

        return view('admin-panel.vendor.edit',compact('Permissions','intake','existingPermissions'));
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

        return redirect()->route('vendor.index')->with('success', 'Vendor permissions updated successfully.');
    }
}

