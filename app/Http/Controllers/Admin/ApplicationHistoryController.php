<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\StudentInformation;
use App\Models\Permission;
use App\Models\ApplicationTimeline;
use App\Models\StudentDocument;
use App\Models\applicationComment;
use App\Models\University;
use App\Models\AssignApplication;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Intake;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\ApplicationStages;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicationPermission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ApplicationHistoryController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.application-history.create');
    }

    public function index(Request $request)
    {   
        $universities = University::get()->toArray();
        $courseIntakes = Course::select('id','course_name')->get()->toArray();
        $intakes = Intake::select('id','name')->get()->toArray();
        $applicationStage = ApplicationStages::select('id','name')->get()->toArray();
        $permissionRecord = Permission::where('name', "application.create")->first()->toArray();
        $assignList = [];
        if ($permissionRecord) {
            $rolePermissions = RolePermission::where('permission_id', $permissionRecord['id'])->get();
            if ($rolePermissions->isNotEmpty()) {
                $roleIds = $rolePermissions->pluck('role_id')->toArray();
                $permission_id = $permissionRecord['id'];
                $assignList = User::whereRaw('FIND_IN_SET(roles_ids, ?)', [implode(',', $roleIds)])->whereRaw('FIND_IN_SET(?, permission_ids)', [$permission_id])->select('name','id')->get()->toArray();
            }
        }
        // dd($intakes);
        $universities = University::get()->toArray();
        return view('admin-panel.application-history.index',compact('universities','courseIntakes','intakes','applicationStage','assignList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:application,name',
           
        ]);

        $intake = new Application;
        $intake->name = $request->name;
        $intake->is_active = $request->status ?? 0;
       

        if ($intake->save()) {
            return redirect()->route('intake.index')->with('success', 'Application created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create intake. Please try again.');
        }
    }

    public function fetch(Request $request)
{
    $userId = Auth::user()->id;
    $limit = $request->input('limit', 10);
    $currentPage = $request->input('page', 1);
    $offset = ($currentPage - 1) * $limit;
    // Build the base query
    $query = Application::where('temp_application_submittion', 0)
        ->leftJoin('student_informations', 'student_informations.application_id', 'applications.id')
        ->leftJoin('courses', 'courses.id', 'applications.course_id')
        ->leftJoin('users', 'users.id', 'applications.counseller_id')
        ->leftJoin('application_stageses', 'application_stageses.id', 'applications.application_stage')
        ->leftJoin('universities', 'universities.id', 'courses.university_id');

        if (!UserCan('application.view_all_application') || ($request->has('assign_id') && !empty($request->assign_id))) {
            $query->join('assign_applications', 'assign_applications.application_id', 'applications.id');
        }

    if ($request->has('application_id') && !empty($request->application_id)) {
        $query->where('applications.id', 'like', '%' . $request->application_id . '%');
    }
    if ($request->has('passport_no') && !empty($request->passport_no)) {
        $query->where('student_informations.student_passport', 'like', '%' . $request->passport_no . '%');
    }
    if ($request->has('student_name') && !empty($request->student_name)) {
        $searchTerm = '%' . $request->student_name . '%';
        $query->where(DB::raw("CONCAT(student_informations.student_first_name, ' ', student_informations.student_last_name)"), 'like', $searchTerm);
    }
    if ($request->has('university_id') && !empty($request->university_id)) {
        $query->where('universities.id', 'like', '%' . $request->university_id . '%');
    }
    if ($request->has('intake_id') && !empty($request->intake_id)) {
        $query->where('applications.intake_id', 'like', '%' . $request->intake_id . '%');
    }
    if ($request->has('applicatio_status') && !empty($request->applicatio_status)) {
        $query->where('applications.application_stage', 'like', '%' . $request->applicatio_status . '%');
    }
    if ($request->has('assign_id') && !empty($request->assign_id)) {
        $userId = $request->assign_id;
    }

    // Status filter
    if ($request->has('filter_btn') && !empty($request->filter_btn)) {
        if ($request->filter_btn === "active") {
            $query->where('assign_applications.is_active', 1);
        } elseif ($request->filter_btn === "deactive") {
            $query->where('assign_applications.is_active', 0);
        }
    }

    // Count total records
    // dd($query->toSql());
    $totalRecordsQuery = $query->clone();
    // dd($totalRecordsQuery->toSql());
    $totalRecordsQuery->select('applications.id');

    // Apply additional conditions if the user does not have 'view_all_application' permission
    if (!UserCan('application.view_all_application') || ($request->has('assign_id') && !empty($request->assign_id))) {
        $totalRecordsQuery->where('assign_applications.is_active', 1)
                        ->where('assign_applications.assign_to', $userId);
    }

    // Ensure distinct values
    $totalRecordsQuery->distinct();

    // Get the count of distinct application IDs
    $totalRecords = $totalRecordsQuery->count('applications.id');

    // Calculate the total pages
    $totalPages = ceil($totalRecords / $limit);

    // Fetch paginated data
    if (!UserCan('application.view_all_application') || ($request->has('assign_id') && !empty($request->assign_id))) {
    $applications = $query
        ->where('assign_applications.is_active', 1)
        ->where('assign_applications.assign_to', $userId);
    }

        $applications = $query
        ->orderBy('applications.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select(
            'applications.id as id',
            'student_informations.student_first_name',
            'student_informations.student_last_name',
            'student_informations.student_passport',
            'courses.institute_name',
            'courses.course_name',
            'applications.created_at as created_at',
            'users.name as counseller_name',
            'application_stageses.name as application_stage',
            'universities.name as university_name',
            'universities.urm_name',
            'universities.urm_contact_no'
        )
        ->distinct('applications.id') // Ensure distinct rows by application ID
        ->get()
        ->toArray();

    return response()->json([
        'html' => view('admin-panel.application-history.table.detail', ['applications' => $applications])->render(),
        'pagination' => view('admin-panel.application-history.table.pagination', [
            'limit' => $limit,
            'offset' => $offset,
            'totalPage' => $totalPages,
            'CurrentPage' => $currentPage
        ])->render()
    ]);
}

    

    public function CountData(Request $request)
    {

        $query = Application::query();
        
        
        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Application::count()],
            // ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Application::where('is_active', 1)->count()],
            // ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Application::where('is_active', 0)->count()]
        ];
        

        // Render the HTML view with filtered counts
        $html = view('admin-panel.application-history.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function deleteApplication($id)
    {
        $intake = Application::find($id);
        if ($intake) {
            $intake->delete();
            return response()->json(['message' => 'Application deleted successfully']);
        } else {
            return response()->json(['message' => 'Application not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:application,id',
        ]);
    
        // Find the intake and toggle its status
        $intake = Application::find($request->id);
        $intake->is_active = !$intake->is_active;
    
        // Save the updated intake and respond accordingly
        if ($intake->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully.',
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
        $application['id'] = $id;
        return view('admin-panel.application-history.edit',compact('application'));
    }

    public function update(Request $request, $id)
    {
        $numericKeys = array_filter(array_keys($request->all()), 'is_numeric');
        $existingPermissions = ApplicationPermission::where('intake_id', $id)->get();
        $existingPermissionIds = $existingPermissions->pluck('permission_id')->toArray();
        $permissionsToAdd = array_diff($numericKeys, $existingPermissionIds);
        $permissionsToRemove = array_diff($existingPermissionIds, $numericKeys);
        foreach ($permissionsToAdd as $permissionId) {
            ApplicationPermission::create([
                'intake_id' => $id,
                'permission_id' => $permissionId,
            ]);
        }

        foreach ($permissionsToRemove as $permissionId) {
            ApplicationPermission::where('intake_id', $id)
                        ->where('permission_id', $permissionId)
                        ->delete();
        }

        return redirect()->route('intake.index')->with('success', 'Application permissions updated successfully.');
    }
    public function studentInfo(Request $request){
        $application_id = $request->application_id;
        $student_info = StudentInformation::where('application_id',$request->application_id)->first();
       
        return view('admin-panel.application-history.edit-page.student-information',compact('application_id','student_info'));
    }

    public function student_info_save(Request $request)
    {

        $request->validate([
            'id' => 'nullable|integer',
            'counsellor_email' => 'nullable|email',
            'counsellor_number' => 'nullable|numeric'
        ]);

        $student_info = StudentInformation::find($request->id);
        if (!$student_info) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        if ($request->has('counsellor_email') && $request->counsellor_email != $student_info->counsellor_email) {
            $student_info->counsellor_email = $request->counsellor_email;
        }

        if ($request->has('counsellor_number') && $request->counsellor_number != $student_info->counsellor_number) {
            $student_info->counsellor_number = $request->counsellor_number;
        }

        if (!$student_info->save()) {
            return response()->json(['error' => 'Failed to save data'], 500);
        }

        return response()->json(['message' => 'Data has been saved successfully']);
    }

    public function course_detail(Request $request){
        $application_id = $request->application_id;
        $courseinfo = Application::where('applications.id',$application_id )
        ->join('courses','courses.id','applications.course_id')
        ->join('intakes','intakes.id','applications.intake_id')
        ->join('universities','universities.id','courses.university_id')
        ->select('courses.course_name','courses.institute_name','intakes.name as intake_name','courses.tuition_fee','courses.tuition_fee','universities.name as university_name')
        ->first();
        
        return view('admin-panel.application-history.edit-page.course_detail',compact('application_id','courseinfo'));
    }
    public function studentDocument(Request $request){
        $application_id = $request->application_id;
        $documents_list = StudentDocument::where('application_id',$application_id)->get();
        return view('admin-panel.application-history.edit-page.student-document', compact('application_id','documents_list'));
    }


    public function application_history(Request $request){
        $application_id = $request->application_id;
        $Applicationhistories = ApplicationTimeline::where('application_id',$application_id)
        ->leftjoin('application_stageses','application_stageses.id','application_timelines.stage_id')
        ->leftjoin('users','users.id','application_timelines.changed_by')
        ->select('application_stageses.name as stage_name','application_timelines.created_at  as created_at', 'application_timelines.reason','application_timelines.email_body','application_timelines.email_subject','application_timelines.email_send_to','application_timelines.type','users.name as created_by')
        ->orderBy('application_timelines.created_at', 'desc')
        ->get()->toArray();
        return view('admin-panel.application-history.edit-page.application_history', compact('application_id','Applicationhistories'));
    }

    public function comments(Request $request){
        $comments = Comment::where('is_active',1)->get()->toArray();
        $application_id = $request->application_id;
        $comments_show = applicationComment::where('application_id',$request->application_id)
        ->join('comments','comments.id','application_comments.comment_type')
        ->select('application_comments.comment','application_comments.sender_name','application_comments.sender_id','comments.name as comment_type','application_comments.*')
        ->get()->toArray();

    //  dd($comments_show);
        return view('admin-panel.application-history.edit-page.comment',compact('comments','application_id','comments_show'));
    }

    

    public function comments_save(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'commentType' => 'required|integer',
            'application_id' => 'required|integer',
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400); // 400 Bad Request
        }
       
        try {
            // Create a new comment
            $applicationComment = new applicationComment();
            $applicationComment->comment = $request->comment;
            $applicationComment->sender_id = Auth::user()->id;
            $applicationComment->sender_name = Auth::user()->name;
            $applicationComment->application_id = $request->application_id;
            $applicationComment->comment_type = $request->commentType;

            $applicationComment->save();
            return response()->json([
                'success' => 'true',
                'message' => 'Comment saved successfully!'
            ], 200); // 200 OK
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return response()->json([
                'success' => 'false',
                'message' => 'An error occurred while saving the comment.',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }


    public function fetch_comments(Request $request)
{
    $applicationId = $request->application_id;
    $date = $request->date; 
    
    if ($date) {
        try {
            // Use DateTime to parse the date in d-m-Y h:i:s A format
            $dateTime = \DateTime::createFromFormat('d-m-Y h:i:s A', $date);
            if ($dateTime) {
                // Format the date as a string in the format Y-m-d H:i:s
                $date = $dateTime->format('Y-m-d H:i:s');
            } else {
                // Handle invalid date format; return all comments if date is invalid
                $date = null;
            }
        } catch (\Exception $e) {
            // Handle invalid date format; return all comments if date is invalid
            $date = null;
        }
    }
    
    
    // Fetch comments
    $query = ApplicationComment::where('application_id', $applicationId);

    // Debugging the date format
    if ($date) {
        // Uncomment to debug the date if needed
        // dd($date);

        $query->where('application_comments.created_at', '>', $date);
    }
    // dd($date);
    $comments_show = $query
        ->join('comments', 'comments.id', '=', 'application_comments.comment_type')
        ->select('application_comments.comment', 'application_comments.sender_name', 'application_comments.sender_id', 'comments.name as comment_type', 'application_comments.created_at')
        ->get()
        ->toArray();
    
        foreach($comments_show as &$comments){
            $comments['created_at'] = DateTimeFormate($comments['created_at']);
        }

    return response()->json(['comments' => $comments_show], 200);
}


    public function urm_detail(Request $request){
        $request->application_id;
        $urmList = Application::where('applications.id',$request->application_id)
        ->leftjoin('courses','courses.id','applications.course_id')
        ->leftjoin('universities','universities.id','courses.university_id')
        ->select('universities.name as university_name','universities.urm_name','universities.urm_contact_no')
       ->first();
    //   dd($urmList);
        return view('admin-panel.application-history.edit-page.urm_detail',compact('urmList'));
    }

    public function assign_application(Request $request){
        $application_id = $request->application_id;
        $applicationAsignList = AssignApplication::where('assign_applications.application_id',$request->application_id)
        ->leftjoin('users','users.id','assign_applications.assign_to')
        ->leftjoin('users as assign_bylist','assign_bylist.id','assign_applications.assign_by')
        ->select('assign_bylist.name as assign_by','users.name as assign_to','assign_applications.assign_reason','assign_applications.disable_assign_reason','assign_applications.is_active','assign_applications.id as id','assign_applications.created_at as created_date')
        ->orderBy('assign_applications.is_active', 'desc')
       ->get();
        // dd($applicationAsignList);
        $permissionRecord = Permission::where('name', "application.create")->first()->toArray();
        $assignList = [];
        if ($permissionRecord) {
            // Get role IDs associated with the permission
            $rolePermissions = RolePermission::where('permission_id', $permissionRecord['id'])->get();
            
            if ($rolePermissions->isNotEmpty()) {
                $roleIds = $rolePermissions->pluck('role_id')->toArray();
                $permission_id = $permissionRecord['id'];
              
                $assignList = User::whereRaw('FIND_IN_SET(roles_ids, ?)', [implode(',', $roleIds)])->whereRaw('FIND_IN_SET(?, permission_ids)', [$permission_id])->select('name','id')->get()->toArray();
            }
        }
        return view('admin-panel.application-history.edit-page.application_assign',compact('application_id','applicationAsignList','assignList'));
    }

    public function assign_application_save(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer',
            'assign_to' => 'required|integer',
            'assign_reason' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $AssignApplication = new AssignApplication();
            $AssignApplication->application_id = $request->application_id;
            $AssignApplication->assign_to = $request->assign_to;
            $AssignApplication->is_active = 1; // Use 1 for true
            $AssignApplication->assign_by = Auth::User()->id;
            
            $AssignApplication->assign_reason = $request->assign_reason;
        
            if( $AssignApplication->save()){

                $userName = User::where('id',$request->assign_to)->first();
                // dd($userName);
                $ApplicationTimeline = new ApplicationTimeline;
                $ApplicationTimeline->application_id = $request->application_id;
                $ApplicationTimeline->reason = "Application Assigned To \"" . $userName->name . "\"<br>by \"" . Auth::user()->name . "\"<br>Comment: " . $request->assign_reason;

                $ApplicationTimeline->changed_by = Auth::user()->id;
                $ApplicationTimeline->save();
              
            }
    
            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Application assignment saved successfully!'
            ], 200);
    
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 200);
        }
    }

    public function update_status_assignee(Request $request)
    {
      
        $validated = $request->validate([
            'id' => 'required|exists:assign_applications,id', // Adjust table name if different
        ]);

        $assignment = AssignApplication::find($validated['id']);
        if ($assignment) {
            $assignment->is_active = 0;
            $assignment->disable_assign_reason =$request->reason;
            $assignment->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully!',
            ]);
        } else {
            // Respond with an error if the assignment is not found
            return response()->json([
                'status' => 'error',
                'message' => 'Assignment not found.',
            ], 404);
        }
    }
}

