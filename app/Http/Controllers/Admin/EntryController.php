<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\Wallet;
use App\Models\Permission;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Bill;
use App\Models\BillProduct;
use App\Models\clientPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDF;
use Exception;

class EntryController extends Controller
{
    public function create(Request $request)
    {
        if(UserCan('entry.create')){
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                return view('admin-panel.entry.create');
            }else{
                return view('admin-panel.error.401');
            }
        }else{
            return view('admin-panel.error.401');
        }
    }



    public function index(Request $request)
    {
        if(UserCan('entry.view')){
            return view('admin-panel.entry.index');
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function store(Request $request)
    {
        if (UserCan('entry.create')) {
            $companyId = getCompanyId();
                if (is_numeric($companyId)) {
                    $request->validate([
                        'name' => 'required|string|max:255',
                        'email' => 'nullable|email|max:255',
                        'phone' => 'nullable|string|max:20',
                        'alt_phone' => 'nullable|string|max:20',
                        'status' => 'nullable|boolean',
                    ]);

                    $client = new Client;
                    $client->name = $request->name;
                    $client->email = $request->email;
                    $client->phone = $request->phone;
                    $client->alt_phone = $request->alt_phone;
                    $client->company_id = Auth::User()->company_id;
                    $client->created_by = Auth::User()->id;
                    $client->is_active = $request->status ?? 0;

                    // Save the client and redirect based on success or failure
                    if ($client->save()) {
                        return redirect()->route('entry.index')->with('success', 'Client created successfully.');
                    } else {
                        return redirect()->back()->withInput()->with('error', 'Failed to create entry. Please try again.');
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
        if(UserCan('entry.view')){
            $query = Bill::leftjoin('users','users.id','bills.user_id');

            $limit = @$request->$limit ?? 10;

            $CurrentPage = @$request->page ?? 1;
            $offset = @($CurrentPage-1)*$limit ?? 10;
            if ($request->has('search') && !empty($request->search)) {
                $query->where('clients.name', 'like', '%' . $request->search . '%');
            }

            $companyId = getCompanyId();
                if (is_numeric($companyId)) {
                    $query->where('bills.company_id', $companyId);
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
                    $query->where('bills.is_active', 1);
                } elseif ($request->filter_btn == "deactive") {
                    $query->where('bills.is_active', 0);
                }
            }

            $totalPage = $query->count();
            $totalPage = ceil($totalPage / $limit);

            $bills = $query->orderBy('bills.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->select('bills.*','users.name as created_by','bills.id as id')
            ->get()
            ->toArray();

            return response()->json([
                'html' => view('admin-panel.entry.table.detail', ['bills' => $bills])->render(),
                'pagination' => view('admin-panel.entry.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
            ]);
        }else{
            return view('admin-panel.error.401');
        }

    }


    public function CountData(Request $request)
    {
        if(UserCan('entry.view')){
            $query = Client::query();
            $filters = [
                ['name' => 'total', 'color' => 'white', 'background-color' => 'gray', 'label' => 'Total ' . Client::count()],
                ['name' => 'active', 'color' => 'white', 'background-color' => 'green', 'label' => 'Active ' . Client::where('is_active', 1)->count()],
                ['name' => 'deactive', 'color' => 'white', 'background-color' => 'red', 'label' => 'Deactive ' . Client::where('is_active', 0)->count()]
            ];
            $html = view('admin-panel.entry.table.countData', compact( 'filters'))->render();
            return response()->json(['html' => $html]);
        }else{
            return view('admin-panel.error.401');
        }

    }




    public function change_status(Request $request)
    {

        if(UserCan('entry.edit')){

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


        if(UserCan('entry.edit')){
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                $client = Client::where('id',$id)->first();
                return view('admin-panel.entry.edit',compact('client'));
            }else{
                return view('admin-panel.error.401');
            }
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function view(Request $request,$id){


        if(UserCan('entry.view')){
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                $bill_detail = Bill::where('bills.id',$id)->first();


                $bill_row_detail = Bill::where('bills.id',$id)->leftjoin('bill_products','bill_products.bill_id','bills.id')->get()->Toarray();
                return view('admin-panel.entry.view',compact('bill_detail','bill_row_detail'));
            }else{
                return view('admin-panel.error.401');
            }
        }else{
            return view('admin-panel.error.401');
        }

    }

    public function downloadBillPDF(Request $request, $id)
    {
        if(UserCan('entry.view')){
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                // Fetch bill details
                $bill_detail = Bill::where('bills.id', $id)->first();

                // Fetch bill product details
                $bill_row_detail = Bill::where('bills.id', $id)
                    ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
                    ->get()->toArray();

                // Generate PDF
                $pdf = PDF::loadView('admin-panel.entry.download', compact('bill_detail', 'bill_row_detail'));
                $pdf->setPaper('a4', 'landscape'); // Set the paper size and orientation

                // Download the PDF file
                return $pdf->download('bill-details.pdf');
            } else {
                return view('admin-panel.error.401');
            }
        } else {
            return view('admin-panel.error.401');
        }
    }

    public function update(Request $request, $id)
{
    if(UserCan('entry.edit')){
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
        return redirect()->route('entry.index')->with('success', 'Client updated successfully.');
    } else {
        return view('admin-panel.error.401');
    }
}

    public function get_list_vendor_client(Request $request)
    {
        $company_id = Auth()->user()->company_id;

        // Check if 'type' parameter is passed and company_id exists
        if ($company_id) {
            // For fetching vendors
            if ($request->type === "vendor") {
                $list = Vendor::where('is_active', 1)
                            ->where('company_id', $company_id)
                            ->get()
                            ->toArray();
            }
            // For fetching clients
            else if ($request->type === "client") {
                $list = Client::where('is_active', 1)
                            ->where('company_id', $company_id)
                            ->get()
                            ->toArray();
            }

            // Return the list as a JSON response
            return response()->json($list);
        }

        // If no company_id or invalid request type, return an empty response
        return response()->json([], 400); // Bad request or empty response
    }

    public function get_detail(Request $request){
        $company_id = Auth()->user()->company_id;
        if ($company_id) {
            if ($request->type === "vendor") {
                $vendor = Vendor::where('is_active', 1)
                                ->where('id', $request->id)
                                ->where('company_id', $company_id)
                                ->first();
                $data = $vendor ? $vendor->toArray() : [];
            } elseif ($request->type === "client") {
                $client = Client::where('is_active', 1)
                                ->where('id', $request->id)
                                ->where('company_id', $company_id)
                                ->first();
                $data = $client ? $client->toArray() : [];
            } else {
                return response()->json(['error' => 'Invalid type'], 400);
            }
            $data['type'] = $request->type;
            return response()->json([
                'html' => view('admin-panel.entry.detail.detail_part',compact('data'))->render(),
                'entry_part' => view('admin-panel.entry.detail.entry_part',compact('data'))->render(),
                'data' => $data
            ]);
        }
        return response()->json([], 400);
    }

    public function get_transaction_page(Request $request){
        $company_id = Auth()->user()->company_id;
        if ($company_id) {
            // Client & Vendor Section
            if ($request->type === "vendor") {
                $wallet_detail = Wallet::where('wallet_person_id',$request->id)->where('wallets_type','vendor')->first();
                if($wallet_detail){
                    $data['remaining_amount'] = $wallet_detail['amount'];
                }else{
                    WalletCreate($request->id,'vendor');
                    $data['remaining_amount'] = 0;
                }
            } elseif ($request->type === "client") {
                $wallet_detail = Wallet::where('wallet_person_id',$request->id)->where('wallets_type','client')->first();
                if($wallet_detail){
                    $data['remaining_amount'] = $wallet_detail['amount'];
                }else{
                    WalletCreate($request->id,'client');
                    $data['remaining_amount'] = 0;
                }
            } else {
                return response()->json(['error' => 'Invalid type'], 400);
            }
            // User Section
            $user_wallet = Wallet::where('wallet_person_id',Auth::User()->id)->where('wallets_type','user')->first();
            if($user_wallet){
                $data['user_remaining_amount'] = $user_wallet['amount'];
            }else{
                WalletCreate(Auth::User()->id,'user');
                $data['user_remaining_amount'] = 0;
            }

            $data['type'] = $request->type;
            $data['id'] = $request->id;

            if($request->transaction_type =="debit"){
                return response()->json([
                    'html' => view('admin-panel.entry.detail.money_sending_page',compact('data'))->render(),
                ]);
            }else if($request->transaction_type =="credit"){
                return response()->json([
                    'html' => view('admin-panel.entry.detail.money_received_page',compact('data'))->render(),
                ]);
            }
            else if($request->transaction_type =="Material Send"){
                $productList = Product::where('is_active',1)->where('company_id',$company_id)->get()->toArray();
                $transaction_type = $request->transaction_type;
                return response()->json([
                    'html' => view('admin-panel.entry.detail.material_sending',compact('data','productList','transaction_type'))->render(),
                ]);
            }else if($request->transaction_type =="Material Receive"){
                $productList = Product::where('is_active',1)->where('company_id',$company_id)->get()->toArray();
                $transaction_type = $request->transaction_type;
                return response()->json([
                    'html' => view('admin-panel.entry.detail.material_receiving',compact('data','productList','transaction_type'))->render(),
                ]);
            }

        }
        return response()->json([], 400);
    }

    public function saveTransaction(Request $request)
    {
        $company_id = auth()->user()->company_id;

        if ($company_id) {


            if ($request->type === "vendor" && $request->transaction_type === "debit") {

                // Start a database transaction
                DB::beginTransaction();

                try {
                    // Get user wallet details
                    $wallet_detail = Wallet::where('wallets_type', 'user')
                        ->where('wallet_person_id', auth()->user()->id)
                        ->first();

                    // Get vendor wallet details
                    $vendor_wallet_detail = Wallet::where('wallets_type', 'vendor')
                        ->where('wallet_person_id', $request->vendor_id)
                        ->first();

                    // Check if both wallets exist and user has sufficient funds
                    if ($wallet_detail && $vendor_wallet_detail && $wallet_detail->amount >= $request->amount) {

                        // First Transaction - Debit from User
                        $transaction = new Transaction();
                        $transaction->wallet_id = $wallet_detail->id;
                        $transaction->transaction_type = $request->transaction_type;
                        $transaction->created_by = auth()->user()->id;
                        $transaction->transaction_status = 'success';
                        $transaction->transaction_amount = $request->amount;
                        $transaction->last_wallet_history = json_encode($wallet_detail);
                        $transaction->company_id = $company_id;  // Corrected variable name
                        $transaction->sender_id = auth()->user()->id;
                        $transaction->sender_type = 'user';
                        $transaction->receiver_id = $request->vendor_id;
                        $transaction->receiver_type = 'vendor';
                        $transaction->remark = "User Send Money To Vendor";

                        if (!$transaction->save()) {
                            throw new \Exception('Failed to save user transaction.');
                        }

                        // Deduct from user's wallet
                        $wallet_detail->amount -= $request->amount;
                        if (!$wallet_detail->save()) {
                            throw new \Exception('Failed to update user wallet.');
                        }

                        // Second Transaction - Credit to Vendor
                        $transaction2 = new Transaction();
                        $transaction2->wallet_id = $vendor_wallet_detail->id;
                        $transaction2->transaction_type = 'credit';
                        $transaction2->created_by = auth()->user()->id;
                        $transaction2->transaction_status = 'success';
                        $transaction2->transaction_amount = $request->amount;
                        $transaction2->last_wallet_history = json_encode($vendor_wallet_detail);  // Update vendor's history
                        $transaction2->company_id = $company_id;  // Corrected variable name
                        $transaction2->sender_id = auth()->user()->id;
                        $transaction2->sender_type = 'user';
                        $transaction2->receiver_id = $request->vendor_id;
                        $transaction2->receiver_type = 'vendor';
                        $transaction2->remark = "User Send Money To Vendor";

                        if (!$transaction2->save()) {
                            throw new \Exception('Failed to save vendor transaction.');
                        }

                        // Add to vendor's wallet
                        $vendor_wallet_detail->amount += $request->amount;
                        if (!$vendor_wallet_detail->save()) {
                            throw new \Exception('Failed to update vendor wallet.');
                        }

                        // Commit the transaction if everything is successful
                        DB::commit();

                        // Return success message or perform further actions
                        return response()->json(['status' => 'success', 'message' => 'Transaction completed successfully'], 200);

                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'Insufficient funds in user account or vendor wallet not found.'], 422);
                    }

                } catch (\Exception $e) {
                    // Rollback the transaction if any exception occurs
                    DB::rollBack();

                    // Return error message to the user
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }else if ($request->type === "vendor" && $request->transaction_type === "credit") {

                // Start a database transaction
                DB::beginTransaction();

                try {
                    // Get user wallet details
                    $wallet_detail = Wallet::where('wallets_type', 'user')
                        ->where('wallet_person_id', auth()->user()->id)
                        ->first();

                    // Get vendor wallet details
                    $vendor_wallet_detail = Wallet::where('wallets_type', 'vendor')
                        ->where('wallet_person_id', $request->vendor_id)
                        ->first();

                    // Check if both wallets exist and user has sufficient funds
                    if ($wallet_detail && $vendor_wallet_detail) {

                        // First Transaction - Debit from User
                        $transaction = new Transaction();
                        $transaction->wallet_id = $wallet_detail->id;
                        $transaction->transaction_type = $request->transaction_type;
                        $transaction->created_by = auth()->user()->id;
                        $transaction->transaction_status = 'success';
                        $transaction->transaction_amount = $request->amount;
                        $transaction->last_wallet_history = json_encode($wallet_detail);
                        $transaction->company_id = $company_id;  // Corrected variable name
                        $transaction->sender_id = $request->vendor_id;
                        $transaction->sender_type = 'vendor';
                        $transaction->receiver_id = auth()->user()->id;
                        $transaction->receiver_type = 'user';
                        $transaction->remark = "Vendor Send Money To User";

                        if (!$transaction->save()) {
                            throw new \Exception('Failed to save user transaction.');
                        }

                        // Deduct from user's wallet
                        $wallet_detail->amount += $request->amount;
                        if (!$wallet_detail->save()) {
                            throw new \Exception('Failed to update user wallet.');
                        }

                        // Second Transaction - Credit to Vendor
                        $transaction2 = new Transaction();
                        $transaction2->wallet_id = $vendor_wallet_detail->id;
                        $transaction2->transaction_type = 'credit';
                        $transaction2->created_by = auth()->user()->id;
                        $transaction2->transaction_status = 'success';
                        $transaction2->transaction_amount = $request->amount;
                        $transaction2->last_wallet_history = json_encode($vendor_wallet_detail);  // Update vendor's history
                        $transaction2->company_id = $company_id;  // Corrected variable name
                        $transaction2->sender_id = $request->vendor_id;
                        $transaction2->sender_type = 'vendor';
                        $transaction2->receiver_id = auth()->user()->id;
                        $transaction2->receiver_type = 'user';
                        $transaction2->remark = "Vendor Send Money To User";

                        if (!$transaction2->save()) {
                            throw new \Exception('Failed to save vendor transaction.');
                        }

                        // Add to vendor's wallet
                        $vendor_wallet_detail->amount -= $request->amount;
                        if (!$vendor_wallet_detail->save()) {
                            throw new \Exception('Failed to update vendor wallet.');
                        }

                        // Commit the transaction if everything is successful
                        DB::commit();

                        // Return success message or perform further actions
                        return response()->json(['status' => 'success', 'message' => 'Transaction completed successfully'], 200);

                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'Insufficient funds in user account or vendor wallet not found.'], 422);
                    }

                } catch (\Exception $e) {
                    // Rollback the transaction if any exception occurs
                    DB::rollBack();

                    // Return error message to the user
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }

            else if ($request->type === "client" && $request->transaction_type === "debit") {

                // Start a database transaction
                DB::beginTransaction();

                try {
                    // Get user wallet details
                    $wallet_detail = Wallet::where('wallets_type', 'user')
                        ->where('wallet_person_id', auth()->user()->id)
                        ->first();

                    // Get vendor wallet details
                    $client_wallet_detail = Wallet::where('wallets_type', 'client')
                        ->where('wallet_person_id', $request->vendor_id)
                        ->first();

                    // Check if both wallets exist and user has sufficient funds
                    if ($wallet_detail && $client_wallet_detail && $wallet_detail->amount >= $request->amount) {

                        // First Transaction - Debit from User
                        $transaction = new Transaction();
                        $transaction->wallet_id = $wallet_detail->id;
                        $transaction->transaction_type = $request->transaction_type;
                        $transaction->created_by = auth()->user()->id;
                        $transaction->transaction_status = 'success';
                        $transaction->transaction_amount = $request->amount;
                        $transaction->last_wallet_history = json_encode($wallet_detail);
                        $transaction->company_id = $company_id;  // Corrected variable name
                        $transaction->sender_id = auth()->user()->id;
                        $transaction->sender_type = 'user';
                        $transaction->receiver_id = $request->client_id;
                        $transaction->receiver_type = 'client';
                        $transaction->remark = "User Send Money To Client";

                        if (!$transaction->save()) {
                            throw new \Exception('Failed to save user transaction.');
                        }

                        // Deduct from user's wallet
                        $wallet_detail->amount -= $request->amount;
                        if (!$wallet_detail->save()) {
                            throw new \Exception('Failed to update user wallet.');
                        }

                        // Second Transaction - Credit to Vendor
                        $transaction2 = new Transaction();
                        $transaction2->wallet_id = $client_wallet_detail->id;
                        $transaction2->transaction_type = 'credit';
                        $transaction2->created_by = auth()->user()->id;
                        $transaction2->transaction_status = 'success';
                        $transaction2->transaction_amount = $request->amount;
                        $transaction2->last_wallet_history = json_encode($client_wallet_detail);  // Update vendor's history
                        $transaction2->company_id = $company_id;  // Corrected variable name
                        $transaction2->sender_id = auth()->user()->id;
                        $transaction2->sender_type = 'user';
                        $transaction2->receiver_id = $request->client_id;
                        $transaction2->receiver_type = 'client';
                        $transaction2->remark = "User Send Money To Client";

                        if (!$transaction2->save()) {
                            throw new \Exception('Failed to save vendor transaction.');
                        }

                        // Add to vendor's wallet
                        $client_wallet_detail->amount += $request->amount;
                        if (!$client_wallet_detail->save()) {
                            throw new \Exception('Failed to update vendor wallet.');
                        }

                        // Commit the transaction if everything is successful
                        DB::commit();

                        // Return success message or perform further actions
                        return response()->json(['status' => 'success', 'message' => 'Transaction completed successfully'], 200);

                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'Insufficient funds in user account or vendor wallet not found.'], 422);
                    }

                } catch (\Exception $e) {
                    // Rollback the transaction if any exception occurs
                    DB::rollBack();

                    // Return error message to the user
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }else if ($request->type === "client" && $request->transaction_type === "credit") {

                // Start a database transaction
                DB::beginTransaction();

                try {
                    // Get user wallet details
                    $wallet_detail = Wallet::where('wallets_type', 'user')
                        ->where('wallet_person_id', auth()->user()->id)
                        ->first();

                    // Get vendor wallet details
                    $client_wallet_detail = Wallet::where('wallets_type', 'client')
                        ->where('wallet_person_id', $request->client_id)
                        ->first();

                    // Check if both wallets exist and user has sufficient funds
                    if ($wallet_detail && $client_wallet_detail) {

                        // First Transaction - Debit from User
                        $transaction = new Transaction();
                        $transaction->wallet_id = $wallet_detail->id;
                        $transaction->transaction_type = $request->transaction_type;
                        $transaction->created_by = auth()->user()->id;
                        $transaction->transaction_status = 'success';
                        $transaction->transaction_amount = $request->amount;
                        $transaction->last_wallet_history = json_encode($wallet_detail);
                        $transaction->company_id = $company_id;  // Corrected variable name
                        $transaction->sender_id = $request->client_id;
                        $transaction->sender_type = 'vendor';
                        $transaction->receiver_id = auth()->user()->id;
                        $transaction->receiver_type = 'user';
                        $transaction->remark = "Vendor Send Money To User";

                        if (!$transaction->save()) {
                            throw new \Exception('Failed to save user transaction.');
                        }

                        // Deduct from user's wallet
                        $wallet_detail->amount += $request->amount;
                        if (!$wallet_detail->save()) {
                            throw new \Exception('Failed to update user wallet.');
                        }

                        // Second Transaction - Credit to Vendor
                        $transaction2 = new Transaction();
                        $transaction2->wallet_id = $client_wallet_detail->id;
                        $transaction2->transaction_type = 'credit';
                        $transaction2->created_by = auth()->user()->id;
                        $transaction2->transaction_status = 'success';
                        $transaction2->transaction_amount = $request->amount;
                        $transaction2->last_wallet_history = json_encode($client_wallet_detail);  // Update vendor's history
                        $transaction2->company_id = $company_id;  // Corrected variable name
                        $transaction2->sender_id = $request->client_id;
                        $transaction2->sender_type = 'vendor';
                        $transaction2->receiver_id = auth()->user()->id;
                        $transaction2->receiver_type = 'user';
                        $transaction2->remark = "Vendor Send Money To User";

                        if (!$transaction2->save()) {
                            throw new \Exception('Failed to save vendor transaction.');
                        }

                        // Add to vendor's wallet
                        $client_wallet_detail->amount -= $request->amount;
                        if (!$client_wallet_detail->save()) {
                            throw new \Exception('Failed to update vendor wallet.');
                        }

                        // Commit the transaction if everything is successful
                        DB::commit();

                        // Return success message or perform further actions
                        return response()->json(['status' => 'success', 'message' => 'Transaction completed successfully'], 200);

                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'Insufficient funds in user account or vendor wallet not found.'], 422);
                    }

                } catch (\Exception $e) {
                    // Rollback the transaction if any exception occurs
                    DB::rollBack();

                    // Return error message to the user
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }else if ($request->type === "client" && $request->transaction_type === "Material Send") {
                DB::beginTransaction(); // Start a new transaction

                try {
                    $validator = Validator::make($request->all(), [
                        'product_id' => 'required|array',
                        'product_price' => 'required|array',
                        'quantity' => 'required|array',
                        'discount' => 'required|array',
                        'total_price' => 'required|array',
                        'tax_pay' => 'required|array',
                        'cgst' => 'required|array',
                        'sgst' => 'required|array',
                        'igst' => 'required|array',
                        'total' => 'required|array',
                        'type' => 'required|string',
                        'client_id' => 'required|integer',
                        'transaction_type' => 'required|string'
                    ]);

                    // If validation fails, return errors as JSON
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => 'error',
                            'errors' => $validator->errors()
                        ], 422);
                    }

                    $grandTotal = 0;

                    foreach ($request->total as $value) {
                        $grandTotal += $value;
                    }

                    foreach ($request->product_id as $index => $product_id) {
                        $product = Product::find($product_id); // Fetch the product by ID

                        if (!$product) {
                            throw new Exception('Product not found.'); // Throw exception for rollback
                        }

                        if ($product->remaining_quantity >= $request->quantity[$index]) {
                            $product->remaining_quantity -= $request->quantity[$index];
                            $product->save();
                        } else {
                            throw new Exception('Not enough quantity available for product ID ' . $product_id . 'Name ' .$product->name); // Throw exception for rollback
                        }
                    }
                    $customer_detail = Client::where('id',$request->client_id)->first();
                    $bill = new Bill();
                    $bill->date = $request->date;
                    $bill->type = $request->type;
                    $bill->user_id = Auth::User()->id;
                    $bill->customer_name = $customer_detail['name'];
                    $bill->customer_address = $customer_detail['address'];
                    $bill->company_id = $company_id;
                    $bill->bill_for = $request->transaction_type;
                    $bill->bill_for_id = $request->client_id;
                    $bill->save();

                    foreach ($request->product_id as $index => $product_id) {
                        $productbill = new BillProduct();
                        $productbill->bill_id = $bill->id;
                        $productbill->product_id = $product_id;
                        $productbill->product_name = $request->product_name[$index];
                        $productbill->product_price = $request->product_price[$index];
                        $productbill->product_qty = $request->quantity[$index];
                        $productbill->product_discount = $request->discount[$index];
                        $productbill->total_tax_pay = $request->tax_pay[$index];
                        $productbill->cgst = $request->cgst[$index];
                        $productbill->sgst = $request->sgst[$index];
                        $productbill->igst = $request->igst[$index];
                        $productbill->ratecgst = $request->cgst[$index];
                        $productbill->ratesgst = $request->sgst[$index];
                        $productbill->rateigst = $request->igst[$index];
                        $productbill->total = $request->total[$index];
                        $productbill->save();
                    }

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaction saved successfully.  Bill Generated Bill No '.$bill->id,
                        'bill_id' => $bill->id
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ], 400);
                }
            }else if ($request->type === "vendor" && $request->transaction_type === "Material Send") {

                DB::beginTransaction(); // Start a new transaction

                try {
                    $validator = Validator::make($request->all(), [
                        'product_id' => 'required|array',
                        'product_price' => 'required|array',
                        'quantity' => 'required|array',
                        'discount' => 'required|array',
                        'total_price' => 'required|array',
                        'tax_pay' => 'required|array',
                        'cgst' => 'required|array',
                        'sgst' => 'required|array',
                        'igst' => 'required|array',
                        'total' => 'required|array',
                        'type' => 'required|string',
                        'client_id' => 'required|integer',
                        'transaction_type' => 'required|string'
                    ]);

                    // If validation fails, return errors as JSON
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => 'error',
                            'errors' => $validator->errors()
                        ], 422);
                    }

                    $grandTotal = 0;

                    foreach ($request->total as $value) {
                        $grandTotal += $value;
                    }

                    foreach ($request->product_id as $index => $product_id) {
                        $product = Product::find($product_id); // Fetch the product by ID

                        if (!$product) {
                            throw new Exception('Product not found.'); // Throw exception for rollback
                        }
                        $product->remaining_quantity += $request->quantity[$index];

                        if ($product->save()) {

                        } else {
                            throw new Exception('Not enough quantity available for product ID ' . $product_id . 'Name ' .$product->name); // Throw exception for rollback
                        }
                    }
                    $customer_detail = Client::where('id',$request->client_id)->first();
                    $bill = new Bill();
