<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\Mlm;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\LevelIncomeUser;
use App\Models\TradingIncome;

class PlanController extends Controller
{
    function planIndex()
    {
        $user = auth()->user();
        $pageTitle = "Plans";
        // $plans = Plan::where('status', Status::ENABLE)->orderBy('price', 'asc')->paginate(getPaginate(15));
        $plans = Plan::where('status', Status::ENABLE)->where('plan_created_by_user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate(15));
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        return view($this->activeTemplate . 'user.plan', compact('pageTitle', 'plans', 'gatewayCurrency'));
    }

    function activation(Request $request)
    {

        $user = auth()->user();
        // dd([setting('minimum_plan_amount', 1)]);


        $pageTitle = "Plan Activation";
        $plans = Plan::where('status', Status::ENABLE)->orderBy('price', 'asc')->paginate(getPaginate(15));
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        return view($this->activeTemplate . 'user.plan_activation', compact('pageTitle', 'plans', 'gatewayCurrency'));
    }

    function tradingIncome(Request $request)
    {

        $user = auth()->user();

        $pageTitle = "Trading Income";


        $trading_income = TradingIncome::where('user_id', $user->username)->orderBy('id', 'desc')->paginate(getPaginate());


        foreach ($trading_income as $trading_income_key => $trading_income_v) {

            // $user_current_plane_amount = Plan::where('plan_created_by_user_id', $trading_income_v->user_table_id)->sum('price');
            // $trading_income_v->user_all_plans_amount_sum = $user_current_plane_amount;
        }

        return view($this->activeTemplate . 'user.trading_income', compact('pageTitle', 'trading_income'));
    }

    function direct_income(Request $request)
    {
        //return $gs = DB::table('general_settings')->first()->direct_income;
        $user = auth()->user();

        $pageTitle = "Direct Income";
 
        $directIncome = Transaction::where('remark','referral_commission')->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());
 
        return view($this->activeTemplate . 'user.direct_income', compact('pageTitle', 'directIncome'));
    }




    function bstlPayment(Request $request)
    {

        $user = auth()->user();

        $pageTitle = "BSL Payment";


        $deposits = Deposit::with(['user', 'gateway']);

        // $deposits = $deposits->searchable(['trx', 'user:username'])->dateFilter();

        // $request = request();
        //vai method

        $method = Gateway::where('alias', 'rstl_bsl')->firstOrFail();
        $deposits = $deposits->where('method_code', $method->code);

        $deposits = $deposits->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());




        $trading_income = TradingIncome::where('user_id', $user->username)->orderBy('id', 'desc')->paginate(getPaginate());


        $rstl_deposit_approved_amount_for_package = setting('pgrs_rstl_deposit_approved_amount_for_package', 34);

