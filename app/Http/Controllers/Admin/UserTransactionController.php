<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use App\Models\IntakePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class UserTransactionController extends Controller
{
    public function create(Request $request)
    {
        if (UserCan('user_transaction.create')) {
            $companyId = getCompanyId();
            $available_amount = Wallet::where('company_id', $companyId)->pluck('amount')->first();
            if (is_numeric($companyId)) {
                if(CheckLoginUserMainAdmin()){
                    $userList = User::where('users.company_id',$companyId)->leftjoin('wallets','wallets.wallet_person_id','users.id')->where('wallets.wallets_type','user')->select('users.id as id','users.name as name','wallets.amount as amount')->get()->toArray();
                    return view('admin-panel.user_transaction.create',compact('userList','available_amount'));
                }
            }
        }else {
            return view('admin-panel.error.401');
        }
        return view('admin-panel.error.401');
    }



    public function index(Request $request)
    {

        if (UserCan('user_transaction.view')) {
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                $heading = "";
                if(CheckLoginUserMainAdmin()){
                    $userList = User::where('users.company_id',$companyId)->leftjoin('wallets','wallets.wallet_person_id','users.id')->where('wallets.wallets_type','user')->select('users.id as id','users.name as name','wallets.amount as amount')->get()->toArray();
                    $heading = "Users Transactions List";
                }else{
                    $userList = [];
                    $heading = "Self Transaction";

                };
                return view('admin-panel.user_transaction.index',compact('userList','heading'));
            }
        }else {
            return view('admin-panel.error.401');
        }

    }

    public function store(Request $request)
    {

        if (UserCan('user_transaction.create')) {
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                if(CheckLoginUserMainAdmin()){

                    $validated = $request->validate([
                        'transaction_type' => 'required|in:credit,debit',
                        'amount' => 'required|numeric|min:0.01',
                    ]);
                    DB::beginTransaction();
                    try {
                        $wallet_detail = Wallet::where('wallet_person_id', $request->user_id)
                            ->where('wallets_type', 'user')
                            ->first();
                        $company_wallet_detail = Wallet::where('wallet_person_id', $companyId)
                            ->where('wallets_type', 'company')
                            ->first();

                        if ($request->transaction_type === 'debit') {
                            if ($wallet_detail->amount < $request->amount) {
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['amount' => 'Enter Amount Not available in User Account, you cannot debit this amount.']);
                            }

                            $transaction = new Transaction;
                            $transaction->wallet_id = $wallet_detail->id;
                            $transaction->transaction_type = $request->transaction_type;
                            $transaction->created_by = Auth::user()->id;
                            $transaction->transaction_status = 'success';
                            $transaction->transaction_amount = $request->amount;
                            $transaction->last_wallet_history = json_encode($wallet_detail);
                            $transaction->company_id = $companyId;
                            $transaction->sender_id = $companyId;
                            $transaction->sender_type = 'company';
                            $transaction->receiver_id = $request->user_id;
                            $transaction->receiver_type = 'user';
                            $transaction->remark = "Amount Debit User Account To credit In Company Account";
                            if (!$transaction->save()) {
                                throw new \Exception('Failed to save transaction.');
                            }else{
                                $wallet_detail->amount -= $request->amount;
                                $wallet_detail->save();
                            }

                            $transaction2 = new Transaction;
                            $transaction2->wallet_id = $company_wallet_detail->id;
                            $transaction2->transaction_type = 'credit';
                            $transaction2->created_by = Auth::user()->id;
                            $transaction2->transaction_status = 'success';
                            $transaction2->transaction_amount = $request->amount;
                            $transaction2->last_wallet_history = json_encode($wallet_detail);
                            $transaction2->company_id = $companyId;

                            $transaction2->sender_id = $companyId;
                            $transaction2->sender_type = 'company';
                            $transaction2->receiver_id = $request->user_id;
                            $transaction2->receiver_type = 'user';
                            $transaction2->remark = "Amount credit From User Account To Company Account";

                            if (!$transaction2->save()) {
                                throw new \Exception('Failed to save transaction.');
                            }
                            else{
                                $company_wallet_detail->amount += $request->amount;
                                $company_wallet_detail->save();
                            }
                        }else{


                            if ($company_wallet_detail->amount < $request->amount) {
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['amount' => 'Enter Amount Not available in Company Account, you cannot debit this amount.']);
                            }

                            $transaction = new Transaction;
                            $transaction->wallet_id = $wallet_detail->id;
                            $transaction->transaction_type = $request->transaction_type;
                            $transaction->created_by = Auth::user()->id;
                            $transaction->transaction_status = 'success';
                            $transaction->transaction_amount = $request->amount;
                            $transaction->last_wallet_history = json_encode($wallet_detail);
                            $transaction->company_id = $companyId;
                            $transaction->sender_id = $request->user_id;
                            $transaction->sender_type = 'user';
                            $transaction->receiver_id = $companyId;
                            $transaction->receiver_type = 'company';
                            $transaction->remark = "Amount credit From Company Account To User Account";
                            if (!$transaction->save()) {
                                throw new \Exception('Failed to save transaction.');
                            }
                            else{
                                $wallet_detail->amount += $request->amount;
                                $wallet_detail->save();
                            }

                            $transaction2 = new Transaction;
                            $transaction2->wallet_id = $company_wallet_detail->id;
                            $transaction2->transaction_type = 'debit';
                            $transaction2->created_by = Auth::user()->id;
                            $transaction2->transaction_status = 'success';
                            $transaction2->transaction_amount = $request->amount;
                            $transaction2->last_wallet_history = json_encode($wallet_detail);
                            $transaction2->company_id = $companyId;
                            $transaction2->sender_id = $request->user_id;
                            $transaction2->sender_type = 'user';
                            $transaction2->receiver_id = $companyId;
                            $transaction2->receiver_type = 'company';
                            $transaction2->remark = "Amount Debit Company Account To credit In User Account";

                            if (!$transaction2->save()) {
                                throw new \Exception('Failed to save transaction.');
                            }else{
                                $company_wallet_detail->amount -= $request->amount;
                                $company_wallet_detail->save();
                            }

                        }

                        DB::commit(); // Commit the transaction if everything is successful
                        return redirect()->route('user_transaction.index')->with('success', 'Transaction created successfully.');
                    } catch (\Exception $e) {
                        DB::rollBack(); // Roll back the transaction if any error occurs
                        return redirect()->back()->withInput()->with('error', $e->getMessage());
                    }
                }else{

                }
            } else {
                return view('admin-panel.error.401');
            }
        } else {
            return view('admin-panel.error.401');
        }
        return view('admin-panel.error.401');
    }

    public function fetch(Request $request)
    {
        $companyId = getCompanyId();
        if (!is_numeric($companyId)) {
            return response()->json([
                'html' => "<center>No Transaction Available</center>",
                'pagination' => "",
            ]);
        }

        $wallet_id = "";
        if ($request->has('user_id') && !empty($request->user_id)) {
            $wallet_detail = Wallet::where('wallets_type', 'user')
                                   ->where('wallet_person_id', $request->user_id)
                                   ->first();
            $wallet_id = $wallet_detail ? $wallet_detail->id : "";
        }
        if(!CheckLoginUserMainAdmin()){
            $wallet_detail = Wallet::where('wallets_type', 'user')
            ->where('wallet_person_id', Auth::User()->id)
            ->first();
            $wallet_id = $wallet_detail ? $wallet_detail->id : "";
        }

        $query = Transaction::query()
            ->leftJoin('users as creator', 'creator.id', '=', 'transactions.created_by')
            ->leftJoin(DB::raw('(SELECT id, name FROM vendors) as v'), function ($join) {
                $join->on('v.id', '=', 'transactions.sender_id')
                     ->where('transactions.sender_type', 'vendor');
            })
            ->leftJoin(DB::raw('(SELECT id, name FROM clients) as c'), function ($join) {
                $join->on('c.id', '=', 'transactions.sender_id')
                     ->where('transactions.sender_type', 'client');
            })
            ->leftJoin(DB::raw('(SELECT id, name FROM users) as u'), function ($join) {
                $join->on('u.id', '=', 'transactions.sender_id')
                     ->where('transactions.sender_type', 'user');
            })
            ->leftJoin(DB::raw('(SELECT id, name FROM companies) as co'), function ($join) {
                $join->on('co.id', '=', 'transactions.sender_id')
                     ->where('transactions.sender_type', 'company');
            })
            ->leftJoin(DB::raw('(SELECT id, name FROM vendors) as rv'), function ($join) {
                $join->on('rv.id', '=', 'transactions.receiver_id')
                     ->where('transactions.receiver_type', 'vendor');
            })
            ->leftJoin(DB::raw('(SELECT id, name FROM clients) as rc'), function ($join) {
                $join->on('rc.id', '=', 'transactions.receiver_id')
                     ->where('transactions.receiver_type', 'client');
            })
            ->leftJoin(DB::raw('(SELECT id, name FROM users) as ru'), function ($join) {
                $join->on('ru.id', '=', 'transactions.receiver_id')
                     ->where('transactions.receiver_type', 'user');
            })
            ->leftJoin(DB::raw('(SELECT id, name FROM companies) as rco'), function ($join) {
                $join->on('rco.id', '=', 'transactions.receiver_id')
                     ->where('transactions.receiver_type', 'company');
            });

        $limit = $request->input('limit', 10);
        $CurrentPage = $request->input('page', 1);
        $offset = ($CurrentPage - 1) * $limit;

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('transactions.id', 'like', '%' . $search . '%')
                      ->orWhere('transactions.transaction_amount', 'like', '%' . $search . '%')
                      ->orWhere('transactions.transaction_status', 'like', '%' . $search . '%');
            });
        }

        if (($request->has('user_id') && !empty($request->user_id)) || !CheckLoginUserMainAdmin()) {

            $query->where('transactions.wallet_id', $wallet_id);
        }


        if ($request->has('filter_btn') && !empty($request->filter_btn)) {
            if ($request->filter_btn == "active") {
                $query->where('is_active', 1);
            } elseif ($request->filter_btn == "deactive") {
                $query->where('is_active', 0);
            }
        }

        $totalPage = $query->count();
        $totalPage = ceil($totalPage / $limit);

        $transactions = $query->orderBy('transactions.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->select('transactions.*',
                     'creator.name as created_by',
                     DB::raw("CASE
                         WHEN transactions.sender_type = 'vendor' THEN v.name
                         WHEN transactions.sender_type = 'client' THEN c.name
                         WHEN transactions.sender_type = 'user' THEN u.name
                         WHEN transactions.sender_type = 'company' THEN co.name
                         ELSE ''
                     END as sender_name"),
                     DB::raw("CASE
                         WHEN transactions.receiver_type = 'vendor' THEN rv.name
                         WHEN transactions.receiver_type = 'client' THEN rc.name
                         WHEN transactions.receiver_type = 'user' THEN ru.name
                         WHEN transactions.receiver_type = 'company' THEN rco.name
                         ELSE ''
                     END as receiver_name")
            )
            ->get()
            ->toArray();

        return response()->json([
            'html' => view('admin-panel.user_transaction.table.detail', ['transactions' => $transactions])->render(),
            'pagination' => view('admin-panel.user_transaction.table.pagination', ['limit' => $limit, 'offset' => $offset, 'totalPage' => $totalPage, 'CurrentPage' => $CurrentPage])->render()
        ]);
    }

    public function CountData(Request $request)
    {
        $companyId = getCompanyId();

        if (!is_numeric($companyId)) {
            return response()->json(['html' => 'Invalid company ID']);
        }

        $wallet_id = null;
        if ($request->has('user_id') && !empty($request->user_id)) {
            $wallet = Wallet::where('wallets_type', 'user')
                            ->where('wallet_person_id', $request->user_id)
                            ->first();
            $wallet_id = $wallet ? $wallet->id : null;
        }else if(!CheckLoginUserMainAdmin()){
            $wallet_detail = Wallet::where('wallets_type', 'user')
            ->where('wallet_person_id', Auth::User()->id)
            ->first();
            $wallet_id = $wallet_detail ? $wallet_detail->id : "";
        }else{
            $html = "";
        return response()->json(['html' => $html]);
        }

        // Helper function to apply wallet_id conditionally
        $applyWalletFilter = function($query) use ($wallet_id) {
            if ($wallet_id) {
                $query->where('wallet_id', $wallet_id);
            }
        };

        // Create filters array with counts
        $filters = [
            [
                'name' => '',
                'color' => 'white',
                'background-color' => 'gray',
                'label' => 'Available Balance: ' . number_format(
                    Wallet::query()
                        ->when($wallet_id, function($query) use ($wallet_id) {
                            $query->where('id', $wallet_id);
                        })
                        ->when(!$wallet_id, function($query) use ($companyId) {
                            $query->where('company_id', $companyId);
                        })
                        ->pluck('amount')
                        ->first() ?? 0, 2
                ),
            ],
            [
                'name' => 'total_transactions',
                'color' => 'white',
                'background-color' => 'gray',
                'label' => 'Total Transactions: ' . Transaction::when($wallet_id, $applyWalletFilter)
                    ->where('company_id', $companyId)
                    ->count(),
            ],
            [
                'name' => 'active_transactions',
                'color' => 'white',
                'background-color' => 'green',
                'label' => 'Success Transactions: ' . Transaction::when($wallet_id, $applyWalletFilter)
                    ->where('company_id', $companyId)
                    ->where('transaction_status', 'success')
                    ->count(),
            ],
            [
                'name' => 'failed_transactions',
                'color' => 'white',
                'background-color' => 'red',
                'label' => 'Failed Transactions: ' . Transaction::when($wallet_id, $applyWalletFilter)
                    ->where('company_id', $companyId)
                    ->where('transaction_status', 'failed')
                    ->count(),
            ],
            [
                'name' => 'total_credit_amount',
                'color' => 'white',
                'background-color' => 'blue',
                'label' => 'Total Credit Amount: ' . number_format(
                    Transaction::when($wallet_id, $applyWalletFilter)
                        ->where('company_id', $companyId)
                        ->where('transaction_type', 'credit')
                        ->where('transaction_status', 'success')
                        ->sum('transaction_amount'), 2
                ),
            ],
            [
                'name' => 'total_debit_amount',
                'color' => 'white',
                'background-color' => 'orange',
                'label' => 'Total Debit Amount: ' . number_format(
                    Transaction::when($wallet_id, $applyWalletFilter)
                        ->where('company_id', $companyId)
                        ->where('transaction_type', 'debit')
                        ->where('transaction_status', 'success')
                        ->sum('transaction_amount'), 2
                ),
            ]
        ];

        // Render the HTML view
        $html = view('admin-panel.user_transaction.table.countData', compact('filters'))->render();
        return response()->json(['html' => $html]);
    }


}



