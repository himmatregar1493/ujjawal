<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisaTool;
use App\Models\Permission;
use App\Models\visatoolPermission;

use Illuminate\Validation\Rule;
use Toastr;
use Illuminate\Support\Facades\Session;

class VisaToolsController extends Controller
{
    public function create(Request $request)
    {

        return view('admin-panel.visatool.create');
    }

    public function index(Request $request)
    {
        return view('admin-panel.visatool.index');
    }

    public function view(Request $request)
    {
        $visatool = new VisaTool;
        return view('admin-panel.visatool.fronted.index', compact('visatool'));
    }

    public function view_data_fetch(Request $request)
    {
        $query = visatool::Query();
        $limit = @$request->$limit ?? 10;
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('visa_tools.name', 'like', '%' . $request->search . '%');
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

        $visa_tools = $query
        ->orderBy('visa_tools.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('visa_tools.name as name', 'visa_tools.created_at as created_at', 'visa_tools.id as id', 'visa_tools.is_active as is_active', 'visa_tools.link')
        ->get()
        ->toArray();

        $documents = [];
        $defaultPaths = [
            'docx' => 'https://w7.pngwing.com/pngs/854/300/png-transparent-microsoft-word-microsoft-office-2016-microsoft-excel-microsoft-template-blue-angle-thumbnail.png',
            'pdf' => 'https://play-lh.googleusercontent.com/kIwlXqs28otssKK_9AKwdkB6gouex_U2WmtLshTACnwIJuvOqVvJEzewpzuYBXwXQQ=w240-h480-rw'
        ];

        foreach($visa_tools as $visa_tool){
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
            'html' => view('admin-panel.visatool.fronted.view', ['documents' => $documents])->render(),
            'pagination' => view('admin-panel.visatool.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required', // Validates the 'name' field to be required and unique in the visa_tools table.
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
        $query = visatool::Query();

        $limit = @$request->$limit ?? 10;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 10;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('visa_tools.name', 'like', '%' . $request->search . '%');
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

        $visa_tools = $query->orderBy('visa_tools.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('visa_tools.name as name','visa_tools.created_at as created_at','visa_tools.id as id','visa_tools.id as id','visa_tools.is_active as is_active','visa_tools.link')
        ->get()
        ->toArray();

        return response()->json([
            'html' => view('admin-panel.visatool.table.detail', ['visa_tools' => $visa_tools])->render(),
            'pagination' => view('admin-panel.visatool.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
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
        $html = view('admin-panel.visatool.table.countData', compact( 'filters'))->render();

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
            'link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 'required|integer|exists:visa_tools,id',
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
        return view('admin-panel.visatool.edit',compact('visatool'));
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

