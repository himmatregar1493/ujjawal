<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config; // Corrected import for Config
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;// Assuming this is your Mailable class    
use App\Models\University;
use App\Models\Permission;
use App\Models\Course;
use App\Models\intake;
use App\Models\Country;                                                                                       
use App\Models\Application;
use App\Models\EmailLog;
use App\Models\ApplicationTimeline;
use App\Models\StudentDocument;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Mail\ApplicationSubmit;
use App\Http\Controllers\MailLogController;
use Symfony\Component\Mime\Email;
use Illuminate\Mail\Events\MessageSent;
use App\Http\Controllers\Admin\EmailController;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        if(UserCan('application.create')){
            $intakes = intake::where('is_active',1)->get()->toArray();
            $universities = University::where('is_active',1)->get()->toArray();
            $countries = Country::get()->toArray();
            $courseTypes = config('constants.course_types');
            $courses = Course::where('is_active',1)->get()->toArray();
            Session::put('application_data', 1);
            return view('admin-panel.application.create', compact('intakes','universities','courseTypes','courses'));
        }else{
            return app(ErrorController::class)->UserNotAuthorized();
        }
        
    }
    
    public function detailFetch(Request $request) {
        $courseQuery = Course::join('universities','universities.id','courses.university_id'); // Initialize the query builder correctly
    
        // Check each parameter and apply filters accordingly
        if ($request->has('courses') && !empty($request->courses)) {
            $courseQuery->where('courses.id', $request->courses);
        }else{
            if ($request->has('intake') && !empty($request->intake)) {
                $courseQuery->whereRaw('FIND_IN_SET(?, intake)', [$request->intake]);
            }
            if ($request->has('course_type') && !empty($request->course_type)) {
                $courseQuery->where('course_type', $request->course_type);
            }
            if ($request->has('university_id') && !empty($request->university_id)) {
                $courseQuery->where('university_id', $request->university_id);
            }
        }
        
        $intake_id = $request->intake;
        $courses = $courseQuery->select('universities.name as university_name','courses.course_name','courses.tuition_fees_inr','courses.duration','courses.location','courses.course_type','courses.campus','courses.general_requirement','courses.entry_requirement','courses.cover_image','courses.id as id')->get()->toArray();
        $html = view('admin-panel.application.detail-part.detail-fetch', compact('courses','intake_id'))->render();
        return response()->json(['html' => $html]);
    }

    public function getCourseType(Request $request){
        if ($request->has('intake') && !empty($request->intake)) {
            $courses = Course::where('courses.is_active',1)->whereRaw('FIND_IN_SET(?, intake)', [$request->intake])->select('courses.course_type')->get()->toArray();
        }else{
            $courses = [];
        }
        return response()->json(['courses' => $courses]);  
    }

    public function getUniversityList(Request $request){
        $courseQuery = Course::where('courses.is_active', 1)
            ->join('universities', 'universities.id', '=', 'courses.university_id');
    
        if ($request->has('course_type') && !empty($request->course_type)) {
            $courseQuery->where('course_type', $request->course_type);
        }
    
        if ($request->has('intake') && !empty($request->intake)) {
            $courseQuery->whereRaw('FIND_IN_SET(?, intake)', [$request->intake]);
        }
    
        $university = $courseQuery->select('universities.name as name', 'universities.id as id')->get()->toArray();
    
        return response()->json(['university' => $university]);
    }

    public function getCourse(Request $request){
        $courseQuery = Course::where('courses.is_active', 1); // Initialize the query builder correctly
    
        if ($request->has('university_id') && !empty($request->university_id)) {
            $courseQuery->where('university_id', $request->university_id);
        }
    
        if ($request->has('intake') && !empty($request->intake)) {
            $courseQuery->whereRaw('FIND_IN_SET(?, intake)', [$request->intake]);
        }
    
        if ($request->has('course_type') && !empty($request->course_type)) {
            $courseQuery->where('course_type', $request->course_type);
        }
    
        $courses = $courseQuery->select('courses.course_name as name', 'courses.id as id')->get()->toArray();
        
        return response()->json(['courses' => $courses]);  
    }
        
    public function detail(Request $request)
    {   
       
        $value = Session::get('application_data');
        if($request->type =="course_type"){
            $value = Session::get('course_side');
            Session::forget('course_side');
             
        }
        if($value == 1){
            $application = new Application;
            $application->course_id = $request->course_id;
            $application->temp_application_submittion = 1;
            $application->intake_id = $request->intake_id;
            $application->save();         
            Session::put('application_id', $application->id);
            Session::forget('application_data');
        }else{
            $application_id = Session::get('application_id');
            $application = Application::where('id',$application_id)->first();
        }
       
        
        if($application){
            
            $courseQuery = Course::where('courses.id', $request->course_id)
            ->join('universities', 'universities.id', '=', 'courses.university_id')
            ->select(
                'universities.name as university_name',
                'courses.course_name',
                'courses.tuition_fees_inr',
                'courses.duration',
                'courses.location',
                'courses.course_type',
                'courses.campus',
                'courses.general_requirement',
                'courses.entry_requirement',
                'courses.cover_image',
                'courses.id as id',
                'courses.intake' 
            );
            $course = $courseQuery->first();
            if ($course) {
                $course = $course->toArray();
                $intakeIds = explode(',', $course['intake']);
                $intakeNames = Intake::whereIn('id', $intakeIds)
                    ->pluck('name')
                    ->toArray();
                $course['intake'] = implode(', ', $intakeNames);
            } else {

            }
           
            return view('admin-panel.application.detail-part.detail', compact('course','application'));
        }else{
            return redirect()->back();
        }

    }

    public function studentInfo(Request $request){
        $application_id = $request->application_id;
        $student_info = StudentInformation::where('application_id',$request->application_id)->first();
       
        return view('admin-panel.application.detail-part.student-information',compact('application_id','student_info'));
    }

    public function studentDocument(Request $request){
        $application_id = $request->application_id;
        $documents_list = StudentDocument::where('application_id',$application_id)->get();
     
        return view('admin-panel.application.detail-part.student-document', compact('application_id','documents_list'));
    }

    public function student_info_save(Request $request){
        $rules = [
            'id' => 'nullable|integer',
            'student_passport' => 'required|string|max:255',
            'student_first_name' => 'required|string|max:255',
            'student_last_name' => 'required|string|max:255',
            'student_email' => 'required|string|email|max:255',
            'counsellor_email' => 'required|string|email|max:255',
            'student_whatsapp' => 'required|string|max:20',
            'counsellor_number' => 'required|string|max:20',
            'gender' => 'required|string|max:10',
            'application_id' => 'required|integer'
        ];
    
        // Create the validator instance
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        if(@$request->id != ""){
            $student_info = StudentInformation::where('id',$request->id)->first();
        }else{
            $student_info = new StudentInformation;
        }
    
        $student_info->student_passport = $request->input('student_passport');
        $student_info->student_first_name = $request->input('student_first_name');
        $student_info->student_last_name = $request->input('student_last_name');
        $student_info->student_email = $request->input('student_email');
        $student_info->counsellor_email = $request->input('counsellor_email');
        $student_info->student_whatsapp = $request->input('student_whatsapp');
        $student_info->counsellor_number = $request->input('counsellor_number');
        $student_info->gender = $request->input('gender');
        $student_info->visa_refusal = $request->input('visa_refusal');
        if($request->input('visa_refusal') == "Yes"){
            $student_info->visa_refusal_reason = $request->input('visa_reason');
        }
        $student_info->application_id = $request->input('application_id');
    
        // Save the instance to the database
        if (!$student_info->save()) {
            return response()->json(['error' => 'Failed to save data'], 500);
        }
    
        return response()->json(['message' => 'Data has been saved successfully']);
    }

    public function upload_student_document(Request $request)
{
    $request->validate([
        'documents.*' => 'required|file|mimes:pdf,doc,docx,jpeg,jpg,png,webp|max:100240', // Adjust validation as needed
    ]);

    $studentId = 1; // Replace with actual student ID

    foreach ($request->file('documents') as $file) {
        
        
        $storedName = $file->store('student_documents'); // Stores in storage/app/student_documents
        
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/student_documents/'), $filename);

            StudentDocument::create([
                'application_id' => $request->application_id, // Set the actual student ID
                'original_name' => $originalName,
                'stored_name' => $filename,
                'mime_type' => $mimeType,
            
            ]);
        }

        return response()->json(['message' => 'Files uploaded successfully!']);
    }

    public function application_submit(Request $request)
    {
        $errors = [];
        $application_time_line = ApplicationTimeline::where('application_id',$request->application_id)->first();
        if($application_time_line){
            if($application_time_line['stage_id'] == 1){
                return response()->json([
                    'message' => 'Your application has been successfully processed.',
                    'status' => 'success'
                ], 200);
            }
        }
        // Check for uploaded documents
        $student_documents = StudentDocument::where('application_id', $request->application_id)->first();
        if ($student_documents == null) {
            $errors['document'] = "Documents Not Uploaded. Please Upload at least one document.";
        }

        // Check for saved student information
        $student_info = StudentInformation::where('application_id', $request->application_id)->first();
        if ($student_info == null) {
            $errors['information'] = "Student Information Not Saved. First Save Information then Save Final.";
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors, 'status' => 'failed'], 200);
        }

        $studentEmail = $student_info['student_email'];
        $counsellorEmail = $student_info['counsellor_email'];
        $UserEmail = Auth::User()->email;

        $emailTemplates = [
            [
                'email' => $studentEmail,
                'mailable' => new ApplicationSubmit([
                    'subject' => 'Student Email Subject Here',
                    'body' => 'This is the student email body.'
                ]),
                'subject' => 'Student Email Subject Here',
                'body' => 'This is the student email body.'
            ],
            [
                'email' => $counsellorEmail,
                'mailable' => new ApplicationSubmit([
                    'subject' => 'Counsellor Email Subject Here',
                    'body' => 'This is the counsellor email body.'
                ]),
                'subject' => 'Student Email Subject Here',
                'body' => 'This is the student email body.'
            ],
            [
                'email' => $UserEmail,
                'mailable' => new ApplicationSubmit([
                    'subject' => 'Counsellor Email Subject Here',
                    'body' => 'This is the counsellor email body.'
                ]),
                'subject' => 'Student Email Subject Here',
                'body' => 'This is the student email body.'
            ]
        ];

        foreach ($emailTemplates as $template) {
            
            $emailDetails = [];
            try {
                $sentMessage = Mail::to($template['email'])->send($template['mailable']);
                $emailDetails = EmailController::extractEmailDetails($sentMessage);
                EmailController::storeEmailLog($request->application_id, $template['email'], $emailDetails['body'], $emailDetails['subject'], 'sent', $emailDetails['attachments']);
            } catch (\Exception $e) {
                EmailController::storeEmailLog($request->application_id, $template['email'], "", "", 'failed',"blank");
            }
           
            if (count($emailDetails) > 0) { // or if (!empty($emailDetails)) {
                // This code will only run if $emailDetails has at least one key-value pair
                ApplicationTimeline::create([
                    'application_id' => $request->application_id,
                    'stage_id' => 1,
                    'type' => "email_send",
                    'email_body' => isset($emailDetails['body']) ? $emailDetails['body'] : "",
                    'email_subject' => isset($emailDetails['subject']) ? $emailDetails['subject'] : "",
                    'email_send_to' => isset($template['email']) ? $template['email'] : "",
                ]);
            }
            
            sleep(2); 
        }

        $application = Application::where('id',$request->application_id)->first();
        $application->temp_application_submittion = 0;
        $application->counseller_id = Auth::User()->id;
        $application->application_stage = 1;
        $application->save();

        $ApplicationTimeline = new ApplicationTimeline;
        $ApplicationTimeline->application_id = $request->application_id;
        $ApplicationTimeline->reason = "Application Created Successfully";
        $ApplicationTimeline->changed_by = Auth::User()->id;
        $ApplicationTimeline->stage_id = 1;
        $ApplicationTimeline->save();

        return response()->json([
            'message' => 'Your application has been successfully processed.',
            'status' => 'success'
        ], 200);
    }   

   
}
