<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UniversityPresentation;
use App\Models\Permission;
use App\Models\University;
use App\Models\UniversityPresentationPermission;

use Illuminate\Validation\Rule;
use Toastr;
use Illuminate\Support\Facades\Session;

class UniversityPresentationController extends Controller
{
    public function create(Request $request)
    {
        $universities = University::where('is_active',1)->get()->toArray();
        return view('admin-panel.university-presentation.create' , compact('universities'));
    }

    public function index(Request $request)
    {   
        return view('admin-panel.university-presentation.index');
    }

    public function view(Request $request)
    {
        $UniversityPresentation = new UniversityPresentation;
        
        return view('admin-panel.university-presentation.fronted.index', compact('UniversityPresentation'));
    }

    public function view_data_fetch(Request $request)
    {
      
        $query = UniversityPresentation::leftjoin('universities','universities.id','university_presentations.university_id');
        $limit = @$request->$limit ?? 12;
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 12;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('university_presentations.name', 'like', '%' . $request->search . '%');
        }

        // if ($request->has('filter_btn') && !empty($request->filter_btn)) {
        //     if ($request->filter_btn == "total") {
        //     } elseif ($request->filter_btn == "active") {
        //         $query->where('is_active', 1);
        //     } elseif ($request->filter_btn == "deactive") {
        //         $query->where('is_active', 0);
        //     }
        // }
        $query->where('university_presentations.is_active', 1);

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $university_presentations = $query->orderBy('university_presentations.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('university_presentations.name as name', 'university_presentations.created_at as created_at', 'university_presentations.id as id', 'university_presentations.is_active as is_active', 'university_presentations.link','universities.name as university_name','universities.logo as university_logo')
        ->get()
        ->toArray();

        $documents = [];
        $defaultPaths = [
            'docx' => 'https://w7.pngwing.com/pngs/854/300/png-transparent-microsoft-word-microsoft-office-2016-microsoft-excel-microsoft-template-blue-angle-thumbnail.png',
            'pdf' => 'https://play-lh.googleusercontent.com/kIwlXqs28otssKK_9AKwdkB6gouex_U2WmtLshTACnwIJuvOqVvJEzewpzuYBXwXQQ=w240-h480-rw'
        ];

        foreach($university_presentations as $visa_tool){
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
                'link' => asset('/admin_assets/images/UniversityPresentation_image/' . $visa_tool['link']),
                'extension' => $extension,
                'path' => $path,
                'university_name' => $visa_tool['university_name'],
                'university_logo' => asset('/admin_assets/images/university_image/' . $visa_tool['university_logo'])
            ];
        }


        

        return response()->json([
            'html' => view('admin-panel.university-presentation.fronted.view', ['documents' => $documents])->render(),
            'pagination' => view('admin-panel.university-presentation.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required', // Validates the 'name' field to be required and unique in the university_presentations table.
            'file' => 'nullable|mimes:ppt,pptx|max:400000', // Validates the 'file' field, allowing specific file types and a maximum size.
        ]);


        $UniversityPresentation = new UniversityPresentation;
        $UniversityPresentation->name = $request->name;
        $UniversityPresentation->is_active = $request->status ?? 0;
        $UniversityPresentation->university_id = $request->university_id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileFilename = time() . '_file.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/UniversityPresentation/'), $fileFilename);
            $UniversityPresentation->link = $fileFilename;
        }else{
            Session::put('error', 'Failed to create Refund Request. Please try again.');
            return redirect()->back()->withInput();
        }

        if ($UniversityPresentation->save()) {
            Session::put('success', 'Refund Request created successfully.');
            return redirect()->route('university-presentation.index');
        } else {
            Session::put('error', 'Failed to create Refund Request. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function fetch(Request $request)
    {
        $query = UniversityPresentation::leftjoin('universities','universities.id','university_presentations.university_id');

        $limit = @$request->$limit ?? 12;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 12;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('university_presentations.name', 'like', '%' . $request->search . '%');
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

        $university_presentations = $query->orderBy('university_presentations.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('university_presentations.name as name','university_presentations.created_at as created_at','university_presentations.id as id','university_presentations.id as id','university_presentations.is_active as is_active','university_presentations.link','universities.name as university_name')
        ->get()
        ->toArray();
       
        return response()->json([
            'html' => view('admin-panel.university-presentation.table.detail', ['university_presentations' => $university_presentations])->render(),
            'pagination' => view('admin-panel.university-presentation.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = UniversityPresentation::query();


        $filters = [
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . UniversityPresentation::count()],
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . UniversityPresentation::where('is_active', 1)->count()],
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . UniversityPresentation::where('is_active', 0)->count()]
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.university-presentation.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function delete($id)
    {
        $UniversityPresentation = UniversityPresentation::find($id);
        if ($UniversityPresentation) {
            $UniversityPresentation->delete();
            return response()->json(['message' => 'Refund Request deleted successfully']);
        } else {
            return response()->json(['message' => 'Refund Request not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 'required|integer|exists:university_presentations,id',
        ]);

        // Find the UniversityPresentation and toggle its status
        $UniversityPresentation = UniversityPresentation::find($request->id);
        $UniversityPresentation->is_active = !$UniversityPresentation->is_active;

        // Save the updated UniversityPresentation and respond accordingly
        if ($UniversityPresentation->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Refund Request status updated successfully.',
                'status' => $UniversityPresentation->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $UniversityPresentation->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update Refund Request status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){


        $UniversityPresentation = UniversityPresentation::where('id',$id)->first();
        $universities = University::where(function($query) use ($UniversityPresentation) {
            $query->where('is_active', 1);
            $query->orWhere('id', $UniversityPresentation->university_id);
        })->get();
        return view('admin-panel.university-presentation.edit',compact('UniversityPresentation','universities'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx,txt|max:120000',
        ]);

        // Find the UniversityPresentation by ID
        $UniversityPresentation = UniversityPresentation::findOrFail($id);

        // Update the UniversityPresentation's name and status
        $UniversityPresentation->name = $request->name;
        $UniversityPresentation->is_active = $request->status ? 1 : 0;
        $UniversityPresentation->university_id = $request->university_id;

        // Handle the file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            $oldFilePath = public_path('admin_assets/images/UniversityPresentation/' . $UniversityPresentation->link);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            // Store the new file and update the UniversityPresentation record
            $file = $request->file('file');
            $fileFilename = time() . '_file.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/UniversityPresentation_image/'), $fileFilename);
            $UniversityPresentation->link = $fileFilename;
        }

        // Save the updated record
        if ($UniversityPresentation->save()) {
            Session::put('success', 'UniversityPresentation updated successfully.');
            return redirect()->route('university-presentation.index');
        } else {
            Session::put('error', 'Failed to update UniversityPresentation. Please try again.');
            return redirect()->back()->withInput();
        }
    }

}

