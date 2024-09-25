<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EntryRequirement;
use App\Models\Permission;
use App\Models\University;
use App\Models\EntryRequirementPermission;

use Illuminate\Validation\Rule;
use Toastr;
use Illuminate\Support\Facades\Session;

class EntryRequirementController extends Controller
{
    public function create(Request $request)
    {  
       
        $universities = University::
            where('is_active', 1)
            ->select('name','id')
            ->get()->toArray();
        return view('admin-panel.entry-requirement.create', compact('universities'));
    }

    public function index(Request $request)
    {
        if(!UserCan('entry-requirement.view')){
            return view('admin-panel.error.401');
        }
        $universities = University::
            where('is_active', 1)
            ->select('name','id')
            ->get()->toArray();
            $entry_requirements = EntryRequirement::whereNotNull('english_requirement')
            ->where('english_requirement', '!=', '')
            ->distinct()
            ->pluck('english_requirement')
            ->toArray();
        
        return view('admin-panel.entry-requirement.index',compact('entry_requirements','universities'));
    }

    public function view(Request $request)
    {
        $EntryRequirement = new EntryRequirement;
        return view('admin-panel.entry-requirement.fronted.index', compact('EntryRequirement'));
    }

    public function view_data_fetch(Request $request)
    {
        $query = EntryRequirement::Query();
        $limit = @$request->$limit ?? 10;
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('entry_requirements.english_requirements', 'like', '%' . $request->search . '%');
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
        ->orderBy('entry_requirements.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('entry_requirements.english_requirements as name', 'entry_requirements.created_at as created_at', 'entry_requirements.id as id', 'entry_requirements.is_active as is_active' )
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
                'link' => asset('/admin_assets/images/EntryRequirement_image/' . $visa_tool['link']),
                'extension' => $extension,
                'path' => $path
            ];
        }



        
        return response()->json([
            'html' => view('admin-panel.entry-requirement.fronted.view', ['documents' => $documents])->render(),
            'pagination' => view('admin-panel.entry-requirement.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
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

        // Create new EntryRequirement instance
        $entryRequirement = new EntryRequirement;
        $entryRequirement->university_id = $request->university_id;
        $entryRequirement->english_requirement = $request->english_requirement;
        $entryRequirement->academic_requirement = $request->academic_requirement;
        $entryRequirement->offer_timeline = $request->offer_timeline;
        $entryRequirement->credibility = $request->credibility;
        $entryRequirement->finance = $request->finance;
        $entryRequirement->is_active = $request->status ?? 0;
        $entryRequirement->type = $request->type;
        // Save data and handle response
        if ($entryRequirement->save()) {
            return redirect()->route('entry-requirement.index')->with('success', 'Entry Requirement created successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create Entry Requirement. Please try again.')->withInput();
        }
    }


    public function fetch(Request $request)
    {
        $query = EntryRequirement::join('universities','universities.id','entry_requirements.university_id');

        $limit = @$request->$limit ?? 10;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('universities.name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('english_requirement') && !empty($request->english_requirement)) {
            $query->where('entry_requirements.english_requirement', $request->english_requirement);
        }
        if ($request->has('university_id') && !empty($request->university_id)) {
            $query->where('entry_requirements.university_id',$request->university_id);
        }
        if ($request->has('type') && !empty($request->type)) {
            $query->where('entry_requirements.type', $request->type);
        }
        if ($request->has('credibility') && !empty($request->credibility)) {
            $query->where('entry_requirements.credibility', $request->credibility);
        }

        if ($request->has('filter_btn') && !empty($request->filter_btn)) {
            if ($request->filter_btn == "total") {
            } elseif ($request->filter_btn == "active") {
                $query->where('entry_requirements.is_active', 1);
            } elseif ($request->filter_btn == "deactive") {
                $query->where('entry_requirements.is_active', 0);
            }
            elseif ($request->filter_btn == "UG") {
                $query->where('entry_requirements.type', "UG");
            }
            elseif ($request->filter_btn == "PG") {
                $query->where('entry_requirements.type', 'PG');
            }
        }

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $entry_requirement = $query->orderBy('entry_requirements.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('entry_requirements.finance','entry_requirements.credibility','entry_requirements.offer_timeline','entry_requirements.academic_requirement','universities.name as university_name','entry_requirements.english_requirement','entry_requirements.created_at as created_at','entry_requirements.id as id','entry_requirements.id as id','entry_requirements.is_active as is_active','entry_requirements.type')
        ->get()
        ->toArray();
       
        return response()->json([
            'html' => view('admin-panel.entry-requirement.table.detail', ['entry_requirement' => $entry_requirement])->render(),
            'pagination' => view('admin-panel.entry-requirement.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = EntryRequirement::query();


        $filters = [
            ['link'=> "", 'id' => 1, 'name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . EntryRequirement::count()],
            ['link'=> "", 'id' => 1, 'name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . EntryRequirement::where('is_active', 1)->count()],
            ['link'=> "", 'id' => 1, 'name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . EntryRequirement::where('is_active', 0)->count()],
            ['link'=> "", 'id' => 1, 'name' => 'PG', 'color' => 'white', 'background-color' => '#4b6e8d', 'label' => 'PG ' . EntryRequirement::where('type', 'PG')->count()],
            ['link'=> "", 'id' => 1, 'name' => 'UG', 'color' => 'white', 'background-color' => '#139d99', 'label' => 'UG ' . EntryRequirement::where('type', 'UG')->count()]
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.entry-requirement.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function delete($id)
    {
        $EntryRequirement = EntryRequirement::find($id);
        if ($EntryRequirement) {
            $EntryRequirement->delete();
            return response()->json(['message' => 'EntryRequirement deleted successfully']);
        } else {
            return response()->json(['message' => 'EntryRequirement not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:entry_requirements,id',
        ]);
    
        // Find the EntryRequirement and toggle its status
        $entryRequirement = EntryRequirement::find($request->id);
        $entryRequirement->is_active = !$entryRequirement->is_active;
    
        // Save the updated EntryRequirement and respond accordingly
        if ($entryRequirement->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Entry Requirement status updated successfully.',
                'status' => $entryRequirement->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $entryRequirement->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update EntryRequirement status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){


        $EntryRequirement = EntryRequirement::where('entry_requirements.id',$id)->join('universities','universities.id','entry_requirements.university_id')
        ->select('entry_requirements.english_requirement', 'entry_requirements.academic_requirement', 'entry_requirements.id as id', 'entry_requirements.is_active as is_active','universities.name as university_name','entry_requirements.offer_timeline','entry_requirements.credibility','entry_requirements.finance','entry_requirements.university_id','entry_requirements.type')
        ->first();
        if($EntryRequirement){
            return view('admin-panel.entry-requirement.edit',compact('EntryRequirement'));
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

    // Find the EntryRequirement by ID
    $entryRequirement = EntryRequirement::findOrFail($id);

    // Update the EntryRequirement's attributes
    $entryRequirement->university_id = $request->university_id;
    $entryRequirement->english_requirement = $request->english_requirement;
    $entryRequirement->academic_requirement = $request->academic_requirement;
    $entryRequirement->offer_timeline = $request->offer_timeline;
    $entryRequirement->credibility = $request->credibility;
    $entryRequirement->finance = $request->finance;
    $entryRequirement->type = $request->type;
    $entryRequirement->is_active = $request->status ?? 0;

    if ($entryRequirement->save()) {
        return redirect()->route('entry-requirement.index')->with('success', 'Entry Requirement updated successfully.');
    } else {
        return redirect()->back()->with('error', 'Failed to update Entry Requirement. Please try again.')->withInput();
    }
}

}

