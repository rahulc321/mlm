<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\Mlm;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function deposit()
    {
        $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');
        $rstl_payment_gateway_emi_repayment_id = setting('pgrs_rstl_payment_gateway_emi_repayment_loan_id_text', '');
        $rstl_payment_gateway_closing_loan_id = setting('pgrs_rstl_payment_gateway_closing_loan_id_text', '');

        if (!empty($rstl_payment_gateway_id)) {
            if (!empty($rstl_payment_gateway_emi_repayment_id)) {

                if (!empty($rstl_payment_gateway_emi_repayment_id)) {

                    $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                        $gate->where('status', Status::ENABLE);
                    })
                        ->with('method')->where('name', '<>', $rstl_payment_gateway_id)
                        ->with('method')->where('name', '<>', $rstl_payment_gateway_emi_repayment_id)
                        ->with('method')->where('name', '<>', $rstl_payment_gateway_closing_loan_id)
                        ->orderby('method_code')->get();
                } else {

                    $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                        $gate->where('status', Status::ENABLE);
                    })
                        ->with('method')->where('name', '<>', $rstl_payment_gateway_id)
                        ->with('method')->where('name', '<>', $rstl_payment_gateway_emi_repayment_id)
                        ->orderby('method_code')->get();
                }
            } else {

                $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->with('method')->where('name', '<>', $rstl_payment_gateway_id)->orderby('method_code')->get();
            }
        } else {
            $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->with('method')->orderby('method_code')->get();
        }

        // $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
        //     $gate->where('status', Status::ENABLE);
        // })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Deposit Methods';
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);


        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $data = self::insertDeposit($gate, $request->amount);

        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public static function insertDeposit($gateway, $amount, $investPlan = null)
    {
        $user = auth()->user();
        $charge = $gateway->fixed_charge + ($amount * $gateway->percent_charge / 100);
        $payable = $amount + $charge;
        $final_amo = $payable * $gateway->rate;

        $data = new Deposit();
        if ($investPlan) {
            $data->plan_id = $investPlan->id;
        }
        $data->user_id = $user->id;
        $data->method_code = $gateway->method_code;
        $data->method_currency = strtoupper($gateway->currency);
        $data->amount = $amount;
        $data->charge = $charge;
        $data->rate = $gateway->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();


        //Save loan application
        $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');
        $rstl_deposit_approved_amount_for_package = setting('pgrs_rstl_deposit_approved_amount_for_package', 34);
        if ($rstl_payment_gateway_id == $gateway->name) {
            $datetime = now();
            DB::table('loan_applications')->insertGetId(
                [
                    // 'loan_id' => generateLaonId(),
                    'deposit_id' =>  $data->id,
                    'user_id' => $user->id,
                    'payment_gateway_id' => $gateway->id,
                    'loan_processing_fees' => $amount,
                    'loan_amount' => $rstl_deposit_approved_amount_for_package,
                    // 'transaction_id' => '',

                    'created_at' => $datetime->format("Y-m-d H:i:s"),
                    'updated_at' => $datetime->format("Y-m-d H:i:s"),
                ]
            );
        }



        return $data;
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            return "Sorry, invalid URL.";
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);

            if ($deposit->plan_id) {
                $mlm = new Mlm($user, $deposit->plan, $deposit->trx);
                $mlm->purchasePlan();
            }
        }
    }

    public static function userDataUpdateRSTL($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $rstl_deposit_approved_amount_for_package = setting('pgrs_rstl_deposit_approved_amount_for_package', 34);

            $user = User::find($deposit->user_id);
            // $user->balance += $deposit->amount;
            $user->balance += $rstl_deposit_approved_amount_for_package;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            // $transaction->amount = $deposit->amount;
            $transaction->amount = $rstl_deposit_approved_amount_for_package;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'RSTL Deposit Approved Via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();


            //Approve loan application
            $loan_application = DB::table('loan_applications')
                ->where('deposit_id', '=', $deposit->id)
                ->first();
            if (isset($loan_application->id)) {
                // $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');
                // $rstl_deposit_approved_amount_for_package = setting('pgrs_rstl_deposit_approved_amount_for_package', 34);
                // if ($rstl_payment_gateway_id == $gateway->name) {
                $datetime = now();
                $loan_approved_date = now();
                // $loan_approved_date = $datetime->format("Y-m-d H:i:s");

                DB::table('loan_applications')
                    ->where('id', $loan_application->id)  // find your user by their email
                    ->update(array('is_application_approved' => 1));


                $created_loan_id = DB::table('loan_approved')->insertGetId(
                    [
                        'loan_id' => generateLaonId(),
                        // 'deposit_id' =>  $loan_application->deposit_id,
                        'loan_application_id' =>  $loan_application->id,
                        // 'user_id' => $loan_application->user_id,
                        // 'payment_gateway_id' => $loan_application->payment_gateway_id,
                        // 'loan_processing_fees' => $amount,
                        // 'loan_amount' => $rstl_deposit_approved_amount_for_package,
                        // 'transaction_id' => '',

                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                    ]
                );




                $loan_detail = $deposit->detail;
                // if (empty($loan_detail)) {

                //     // echo "Loan Form Attributes not found<br/>";
                //     // dd($loan_detail);
                //     return;
                // }
                // dd($loan_detail);

                if (is_string($loan_detail)) {
                    @$loan_detail = json_decode($loan_detail);
                }

                if (!empty($loan_detail)) {

                    $loan_tenure = null;
                    foreach ($loan_detail as  $loan_detail_item) {
                        if (isset($loan_detail_item->name)) {
                            if (preg_match('#tenure#i', $loan_detail_item->name) === 1) {
                                if (is_array($loan_detail_item->value) && isset($loan_detail_item->value[0])) {
                                    if (preg_match('#(?<tenure>\d+)#', $loan_detail_item->value[0], $tenmatch) === 1) {

                                        if (isset($tenmatch['tenure'])) {
                                            $loan_tenure = $tenmatch['tenure'];
                                        }
                                    }
                                } else {
                                    if (is_string($loan_detail_item->value) && !empty($loan_detail_item->value)) {
                                        if (preg_match('#(?<tenure>\d+)#', $loan_detail_item->value, $tenmatch) === 1) {

                                            if (isset($tenmatch['tenure'])) {
                                                $loan_tenure = $tenmatch['tenure'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($loan_tenure)) {
                        if ($loan_application->loan_amount % $loan_tenure == 0) {
                            $loan_emis = $loan_application->loan_amount / $loan_tenure;

                            for ($loan_loop_counter = 1; $loan_loop_counter <= $loan_tenure; $loan_loop_counter++) {

                                $loan_approved_date = $loan_approved_date->addDays(30);
                                DB::table('loan_repayments')->insertGetId(
                                    [
                                        'loan_id' => $created_loan_id,
                                        'emi_amount' => $loan_emis,
                                        'is_emi_paid' => 0,
                                        'emi_to_be_paid_datetime' => $loan_approved_date->format("Y-m-d H:i:s"),
                                        'user_id' => $loan_application->user_id,
                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                    ]
                                );
                            }
                        } else {
                            $loan_emis = $loan_application->loan_amount / $loan_tenure;
                            $loan_emi_rounded = round($loan_emis, 0);
                            $last_month_emi = ($loan_application->loan_amount - ($loan_emi_rounded * ($loan_tenure - 1)));


                            for ($loan_loop_counter = 1; $loan_loop_counter <= $loan_tenure; $loan_loop_counter++) {
                                $loan_approved_date = $loan_approved_date->addDays(30);
                                DB::table('loan_repayments')->insertGetId(
                                    [
                                        'loan_id' => $created_loan_id,
                                        'emi_amount' => $loan_loop_counter == $loan_tenure ? $last_month_emi : $loan_emi_rounded,
                                        'is_emi_paid' => 0,
                                        'emi_to_be_paid_datetime' => $loan_approved_date->format("Y-m-d H:i:s"),
                                        'user_id' => $loan_application->user_id,
                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                    ]
                                );
                            }
                            // echo " all month emi $loan_emi_rounded <br/>";
                            // echo " last_month_emi month emi $last_month_emi";
                            // $amount_reduced = 0;
                            // $loan_amount_divisble = $loan_application->loan_amount;
                            // while (1) {
                            //     if ($loan_amount_divisble % $loan_tenure == 0) {
                            //         break;
                            //     }
                            //     $amount_reduced++;
                            //    echo "  ". $loan_amount_divisble = $loan_application->loan_amount - $amount_reduced;
                            // }
                            // $loan_amount_divisble = $loan_application->loan_amount - 1;
                            // echo " $amount_reduced not  $loan_application->loan_amount divided $loan_amount_divisble";
                            // echo "<br/>";
                        }
                    }
                    // }
                }
            }




            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);

            // if ($deposit->plan_id) {
            //     $mlm = new Mlm($user, $deposit->plan, $deposit->trx);
            //     $mlm->purchasePlan();
            // }


            //custom


            $plan = new Plan();


            $plan->name             = '2ReturnSure Trading Package';
            $plan->price            = $rstl_deposit_approved_amount_for_package;


            $plan->plan_created_by_user_id = $user->id;



            $plan->bv               = $rstl_deposit_approved_amount_for_package;
            $plan->ref_com          = 5;
            $plan->tree_com         = 5;






            $plan->save();
            $plan = Plan::where('status', Status::ENABLE)->findOrFail($plan->id);

            $trx = getTrx();

            // $mlm = new Mlm($user, $plan, $trx);
            // $mlm->purchasePlan();




            $mlm = new Mlm($user, $plan, $trx);
            $mlm->purchasePlanForSomeoneAsLoan($user, $plan, $trx, auth()->user());
        }
    }

    public static function userDataUpdateRSTLEmiRepayment($deposit_info, $isManual = null)
    {

        if ($deposit_info->status == Status::PAYMENT_INITIATE || $deposit_info->status == Status::PAYMENT_PENDING) {
            $deposit_info->status = Status::PAYMENT_SUCCESS;
            $deposit_info->save();

            $user = User::find($deposit_info->user_id);
            $user->balance += $deposit_info->amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $deposit_info->user_id;
            $transaction->amount = $deposit_info->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit_info->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'RSTL EMI Deposit Via ' . $deposit_info->gatewayCurrency()->name;
            $transaction->trx = $deposit_info->trx;
            $transaction->remark = 'deposit_info';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $deposit_info->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit_info->gatewayCurrency()->name,
                'method_currency' => $deposit_info->method_currency,
                'method_amount' => showAmount($deposit_info->final_amo),
                'amount' => showAmount($deposit_info->amount),
                'charge' => showAmount($deposit_info->charge),
                'rate' => showAmount($deposit_info->rate),
                'trx' => $deposit_info->trx,
                'post_balance' => showAmount($user->balance)
            ]);



            //Approve loan application
            $loan_detail = $deposit_info->detail;
            if (empty($loan_detail)) {

                // echo "Loan Form Attributes not found<br/>";
                // dd($loan_detail);
                return;
            }
            // dd($loan_detail);

            if (is_string($loan_detail)) {
                @$loan_detail = json_decode($loan_detail);
            }


            $loan_repayment_id = null;

            foreach ($loan_detail as  $loan_detail_item) {
                if (isset($loan_detail_item->name)) {
                    if (preg_match('#repayment_id#i', $loan_detail_item->name) === 1) {
                        $loan_repayment_id = $loan_detail_item->value;
                    }
                }
            }

            if ($loan_repayment_id > 0) {

                $datetime = now();




                $loan_repayment_item = DB::table('loan_repayments')
                    ->where('id', $loan_repayment_id)
                    ->first();

                if (isset($loan_repayment_item->loan_id)) {

                    $loan_approved_info = DB::table('loan_approved')
                        ->where('id', $loan_repayment_item->loan_id)
                        ->first();


                    DB::table('loan_repayments')
                        ->where('id', $loan_repayment_id)  // find your user by their email
                        ->update(array('is_emi_paid' => 1, 'emi_paid_datetime' => $datetime->format("Y-m-d H:i:s")));


                    $loan_repayments_is_emi_paid = DB::table('loan_repayments')
                        ->where('loan_id', $loan_repayment_item->loan_id)
                        ->where('is_emi_paid', 1)
                        ->where('is_active', 1)
                        ->count();

                    $loan_repayments_total_count = DB::table('loan_repayments')
                        ->where('loan_id', $loan_repayment_item->loan_id)
                        ->where('is_active', 1)
                        ->count();

                    if ($loan_repayments_total_count > 0) {
                        if ($loan_repayments_is_emi_paid == $loan_repayments_total_count) {
                            DB::table('loan_approved')
                                ->where('id', $loan_repayment_item->loan_id)  // find your user by their email
                                ->update(array('is_loan_closed' => 1));
                        }
                    }


                    //deduct money
                    $user = User::findOrFail($loan_repayment_item->user_id);
                    $amount = $loan_repayment_item->emi_amount;

                    // $notifyTemplate = 'BAL_SUB';

                    if ($amount > $user->balance) {
                    } else {


                        // $general = gs();
                        $trx = getTrx();

                        $transaction = new Transaction();

                        $user->balance -= $amount;

                        $transaction->trx_type = '-';
                        $transaction->remark = 'balance_subtract_loan_emi';


                        // $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];


                        $user->save();

                        $transaction->user_id = $user->id;
                        $transaction->amount = $amount;
                        $transaction->post_balance = $user->balance;
                        $transaction->charge = 0;
                        $transaction->trx =  $trx;
                        $transaction->details = 'Loan EMI for LOAN:' . $loan_approved_info->loan_id;
                        // $transaction->details = 'Loan EMI';
                        $transaction->save();



                        //// $user = User::findOrFail(37);
                        // notify($user, $notifyTemplate, [
                        //     'trx' => $trx,
                        //     'amount' => showAmount($amount),
                        //     // 'message'=>"Hello {$user->fullname} your emi payment for your loan {$loan->loan_id} is failed. Please pay your emi.",
                        //     // 'remark' => 'Loan EMI for LOAN:' . $loan->loan_id,
                        //     'remark' => "Your emi payment for your loan {$loan->loan_id} is paid",
                        //     'post_balance' => showAmount($user->balance)
                        // ]);
                    }
                    //deduct money



                    // notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                    //     'method_name' => $deposit->gatewayCurrency()->name,
                    //     'method_currency' => $deposit->method_currency,
                    //     'method_amount' => showAmount($deposit->final_amo),
                    //     'amount' => showAmount($deposit->amount),
                    //     'charge' => showAmount($deposit->charge),
                    //     'rate' => showAmount($deposit->rate),
                    //     'trx' => $deposit->trx,
                    //     'post_balance' => showAmount($user->balance)
                    // ]);
                }
            }
            // dd($loan_repayment_id);





            // if ($deposit->plan_id) {
            //     $mlm = new Mlm($user, $deposit->plan, $deposit->trx);
            //     $mlm->purchasePlan();
            // }


            //custom

        }
    }

    public static function userDataUpdateRSTLLoanCloserXX($deposit_info, $isManual = null)
    {



        if ($deposit_info->status == Status::PAYMENT_INITIATE || $deposit_info->status == Status::PAYMENT_PENDING) {



            //Approve loan application
            $loan_detail = $deposit_info->detail;
            if (empty($loan_detail)) {

                // echo "Loan Form Attributes not found<br/>";
                // dd($loan_detail);
                return;
            }
            // dd($loan_detail);

            if (is_string($loan_detail)) {
                @$loan_detail = json_decode($loan_detail);
            }


            // $loan_repayment_id = null;

            // foreach ($loan_detail as  $loan_detail_item) {
            //     if (isset($loan_detail_item->name)) {
            //         if (preg_match('#repayment_id#i', $loan_detail_item->name) === 1) {
            //             $loan_repayment_id = $loan_detail_item->value;
            //         }
            //     }
            // }

            if (1) {

                $datetime = now();

                $loan_application = DB::table('loan_applications')
                    ->where('user_id', $deposit_info->user_id)
                    ->where('is_application_approved', 1)
                    ->first();

                if (isset($loan_application->id)) {

                    $loan_approved = DB::table('loan_approved')
                        ->where('loan_application_id', $loan_application->id)
                        ->first();
                    if (isset($loan_approved->id)) {
                    }
                }





                $loan_repayment_item = DB::table('loan_repayments')
                    ->where('id', $loan_repayment_id)
                    ->first();

                if (isset($loan_repayment_item->loan_id)) {


                    DB::table('loan_repayments')
                        ->where('id', $loan_repayment_id)  // find your user by their email
                        ->update(array('is_emi_paid' => 1, 'emi_paid_datetime' => $datetime->format("Y-m-d H:i:s")));


                    $loan_repayments_is_emi_paid = DB::table('loan_repayments')
                        ->where('loan_id', $loan_repayment_item->loan_id)
                        ->where('is_emi_paid', 1)
                        ->where('is_active', 1)
                        ->count();

                    $loan_repayments_total_count = DB::table('loan_repayments')
                        ->where('loan_id', $loan_repayment_item->loan_id)
                        ->where('is_active', 1)
                        ->count();

                    if ($loan_repayments_total_count > 0) {
                        if ($loan_repayments_is_emi_paid == $loan_repayments_total_count) {
                            DB::table('loan_approved')
                                ->where('id', $loan_repayment_item->loan_id)  // find your user by their email
                                ->update(array('is_loan_closed' => 1));
                        }
                    }

                    // notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                    //     'method_name' => $deposit->gatewayCurrency()->name,
                    //     'method_currency' => $deposit->method_currency,
                    //     'method_amount' => showAmount($deposit->final_amo),
                    //     'amount' => showAmount($deposit->amount),
                    //     'charge' => showAmount($deposit->charge),
                    //     'rate' => showAmount($deposit->rate),
                    //     'trx' => $deposit->trx,
                    //     'post_balance' => showAmount($user->balance)
                    // ]);
                }
            }
            // dd($loan_repayment_id);





            // if ($deposit->plan_id) {
            //     $mlm = new Mlm($user, $deposit->plan, $deposit->trx);
            //     $mlm->purchasePlan();
            // }


            //custom

        }
    }


    public static function userDataUpdateRSTLLoanCloser($deposit_info, $isManual = null)
    {

        if ($deposit_info->status == Status::PAYMENT_INITIATE || $deposit_info->status == Status::PAYMENT_PENDING) {
            $deposit_info->status = Status::PAYMENT_SUCCESS;
            $deposit_info->save();

            $user = User::find($deposit_info->user_id);
            $user->balance += $deposit_info->amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $deposit_info->user_id;
            $transaction->amount = $deposit_info->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit_info->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'RSTL Loan Closing Deposit Via ' . $deposit_info->gatewayCurrency()->name;
            $transaction->trx = $deposit_info->trx;
            $transaction->remark = 'deposit_info';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $deposit_info->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit_info->gatewayCurrency()->name,
                'method_currency' => $deposit_info->method_currency,
                'method_amount' => showAmount($deposit_info->final_amo),
                'amount' => showAmount($deposit_info->amount),
                'charge' => showAmount($deposit_info->charge),
                'rate' => showAmount($deposit_info->rate),
                'trx' => $deposit_info->trx,
                'post_balance' => showAmount($user->balance)
            ]);



            //Approve loan application
            $loan_detail = $deposit_info->detail;
            if (empty($loan_detail)) {

                // echo "Loan Form Attributes not found<br/>";
                // dd($loan_detail);
                return;
            }
            // dd($loan_detail);

            if (is_string($loan_detail)) {
                @$loan_detail = json_decode($loan_detail);
            }




            //closing
            $datetime = now();
            $loan_application = DB::table('loan_applications')
                ->where('user_id', $deposit_info->user_id)
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

                    if ($loan_repayment_sum != $deposit_info->amount) {
                        // $notify[] = ['error', "Total sum of Emi amount and Submitted closing amount not same"];
                        // return to_route('admin.deposit.pending')->withNotify($notify);
                    } else {
                        DB::table('loan_approved')
                            ->where('id', $loan_approved->id)  // find your user by their email
                            ->update(array('is_loan_closed' => 1));

                        DB::table('loan_repayments')
                            ->where('is_active', 1)
                            ->where('loan_id', $loan_approved->id)  // find your user by their email
                            ->update(array(
                                'is_emi_paid' => 1,
                                'transaction_id' => $deposit_info->trx,
                                'emi_paid_datetime' => $datetime->format("Y-m-d H:i:s")
                            ));


                        //deduct money
                        $user = User::findOrFail($deposit_info->user_id);
                        $amount = $loan_repayment_sum;

                        // $notifyTemplate = 'BAL_SUB';

                        if ($amount > $user->balance) {
                        } else {


                            // $general = gs();
                            $trx = getTrx();

                            $transaction = new Transaction();

                            $user->balance -= $amount;

                            $transaction->trx_type = '-';
                            $transaction->remark = 'balance_subtract_loan_closing';


                            // $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];


                            $user->save();

                            $transaction->user_id = $user->id;
                            $transaction->amount = $amount;
                            $transaction->post_balance = $user->balance;
                            $transaction->charge = 0;
                            $transaction->trx =  $trx;
                            $transaction->details = 'Loan Closing for LOAN:' . $loan_approved->loan_id;
                            // $transaction->details = 'Loan EMI';
                            $transaction->save();



                            //// $user = User::findOrFail(37);
                            // notify($user, $notifyTemplate, [
                            //     'trx' => $trx,
                            //     'amount' => showAmount($amount),
                            //     // 'message'=>"Hello {$user->fullname} your emi payment for your loan {$loan->loan_id} is failed. Please pay your emi.",
                            //     // 'remark' => 'Loan EMI for LOAN:' . $loan->loan_id,
                            //     'remark' => "Your emi payment for your loan {$loan->loan_id} is paid",
                            //     'post_balance' => showAmount($user->balance)
                            // ]);
                        }
                        //deduct money
                    }
                }
            }
            //closing








            // if ($deposit->plan_id) {
            //     $mlm = new Mlm($user, $deposit->plan, $deposit->trx);
            //     $mlm->purchasePlan();
            // }


            //custom

        }
    }

    public function manualDepositConfirm()
    {

        $track = session()->get('Track');

        $rstl_loan_repayment_id = session()->get("rstl_loan_repayment_id");
        if ($rstl_loan_repayment_id > 0) {
        } else {
            $rstl_loan_repayment_id = 0;
        }


        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {

            $pageTitle = 'Deposit Confirm';
            $method = $data->gatewayCurrency();
            // dd($method);
            $gateway = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway', 'rstl_loan_repayment_id'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {



        //wallet

        $curr_user = auth()->user();
        $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');
        $rstl_payment_gateway_emi_repayment_id = setting('pgrs_rstl_payment_gateway_emi_repayment_loan_id_text', '');
        $rstl_payment_gateway_closing_loan_id = setting('pgrs_rstl_payment_gateway_closing_loan_id_text', '');
        //wallet




        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        // dd($request->all(),$gateway->name,$request->repayment_id,$data,$validationRule);



        //wallet
        if (($request->get('payment_mode') == 'Wallet' || $request->get('payment_mode') == 'wallet') || ($request->get('mode_of_paymnet') == 'Wallet' || $request->get('mode_of_paymnet') == 'wallet')) {
            //          if ($gateway->name == $rstl_payment_gateway_id) {
            if (in_array($gateway->name, [$rstl_payment_gateway_id, $rstl_payment_gateway_emi_repayment_id, $rstl_payment_gateway_closing_loan_id])) {
                $validationRule['utr_number'] = ['nullable'];
                // $validationRule['file_payment_slip'] = ['nullable'];
                if ($request->has('payment_mode')) {
                    $validationRule['payment_slip'] = ['nullable'];
                    $validationRule['file_payment_slip'] = ['nullable'];
                } else {
                    if ($request->has('mode_of_paymnet')) {
                        $validationRule['payment_slip'] = ['nullable'];
                    } else {
                        $validationRule['payment_slip'] = ['nullable'];
                    }
                }






                $user = User::findOrFail($curr_user->id);
                $amount = $data->amount;
                // if ($amount > $user->balance) {
                if ($amount > $user->balance) {

                    $notify[] = ['error', 'You do not have sufficient balance'];
                    return back()->withNotify($notify);
                } else {
                }
            } else {
            }
        } else {
            $utr_number = '';
            if ($request->has('utr_number')) {
                $utr_number = $request->get('utr_number');
            } elseif ($request->has('transaction_number')) {
                $utr_number = $request->get('transaction_number');
            }


            if (!empty($utr_number)) {
                $utr_number_found_count = Deposit::where('detail', 'LIKE', "%{$utr_number}%")
                    ->whereIn('status', [1, 2])->count();

                if ($utr_number_found_count > 0) {
                    $notify[] = ['error', 'UTR/Transaction number already used'];
                    return back()->withNotify($notify);
                }
            }
        }
        //wallet



        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);







        //wallet


        // dd($request->all(),$gateway->name,$data);
        if (($request->get('payment_mode') == 'Wallet' || $request->get('payment_mode') == 'wallet') || ($request->get('mode_of_paymnet') == 'Wallet' || $request->get('mode_of_paymnet') == 'wallet')) {

            //if ($gateway->name == $rstl_payment_gateway_id) {
            if (in_array($gateway->name, [$rstl_payment_gateway_id, $rstl_payment_gateway_emi_repayment_id, $rstl_payment_gateway_closing_loan_id])) {

                // dd($data->amount, $validationRule, $gateway->name, $rstl_payment_gateway_id, $request->get('payment_mode'), $request->all());
                $user = User::findOrFail($curr_user->id);
                $amount = $data->amount;



                // if ($amount > $user->balance) {
                if ($amount > $user->balance) {

                    $notify[] = ['error', 'You do not have sufficient balance'];
                    return back()->withNotify($notify);
                } else {



                    // $trx = getTrx();

                    // $transaction = new Transaction();

                    // $user->balance -= $amount;

                    // $transaction->trx_type = '-';
                    // //$transaction->remark = 'balance_subtract_loan_processing_fee';
                    // $transaction->remark = 'balance_subtract_rstl_loan';

                    // $user->save();

                    // $transaction->user_id = $user->id;
                    // $transaction->amount = $amount;
                    // $transaction->post_balance = $user->balance;
                    // $transaction->charge = 0;
                    // $transaction->trx =  $trx;
                    // //$transaction->details = 'RSTL Loan Processing Fee';
                    // $transaction->details = $gateway->name;
                    // $transaction->save();






                    //pay emi
                    if (in_array($gateway->name, [$rstl_payment_gateway_emi_repayment_id]) && isset($request->repayment_id) && $request->repayment_id > 0) {
                        $loan_repayment_id = $request->repayment_id;


                        if ($loan_repayment_id > 0) {

                            $datetime = now();




                            $loan_repayment_item = DB::table('loan_repayments')
                                ->where('id', $loan_repayment_id)
                                ->first();

                            if (isset($loan_repayment_item->loan_id)) {

                                $loan_approved_info = DB::table('loan_approved')
                                    ->where('id', $loan_repayment_item->loan_id)
                                    ->first();


                                DB::table('loan_repayments')
                                    ->where('id', $loan_repayment_id)  // find your user by their email
                                    ->update(array('is_emi_paid' => 1, 'emi_paid_datetime' => $datetime->format("Y-m-d H:i:s")));


                                $loan_repayments_is_emi_paid = DB::table('loan_repayments')
                                    ->where('loan_id', $loan_repayment_item->loan_id)
                                    ->where('is_emi_paid', 1)
                                    ->where('is_active', 1)
                                    ->count();

                                $loan_repayments_total_count = DB::table('loan_repayments')
                                    ->where('loan_id', $loan_repayment_item->loan_id)
                                    ->where('is_active', 1)
                                    ->count();

                                if ($loan_repayments_total_count > 0) {
                                    if ($loan_repayments_is_emi_paid == $loan_repayments_total_count) {
                                        DB::table('loan_approved')
                                            ->where('id', $loan_repayment_item->loan_id)  // find your user by their email
                                            ->update(array('is_loan_closed' => 1));
                                    }
                                }


                                //deduct money
                                $user = User::findOrFail($loan_repayment_item->user_id);
                                $amount = $loan_repayment_item->emi_amount;

                                // $notifyTemplate = 'BAL_SUB';

                                if ($amount > $user->balance) {
                                } else {


                                    // $general = gs();
                                    $trx = getTrx();

                                    $transaction = new Transaction();

                                    $user->balance -= $amount;

                                    $transaction->trx_type = '-';
                                    $transaction->remark = 'balance_subtract_loan_emi';


                                    // $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];


                                    $user->save();

                                    $transaction->user_id = $user->id;
                                    $transaction->amount = $amount;
                                    $transaction->post_balance = $user->balance;
                                    $transaction->charge = 0;
                                    $transaction->trx =  $trx;
                                    $transaction->details = 'Loan EMI for LOAN:' . $loan_approved_info->loan_id;
                                    // $transaction->details = 'Loan EMI';
                                    $transaction->save();

                                    $notify[] = ['success', 'You have request has been processed'];
                                    return to_route('user.plan.bstl_payment')->withNotify($notify);

                                    //// $user = User::findOrFail(37);
                                    // notify($user, $notifyTemplate, [
                                    //     'trx' => $trx,
                                    //     'amount' => showAmount($amount),
                                    //     // 'message'=>"Hello {$user->fullname} your emi payment for your loan {$loan->loan_id} is failed. Please pay your emi.",
                                    //     // 'remark' => 'Loan EMI for LOAN:' . $loan->loan_id,
                                    //     'remark' => "Your emi payment for your loan {$loan->loan_id} is paid",
                                    //     'post_balance' => showAmount($user->balance)
                                    // ]);
                                }
                                //deduct money



                                // notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                                //     'method_name' => $deposit->gatewayCurrency()->name,
                                //     'method_currency' => $deposit->method_currency,
                                //     'method_amount' => showAmount($deposit->final_amo),
                                //     'amount' => showAmount($deposit->amount),
                                //     'charge' => showAmount($deposit->charge),
                                //     'rate' => showAmount($deposit->rate),
                                //     'trx' => $deposit->trx,
                                //     'post_balance' => showAmount($user->balance)
                                // ]);
                            }
                        }
                    } else {
                        // die('s');
                        if (in_array($gateway->name, [$rstl_payment_gateway_closing_loan_id]) && isset($request->loan_account_number)) {
                            // die('s2');
                            //Approve loan application





                            //closing
                            $datetime = now();
                            // $loan_application = DB::table('loan_applications')
                            //     ->where('user_id', $deposit_info->user_id)
                            //     ->where('is_application_approved', 1)
                            //     ->first();

                            if (!empty($request->loan_account_number)) {

                                // $loan_approved = DB::table('loan_approved')
                                //     ->where('loan_application_id', $loan_application->id)
                                //     ->first();

                                $loan_approved = DB::table('loan_approved')
                                    ->where('loan_id', $request->loan_account_number)
                                    ->first();


                                $loan_application = DB::table('loan_applications')
                                    ->where('id', $loan_approved->loan_application_id)
                                    // ->where('is_application_approved', 1)
                                    ->first();

                                if (isset($loan_approved->id)) {

                                    $loan_repayment_sum = DB::table('loan_repayments')
                                        ->where('loan_id', $loan_approved->id)
                                        ->where('is_active', 1)
                                        ->where('is_emi_paid', '<>', 1)
                                        ->sum('emi_amount');

                                    if ($loan_repayment_sum != $data->amount) {
                                        // $notify[] = ['error', "Total sum of Emi amount and Submitted closing amount not same"];
                                        // return to_route('admin.deposit.pending')->withNotify($notify);
                                    } else {
                                        DB::table('loan_approved')
                                            ->where('id', $loan_approved->id)  // find your user by their email
                                            ->update(array('is_loan_closed' => 1));

                                        DB::table('loan_repayments')
                                            ->where('is_active', 1)
                                            ->where('loan_id', $loan_approved->id)  // find your user by their email
                                            ->update(array(
                                                'is_emi_paid' => 1,
                                                'transaction_id' => $data->trx,
                                                'emi_paid_datetime' => $datetime->format("Y-m-d H:i:s")
                                            ));


                                        //deduct money
                                        $user = User::findOrFail($data->user_id);
                                        $amount = $loan_repayment_sum;

                                        // $notifyTemplate = 'BAL_SUB';

                                        if ($amount > $user->balance) {
                                        } else {


                                            // $general = gs();
                                            $trx = getTrx();

                                            $transaction = new Transaction();

                                            $user->balance -= $amount;

                                            $transaction->trx_type = '-';
                                            $transaction->remark = 'balance_subtract_loan_closing';


                                            // $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];


                                            $user->save();

                                            $transaction->user_id = $user->id;
                                            $transaction->amount = $amount;
                                            $transaction->post_balance = $user->balance;
                                            $transaction->charge = 0;
                                            $transaction->trx =  $trx;
                                            $transaction->details = 'Loan Closing for LOAN:' . $loan_approved->loan_id;
                                            // $transaction->details = 'Loan EMI';
                                            $transaction->save();

                                            $notify[] = ['success', 'You have request has been processed'];
                                            return to_route('user.plan.bstl_payment')->withNotify($notify);


                                            //// $user = User::findOrFail(37);
                                            // notify($user, $notifyTemplate, [
                                            //     'trx' => $trx,
                                            //     'amount' => showAmount($amount),
                                            //     // 'message'=>"Hello {$user->fullname} your emi payment for your loan {$loan->loan_id} is failed. Please pay your emi.",
                                            //     // 'remark' => 'Loan EMI for LOAN:' . $loan->loan_id,
                                            //     'remark' => "Your emi payment for your loan {$loan->loan_id} is paid",
                                            //     'post_balance' => showAmount($user->balance)
                                            // ]);
                                        }
                                        //deduct money
                                    }
                                }
                            }
                            //closing



                        } else {
                            // die('sx');
                            if (in_array($gateway->name, [$rstl_payment_gateway_id])) {
                                // die('ss');
                                $trx = getTrx();

                                $transaction = new Transaction();

                                $user->balance -= $amount;

                                $transaction->trx_type = '-';
                                //$transaction->remark = 'balance_subtract_loan_processing_fee';
                                $transaction->remark = 'balance_subtract_rstl_loan';

                                $user->save();

                                $transaction->user_id = $user->id;
                                $transaction->amount = $amount;
                                $transaction->post_balance = $user->balance;
                                $transaction->charge = 0;
                                $transaction->trx =  $trx;
                                //$transaction->details = 'RSTL Loan Processing Fee';
                                $transaction->details = $gateway->name;
                                $transaction->save();





                                ///////////////////plan starts/////////////////

                                $rstl_deposit_approved_amount_for_package = setting('pgrs_rstl_deposit_approved_amount_for_package', 34);
                                //give loan balance
                                $user = User::find($user->id);
                                // $user->balance += $deposit->amount;
                                $user->balance += $rstl_deposit_approved_amount_for_package;
                                $user->save();

                                $transaction = new Transaction();
                                $transaction->user_id = $user->id;
                                // $transaction->amount = $deposit->amount;
                                $transaction->amount = $rstl_deposit_approved_amount_for_package;
                                $transaction->post_balance = $user->balance;
                                $transaction->charge = $data->charge;
                                $transaction->trx_type = '+';
                                $transaction->details = 'RSTL Deposit Approved Via ' . $data->gatewayCurrency()->name . ". Loan amount added in balance";
                                $transaction->trx = $data->trx;
                                $transaction->remark = 'deposit';
                                $transaction->save();
                                //give loan balance




                                //balance to loan
                                $user = User::find($user->id);
                                $plan = new Plan();


                                $plan->name             = '2ReturnSure Trading Package';
                                $plan->price            = $rstl_deposit_approved_amount_for_package;


                                $plan->plan_created_by_user_id = $user->id;



                                $plan->bv               = $rstl_deposit_approved_amount_for_package;
                                $plan->ref_com          = 5;
                                $plan->tree_com         = 5;






                                $plan->save();
                                $plan = Plan::where('status', Status::ENABLE)->findOrFail($plan->id);

                                $trx = getTrx();

                                // $mlm = new Mlm($user, $plan, $trx);
                                // $mlm->purchasePlan();




                                $mlm = new Mlm($user, $plan, $trx);
                                $mlm->purchasePlanForSomeoneAsLoan($user, $plan, $trx, auth()->user());
                                $user = User::find($user->id);

                                $data->detail = $userData;
                                $data->status = 1;
                                $data->save();
                                ////////////////////plan ends////////////////




                                //Approve loan application
                                $datetime = now();
                                $rstl_deposit_approved_amount_for_package = setting('pgrs_rstl_deposit_approved_amount_for_package', 34);
                                $loan_application_created_id = DB::table('loan_applications')->insertGetId(
                                    [
                                        // 'loan_id' => generateLaonId(),
                                        'deposit_id' =>  $data->id,
                                        'user_id' => $user->id,
                                        'payment_gateway_id' => $gateway->id,
                                        'loan_processing_fees' => $amount,
                                        'loan_amount' => $rstl_deposit_approved_amount_for_package,
                                        // 'transaction_id' => '',

                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                    ]
                                );



                                $loan_application = DB::table('loan_applications')
                                    ->where('id', '=', $loan_application_created_id)
                                    ->first();

                                if (isset($loan_application->id)) {
                                    // $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');
                                    // $rstl_deposit_approved_amount_for_package = setting('pgrs_rstl_deposit_approved_amount_for_package', 34);
                                    // if ($rstl_payment_gateway_id == $gateway->name) {
                                    // $datetime = now();
                                    $loan_approved_date = now();
                                    // $loan_approved_date = $datetime->format("Y-m-d H:i:s");

                                    DB::table('loan_applications')
                                        ->where('id', $loan_application->id)  // find your user by their email
                                        ->update(array('is_application_approved' => 1));


                                    $created_loan_id = DB::table('loan_approved')->insertGetId(
                                        [
                                            'loan_id' => generateLaonId(),
                                            // 'deposit_id' =>  $loan_application->deposit_id,
                                            'loan_application_id' =>  $loan_application->id,
                                            // 'user_id' => $loan_application->user_id,
                                            // 'payment_gateway_id' => $loan_application->payment_gateway_id,
                                            // 'loan_processing_fees' => $amount,
                                            // 'loan_amount' => $rstl_deposit_approved_amount_for_package,
                                            // 'transaction_id' => '',

                                            'created_at' => $datetime->format("Y-m-d H:i:s"),
                                            'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                        ]
                                    );






                                    if (1) {

                                        $loan_tenure = null;
                                        if (is_string($request->rstl_bsl_emi_tenure) && !empty($request->rstl_bsl_emi_tenure)) {
                                            if (preg_match('#(?<tenure>\d+)#', $request->rstl_bsl_emi_tenure, $tenmatch) === 1) {

                                                if (isset($tenmatch['tenure'])) {
                                                    $loan_tenure = $tenmatch['tenure'];
                                                }
                                            }
                                        }

                                        if (!empty($loan_tenure)) {
                                            if ($loan_application->loan_amount % $loan_tenure == 0) {
                                                $loan_emis = $loan_application->loan_amount / $loan_tenure;

                                                for ($loan_loop_counter = 1; $loan_loop_counter <= $loan_tenure; $loan_loop_counter++) {

                                                    $loan_approved_date = $loan_approved_date->addDays(30);
                                                    DB::table('loan_repayments')->insertGetId(
                                                        [
                                                            'loan_id' => $created_loan_id,
                                                            'emi_amount' => $loan_emis,
                                                            'is_emi_paid' => 0,
                                                            'emi_to_be_paid_datetime' => $loan_approved_date->format("Y-m-d H:i:s"),
                                                            'user_id' => $loan_application->user_id,
                                                            'created_at' => $datetime->format("Y-m-d H:i:s"),
                                                            'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                                        ]
                                                    );
                                                }
                                            } else {
                                                $loan_emis = $loan_application->loan_amount / $loan_tenure;
                                                $loan_emi_rounded = round($loan_emis, 0);
                                                $last_month_emi = ($loan_application->loan_amount - ($loan_emi_rounded * ($loan_tenure - 1)));


                                                for ($loan_loop_counter = 1; $loan_loop_counter <= $loan_tenure; $loan_loop_counter++) {
                                                    $loan_approved_date = $loan_approved_date->addDays(30);
                                                    DB::table('loan_repayments')->insertGetId(
                                                        [
                                                            'loan_id' => $created_loan_id,
                                                            'emi_amount' => $loan_loop_counter == $loan_tenure ? $last_month_emi : $loan_emi_rounded,
                                                            'is_emi_paid' => 0,
                                                            'emi_to_be_paid_datetime' => $loan_approved_date->format("Y-m-d H:i:s"),
                                                            'user_id' => $loan_application->user_id,
                                                            'created_at' => $datetime->format("Y-m-d H:i:s"),
                                                            'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                                        ]
                                                    );
                                                }
                                                // echo " all month emi $loan_emi_rounded <br/>";
                                                // echo " last_month_emi month emi $last_month_emi";
                                                // $amount_reduced = 0;
                                                // $loan_amount_divisble = $loan_application->loan_amount;
                                                // while (1) {
                                                //     if ($loan_amount_divisble % $loan_tenure == 0) {
                                                //         break;
                                                //     }
                                                //     $amount_reduced++;
                                                //    echo "  ". $loan_amount_divisble = $loan_application->loan_amount - $amount_reduced;
                                                // }
                                                // $loan_amount_divisble = $loan_application->loan_amount - 1;
                                                // echo " $amount_reduced not  $loan_application->loan_amount divided $loan_amount_divisble";
                                                // echo "<br/>";
                                            }

                                            $notify[] = ['success', 'You have request has been processed'];
                                            return to_route('user.plan.bstl_payment')->withNotify($notify);
                                        }
                                        // }
                                    }
                                }
                            }
                        }
                    }

                    //pay emi
                }
            } else {
            }
        }



        //wallet



        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();



        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'rate' => showAmount($data->rate),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
