<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Permission;
use App\Models\clientPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ClientController extends Controller
{
    public function create(Request $request)
    {
        if(UserCan('client.create')){
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                return view('admin-panel.client.create');
            }else{
                return view('admin-panel.error.401');
            }
        }else{
            return view('admin-panel.error.401');
        }
    }

    public function index(Request $request)
    {
        if(UserCan('client.view')){
            return view('admin-panel.client.index');
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function store(Request $request)
    {
        // Check if the user has permission to create a client
        if (UserCan('client.create')) {
            $companyId = getCompanyId();

            if (is_numeric($companyId)) {
                // Validate input fields
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'phone' => 'nullable|string|max:20',
                    'alt_phone' => 'nullable|string|max:20',
                    'status' => 'nullable|boolean',
                ]);

                DB::beginTransaction(); // Start a transaction

                try {
                    // Create new Client
                    $client = new Client;
                    $client->name = $request->name;
                    $client->email = $request->email;
                    $client->phone = $request->phone;
                    $client->alt_phone = $request->alt_phone;
                    $client->company_id = Auth::User()->company_id;
                    $client->created_by = Auth::User()->id;
                    $client->is_active = $request->status ?? 0;

                    // Save the client
                    if (!$client->save()) {
                        throw new \Exception('Failed to save client.');
                    }

                    // Create wallet for the client
                    WalletCreate($client->id, 'client');

                    DB::commit(); // Commit transaction if everything went well

                    return redirect()->route('client.index')->with('success', 'Client created successfully.');
                } catch (\Exception $e) {
                    DB::rollBack(); // Roll back the transaction in case of an error
                    return redirect()->back()->withInput()->with('error', $e->getMessage());
                }
            } else {
                return view('admin-panel.error.401');
            }
        } else {
            return view('admin-panel.error.401');
        }
    }



    public function fetch(Request $request)
    {
        if(UserCan('client.view')){
            $query = Client::Query();

            $limit = @$request->$limit ?? 10;

            $CurrentPage = @$request->page ?? 1;
            $offset = @($CurrentPage-1)*$limit ?? 10;
            if ($request->has('search') && !empty($request->search)) {
                $query->where('clients.name', 'like', '%' . $request->search . '%');
            }

            $companyId = getCompanyId();
                if (is_numeric($companyId)) {
                    $query->where('company_id', $companyId);
                }else if($companyId =="Super Admin"){

                }else{
                    return response()->json([
                        'html' => "No data Available",
                        'pagination' => "",
                    ]);
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

            $clients = $query->orderBy('clients.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->select('clients.name as name','clients.created_at as created_at','clients.id as id','clients.id as id','clients.is_active as is_active')
            ->get()
            ->toArray();

            return response()->json([
                'html' => view('admin-panel.client.table.detail', ['clients' => $clients])->render(),
                'pagination' => view('admin-panel.client.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
            ]);
        }else{
            return view('admin-panel.error.401');
        }

    }


    public function CountData(Request $request)
    {
        if(UserCan('client.view')){
            $query = Client::query();
            $filters = [
                ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Client::count()],
                ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Client::where('is_active', 1)->count()],
                ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Client::where('is_active', 0)->count()]
            ];
            $html = view('admin-panel.client.table.countData', compact( 'filters'))->render();
            return response()->json(['html' => $html]);
        }else{
            return view('admin-panel.error.401');
        }

    }




    public function change_status(Request $request)
    {

        if(UserCan('client.edit')){

            if($request->id){
                $client = Client::find($request->id);
                $client->is_active = !$client->is_active;
                if ($client->save()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Client status updated successfully.',
                        'status' => $client->is_active // Return the updated status
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'status' => $client->is_active, // Return the current status, which is the same as before the save attempt
                        'message' => 'Failed to update client status.'
                    ], 500);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'status' => "", // Return the current status, which is the same as before the save attempt
                    'message' => 'Failed to update client status.'
                ], 500);
            }
        }else{
            return view('admin-panel.error.401');
        }
    }

    public function edit(Request $request,$id){


        if(UserCan('client.edit')){
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                $client = Client::where('id',$id)->first();
                return view('admin-panel.client.edit',compact('client'));
            }else{
                return view('admin-panel.error.401');
            }
        }else{
            return view('admin-panel.error.401');
        }

    }


    public function view(Request $request,$id){


        if(UserCan('client.edit')){
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                $client = Client::where('id',$id)->first();
                return view('admin-panel.client.view',compact('client'));
            }else{
                return view('admin-panel.error.401');
            }
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function update(Request $request, $id)
{
    if(UserCan('client.edit')){
        $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'phone' => 'nullable|string|max:20',
                    'alt_phone' => 'nullable|string|max:20',
                    'status' => 'nullable|boolean',
                ]);

                // Find the client by ID
                $client = Client::findOrFail($id);

                // Update the client with the validated data
                $client->update([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'] ?? null,
                    'phone' => $validatedData['phone'] ?? null,
                    'alt_phone' => $validatedData['alt_phone'] ?? null,
                    'is_active' => $validatedData['status'] ?? false,
                ]);
            }else{
                return view('admin-panel.error.401');
            }
        // Redirect back to the client list with a success message
        return redirect()->route('client.index')->with('success', 'Client updated successfully.');
    } else {
        return view('admin-panel.error.401');
    }
}
}