$bill->date = $request->date;
                    $bill->type = $request->type;
                    $bill->user_id = Auth::User()->id;
                    $bill->customer_name = $customer_detail['name'];
                    $bill->customer_address = $customer_detail['address'];
                    $bill->company_id = $company_id;
                    $bill->bill_for = $request->transaction_type;
                    $bill->bill_for_id = $request->client_id;
                    $bill->save();

                    foreach ($request->product_id as $index => $product_id) {
                        $productbill = new BillProduct();
                        $productbill->bill_id = $bill->id;
                        $productbill->product_id = $product_id;
                        $productbill->product_name = $request->product_name[$index];
                        $productbill->product_price = $request->product_price[$index];
                        $productbill->product_qty = $request->quantity[$index];
                        $productbill->product_discount = $request->discount[$index];
                        $productbill->total_tax_pay = $request->tax_pay[$index];
                        $productbill->cgst = $request->cgst[$index];
                        $productbill->sgst = $request->sgst[$index];
                        $productbill->igst = $request->igst[$index];
                        $productbill->ratecgst = $request->cgst[$index];
                        $productbill->ratesgst = $request->sgst[$index];
                        $productbill->rateigst = $request->igst[$index];
                        $productbill->total = $request->total[$index];
                        $productbill->save();
                    }

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaction saved successfully.  Bill Generated Bill No '.$bill->id,
                        'bill_id' => $bill->id
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ], 400);
                }
            }else if ($request->type === "client" && $request->transaction_type === "Material Receive") {
                DB::beginTransaction();

                try {
                    $validator = Validator::make($request->all(), [
                        'product_id' => 'required|array',
                        'product_price' => 'required|array',
                        'quantity' => 'required|array',
                        'discount' => 'required|array',
                        'total_price' => 'required|array',
                        'tax_pay' => 'required|array',
                        'cgst' => 'required|array',
                        'sgst' => 'required|array',
                        'igst' => 'required|array',
                        'total' => 'required|array',
                        'type' => 'required|string',
                        'client_id' => 'required|integer',
                        'transaction_type' => 'required|string'
                    ]);

                    // If validation fails, return errors as JSON
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => 'error',
                            'errors' => $validator->errors()
                        ], 422);
                    }

                    $grandTotal = 0;

                    foreach ($request->total as $value) {
                        $grandTotal += $value;
                    }

                    foreach ($request->product_id as $index => $product_id) {
                        $product = Product::find($product_id); // Fetch the product by ID

                        if (!$product) {
                            throw new Exception('Product not found.'); // Throw exception for rollback
                        }
                        $product->remaining_quantity += $request->quantity[$index];
                        $product->save();

                    }
                    $customer_detail = Client::where('id',$request->client_id)->first();
                    $bill = new Bill();
