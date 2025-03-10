<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Lib\Mlm;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\Form;
use App\Models\IncomeLosts;
use App\Models\LevelIncomes;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TradingIncome;
use App\Models\UserExtra;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\GatewayCurrency;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function rstlDeposit()
    {
        $user = auth()->user();

        $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_id_text', '');


        if ($user->plan_id > 0) {

            $gatewayCurrency = [];
        } else {

            if (!empty($rstl_payment_gateway_id)) {

                $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->with('method')->where('name', $rstl_payment_gateway_id)->orderby('method_code')->get();
            } else {
                $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->with('method')->orderby('method_code')->get();
            }
        }

        // dd( $gatewayCurrency->first());

        $loan_application_current_not_approved = 0;

        $loan_application_current_not_approveds = DB::table('loan_applications')
            ->where('user_id', '=', $user->id)
            ->where('is_application_approved', '=', 0)
            ->get();

        foreach ($loan_application_current_not_approveds as $loan_application_currenna_itm) {
            $gateinfo_count_not_completed = DB::table('deposits')
                ->select('status')
                ->where('id', '=', $loan_application_currenna_itm->deposit_id)
                ->get();
            foreach ($gateinfo_count_not_completed as $gateinfo_count_not_completeditm) {
                if ($gateinfo_count_not_completeditm->status == 2 || $gateinfo_count_not_completeditm->status == 1) {
                    $loan_application_current_not_approved++;
                } else {
                }
            }
        }

        // $gateinfo = $gatewayCurrency->first();
        // if (isset($gateinfo->id)) {
        //     // dd($loan_application_current_not_approved);
        //     $gateinfo_count_not_completed = DB::table('deposits')
        //         ->where('user_id', '=', $user->id)
        //         ->where('method_code', '=', $gateinfo->method_code)
        //         ->where('status', '<>', 0)
        //         ->count();

        //     if ($gateinfo_count_not_completed > 0) {
        //     } else {
        //         // $loan_application_current_not_approved = 0;
        //     }
        // }

        // $loan_application_current_not_approved=0;

        $pageTitle = 'Deposit Methods';
        return view($this->activeTemplate . 'user.payment.deposit_rstl', compact('gatewayCurrency', 'pageTitle', 'loan_application_current_not_approved'));
    }
    public function rstlDepositClosing()
    {
        $user = auth()->user();

        $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_closing_loan_id_text', '');



        // $user_loan_id='';

        $user_loan_app = DB::table('loan_applications')
            ->where('user_id', '=', $user->id)
            ->where('is_application_approved', '=', 1)
            ->first();
        if (isset($user_loan_app->id)) {
            $user_loan_info = DB::table('loan_approved')
                ->where('loan_application_id', '=', $user_loan_app->id)

                ->first();
            if (isset($user_loan_info->loan_id)) {
                // $user_loan_id=$user_loan_info->loan_id;
                session()->put("rstl_user_loan_number", $user_loan_info->loan_id);
            }
        }

        $loan_repayments_emi_amount = DB::table('loan_repayments')
            ->where('user_id', $user->id)
            ->where('is_emi_paid', '<>', 1)
            ->where('is_active', 1)
            ->sum('emi_amount');

        if ($user->plan_id > 0 && false) {

            $gatewayCurrency = [];
        } else {

            if (!empty($rstl_payment_gateway_id)) {

                $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->with('method')->where('name', $rstl_payment_gateway_id)->orderby('method_code')->get();
            } else {
                $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->with('method')->orderby('method_code')->get();
            }
        }




        // $pgrs_rstl_payment_gateway_closing_loan_id_text = setting('pgrs_rstl_payment_gateway_closing_loan_id_text', '');

        $loan_application_current_not_approved = 0;
        $gateinfo = $gatewayCurrency->first();
        if (isset($gateinfo->id)) {
            // dd($loan_application_current_not_approved);
            $gateinfo_count_not_completed = DB::table('deposits')
                ->where('user_id', '=', $user->id)
                ->where('method_code', '=', $gateinfo->method_code)
                // ->where('status', '<>', 0)
                ->get();


            foreach ($gateinfo_count_not_completed as $gateinfo_count_not_completeditm) {
                if ($gateinfo_count_not_completeditm->status == 2 || $gateinfo_count_not_completeditm->status == 1) {
                    $loan_application_current_not_approved++;
                } else {
                }
            }
            // if ($gateinfo_count_not_completed > 0) {
            // } else {
            //     // $loan_application_current_not_approved = 0;
            // }
        }





        // $gateinfo_count_not_completed = DB::table('deposits')
        //     ->select('status')
        //     ->where('id', '=', $loan_application_currenna_itm->deposit_id)
        //     ->get();
        // foreach ($gateinfo_count_not_completed as $gateinfo_count_not_completeditm) {
        //     if ($gateinfo_count_not_completeditm->status == 2 || $gateinfo_count_not_completeditm->status == 1) {
        //         $loan_application_current_not_approved++;
        //     } else {
        //     }
        // }



        $pageTitle = 'Deposit Methods';
        return view($this->activeTemplate . 'user.payment.deposit_rstl_closing', compact('gatewayCurrency', 'pageTitle', 'loan_repayments_emi_amount', 'loan_application_current_not_approved'));
    }
    public function rstlEmiRepayment()
    {
        $user = auth()->user();
        $repayment_id = request()->route('repayment_id');
        $loan_repayment = DB::table('loan_repayments')
            ->where('id', $repayment_id)
            ->first();

        if (!isset($loan_repayment->emi_amount)) {
            $notify[] = ['error', 'Invalid Emi'];
            return back()->withNotify($notify);
        }


        session()->put("rstl_loan_repayment_id", $repayment_id);




        $rstl_payment_gateway_id = setting('pgrs_rstl_payment_gateway_emi_repayment_loan_id_text', '');


        if ($user->plan_id > 0 && false) {

            $gatewayCurrency = [];
        } else {

            if (!empty($rstl_payment_gateway_id)) {

                $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->with('method')->where('name', $rstl_payment_gateway_id)->orderby('method_code')->get();
            } else {
                $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->with('method')->orderby('method_code')->get();
            }
        }

        $pageTitle = 'EMI Repayment Methods';
        return view($this->activeTemplate . 'user.payment.emi_repayment_rstl_closing', compact('gatewayCurrency', 'pageTitle', 'loan_repayment'));
    }

    public function home()
    {
        $pageTitle = 'Dashboard';
        $user = auth()->user();



        $user_members_defaulter_check = checkUserHasEmiDefaulterInHisTeam($user);


        if (isset($user_members_defaulter_check['members_user_not_paid_emi_in_team']) && !empty($user_members_defaulter_check['members_user_not_paid_emi_in_team'])) {
            $user_members_defaulter_check['members_user_not_paid_emi_in_team_details'] = [];
            foreach ($user_members_defaulter_check['members_user_not_paid_emi_in_team'] as $udk => $udv) {
                $user_members_defaulter_check['members_user_not_paid_emi_in_team_details'][] = User::where('username', $udv)->first();
            }
        }
        // dd($user_members_defaulter_check);

        // dd($user);
        // dd($user->plan->price);

        $user_current_plane_amount = 0;
        $user_current_plane_amount_remaining = 0;
        $user_total_purchased_plans = 0;

        if (isset($user->plan->price)) {
            // dd(Plan::where('plan_created_by_user_id', $user->id)->sum('price'));
            $user_current_plane_amount = Plan::where('plan_created_by_user_id', $user->id)->sum('price');
            $user_total_purchased_plans = Plan::where('plan_created_by_user_id', $user->id)->count();
            $user_current_total_trading_amount = TradingIncome::where('user_table_id', $user->id)->sum('income');
            $user_current_plane_amount_remaining = (($user_current_plane_amount * 5)  - $user_current_total_trading_amount - $user->total_ref_com - $user->total_binary_com);
        }




        $current_loop_user_left_downline_info = Mlm::getUserDownline($user, 'LEFT', 1, 1, 0, ['defaulters_total_bv_sum', 'defaulters_total_count',]);
        // $current_loop_user_left_active_member = $current_loop_user_left_downline_info['non_defaulters_total_count'];
        // dd($current_loop_user_left_downline_info);

        $current_loop_user_right_downline_info = Mlm::getUserDownline($user, 'RIGHT',  1, 1, 0, ['defaulters_total_bv_sum', 'defaulters_total_count',]);
        // $current_loop_user_right_active_member = Mlm::getRightActivePaidUser($user);
        // $current_loop_user_right_active_member = $current_loop_user_right_downline_info['non_defaulters_total_count'];
        // dd($current_loop_user_right_downline_info);


        $total_paid_bv = DB::table('transactions')

            ->where('user_id', '=', $user->id)
            ->where('remark', '=', 'paid_bv')
            ->sum('amount');

        $total_paid_daily_trading_income = DB::table('transactions')

            ->where('user_id', '=', $user->id)
            ->where('remark', '=', 'paid_daily_trading_income')
            ->sum('amount');
        $total_paid_level_income = DB::table('transactions')

            ->where('user_id', '=', $user->id)
            ->where('remark', '=', 'paid_level_income')
            ->sum('amount');
        $total_referral_commission = DB::table('transactions')

            ->where('user_id', '=', $user->id)
            ->where('remark', '=', 'referral_commission')
            ->sum('amount');




        $totalDeposit = Deposit::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $incomeTotalLost = IncomeLosts::where('user_table_id', $user->id)->sum('income_lost');
        $totalSentAmount = IncomeLosts::where('user_table_id', $user->id)->sum('income_sent');
        $totalGeneratedAmount = IncomeLosts::where('user_table_id', $user->id)->sum('income_generated');
        $submittedDeposit = Deposit::where('status', '!=', Status::PAYMENT_INITIATE)->where('user_id', $user->id)->sum('amount');
        $pendingDeposit = Deposit::pending()->where('user_id', $user->id)->sum('amount');
        $rejectedDeposit = Deposit::rejected()->where('user_id', $user->id)->sum('amount');

        $totalWithdraw = Withdrawal::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $submittedWithdraw = Withdrawal::where('status', '!=', Status::PAYMENT_INITIATE)->where('user_id', $user->id)->sum('amount');
        $pendingWithdraw = Withdrawal::pending()->where('user_id', $user->id)->count();
        $rejectWithdraw = Withdrawal::rejected()->where('user_id', $user->id)->sum('amount');

        $totalRef = User::where('ref_by', $user->id)->count();
        $totalBvCut = BvLog::where('user_id', $user->id)->where('trx_type', '-')->sum('amount');
        $totalLeft = @$user->userExtra->free_left + @$user->userExtra->paid_left;
        $totalRight = @$user->userExtra->free_right + @$user->userExtra->paid_right;
        $totalBv = @$user->userExtra->bv_left + @$user->userExtra->bv_right;
        $logs = UserExtra::where('user_id', $user->id)->firstOrFail();

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'user', 'totalDeposit', 'submittedDeposit', 'pendingDeposit', 'rejectedDeposit', 'totalWithdraw', 'submittedWithdraw', 'pendingWithdraw', 'rejectWithdraw', 'totalRef', 'totalBvCut', 'totalLeft', 'totalRight', 'totalBv', 'logs', 'user_current_plane_amount', 'user_current_plane_amount_remaining', 'incomeTotalLost', 'totalGeneratedAmount', 'totalSentAmount', 'user_members_defaulter_check', 'current_loop_user_right_downline_info', 'current_loop_user_left_downline_info', 'total_paid_bv', 'total_paid_daily_trading_income', 'total_paid_level_income', 'total_referral_commission','user_total_purchased_plans'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request)
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'kyc')->first();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);
        $user = auth()->user();
        $user->kyc_data = $userData;
        $user->kv = 2;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }


    public function getUserNameByUserID()
    {
        $user_id = request()->get('user_id');
        $user = User::where('username', $user_id)->first();
        if (isset($user->firstname) && isset($user->lastname)) {
            return response()->json(['status' => 'success', 'data' => $user->firstname . " " . $user->lastname]);
        }
        return response()->json(['status' => 'error',]);
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
        ];
        $user->profile_complete = 1;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function bvLog(Request $request)
    {
        $user = auth()->user();
        if ($request->type) {
            if ($request->type == 'leftBV') {
                $pageTitle = "Left BV";
                $logs = BvLog::where('user_id', $user->id)->where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'rightBV') {
                $pageTitle = "Right BV";
                $logs = BvLog::where('user_id', $user->id)->where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'cutBV') {
                $pageTitle = "Cut BV";
                $logs = BvLog::where('user_id', $user->id)->where('trx_type', '-')->orderBy('id', 'desc')->paginate(getPaginate());
            } else {
                $pageTitle = "All Paid BV";
                $logs = BvLog::where('user_id', $user->id)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            }
        } else {
            $pageTitle = "BV LOG";
            $logs = BvLog::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());
        }
        return view($this->activeTemplate . 'user.bv_log', compact('pageTitle', 'logs'));
    }

    public function getUserStage($userId)
    {
        $stage = 1;
        $user = User::find($userId);

        while ($user && $user->ref_by) {
            $user = User::find($user->ref_by);
            $stage++;
        }

        return $stage;
    }

    public function myReferralLog()
    {
       $pageTitle = "My Referral";

        // Fetch users referred by the authenticated user
        $logs = User::where('ref_by', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        // Add stage to each referred user
        foreach ($logs as $log) {
            $log->stage = $this->getUserStage($log->id);
        }

        echo '<pre>';print_r($logs->toArray());die;

        return view($this->activeTemplate . 'user.my_referral', compact('pageTitle', 'logs'));

    }
    public function myTeam()
    {
        $pageTitle = "My Team";

        // dd(Mlm::getAllActivePaidUserOfUser(User::where('id', 37)->first()));


        // $team = User::where('ref_by', auth()->id())->orWhere('pos_id', auth()->id())

        //     ->orderBy('id', 'desc')->paginate(getPaginate());

        $view_team_position_side = request()->get('view_members_side');

        if ($view_team_position_side == 'LEFT_DEFAULTERS') {
            $team = array_reverse(Mlm::getUserDownline(User::where('id', auth()->user()->id)->first(), 'LEFT', 1, 0, 0, ['defaulters_emi_loan_user_only']));
            $team = $team['defaulters_emi_loan_user_only'];
        } else {
            if ($view_team_position_side == 'RIGHT_DEFAULTERS') {
                $team = array_reverse(Mlm::getUserDownline(User::where('id', auth()->user()->id)->first(), 'RIGHT', 1, 0, 0, ['defaulters_emi_loan_user_only']));
                $team = $team['defaulters_emi_loan_user_only'];
            } else {
                // $lteam = Mlm::getAllActivePaidUserOfUserDirectIndirectLeft(User::where('id', auth()->user()->id)->first());
                $lteam = array_reverse(Mlm::getUserDownline(User::where('id', auth()->user()->id)->first(), 'LEFT', 1, 0, 0));
                // $rteam = Mlm::getAllActivePaidUserOfUserDirectIndirectRight(User::where('id', auth()->user()->id)->first());
                $rteam = array_reverse(Mlm::getUserDownline(User::where('id', auth()->user()->id)->first(), 'RIGHT', 1, 0, 0));

                if ($view_team_position_side == 'LEFT') {
                    $team = $lteam;
                } else {
                    if ($view_team_position_side == 'RIGHT') {
                        // $team = Mlm::getAllActivePaidUserOfUserDirectIndirectRight(User::where('id', auth()->user()->id)->first());
                        $team = $rteam;
                    } else {
                        // $team = Mlm::getAllActivePaidUserOfUserDirectIndirect(User::where('id', auth()->user()->id)->first());
                        $team = array_reverse(Mlm::getUserDownline(User::where('id', auth()->user()->id)->first(), 'ALL', 1, 0, 0));
                    }
                }
            }
        }






        // dd($team);
        foreach ($team as $t) {
            if ($view_team_position_side == 'LEFT' || $view_team_position_side == 'LEFT_DEFAULTERS') {
                $t->left_right_position = 'LEFT';
            } else {
                if ($view_team_position_side == 'RIGHT' || $view_team_position_side == 'RIGHT_DEFAULTERS') {
                    $t->left_right_position = 'RIGHT';
                } else {
                    foreach ($lteam as $lt) {
                        if ($t->id == $lt->id) {
                            $t->left_right_position = 'LEFT';
                        }
                    }
                    foreach ($rteam as $rt) {
                        if ($t->id == $rt->id) {
                            $t->left_right_position = 'RIGHT';
                        }
                    }
                }
            }
            if (isset($t->pos_id)) {
                $t->under_who_place_user = User::where('id', $t->pos_id)->first();
            }
            if (isset($t->ref_by)) {
                $t->under_who_ref_user = User::where('id', $t->ref_by)->first();
            }

            $user_current_plane_amount = 0;
            $user_current_plane_amount_remaining = 0;

            if (isset($t->plan_id) && $t->plan_id > 0) {
                $t->user_account_status = 'Paid';
                $user_current_plane_amount = Plan::where('plan_created_by_user_id', $t->id)->where('status', 1)->sum('price');
                $user_current_total_trading_amount = TradingIncome::where('user_table_id', $t->id)->sum('income');
                $user_current_plane_amount_remaining = (($user_current_plane_amount * 5)  - $user_current_total_trading_amount - $t->total_ref_com - $t->total_binary_com);
            } else {
                $t->user_account_status = 'Active';
            }

            // $is_user_has_a_team_direct_left = User::where('pos_id', $t->id)->where('position', 1)->get();
            // $user_has_a_team_direct_left_count = User::where('pos_id', $t->id)->where('position', 1)->where('status', 1)->count();
            $t->user_left_team_count = User::where('ref_by', $t->id)->where('position', 1)->where('status', 1)->count();
            $t->left_direct_team_member_count = User::where('pos_id', $t->id)->where('position', 1)->where('status', 1)->count();
            $t->right_direct_team_member_count = User::where('pos_id', $t->id)->where('position', 2)->where('status', 1)->count();
            $t->total_package_purchased_sum_amount = $user_current_plane_amount;
            $t->user_current_plane_amount_remaining = $user_current_plane_amount_remaining;
            $t->user_right_team_count = User::where('ref_by', $t->id)->where('position', 2)->where('status', 1)->count();

            // if ($is_user_has_a_team_direct_left->count() > 0) {
            //     $t->user_has_a_member_at_left = true;
            // } else {
            //     $t->user_has_a_member_at_left = false;
            // }

            // $is_user_has_a_team_direct_right = User::where('pos_id', $t->id)->where('position', 2)->get();

            // if ($is_user_has_a_team_direct_right->count() > 0) {
            //     $t->user_has_a_member_at_right = true;
            // } else {
            //     $t->user_has_a_member_at_right = false;
            // }
        }

        // dd($team);

        $team = paginateArrayOfObjects($team, 10);
        $team->withPath('team');
        // $logs = User::where('ref_by', auth()->id())->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.my_team', compact('pageTitle', 'team', 'view_team_position_side'));
    }
    public function myTeamByLevel()
    {
        $pageTitle = "My Level Team";

        // dd(Mlm::getAllActivePaidUserOfUser(User::where('id', 37)->first()));


        $team = User::where('ref_by', auth()->id())->orWhere('pos_id', auth()->id())

            ->orderBy('id', 'desc')->paginate(getPaginate());

        $level_selected = request()->get('level', 1);






        // $eligibleUsers = User::where('plan_id', '>', 0)
        //     ->where('status', '=', 1)
        //     ->where('id', '=', auth()->id())
        //     //   ->where('username', '=', '2RS439923')
        //     ->get();



        $all_income_levels = LevelIncomes::where('is_disabled', '=', 0)
            ->orderBy('level', 'asc')
            ->get();
        // $all_income_levels_sum = LevelIncomes::where('is_disabled', '=', 0)
        //     ->sum('income');
        // echo "<pre>";
        //  print_r($trading_incomes_tmp_saved);
        // echo "</pre>";

        // $level_selected = 1;

        // foreach ($eligibleUsers as $key => $user) {
        $user = auth()->user();
        if (1) {

            // if ($user->username != '2RS439923') {
            //     continue;
            // }





            //has at least two direct
            //direct users have total sum business minimum 100
            //is refer is se 100 dollor ka business ho gya hai
            // trading inconme%sum of level income column/100 ex 5* 30/100 =1.5
            //
            //1st level - // 2 direct  
            //2st level - // 2 direct 
            // echo '<br/><br/>Level income for user:' . $user->username . "<br/>";

            // $user_total_business_self = Plan::where('plan_created_by_user_id', $user->id)
            //     ->sum('price');
            // echo 'user_total_business_self:' . $user_total_business_self . "<br/>";

            if (1) {

                // $all_reffered_users = User::where('ref_by', $user->id)
                //     ->where('plan_id', '>', 0)
                //     ->where('status', '=', 1)
                //     ->get();


                // $total_direct_paid_members_100_business = 0;
                // foreach ($all_reffered_users as $all_reffered_users_k => $all_reffered_users_v) {
                //     $user_total_business = Plan::where('plan_created_by_user_id', $all_reffered_users_v->id)
                //         ->sum('price');
                //     if ($user_total_business >= 100) {
                //         $total_direct_paid_members_100_business++;
                //     }
                // }
                // echo 'total_direct_paid_members_with_any_business:' .  $all_reffered_users->count() . "<br/>";
                // echo 'total_direct_paid_members_100 or more _business:' .  $total_direct_paid_members_100_business . "<br/>";

                if (1) {

                    $all_users_in_levels = [];

                    foreach ($all_income_levels as  $all_income_level_item) {

                        if (1) {

                            // echo '=================== calculation for level:' . $all_income_level_item->level . "==========<br/>";

                            if ($all_income_level_item->level == 1) {

                                $all_reffered_users_members_tmp = $all_reffered_users_members_tmp_level_next_tmp = User::where('ref_by', $user->id)
                                    ->where('plan_id', '>', 0)
                                    ->where('status', '=', 1)
                                    ->get();

                                $all_users_in_levels[$all_income_level_item->level + 1] = [];
                                foreach ($all_reffered_users_members_tmp as $all_reffered_users_members_tmpk => $all_reffered_users_members_tmpv) {
                                    $all_users_in_levels[$all_income_level_item->level + 1][] = $all_reffered_users_members_tmpv->id;
                                }
                                // break;
                            } else if ($all_income_level_item->level == 2) {
                                // print_r($all_users_in_levels[$all_income_level_item->level]);
                                // die;
                                $all_users_in_levels[$all_income_level_item->level + 1] = [];

                                if (isset($all_users_in_levels[$all_income_level_item->level])) {



                                    // $all_reffered_users_members_tmp_level_next = $all_reffered_users_members_tmp_level_next_tmp;

                                    foreach ($all_users_in_levels[$all_income_level_item->level] as $all_reffered_users_members_tmpk => $all_reffered_users_members_tmpv) {

                                        $all_reffered_users_members_tmpv = User::where('id', $all_reffered_users_members_tmpv)
                                            ->first();

                                        $all_reffered_users_members_tmp  = User::where('ref_by', $all_reffered_users_members_tmpv->id)
                                            ->where('plan_id', '>', 0)
                                            ->where('status', '=', 1)
                                            ->get();
                                        foreach ($all_reffered_users_members_tmp as $all_reffered_users_members_tmpk => $all_reffered_users_members_tmpv) {
                                            $all_users_in_levels[$all_income_level_item->level + 1][] = $all_reffered_users_members_tmpv->id;
                                        }
                                    }
                                }
                                // break;

                                //  $all_reffered_users_members_tmp = User::where('ref_by', $user->id)
                                //                         ->where('plan_id', '>', 0)
                                //                         ->where('status', '=', 1)
                                //                         ->get();

                            } else if ($all_income_level_item->level >= 3) {
                                // print_r($all_users_in_levels[$all_income_level_item->level]);
                                // die;
                                $all_users_in_levels[$all_income_level_item->level + 1] = [];

                                if (isset($all_users_in_levels[$all_income_level_item->level])) {



                                    // $all_reffered_users_members_tmp_level_next = $all_reffered_users_members_tmp_level_next_tmp;

                                    foreach ($all_users_in_levels[$all_income_level_item->level] as $all_reffered_users_members_tmpk => $all_reffered_users_members_tmpv) {

                                        $all_reffered_users_members_tmpv = User::where('id', $all_reffered_users_members_tmpv)
                                            ->first();

                                        $all_reffered_users_members_tmp  = User::where('ref_by', $all_reffered_users_members_tmpv->id)
                                            ->where('plan_id', '>', 0)
                                            ->where('status', '=', 1)
                                            ->get();
                                        foreach ($all_reffered_users_members_tmp as $all_reffered_users_members_tmpk => $all_reffered_users_members_tmpv) {
                                            $all_users_in_levels[$all_income_level_item->level + 1][] = $all_reffered_users_members_tmpv->id;
                                        }
                                    }
                                }

                                // if ($level_selected == $all_income_level_item->level) {
                                //     // break;
                                // }
                                //  $all_reffered_users_members_tmp = User::where('ref_by', $user->id)
                                //                         ->where('plan_id', '>', 0)
                                //                         ->where('status', '=', 1)
                                //                         ->get();

                            }
                        }
                    }

                    // dd($all_users_in_levels);
                }
            }



            //level income

        }


        if (isset($all_users_in_levels[$level_selected + 1])) {
            $team = User::whereIn('id', $all_users_in_levels[$level_selected + 1])

                ->orderBy('id', 'asc')->paginate(getPaginate());
        }
        // dd($team);


        // $logs = User::where('ref_by', auth()->id())->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.my_team_by_levels', compact('pageTitle', 'level_selected', 'team'));
    }

    public function binaryTree($user = null)
    {
        // dd(request()->has('view_team_member'));


        if (request()->has('view_team_member')) {
            $user = User::where('username', request()->get('view_team_member'))->first();
            if (!$user) {
                $user = auth()->user();
            }
            $mlm = new Mlm();
            $tree = $mlm->showTreePage($user);
        } else {
            $user = User::where('username', $user)->first();
            if (!$user) {
                $user = auth()->user();
            }
            $mlm = new Mlm();
            $tree = $mlm->showTreePage($user);
        }


        $pageTitle = 'Binary Tree';

        return view($this->activeTemplate . 'user.tree', compact('pageTitle', 'mlm', 'tree'));
    }

    public function balanceTransfer()
    {
        $general = gs();
        if ($general->balance_transfer != Status::ENABLE) {
            abort(404);
        }
        $pageTitle = 'Transfer Balance';
        return view($this->activeTemplate . 'user.balance_transfer', compact('pageTitle'));
    }

    public function transferConfirm(Request $request)
    {
        $general = gs();
        if ($general->balance_transfer != Status::ENABLE) {
            abort(404);
        }

        $request->validate([
            'username' => 'required|exists:users,username',
            'amount' => 'required|numeric|gt:0'
        ]);

        $user = auth()->user();


        $user_members_defaulter_check = checkUserHasEmiDefaulterInHisTeam($user);
        if (isset($user_members_defaulter_check['members_user_not_paid_emi_in_team']) && !empty($user_members_defaulter_check['members_user_not_paid_emi_in_team'])) {
            if (!in_array($request->username, $user_members_defaulter_check['members_user_not_paid_emi_in_team'])) {
                $notify[] = ['error', 'You can only send balance to the emi defaulters members only right now'];
                return back()->withNotify($notify);
            }
        }
        // dd($user_members_defaulter_check);



        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        $toUser = User::where('username', $request->username)->first();

        if ($user->id == $toUser->id) {
            $notify[] = ['error', 'You can\'t send money to your own account'];
            return back()->withNotify($notify);
        }

        $general    = gs();
        $amount     = $request->amount;
        $fixed      = $general->balance_transfer_fixed_charge;
        $percent    = $general->balance_transfer_per_charge;
        $charge     = ($amount * $percent / 100) + $fixed;
        $withCharge = $amount + $charge;

        if ($user->balance < $withCharge) {
            $notify[] = ['error', 'You have no sufficient balance'];
            return back()->withNotify($notify);
        }

        if ($general->balance_transfer_min > $amount) {
            $notify[] = ['error', 'Please follow minimum balance transfer limit'];
            return back()->withNotify($notify);
        }

        if ($general->balance_transfer_max < $amount) {
            $notify[] = ['error', 'Please follow maximum balance transfer limit'];
            return back()->withNotify($notify);
        }

        $user->balance -= $withCharge;
        $user->save();

        $trx                       = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $withCharge;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance transfer to ' . $toUser->username;
        $transaction->trx          = $trx;
        $transaction->save();

        notify($toUser, 'BALANCE_SEND', [
            'amount' => showAmount($amount),
            'charge' => showAmount($charge),
            'username' => $toUser->username,
            'post_balance' => showAmount($user->balance)
        ]);

        $toUser->balance += $amount;
        $toUser->save();

        $transaction = new Transaction();
        $transaction->user_id = $toUser->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $toUser->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->remark = 'balance_transfer';
        $transaction->details = 'Balance receive from ' . $user->username;
        $transaction->trx = $trx;
        $transaction->save();

        notify($toUser, 'BALANCE_RECEIVE', [
            'amount' => showAmount($amount),
            'username' => $user->username,
            'post_balance' => showAmount($toUser->balance)
        ]);

        $notify[] = ['success', 'Balance transferred successfully'];
        return back()->withNotify($notify);
    }
}
