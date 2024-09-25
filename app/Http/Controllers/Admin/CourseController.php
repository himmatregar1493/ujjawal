<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\Permission;
use App\Models\Course;
use App\Models\intake;
use App\Models\Country;
use Illuminate\Validation\Rule;
class CourseController extends Controller
{
    public function create(Request $request)
    {
        $intakes = intake::where('is_active',1)->get()->toArray();
        $universities = University::where('is_active',1)->get()->toArray();
        $countries = Country::get()->toArray();
        $courseTypes = config('constants.course_types');
      
        return view('admin-panel.course.create',compact('intakes','universities','countries','courseTypes'));
    }

    public function index(Request $request)
    {   
        return view('admin-panel.course.index');
    }

    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_type' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'university_id' => 'required|integer|exists:universities,id',
            'intake' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'application_fee' => 'nullable|numeric',
            'tuition_fees_inr' => 'nullable|numeric',
            'duration' => 'nullable|string|max:255',
            'web_url' => 'nullable|url',
            'campus' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Allow specific formats and size limit
        ]);
    
        // Create a new Course instance and assign the validated data
        $course = new Course;
        $course->course_name = $request->input('course_name');
        $course->course_type = $request->input('course_type');
        $course->country = $request->input('country');
        $course->state = $request->input('state');
        $course->university_id = $request->input('university_id');
        $course->intake = $request->input('intake') ? implode(',', $request->input('intake')) : '';
        $course->is_active = $request->input('is_active', false); // Default to false if not provided
        $course->application_fee = $request->input('application_fee');
        $course->tuition_fees_inr = $request->input('tuition_fees_inr');
        $course->duration = $request->input('duration');
        $course->web_url = $request->input('web_url');
        $course->campus = $request->input('campus');
        $course->description = $request->input('description');
        $course->location = $request->input('location');
        
    
        // Handle file upload
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/course_image/'), $filename);
            $course->cover_image = $filename; // Save the filename to the model
        }
    
        // Save the course data
        if ($course->save()) {
            return redirect()->route('course.index')->with('success', 'Course created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create course. Please try again.');
        }
    }

    public function fetch(Request $request)
    {
        $query = Course::Query();
        
        $limit = @$request->$limit ?? 10;
        
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('courses.name', 'like', '%' . $request->search . '%');
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
        
        $universities = $query->orderBy('courses.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('courses.course_name as name','courses.created_at as created_at','courses.id as id','courses.id as id','courses.is_active as is_active')
        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.course.table.detail', ['universities' => $universities])->render(),
            'pagination' => view('admin-panel.course.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {


        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Course::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Course::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Course::where('is_active', 0)->count()]
        ];
        

        // Render the HTML view with filtered counts
        $html = view('admin-panel.course.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function delete($id)
    {
        $university = University::find($id);
        if ($university) {
            $university->delete();
            return response()->json(['message' => 'University deleted successfully']);
        } else {
            return response()->json(['message' => 'University not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:universities,id',
        ]);
    
        // Find the university and toggle its status
        $course = Course::find($request->id); // Ensure this is the correct model
       
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.'
            ], 404);
        }
    
        $course->is_active = !$course->is_active;
    
        // Save the updated university and respond accordingly
        if ($course->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Course status updated successfully.',
                'status' => $course->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $course->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update Course status.'
            ], 500);
        }
    }
    
    public function edit(Request $request,$id){
        
        $intakes = intake::where('is_active',1)->get()->toArray();
        $universities = University::where('is_active',1)->get()->toArray();
        $countries = Country::get()->toArray();
        $courseTypes = config('constants.course_types');
        $course = Course::where('id',$id)->first();
        // dd($course);
        return view('admin-panel.course.edit',compact('intakes','universities','countries','courseTypes','course'));
    }

    public function update(Request $request, $id)
    {
     
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_type' => 'required|string|max:255',
            'country' => 'nullable|integer',
            'state' => 'nullable|integer',
            'university_id' => 'required|integer',
            'intake' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'application_fee' => 'required|numeric',
            'tuition_fees_inr' => 'required|numeric',
            'duration' => 'required|string|max:255',
            'web_url' => 'nullable|url',
            'campus' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Allow specific formats and size limit
        ]);

        $course = Course::find($id);
    
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found.');
        }
        
        $course->course_name = $request->input('course_name');
        $course->course_type = $request->input('course_type');
        $course->country = $request->input('country');
        $course->state = $request->input('state');
        $course->university_id = $request->input('university_id');
        $course->intake = $request->intake ? implode(',', $request->intake) : '';
        $course->is_active = $request->input('is_active');
        $course->application_fee = $request->input('application_fee');
        $course->tuition_fees_inr = $request->input('tuition_fees_inr');
        $course->duration = $request->input('duration');
        $course->web_url = $request->input('web_url');
        $course->campus = $request->input('campus');
        $course->description = $request->input('description');
        $course->location = $request->input('location');
       

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/course_image/'), $filename);
            $course->cover_image = $filename; // Save the filename to the model
        }

        // Save the changes
        if ($course->save()) {
            return redirect()->route('course.index')->with('success', 'Course updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update course. Please try again.');
        }
    }

}