$bill->date = $request->date;
                    $bill->type = $request->type;
                    $bill->user_id = Auth::User()->id;
                    $bill->company_id = $company_id;
                    $bill->customer_name = $customer_detail['name'];
                    $bill->customer_address = $customer_detail['address'];
                    $bill->bill_for = $request->transaction_type;
                    $bill->bill_for_id = $request->client_id;
                    $bill->save();

                    foreach ($request->product_id as $index => $product_id) {
                        $productbill = new BillProduct();
                        $productbill->bill_id = $bill->id;
                        $productbill->product_id = $product_id;
                        $productbill->product_name = $request->product_name[$index];
                        $productbill->product_price = $request->product_price[$index];
                        $productbill->product_qty = $request->quantity[$index];
                        $productbill->product_discount = $request->discount[$index];
                        $productbill->total_tax_pay = $request->tax_pay[$index];
                        $productbill->cgst = $request->cgst[$index];
                        $productbill->sgst = $request->sgst[$index];
                        $productbill->igst = $request->igst[$index];
                        $productbill->ratecgst = $request->cgst[$index];
                        $productbill->ratesgst = $request->sgst[$index];
                        $productbill->rateigst = $request->igst[$index];
                        $productbill->total = $request->total[$index];
                        $productbill->save();
                    }

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaction saved successfully.  Bill Generated Bill No '.$bill->id,
                        'bill_id' => $bill->id
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ], 400);
                }
            }else if ($request->type === "vendor" && $request->transaction_type === "Material Receive") {

                DB::beginTransaction(); // Start a new transaction

                try {
                    $validator = Validator::make($request->all(), [
                        'product_id' => 'required|array',
                        'product_price' => 'required|array',
                        'quantity' => 'required|array',
                        'discount' => 'required|array',
                        'total_price' => 'required|array',
                        'tax_pay' => 'required|array',
                        'cgst' => 'required|array',
                        'sgst' => 'required|array',
                        'igst' => 'required|array',
                        'total' => 'required|array',
                        'type' => 'required|string',
                        'client_id' => 'required|integer',
                        'transaction_type' => 'required|string'
                    ]);

                    // If validation fails, return errors as JSON
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => 'error',
                            'errors' => $validator->errors()
                        ], 422);
                    }

                    $grandTotal = 0;

                    foreach ($request->total as $value) {
                        $grandTotal += $value;
                    }

                    foreach ($request->product_id as $index => $product_id) {
                        $product = Product::find($product_id); // Fetch the product by ID

                        if (!$product) {
                            throw new Exception('Product not found.'); // Throw exception for rollback
                        }
                        $product->remaining_quantity += $request->quantity[$index];

                        if ($product->save()) {

                        } else {
                            throw new Exception('Not enough quantity available for product ID ' . $product_id . 'Name ' .$product->name); // Throw exception for rollback
                        }
                    }
                    $customer_detail = Vendor::where('id',$request->client_id)->first();
                    $bill = new Bill();
