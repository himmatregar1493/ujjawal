<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\IntakePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyTransactionController extends Controller
{
    public function create(Request $request)
    {
        return view('admin-panel.company_transaction.create');
    }

    public function index(Request $request)
    {
        return view('admin-panel.company_transaction.index');
    }

    public function store(Request $request)
    {

        if (UserCan('company_transaction.create')) {
            $companyId = getCompanyId();
            if (is_numeric($companyId)) {
                $validated = $request->validate([
                    'transaction_type' => 'required|in:credit,debit',
                    'amount' => 'required|numeric|min:0.01',
                ]);



                DB::beginTransaction();
                try {
                    $wallet_detail = Wallet::where('wallet_person_id', $companyId)
                                        ->where('wallets_type', 'company')
                                        ->first();
                    if (!$wallet_detail) {
                        WalletCreate($companyId, 'company');
                        $wallet_detail = Wallet::where('wallet_person_id', $companyId)
                                            ->where('wallets_type', 'company')
                                            ->first();
                    }

                    if ($request->transaction_type === 'debit') {
                        if ($wallet_detail->amount < $request->amount) {
                            return redirect()->back()
                                ->withInput() // Preserve old input data
                                ->withErrors(['amount' => 'Enter Amount Not available in company, you cannot debit this amount.']);
                        }
                    }

                    // Create new transaction
                    $transaction = new Transaction;
                    $transaction->wallet_id = $wallet_detail->id;
                    $transaction->transaction_type = $request->transaction_type;
                    $transaction->created_by = Auth::user()->id;
                    $transaction->transaction_status = 'success';
                    $transaction->transaction_amount = $request->amount;
                    $transaction->last_wallet_history = json_encode($wallet_detail);
                    $transaction->company_id = $companyId;

                    // Save the transaction
                    if (!$transaction->save()) {
                        throw new \Exception('Failed to save transaction.');
                    }

                    // Update wallet balance based on transaction type
                    if ($transaction->transaction_type == 'credit') {
                        $wallet_detail->amount += $request->amount;
                    } else {
                        $wallet_detail->amount -= $request->amount;
                    }

                    // Save wallet update
                    if (!$wallet_detail->save()) {
                        throw new \Exception('Failed to update wallet balance.');
                    }

                    DB::commit(); // Commit the transaction if everything is successful
                    return redirect()->route('company_transaction.index')->with('success', 'Transaction created successfully.');
                } catch (\Exception $e) {
                    DB::rollBack(); // Roll back the transaction if any error occurs
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
        $companyId = getCompanyId();
        if (is_numeric($companyId)) {
            $query = Transaction::leftjoin('users','users.id','transactions.created_by');
            $limit = @$request->$limit ?? 10;
            $CurrentPage = @$request->page ?? 1;
            $offset = @($CurrentPage-1)*$limit ?? 10;
            if ($request->has('search') && !empty($request->search)) {
                $query->where('transactions.id', 'like', '%' . $request->search . '%')
                ->where('transactions.transaction_amount', 'like', '%' . $request->search . '%')
                ->where('transactions.transaction_status', 'like', '%' . $request->search . '%')
                ->where('transactions.transaction_amount', 'like', '%' . $request->search . '%');
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

            $transaction = $query->orderBy('transactions.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->select('transactions.*','users.name as created_by')
            ->get()
            ->toArray();

            return response()->json([
                'html' => view('admin-panel.company_transaction.table.detail', ['transaction' => $transaction])->render(),
                'pagination' => view('admin-panel.company_transaction.table.pagination', ['limit' => $limit,'offset' => $offset,'totalPage' => $totalPage,'CurrentPage' =>$CurrentPage])->render()
            ]);
        }else{
            return response()->json([
                'html' => "<center>No Transaction Available</center>",
                'pagination' => "",
            ]);
        }
    }


    public function CountData(Request $request)
    {
        $companyId = getCompanyId();

        if (is_numeric($companyId)) {
            $filters = [
                [
                    'name' => '',
                    'color' => 'white',
                    'background-color' => 'gray',
                    'label' => 'Available Balance: ' . (Wallet::where('company_id', $companyId)->pluck('amount')->first() ?? 0),

                ],
                [
                    'name' => 'total_transactions',
                    'color' => 'white',
                    'background-color' => 'gray',
                    'label' => 'Total Transactions: ' . Transaction::where('company_id', $companyId)->count()
                ],
                [
                    'name' => 'active_transactions',
                    'color' => 'white',
                    'background-color' => 'green',
                    'label' => 'Success Transactions: ' . Transaction::where('company_id', $companyId)->where('transaction_status', "success")->count()
                ],
                [
                    'name' => 'failed_transactions',
                    'color' => 'white',
                    'background-color' => 'red',
                    'label' => 'Failed Transactions: ' . Transaction::where('company_id', $companyId)->where('transaction_status', "failed")->count()
                ],
                [
                    'name' => 'total_credit_amount',
                    'color' => 'white',
                    'background-color' => 'blue',
                    'label' => 'Total Credit Amount: ' . number_format(Transaction::where('company_id', $companyId)
                        ->where('transaction_type', 'credit')
                        ->where('transaction_status', "success")
                        ->sum('transaction_amount'), 2)
                ],
                [
                    'name' => 'total_debit_amount',
                    'color' => 'white',
                    'background-color' => 'orange',
                    'label' => 'Total Debit Amount: ' . number_format(Transaction::where('company_id', $companyId)
                        ->where('transaction_type', 'debit')
                        ->where('transaction_status', "success")
                        ->sum('transaction_amount'), 2)
                ]
            ];

            $html = view('admin-panel.company_transaction.table.countData', compact('filters'))->render();
            return response()->json(['html' => $html]);
        } else {
            return response()->json(['html' => 'Invalid company ID']);
        }
    }

}



