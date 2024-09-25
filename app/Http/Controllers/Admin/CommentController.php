<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Permission;
use App\Models\IntakePermission;
class CommentController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.comment.create');
    }

    public function index(Request $request)
    {   
        return view('admin-panel.comment.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:application_stageses,name',
           
        ]);

        $application_stageses = new Comment;
        $application_stageses->name = $request->name;
        $application_stageses->is_active = $request->status ?? 0;
       

        if ($application_stageses->save()) {
            return redirect()->route('comment.index')->with('success', 'Comment type created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create application_status. Please try again.');
        }
    }

    public function fetch(Request $request)
    {
        $query = Comment::Query();
       
        $limit = @$request->$limit ?? 10;
        
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('comments.name', 'like', '%' . $request->search . '%');
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
        
        $application_stageses = $query->orderBy('comments.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('comments.name as name','comments.created_at as created_at','comments.id as id','comments.id as id','comments.is_active as is_active')
        ->get()
        ->toArray();
        
        return response()->json([
            'html' => view('admin-panel.comment.table.detail', ['application_stageses' => $application_stageses])->render(),
            'pagination' => view('admin-panel.comment.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = Comment::query();
        
        
        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Comment::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Comment::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Comment::where('is_active', 0)->count()]
        ];
        

        // Render the HTML view with filtered counts
        $html = view('admin-panel.comment.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function deleteIntake($id)
    {
        $application_stageses = Comment::find($id);
        if ($application_stageses) {
            $application_stageses->delete();
            return response()->json(['message' => 'Comment type deleted successfully']);
        } else {
            return response()->json(['message' => 'Comment type not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:application_stageses,id',
        ]);
    
        // Find the application_stageses and toggle its status
        $application_stageses = Comment::find($request->id);
        $application_stageses->is_active = !$application_stageses->is_active;
    
        // Save the updated application_stageses and respond accordingly
        if ($application_stageses->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment type status updated successfully.',
                'status' => $application_stageses->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $application_stageses->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update application_stageses status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){
        dd("edit");
        $permissions = Permission::where('is_active', 1)->get();
        $groupedPermissions = $permissions->groupBy('group_name');
        $Permissions = $groupedPermissions->toArray();
        $application_stageses = Comment::where('id',$id)->first();
        $existingPermissions = IntakePermission::where('application_stageses_id', $id)
        ->pluck('permission_id')
        ->toArray();
        
        return view('admin-panel.comment.edit',compact('Permissions','application_stageses','existingPermissions'));
    }

    public function update(Request $request, $id)
    {
        $numericKeys = array_filter(array_keys($request->all()), 'is_numeric');
        $existingPermissions = IntakePermission::where('application_stageses_id', $id)->get();
        $existingPermissionIds = $existingPermissions->pluck('permission_id')->toArray();
        $permissionsToAdd = array_diff($numericKeys, $existingPermissionIds);
        $permissionsToRemove = array_diff($existingPermissionIds, $numericKeys);
        foreach ($permissionsToAdd as $permissionId) {
            IntakePermission::create([
                'application_stageses_id' => $id,
                'permission_id' => $permissionId,
            ]);
        }

        foreach ($permissionsToRemove as $permissionId) {
            IntakePermission::where('application_stageses_id', $id)
                        ->where('permission_id', $permissionId)
                        ->delete();
        }

        return redirect()->route('comment.index')->with('success', 'Intake permissions updated successfully.');
    }

   
}