$bill->date = $request->date;
                    $bill->type = $request->type;
                    $bill->customer_name = $customer_detail['name'];
                    $bill->customer_address = $customer_detail['address'];
                    $bill->user_id = Auth::User()->id;
                    $bill->company_id = $company_id;
                    $bill->bill_for = $request->transaction_type;
                    $bill->bill_for_id = $request->client_id;
                    $bill->save();

                    foreach ($request->product_id as $index => $product_id) {
                        $productbill = new BillProduct();
                        $productbill->bill_id = $bill->id;
                        $productbill->product_id = $product_id;
                        $productbill->product_name = $request->product_name[$index];
                        $productbill->product_price = $request->product_price[$index];
                        $productbill->product_qty = $request->quantity[$index];
                        $productbill->product_discount = $request->discount[$index];
                        $productbill->total_tax_pay = $request->tax_pay[$index];
                        $productbill->cgst = $request->cgst[$index];
                        $productbill->sgst = $request->sgst[$index];
                        $productbill->igst = $request->igst[$index];
                        $productbill->ratecgst = $request->cgst[$index];
                        $productbill->ratesgst = $request->sgst[$index];
                        $productbill->rateigst = $request->igst[$index];
                        $productbill->total = $request->total[$index];
                        $productbill->save();
                    }

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaction saved successfully.  Bill Generated Bill No '.$bill->id,
                        'bill_id' => $bill->id
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ], 400);
                }
            }
        }

        return response()->json(['status' => 'failed', 'message' => 'Company ID not found'], 400);
    }

    public function product_detail(Request $request){
        $product = Product::where('id',$request->product_id)->first()->ToArray();
        return response()->json(['status' => 'success', 'message' => 'Product Data','product_detail' =>$product], 200);
    }

}

