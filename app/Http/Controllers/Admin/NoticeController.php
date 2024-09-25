<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Permission;
use App\Models\University;
use App\Models\NoticePermission;

use Illuminate\Validation\Rule;
use Toastr;
use Illuminate\Support\Facades\Session;

class NoticeController extends Controller
{
    public function create(Request $request)
    {  
       
        $universities = University::
            where('is_active', 1)
            ->select('name','id')
            ->get()->toArray();
        return view('admin-panel.notice.create', compact('universities'));
    }

    public function index(Request $request)
    {
        if(!UserCan('entry-requirement.view')){
            return view('admin-panel.error.401');
        }   
        return view('admin-panel.notice.index');
    }

    public function view(Request $request)
    {
        $Notice = new Notice;
        return view('admin-panel.notice.fronted.index', compact('Notice'));
    }

    public function view_data_fetch(Request $request)
    {
        $query = Notice::Query();
        $limit = @$request->$limit ?? 10;
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('notices.english_requirements', 'like', '%' . $request->search . '%');
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

        $entry_requirement = $query
        ->orderBy('notices.id', 'desc')
        ->limit($limit)
        ->offset($offset)
       
        ->get()
        ->toArray();

        $documents = [];
        $defaultPaths = [
            'docx' => 'https://w7.pngwing.com/pngs/854/300/png-transparent-microsoft-word-microsoft-office-2016-microsoft-excel-microsoft-template-blue-angle-thumbnail.png',
            'pdf' => 'https://play-lh.googleusercontent.com/kIwlXqs28otssKK_9AKwdkB6gouex_U2WmtLshTACnwIJuvOqVvJEzewpzuYBXwXQQ=w240-h480-rw'
        ];

        foreach($entry_requirement as $visa_tool){
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
                'link' => asset('/admin_assets/images/Notice_image/' . $visa_tool['link']),
                'extension' => $extension,
                'path' => $path
            ];
        }



        
        return response()->json([
            'html' => view('admin-panel.notice.fronted.view', ['documents' => $documents])->render(),
            'pagination' => view('admin-panel.notice.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'english_requirement' => 'nullable|string',
            'academic_requirement' => 'nullable|string',
            'offer_timeline' => 'nullable|string',
            'type' => 'required|string',
            'finance' => 'nullable|string',
        ]);

        // Create new Notice instance
        $Notice = new Notice;
        $Notice->university_id = $request->university_id;
        $Notice->english_requirement = $request->english_requirement;
        $Notice->academic_requirement = $request->academic_requirement;
        $Notice->offer_timeline = $request->offer_timeline;
        $Notice->credibility = $request->credibility;
        $Notice->finance = $request->finance;
        $Notice->is_active = $request->status ?? 0;
        $Notice->type = $request->type;
        // Save data and handle response
        if ($Notice->save()) {
            return redirect()->route('entry-requirement.index')->with('success', 'Entry Requirement created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create Entry Requirement. Please try again.')->withInput();
        }
    }


    public function fetch(Request $request)
    {
        $query = Notice::Query();

        $limit = @$request->$limit ?? 10;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('notices.name', 'like', '%' . $request->search . '%');
        }
       

        if ($request->has('filter_btn') && !empty($request->filter_btn)) {
            if ($request->filter_btn == "total") {
            } elseif ($request->filter_btn == "active") {
                $query->where('notices.is_active', 1);
            } elseif ($request->filter_btn == "deactive") {
                $query->where('notices.is_active', 0);
            }
           
        }

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $notice = $query->orderBy('notices.id', 'desc')
        ->limit($limit)
        ->offset($offset)
       
        ->get()
        ->toArray();
       
        return response()->json([
            'html' => view('admin-panel.notice.table.detail', ['notice' => $notice])->render(),
            'pagination' => view('admin-panel.notice.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = Notice::query();


        $filters = [
            ['link'=> "", 'id' => 1, 'name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Notice::count()],
            ['link'=> "", 'id' => 1, 'name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Notice::where('is_active', 1)->count()],
            ['link'=> "", 'id' => 1, 'name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Notice::where('is_active', 0)->count()],
    
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.notice.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function delete($id)
    {
        $Notice = Notice::find($id);
        if ($Notice) {
            $Notice->delete();
            return response()->json(['message' => 'Notice deleted successfully']);
        } else {
            return response()->json(['message' => 'Notice not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:notices,id',
        ]);
    
        // Find the Notice and toggle its status
        $Notice = Notice::find($request->id);
        $Notice->is_active = !$Notice->is_active;
    
        // Save the updated Notice and respond accordingly
        if ($Notice->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Entry Requirement status updated successfully.',
                'status' => $Notice->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $Notice->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update Notice status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){


        $Notice = Notice::where('notices.id',$id)->join('universities','universities.id','notices.university_id')
        
        ->first();
        if($Notice){
            return view('admin-panel.notice.edit',compact('Notice'));
        }
        else{
            return "No record available";
        }
        
    }

    public function update(Request $request, $id)
{
    // Validation rules
    $request->validate([
        'university_id' => 'required|exists:universities,id',
        'english_requirement' => 'nullable|string',
        'academic_requirement' => 'nullable|string',
        'offer_timeline' => 'nullable|string',
        'finance' => 'nullable|string',
        'type' => 'required|string',
    ]);

    // Find the Notice by ID
    $Notice = Notice::findOrFail($id);

    // Update the Notice's attributes
    $Notice->university_id = $request->university_id;
    $Notice->english_requirement = $request->english_requirement;
    $Notice->academic_requirement = $request->academic_requirement;
    $Notice->offer_timeline = $request->offer_timeline;
    $Notice->credibility = $request->credibility;
    $Notice->finance = $request->finance;
    $Notice->type = $request->type;
    $Notice->is_active = $request->status ?? 0;

    if ($Notice->save()) {
        return redirect()->route('entry-requirement.index')->with('success', 'Entry Requirement updated successfully.');
    } else {
        return redirect()->back()->with('error', 'Failed to update Entry Requirement. Please try again.')->withInput();
    }
}

}