        foreach ($deposits as $trading_income_key => $trading_income_v) {
            // $trading_income_v->detail = ($trading_income_v->detail != null) ? json_encode($trading_income_v->detail) : null;
            $trading_income_v->gateway_details = Gateway::where('code', $trading_income_v->method_code)->first();
            $trading_income_v->payment_mode = '';
            $trading_income_v->utr = '';
            $trading_income_v->tenure = '';
            if ($trading_income_v->status == 1) {
                $trading_income_v->loan_status = 'Success';
            } elseif ($trading_income_v->status == 2) {
                $trading_income_v->loan_status = 'Pending';
            } elseif ($trading_income_v->status == 3) {
                $trading_income_v->loan_status = 'Cancel';
            } else {
                $trading_income_v->loan_status = 'Initiated';
            }
            $trading_income_v->loan_amount = $rstl_deposit_approved_amount_for_package;
            if (isset($trading_income_v->detail) && !empty($trading_income_v->detail)) {
                foreach ($trading_income_v->detail as $ddk => $ddv) {
                    $ddv = (array)$ddv;


                    if (isset($ddv['name'])) {
                        switch ($ddv['name']) {
                            case 'Payment Mode':
                                if (is_array($ddv['value'])) {
                                    $trading_income_v->payment_mode = implode(',', $ddv['value']);
                                } else {
                                    $trading_income_v->payment_mode = $ddv['value'];
                                }
                                // $trading_income_v->payment_mode = implode(',', $ddv['value']);
                                break;
                            case 'RSTL BSL EMI Tenure':
                                if (is_array($ddv['value'])) {
                                    $trading_income_v->tenure = implode(',', $ddv['value']);
                                } else {
                                    $trading_income_v->tenure = $ddv['value'];
                                }

                                break;
                            case 'UTR Number':
                                $trading_income_v->utr = ($ddv['value']);
                                break;
                            case 'File Payment Slip':
                                $trading_income_v->slip = route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $ddv['value']));
                                $trading_income_v->slip = '<a href="{{' . $trading_income_v->slip . '}}" class="me-3"><i class="fa fa-file"></i> Slip </a>';
                                break;
                        }
                    }
                }
            }
        }


        $all_loans_repayments = DB::table('loan_repayments')
            // ->where('club_id', '=', $club->id)
            ->where('user_id', '=', $user->id)
            ->where('is_active', '=', 1)
            ->orderBy('id', 'asc')
            ->get();
        foreach ($all_loans_repayments as  $all_loans_repayment) {
            $loan_approved = DB::table('loan_approved')

                ->where('id', '=', $all_loans_repayment->loan_id)
                ->first();
            if (isset($loan_approved->loan_id)) {
                $all_loans_repayment->loan_id_assigned_to_user = $loan_approved->loan_id;
            } else {
                $all_loans_repayment->loan_id_assigned_to_user = '';
            }
            if ($all_loans_repayment->is_emi_paid == 1) {
                $all_loans_repayment->emi_paid_status = 'Paid';
            } else {
                if ($all_loans_repayment->is_emi_paid == 2) {
                    $all_loans_repayment->emi_paid_status = 'Failed';
                } else {
                    $all_loans_repayment->emi_paid_status = 'Pending';
                }
            }

            $all_loans_repayment->emi_to_be_paid_datetime = now()->parse($all_loans_repayment->emi_to_be_paid_datetime)->format('l, d M Y');
        }


        // dd($deposits);


        return view($this->activeTemplate . 'user.bstl_payments', compact('pageTitle', 'deposits', 'all_loans_repayments'));
    }
    function levelIncome(Request $request)
    {

        $user = auth()->user();

        $pageTitle = "Level Income";

        $level_selected = request()->get('level');
        if ($level_selected > 0) {

            $trading_income = LevelIncomeUser::where('to_user_table_id', $user->id)->where('level', $level_selected)->orderBy('id', 'desc')->paginate(getPaginate());
        } else {
            $trading_income = LevelIncomeUser::where('to_user_table_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());
        }






        foreach ($trading_income as $trading_income_key => $trading_income_v) {

            $trading_income_v->from_user = $from_user = User::where('id', $trading_income_v->from_user_table_id)->first();
            // $user_current_plane_amount = Plan::where('plan_created_by_user_id', $trading_income_v->user_table_id)->sum('price');
            // $trading_income_v->from_user = $from_user->firs;
        }

        return view($this->activeTemplate . 'user.level_income', compact('pageTitle', 'trading_income', 'level_selected'));
    }


function turnoverIncome(Request $request)
    {

        $user = auth()->user();

        $pageTitle = "Reward Income";

        // $user_clubs =    DB::table('reward_income_achievers')
        //     ->where('is_active', 1)
        //     ->where('user_id', $user->id)
        //     ->orderBy('pair', 'asc')
        //     ->get();

        $all_clubs =    DB::table('reward_income')
            ->where('is_active', 1)
            ->orderBy('pair', 'asc')
            ->get();



        foreach ($all_clubs as $trading_income_key => $trading_income_v) {

            $trading_income_v->user_club_status =  DB::table('reward_income_achievers')
                ->where('reward_id', $trading_income_v->id)
                ->where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();
        }



        return view($this->activeTemplate . 'user.reward_income', compact('pageTitle', 'all_clubs'));
    }


    function turnoverIncomeX(Request $request)
    {

        $user = auth()->user();

        $pageTitle = "Turnover Income";


        $trading_income = TradingIncome::where('user_id', 99999999)->orderBy('id', 'desc')->paginate(getPaginate());
        $trading_income = DB::table('club_user_income')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());



        foreach ($trading_income as $trading_income_key => $trading_income_v) {
            $trading_income_v->user = User::where('id', $trading_income_v->user_id)->first();
            $trading_income_v->club =  DB::table('clubs')->where('id', $trading_income_v->club_id)->first();
            // $user_current_plane_amount = Plan::where('plan_created_by_user_id', $trading_income_v->user_table_id)->sum('price');
            // $trading_income_v->user_all_plans_amount_sum = $user_current_plane_amount;
        }


        $user_club_data =    DB::table('clubs')->get();

        foreach ($user_club_data as $ucd) {
            $ucd->club_user_achievement_status =    DB::table('club_user_achievement')->where('club_id', $ucd->id)->where('user_id', $user->id)->count();
            $ucd->club_user_achievement_info =    DB::table('club_user_achievement')->where('club_id', $ucd->id)->where('user_id', $user->id)->first();
        }
        // $trading_income=collect[];
        // dd($user_club_data);

        return view($this->activeTemplate . 'user.turnover_income', compact('pageTitle', 'trading_income', 'user_club_data', 'user'));
    }


    function activationTopUpSave(Request $request)
    {
        if ($request->isMethod('post')) {

            $minimum_amount = setting('minimum_plan_amount', 1);
            $minimum_upgrdate_amount = setting('prs_minimum_plant_update_amount', 100);

            $user = auth()->user();
            // dd( $user);
            // dd( $request);
            $is_buy_plan_for_a_different_user = false;

            if (!isset($request->plan_type)) {
            }


            if ($request->plan_type == 'upgrade') {

                if ($request->user_plan_amount < $minimum_upgrdate_amount) {
                    $notify[] = ['error', "Minimum amount should be $minimum_upgrdate_amount or more"];
                    return back()->withNotify($notify);
                }
            } else {
                if ($request->user_plan_amount < $minimum_amount) {
                    $notify[] = ['error', "Minimum activation amount should be $minimum_amount or more"];
                    return back()->withNotify($notify);
                }
            }

            // if ($request->plan_type == 'activate') {
            if ($user->plan_id > 0) {

                // if ($request->user_plan_amount < $minimum_amount) {
                //     $notify[] = ['error', "Minimum amount should be $minimum_amount or more"];
                //     return back()->withNotify($notify);
                // }

                // $notify[] = ['error', "Minimum amount should be $minimum_amount or more"];
                // return back()->withNotify($notify);

                //current user is active
                // if (!isset($request->user_id) || empty($request->user_id)) {
                //     $notify[] = ['error', "Please enter a user ID"];
                //     return back()->withNotify($notify);
                // }

                // if ($request->plan_type == 'upgrade') {
                // } else {
                // if ($request->user_id == $user->username) {
                //     if ($request->plan_type == 'upgrade') {
                //     } else {
                //         $notify[] = ['error', "This ID is already active"];
                //         return back()->withNotify($notify);
                //     }
                // } else {
                //     $buy_plan_for_a_different_user = User::where('username', $request->user_id)->first();
                //     if ($buy_plan_for_a_different_user->plan_id > 0) {
                //         if ($request->plan_type == 'upgrade') {
                //             // die("x");
                //             $is_buy_plan_for_a_different_user = true;
                //         } else {
                //             $notify[] = ['error', "This ID is already active"];
                //             return back()->withNotify($notify);
                //         }
                //     } else {
                //         $is_buy_plan_for_a_different_user = true;
                //     }
                // }




                // }
            } else {
                if ($request->user_plan_amount < $minimum_amount) {
                    $notify[] = ['error', "Minimum amount should be $minimum_amount or more"];
                    return back()->withNotify($notify);
                }
                //current user is not active
            }
            // } else {
            // if ($request->plan_type == 'upgrade') {
            //         if ($user->plan_id > 0) {
            //         } else {
            //             $notify[] = ['error', "Activate your ID first"];
            //             return back()->withNotify($notify);
            //         }
            //     }
            // }





            $plan = new Plan();




            if ($request->id) {
                // $plan = Plan::findOrFail($request->id);
                // $plan = Plan::findOrFail(999999999);
            }

            $user_total_bonus = $user->total_bonus;
            $plan->name             = '2ReturnSure Trading Package';
            $plan->price            = $request->user_plan_amount;
            if ($request->plan_type == 'upgrade') {
                if ($user->total_bonus > 0) {
                    $current_date = now()->format("Y-m-d H:i:s");
                    $user_signup_date = now()->parse($user->created_at);
                    $days_difference = $user_signup_date->diffInDays($current_date);
                    if ($days_difference <= 15) {

                        if ($user->total_bonus >= $request->user_plan_amount) {
                            $plan_price_after_bonus = 0;
                            $user->total_bonus = $user->total_bonus - $request->user_plan_amount;
                        } else {
                            $plan_price_after_bonus = $request->user_plan_amount - $user->total_bonus;
                            $user->total_bonus = 0;
                        }
                    } else {
                        $user->total_bonus = 0;
                    }
                }
            } else {
            }
            // die;
            if ($is_buy_plan_for_a_different_user) {
                $plan->plan_created_by_user_id = $buy_plan_for_a_different_user->id;
            } else {
                $plan->plan_created_by_user_id = $user->id;
            }


            $plan->bv               = $request->user_plan_amount;
            $plan->ref_com          = 5;
            $plan->tree_com         = 5;

            // echo '$plan_price_after_bonus:'.$plan_price_after_bonus;
            // dd($plan);




            // $user = auth()->user();

            if (isset($plan_price_after_bonus)) {
                $plan->plan_price_after_bonus = $plan_price_after_bonus;
                $plan->is_bonus_used_on_plan = 1;
                $plan->bonus_used_by_user = $user_total_bonus;
                if ($user->balance < $plan_price_after_bonus) {

                    $notify[] = ['error', 'You\'ve no sufficient balance'];
                    return back()->withNotify($notify);
                } else {
                    // die("50 will");
                }
            } else {
                if ($user->balance < $plan->price) {
                    $notify[] = ['error', 'You\'ve no sufficient balance1'];
                    return back()->withNotify($notify);
                }
            }
            // dd($user);
            $plan->save();
            $plan = Plan::where('status', Status::ENABLE)->findOrFail($plan->id);
            // $user = auth()->user();
            $trx = getTrx();

            if ($is_buy_plan_for_a_different_user) {

                $mlm = new Mlm($buy_plan_for_a_different_user, $plan, $trx);
                $mlm->purchasePlanForSomeone($buy_plan_for_a_different_user, $plan, $trx, $user);
            } else {
                $mlm = new Mlm($user, $plan, $trx);
                $mlm->purchasePlan();
            }


            $notify[] = ['success', "Plan has been successfully created"];
            return back()->withNotify($notify);
            // dd($request);
        }
    }

    function planPurchase(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'payment_method' => 'required'
        ]);
        $plan = Plan::where('status', Status::ENABLE)->findOrFail($request->id);
        $user = auth()->user();

        // if ($user->plan->price > $plan->price) {
        //     $notify[] = ['error', 'Plan cannot be downgraded'];
        //     return back()->withNotify($notify);
        // }

        if ($request->payment_method != 'balance') {
            $gate = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->find($request->payment_method);

            if (!$gate) {
                $notify[] = ['error', 'Invalid gateway'];
                return back()->withNotify($notify);
            }

            if ($gate->min_amount > $plan->price || $gate->max_amount < $plan->price) {
                $notify[] = ['error', 'Plan price crossed gateway limit.'];
                return back()->withNotify($notify);
            }

            $data = PaymentController::insertDeposit($gate, $plan->price, $plan);
            session()->put('Track', $data->trx);
            return to_route('user.deposit.confirm');
        }

        if ($user->balance < $plan->price) {
            $notify[] = ['error', 'You\'ve no sufficient balance'];
            return back()->withNotify($notify);
        }

        $trx = getTrx();

        $mlm = new Mlm($user, $plan, $trx);
        $mlm->purchasePlan();





        $notify[] = ['success', ucfirst($plan->name) . ' plan purchased Successfully'];
        return redirect()->back()->withNotify($notify);
    }
}
