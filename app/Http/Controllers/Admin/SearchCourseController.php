<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisaTool;
use App\Models\Course;
use App\Models\University;
use App\Models\Intake;
use App\Models\Permission;
use App\Models\visatoolPermission;

use Illuminate\Validation\Rule;
use Toastr;
use Illuminate\Support\Facades\Session;

class SearchCourseController extends Controller
{
    public function create(Request $request)
    {

        return view('admin-panel.search-courses.create');
    }

    public function index(Request $request)
    {
        $universities = University::where('is_active',1)->select('id','name')->get()->toArray();
        $courseTypes = config('constants.course_types');
        $courseIntake = Intake::where('is_active',1)->select('id','name')->get()->toArray();
        $accademic_entry_requirement = config('constants.accademic_entry_requirement');
        $locations = Course::where('is_active',1)->where('location','!='," ")->where('location','!=',null)->select('location')->distinct()->get()->toArray();
        $englishWaiver = config('constants.english_waiver');
        
        return view('admin-panel.search-course.index',compact('universities','courseTypes','courseIntake','accademic_entry_requirement','locations','englishWaiver'));
    }

    public function get_intake_detail(Request $request)
    {   
        Session::put('course_side', 1);
        $request->validate([
            'courseId' => 'required|integer|exists:courses,id',
        ]);
        $course = Course::find($request->courseId);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }
        $intakeIds = explode(',', $course->intake);
        $intake_available = Intake::whereIn('id', $intakeIds)->where('is_active',1)->select('name','id')->get();
        return response()->json($intake_available);
    }

    public function view(Request $request)
    {
        $visatool = new VisaTool;
        return view('admin-panel.search-courses.fronted.index', compact('visatool'));
    }

    public function view_data_fetch(Request $request)
    {
        $query = visatool::Query();
        $limit = @$request->$limit ?? 10;
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('courses.course_name', 'like', '%' . $request->search . '%');
        }

        // if ($request->has('filter_btn') && !empty($request->filter_btn)) {
        //     if ($request->filter_btn == "total") {
        //     } elseif ($request->filter_btn == "active") {
        //         $query->where('is_active', 1);
        //     } elseif ($request->filter_btn == "deactive") {
        //         $query->where('is_active', 0);
        //     }
        // }
        $query->where('is_active', 1);

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $courses = $query
        ->orderBy('courses.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('courses.course_name as name', 'courses.created_at as created_at', 'courses.id as id', 'courses.is_active as is_active', 'courses.link')
        ->get()
        ->toArray();

        $documents = [];
        $defaultPaths = [
            'docx' => 'https://w7.pngwing.com/pngs/854/300/png-transparent-microsoft-word-microsoft-office-2016-microsoft-excel-microsoft-template-blue-angle-thumbnail.png',
            'pdf' => 'https://play-lh.googleusercontent.com/kIwlXqs28otssKK_9AKwdkB6gouex_U2WmtLshTACnwIJuvOqVvJEzewpzuYBXwXQQ=w240-h480-rw'
        ];

        foreach($courses as $visa_tool){
            $extension = pathinfo($visa_tool['link'], PATHINFO_EXTENSION);
            if($extension == "pdf") {
                $path = asset('/admin_assets/images/icons/pdf.webp');
            } elseif($extension == "doc" || $extension == "docx") {
                $path = asset('/admin_assets/images/icons/word.webp');
            } elseif($extension == "xlsx") {
                $path = asset('/admin_assets/images/icons/excel.webp');
            } elseif($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                $path = asset('/admin_assets/images/icons/image.webp');
            } else {
                $path = asset('/admin_assets/images/icons/excel.webp');  // Fallback to Excel icon
            }


            $documents[] = [
                'name' => $visa_tool['name'],
                'link' => asset('/admin_assets/images/visatool_image/' . $visa_tool['link']),
                'extension' => $extension,
                'path' => $path
            ];
        }



        
        return response()->json([
            'html' => view('admin-panel.search-courses.fronted.view', ['documents' => $documents])->render(),
            'pagination' => view('admin-panel.search-courses.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required', // Validates the 'name' field to be required and unique in the courses table.
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip|max:2048', // Validates the 'file' field, allowing specific file types and a maximum size.
        ]);


        $visatool = new VisaTool;
        $visatool->name = $request->name;
        $visatool->is_active = $request->status ?? 0;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileFilename = time() . '_file.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/visatool_image/'), $fileFilename);
            $visatool->link = $fileFilename;
        }else{
            Session::put('error', 'Failed to create visatool. Please try again.');
            return redirect()->back()->withInput();
        }

        if ($visatool->save()) {
            Session::put('success', 'visatool created successfully.');
            return redirect()->route('visatool.index');
        } else {
            Session::put('error', 'Failed to create visatool. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function fetch(Request $request)
    {
        $query = Course::join('universities','universities.id','courses.university_id');

        $limit = @$request->$limit ?? 10;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('courses.course_name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('university_id') && !empty($request->university_id)) {
            $query->where('courses.university_id',  $request->university_id);
        }
        if ($request->has('course_type') && !empty($request->course_type)) {
            $query->where('courses.course_type',$request->course_type);
        }
        if ($request->has('intake') && !empty($request->intake)) {
           
            $query->whereRaw('FIND_IN_SET(?, courses.intake)', [$request->intake]);
        }
        // if ($request->has('aca_entry_requirement') && !empty($request->aca_entry_requirement)) {
        //     $query->where('courses.aca_entry_requirement', $request->aca_entry_requirement);
        // }
        if ($request->has('location') && !empty($request->location)) {
            $query->where('courses.location', $request->location);
        }
        

        $query->where('courses.is_active', 1);

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $courses = $query->orderBy('courses.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('courses.course_name','courses.created_at as created_at','courses.id as id','courses.id as id','courses.is_active as is_active','universities.name as university_name','courses.cover_image','courses.course_type','courses.duration','courses.location')
        ->get()
        ->toArray();
        // dd($courses);
        return response()->json([
            'html' => view('admin-panel.search-course.table.detail', ['courses' => $courses])->render(),
            'pagination' => view('admin-panel.search-course.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = visatool::query();


        $filters = [
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . visatool::count()],
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . visatool::where('is_active', 1)->count()],
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . visatool::where('is_active', 0)->count()]
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.search-course.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function delete($id)
    {
        $visatool = visatool::find($id);
        if ($visatool) {
            $visatool->delete();
            return response()->json(['message' => 'visatool deleted successfully']);
        } else {
            return response()->json(['message' => 'visatool not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 'required|integer|exists:courses,id',
        ]);

        // Find the visatool and toggle its status
        $visatool = visatool::find($request->id);
        $visatool->is_active = !$visatool->is_active;

        // Save the updated visatool and respond accordingly
        if ($visatool->save()) {
            return response()->json([
                'success' => true,
                'message' => 'visatool status updated successfully.',
                'status' => $visatool->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $visatool->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update visatool status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){


        $visatool = visatool::where('id',$id)->first();
        return view('admin-panel.search-courses.edit',compact('visatool'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip|max:2048',
        ]);

        // Find the VisaTool by ID
        $visatool = VisaTool::findOrFail($id);

        // Update the VisaTool's name and status
        $visatool->name = $request->name;
        $visatool->is_active = $request->status ? 1 : 0;

        // Handle the file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            $oldFilePath = public_path('admin_assets/images/visatool_image/' . $visatool->link);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            // Store the new file and update the VisaTool record
            $file = $request->file('file');
            $fileFilename = time() . '_file.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/visatool_image/'), $fileFilename);
            $visatool->link = $fileFilename;
        }

        // Save the updated record
        if ($visatool->save()) {
            Session::put('success', 'VisaTool updated successfully.');
            return redirect()->route('visatool.index');
        } else {
            Session::put('error', 'Failed to update VisaTool. Please try again.');
            return redirect()->back()->withInput();
        }
    }

}

