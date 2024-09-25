<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\Permission;
use App\Models\UniversityPermission;
use App\Models\intake;
use Illuminate\Validation\Rule;
use Toastr;
use Illuminate\Support\Facades\Session;

class UniversityController extends Controller
{
    public function create(Request $request)
    {
        
        return view('admin-panel.university.create');
    }

    public function index(Request $request)
    {   
        return view('admin-panel.university.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:universities,name',
        ]);

        $university = new University;
        $university->name = $request->name;
        $university->is_active = $request->status ?? 0;
        $university->urm_name = $request->urm_name;
        $university->urm_contact_no = $request->urm_contact_no;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoFilename = time() . '_logo.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('admin_assets/images/university_image/'), $logoFilename);
            $university->logo = $logoFilename;
        }

        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerFilename = time() . '_banner.' . $banner->getClientOriginalExtension();
            $banner->move(public_path('admin_assets/images/university_image/'), $bannerFilename);
            $university->banner = $bannerFilename;
        }
       

        if ($university->save()) {
            Session::put('success', 'University created successfully.');
            return redirect()->route('university.index');
        } else {
            Session::put('error', 'Failed to create university. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function fetch(Request $request)
    {
        $query = University::Query();
        
        $limit = @$request->$limit ?? 10;
        
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('universities.name', 'like', '%' . $request->search . '%');
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
        
        $universities = $query->orderBy('universities.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('universities.name as name','universities.created_at as created_at','universities.id as id','universities.id as id','universities.is_active as is_active')
        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.university.table.detail', ['universities' => $universities])->render(),
            'pagination' => view('admin-panel.university.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = University::query();
        
        
        $filters = [
            ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . University::count()],
            ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . University::where('is_active', 1)->count()],
            ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . University::where('is_active', 0)->count()]
        ];
        

        // Render the HTML view with filtered counts
        $html = view('admin-panel.university.table.countData', compact( 'filters'))->render();

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
        $university = University::find($request->id);
        $university->is_active = !$university->is_active;
    
        // Save the updated university and respond accordingly
        if ($university->save()) {
            return response()->json([
                'success' => true,
                'message' => 'University status updated successfully.',
                'status' => $university->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $university->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update university status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){
        
        $intakes = intake::where('is_active',1)->get()->toArray();
        $university = University::where('id',$id)->first();
        return view('admin-panel.university.edit',compact('intakes','university'));
    }

    public function update(Request $request, $id)
{
    // Validate the incoming request
    $request->validate([
        'name' => [
            'required',
            Rule::unique('universities')->ignore($id),
        ],
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Find the university by ID
    $university = University::findOrFail($id);

    // Update the university's name and status
    $university->name = $request->name;
    $university->is_active = $request->status ? 1 : 0;
    $university->urm_name = $request->urm_name;
    $university->urm_contact_no = $request->urm_contact_no;
    // Handle the logo upload if a new file is provided

    if ($request->hasFile('logo')) {
        // Delete the old logo if it exists
        

        // Store the new logo and update the university record
        $logo = $request->file('logo');
        $logoFilename = time() . '_logo.' . $logo->getClientOriginalExtension();
        $logo->move(public_path('admin_assets/images/university_image/'), $logoFilename);
        $university->logo = $logoFilename;
        
    }

    // Handle the banner upload if a new file is provided
    if ($request->hasFile('banner')) {
        
        

        // Store the new banner and update the university record
        $banner = $request->file('banner');
        $bannerFilename = time() . '_banner.' . $banner->getClientOriginalExtension();
        $banner->move(public_path('admin_assets/images/university_image/'), $bannerFilename);
        $university->banner = $bannerFilename;
    }



    // Save the updated university record
    if ($university->save()) {
        
        Session::put('success', 'University updated successfully.');
       
        return redirect()->route('university.index');
    } else {
        Session::put('error', 'Failed to update university. Please try again.');
        return redirect()->back()->withInput();
    }
}

}

