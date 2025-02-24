<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class DepositController extends Controller
{
    public function pending()
    {
        $pageTitle = 'Pending Deposits';
        $deposits = $this->depositData('pending');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }


    public function approved()
    {
        $pageTitle = 'Approved Deposits';
        $deposits = $this->depositData('approved');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function successful()
    {
        $pageTitle = 'Successful Deposits';
        $deposits = $this->depositData('successful');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function rejected()
    {
        $pageTitle = 'Rejected Deposits';
        $deposits = $this->depositData('rejected');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function initiated()
    {
        $pageTitle = 'Initiated Deposits';
        $deposits = $this->depositData('initiated');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function deposit()
    {
        $pageTitle = 'Deposit History';
        $depositData = $this->depositData($scope = null, $summery = true);
        $deposits = $depositData['data'];
        $summery = $depositData['summery'];
        $successful = $summery['successful'];
        $pending = $summery['pending'];
        $rejected = $summery['rejected'];
        $initiated = $summery['initiated'];
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'successful', 'pending', 'rejected', 'initiated'));
    }

    protected function depositData($scope = null, $summery = false)
    {
        if ($scope) {
            $deposits = Deposit::$scope()->with(['user', 'gateway']);
        } else {
            $deposits = Deposit::with(['user', 'gateway']);
        }

        $deposits = $deposits->searchable(['trx', 'user:username'])->dateFilter();

        $request = request();
        //vai method
        if ($request->method) {
            $method = Gateway::where('alias', $request->method)->firstOrFail();
            $deposits = $deposits->where('method_code', $method->code);
        }

        if (!$summery) {
            return $deposits->orderBy('id', 'desc')->paginate(getPaginate());
        } else {
            $successful = clone $deposits;
            $pending = clone $deposits;
            $rejected = clone $deposits;
            $initiated = clone $deposits;

            $successfulSummery = $successful->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
            $pendingSummery = $pending->where('status', Status::PAYMENT_PENDING)->sum('amount');
            $rejectedSummery = $rejected->where('status', Status::PAYMENT_REJECT)->sum('amount');
            $initiatedSummery = $initiated->where('status', Status::PAYMENT_INITIATE)->sum('amount');

            return [
                'data' => $deposits->orderBy('id', 'desc')->paginate(getPaginate()),
                'summery' => [
                    'successful' => $successfulSummery,
                    'pending' => $pendingSummery,
                    'rejected' => $rejectedSummery,
                    'initiated' => $initiatedSummery,
                ]
            ];
        }
    }

    public function details($id)
    {
        $general = gs();
        $deposit = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();
        $pageTitle = $deposit->user->username . ' requested ' . showAmount($deposit->amount) . ' ' . __($general->cur_text);
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit', 'details'));
    }


    public function approve($id)
    {

        $deposit = Deposit::where('id', $id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();

        if (isset($deposit->method_code)) {
            $gatewayinfo = DB::table('gateways')
                ->where('code', $deposit->method_code)
                ->first();
            if (isset($gatewayinfo->name)) {
                $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');
                $rstl_payment_gateway_emi_repayment_id = setting('pgrs_rstl_payment_gateway_emi_repayment_loan_id_text', '');
                $rstl_payment_gateway_closing_loan_id = setting('pgrs_rstl_payment_gateway_closing_loan_id_text', '');

                // if (!empty($rstl_payment_gateway_id)) {
                if ($rstl_payment_gateway_id == $gatewayinfo->name) {



                    $user = User::find($deposit->user_id);
                    if ($user->plan_id > 0) {
                        $notify[] = ['error', "This ID is already active"];
                        return to_route('admin.deposit.pending')->withNotify($notify);
                    }



                    PaymentController::userDataUpdateRSTL($deposit, true);
                    $notify[] = ['success', 'Deposit request approved successfully'];
                    return to_route('admin.deposit.pending')->withNotify($notify);
                } else {
                    if ($rstl_payment_gateway_emi_repayment_id == $gatewayinfo->name) {

                        // PaymentController::userDataUpdate($deposit, true);
                        PaymentController::userDataUpdateRSTLEmiRepayment($deposit, true);


                        $notify[] = ['success', 'Deposit request approved successfully'];

                        return to_route('admin.deposit.pending')->withNotify($notify);

                        // $user = User::find($deposit->user_id);
                        // if ($user->plan_id > 0) {
                        //     $notify[] = ['error', "This ID is already active"];
                        //     return to_route('admin.deposit.pending')->withNotify($notify);
                        // }



                        // PaymentController::userDataUpdateRSTL($deposit, true);
                        // $notify[] = ['success', 'Deposit request approved successfully'];
                        // return to_route('admin.deposit.pending')->withNotify($notify);
                    } else {
                        if ($rstl_payment_gateway_closing_loan_id == $gatewayinfo->name) {


                            // PaymentController::userDataUpdateRSTLLoanCloser($deposit, true);


                            // $notify[] = ['success', 'Deposit request approved successfully'];

                            // return to_route('admin.deposit.pending')->withNotify($notify);

                            if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {

                                $datetime = now();

                                $loan_application = DB::table('loan_applications')
                                    ->where('user_id', $deposit->user_id)
                                    ->where('is_application_approved', 1)
                                    ->first();

                                if (isset($loan_application->id)) {

                                    $loan_approved = DB::table('loan_approved')
                                        ->where('loan_application_id', $loan_application->id)
                                        ->first();

                                    if (isset($loan_approved->id)) {

                                        $loan_repayment_sum = DB::table('loan_repayments')
                                            ->where('loan_id', $loan_approved->id)
                                            ->where('is_active', 1)
                                            ->where('is_emi_paid', '<>', 1)
                                            ->sum('emi_amount');

                                        if ($loan_repayment_sum != $deposit->amount) {
                                            $notify[] = ['error', "Total sum of Emi amount and Submitted closing amount not same"];
                                            return to_route('admin.deposit.pending')->withNotify($notify);
                                        } else {

                                            PaymentController::userDataUpdateRSTLLoanCloser($deposit, true);


                                            $notify[] = ['success', 'Deposit request approved successfully'];

                                            return to_route('admin.deposit.pending')->withNotify($notify);

                                            // DB::table('loan_approved')
                                            //     ->where('id', $loan_approved->id)  // find your user by their email
                                            //     ->update(array('is_loan_closed' => 1));

                                            // DB::table('loan_repayments')
                                            //     ->where('is_active', 1)
                                            //     ->where('loan_id', $loan_approved->id)  // find your user by their email
                                            //     ->update(array(
                                            //         'is_emi_paid' => 1,
                                            //         'transaction_id' => $deposit->trx,
                                            //         'emi_paid_datetime' => $datetime->format("Y-m-d H:i:s")
                                            //     ));
                                        }
                                    }
                                }
                            }
                            // PaymentController::userDataUpdateRSTLLoanCloser($deposit, true);
                        }
                    }
                }
                // }
            }
        }



        PaymentController::userDataUpdate($deposit, true);

        $notify[] = ['success', 'Deposit request approved successfully'];

        return to_route('admin.deposit.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|string|max:255'
        ]);
        $deposit = Deposit::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();



        //wallet
        $loan_detail = $deposit->detail;

        // $loan_detail = json_decode($loan_detail);
        if (is_array($loan_detail)) {
            $loan_payment_mode = null;
            foreach ($loan_detail as  $loan_detail_item) {
                if (isset($loan_detail_item->name)) {
                    if (preg_match('#Payment Mode#i', $loan_detail_item->name) === 1) {

                        if (is_string($loan_detail_item->value) && !empty($loan_detail_item->value)) {
                            $loan_payment_mode = $loan_detail_item->value;
                        }
                    }
                }
            }
            if ($loan_payment_mode == 'Wallet' || $loan_payment_mode == 'wallet') {





                $user = User::findOrFail($deposit->user_id);
                $amount = $deposit->amount;



                $trx = getTrx();

                $transaction = new Transaction();

                $user->balance += $amount;

                $transaction->trx_type = '+';
                //$transaction->remark = 'balance_add_loan_processing_fee';
                $transaction->remark = 'balance_add_rstl_loan';

                $user->save();

                $transaction->user_id = $user->id;
                $transaction->amount = $amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = 0;
                $transaction->trx =  $trx;
                //$transaction->details = 'RSTL Loan Processing Fee Return';
                $transaction->details = 'Rejected Payment Amount Returned ';
                $transaction->save();
            }
        }

        //wallet





        $deposit->admin_feedback = $request->message;
        $deposit->status = Status::PAYMENT_REJECT;
        $deposit->save();




        if (isset($deposit->method_code)) {
            $gatewayinfo = DB::table('gateways')
                ->where('code', $deposit->method_code)
                ->first();
            if (isset($gatewayinfo->name)) {
                $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');
                $rstl_payment_gateway_emi_repayment_id = setting('pgrs_rstl_payment_gateway_emi_repayment_loan_id_text', '');
                $rstl_payment_gateway_closing_loan_id = setting('pgrs_rstl_payment_gateway_closing_loan_id_text', '');

                // if (!empty($rstl_payment_gateway_id)) {
                if ($rstl_payment_gateway_id == $gatewayinfo->name) {


                    DB::table('loan_applications')
                        ->where('deposit_id', $deposit->id)  // find your user by their email
                        ->update(array('is_application_approved' => 2));
                } else {
                }
                // }
            }
        }





        notify($deposit->user, 'DEPOSIT_REJECT', [
            'method_name' => $deposit->gatewayCurrency()->name,
            'method_currency' => $deposit->method_currency,
            'method_amount' => showAmount($deposit->final_amo),
            'amount' => showAmount($deposit->amount),
            'charge' => showAmount($deposit->charge),
            'rate' => showAmount($deposit->rate),
            'trx' => $deposit->trx,
            'rejection_message' => $request->message
        ]);

        $notify[] = ['success', 'Deposit request rejected successfully'];
        return  to_route('admin.deposit.pending')->withNotify($notify);
    }
}
