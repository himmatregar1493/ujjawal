<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RefundRequest;
use App\Models\Permission;
use App\Models\University;
use App\Models\RefundRequestPermission;

use Illuminate\Validation\Rule;
use Toastr;
use Illuminate\Support\Facades\Session;

class RefundRequestController extends Controller
{
    public function create(Request $request)
    {
        $universities = University::where('is_active',1)->get()->toArray();
        return view('admin-panel.refund-request.create' , compact('universities'));
    }

    public function index(Request $request)
    {   
        return view('admin-panel.refund-request.index');
    }

    public function view(Request $request)
    {
        $RefundRequest = new RefundRequest;
        
        return view('admin-panel.refund-request.fronted.index', compact('RefundRequest'));
    }

    public function view_data_fetch(Request $request)
    {
      
        $query = RefundRequest::leftjoin('universities','universities.id','refund_requests.university_id');
        $limit = @$request->$limit ?? 12;
        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 12;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('refund_requests.name', 'like', '%' . $request->search . '%');
        }

        // if ($request->has('filter_btn') && !empty($request->filter_btn)) {
        //     if ($request->filter_btn == "total") {
        //     } elseif ($request->filter_btn == "active") {
        //         $query->where('is_active', 1);
        //     } elseif ($request->filter_btn == "deactive") {
        //         $query->where('is_active', 0);
        //     }
        // }
        $query->where('refund_requests.is_active', 1);

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $refund_requests = $query->orderBy('refund_requests.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('refund_requests.name as name', 'refund_requests.created_at as created_at', 'refund_requests.id as id', 'refund_requests.is_active as is_active', 'refund_requests.link','universities.name as university_name','universities.logo as university_logo')
        ->get()
        ->toArray();

        $documents = [];
        $defaultPaths = [
            'docx' => 'https://w7.pngwing.com/pngs/854/300/png-transparent-microsoft-word-microsoft-office-2016-microsoft-excel-microsoft-template-blue-angle-thumbnail.png',
            'pdf' => 'https://play-lh.googleusercontent.com/kIwlXqs28otssKK_9AKwdkB6gouex_U2WmtLshTACnwIJuvOqVvJEzewpzuYBXwXQQ=w240-h480-rw'
        ];

        foreach($refund_requests as $visa_tool){
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
                'link' => asset('/admin_assets/images/RefundRequest_image/' . $visa_tool['link']),
                'extension' => $extension,
                'path' => $path,
                'university_name' => $visa_tool['university_name'],
                'university_logo' => asset('/admin_assets/images/university_image/' . $visa_tool['university_logo'])
            ];
        }


        

        return response()->json([
            'html' => view('admin-panel.refund-request.fronted.view', ['documents' => $documents])->render(),
            'pagination' => view('admin-panel.refund-request.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required', // Validates the 'name' field to be required and unique in the refund_requests table.
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip|max:400000', // Validates the 'file' field, allowing specific file types and a maximum size.
        ]);


        $RefundRequest = new RefundRequest;
        $RefundRequest->name = $request->name;
        $RefundRequest->is_active = $request->status ?? 0;
        $RefundRequest->university_id = $request->university_id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileFilename = time() . '_file.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/RefundRequest_image/'), $fileFilename);
            $RefundRequest->link = $fileFilename;
        }else{
            Session::put('error', 'Failed to create Refund Request. Please try again.');
            return redirect()->back()->withInput();
        }

        if ($RefundRequest->save()) {
            Session::put('success', 'Refund Request created successfully.');
            return redirect()->route('refund-request.index');
        } else {
            Session::put('error', 'Failed to create Refund Request. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function fetch(Request $request)
    {
        $query = RefundRequest::leftjoin('universities','universities.id','refund_requests.university_id');

        $limit = @$request->$limit ?? 12;

        $CurrentPage = @$request->page ?? 1;
        $offset = @($CurrentPage-1)*$limit ?? 12;
        if ($request->has('search') && !empty($request->search)) {
            $query->where('refund_requests.name', 'like', '%' . $request->search . '%');
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

        $refund_requests = $query->orderBy('refund_requests.id', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->select('refund_requests.name as name','refund_requests.created_at as created_at','refund_requests.id as id','refund_requests.id as id','refund_requests.is_active as is_active','refund_requests.link','universities.name as university_name')
        ->get()
        ->toArray();
       
        return response()->json([
            'html' => view('admin-panel.refund-request.table.detail', ['refund_requests' => $refund_requests])->render(),
            'pagination' => view('admin-panel.refund-request.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
        ]);
    }


    public function CountData(Request $request)
    {

        $query = RefundRequest::query();


        $filters = [
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . RefundRequest::count()],
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . RefundRequest::where('is_active', 1)->count()],
            ['link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 1, 'name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . RefundRequest::where('is_active', 0)->count()]
        ];


        // Render the HTML view with filtered counts
        $html = view('admin-panel.refund-request.table.countData', compact( 'filters'))->render();

        return response()->json(['html' => $html]);
    }

    public function delete($id)
    {
        $RefundRequest = RefundRequest::find($id);
        if ($RefundRequest) {
            $RefundRequest->delete();
            return response()->json(['message' => 'Refund Request deleted successfully']);
        } else {
            return response()->json(['message' => 'Refund Request not found'], 404);
        }
    }


    public function change_status(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'link'=> "https://crizac-assets.s3.ap-south-1.amazonaws.com/agent_docs/No_Objection_letter.doc", 'id' => 'required|integer|exists:refund_requests,id',
        ]);

        // Find the RefundRequest and toggle its status
        $RefundRequest = RefundRequest::find($request->id);
        $RefundRequest->is_active = !$RefundRequest->is_active;

        // Save the updated RefundRequest and respond accordingly
        if ($RefundRequest->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Refund Request status updated successfully.',
                'status' => $RefundRequest->is_active // Return the updated status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => $RefundRequest->is_active, // Return the current status, which is the same as before the save attempt
                'message' => 'Failed to update Refund Request status.'
            ], 500);
        }
    }

    public function edit(Request $request,$id){


        $RefundRequest = RefundRequest::where('id',$id)->first();
        $universities = University::where(function($query) use ($RefundRequest) {
            $query->where('is_active', 1);
            $query->orWhere('id', $RefundRequest->university_id);
        })->get();
        return view('admin-panel.refund-request.edit',compact('RefundRequest','universities'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,xls,xlsx,txt|max:120000',
        ]);

        // Find the RefundRequest by ID
        $RefundRequest = RefundRequest::findOrFail($id);

        // Update the RefundRequest's name and status
        $RefundRequest->name = $request->name;
        $RefundRequest->is_active = $request->status ? 1 : 0;
        $RefundRequest->university_id = $request->university_id;

        // Handle the file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            $oldFilePath = public_path('admin_assets/images/RefundRequest_image/' . $RefundRequest->link);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            // Store the new file and update the RefundRequest record
            $file = $request->file('file');
            $fileFilename = time() . '_file.' . $file->getClientOriginalExtension();
            $file->move(public_path('admin_assets/images/RefundRequest_image/'), $fileFilename);
            $RefundRequest->link = $fileFilename;
        }

        // Save the updated record
        if ($RefundRequest->save()) {
            Session::put('success', 'RefundRequest updated successfully.');
            return redirect()->route('refund-request.index');
        } else {
            Session::put('error', 'Failed to update RefundRequest. Please try again.');
            return redirect()->back()->withInput();
        }
    }

}

