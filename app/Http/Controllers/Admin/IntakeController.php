<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Intake;
use App\Models\Permission;
use App\Models\IntakePermission;
class IntakeController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.intake.create');
    }

    public function index(Request $request)
    {   
        return view('admin-panel.intake.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:intakes,name',
           
        ]);

        $intake = new Intake;
        $intake->name = $request->name;
        $intake->is_active = $request->status ?? 0;
       

        if ($intake->save()) {
            return redirect()->route('intake.index')->with('success', 'Intake created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create intake. Please try again.');
        }
    }

    public function fetchIntakes(Request $request)
    {
        $query = Intake::Query();
        
        $limit = @$request->$limit ?? 10;
        
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('intakes.name', 'like', '%' . $request->search . '%');
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
        
        $intakes = $query->orderBy('intakes.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('intakes.name as name','intakes.created_at as created_at','intakes.id as id','intakes.id as id','intakes.is_active as is_active')
        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.intake.table.detail', ['intakes' => $intakes])->render(),
            'pagination' => view('admin-panel.intake.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = Intake::query();
        
        
        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Intake::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Intake::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Intake::where('is_active', 0)->count()]
        ];
        

        // Render the HTML view with filtered counts
        $html = view('admin-panel.intake.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function deleteIntake($id)
    {
        $intake = Intake::find($id);
        if ($intake) {
            $intake->delete();
            return response()->json(['message' => 'Intake deleted successfully']);
        } else {
            return response()->json(['message' => 'Intake not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:intakes,id',
        ]);
    
        // Find the intake and toggle its status
        $intake = Intake::find($request->id);
        $intake->is_active = !$intake->is_active;
    
        // Save the updated intake and respond accordingly
        if ($intake->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Intake status updated successfully.',
                'status' => $intake->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $intake->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update intake status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){
        $permissions = Permission::where('is_active', 1)->get();
        $groupedPermissions = $permissions->groupBy('group_name');
        $Permissions = $groupedPermissions->toArray();
        $intake = Intake::where('id',$id)->first();
        $existingPermissions = IntakePermission::where('intake_id', $id)
        ->pluck('permission_id')
        ->toArray();
        
        return view('admin-panel.intake.edit',compact('Permissions','intake','existingPermissions'));
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

        return redirect()->route('intake.index')->with('success', 'Intake permissions updated successfully.');
    }
}

