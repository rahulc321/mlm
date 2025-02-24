<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\DB;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = auth()->user();


            // dd($request->route()->getName());

            if ($user->status  && $user->ev  && $user->sv  && $user->tv) {

                // $loan_repayments_failed = DB::table('loan_repayments')
                //     ->where('user_id', $user->id)
                //     ->where('is_emi_paid', 2)
                //     ->where('is_active', 1)
                //     ->count();

                $loan_repayments_failed = $user->loanRepaymentsPending;

                if ($loan_repayments_failed > 0) {


                    if ($request->method() == 'GET') {
                        // echo ($user->loanRepaymentsPending);
                        // echo ($user->anyLoanGoingOn);
                        // dd($user);
                        // file_put_contents(XXX . "/x.txt", "\n" . $request->route()->getName(), FILE_APPEND);
                        // dd($request->route()->getName());
                        if (!in_array($request->route()->getName(), [
                            'user.plan.bstl_payment',
                            'user.my.rstl_closing',
                            'user.my.rstl_emi_repayment',
                            'user.deposit.insert',
                            'user.deposit.update',
                            'user.deposit.manual.confirm',
                            'user.deposit.confirm',
                            'user.deposit.history',
                        ])) {
                            $notify[] = ['error', 'Please pay your EMI'];
                            // return back()->withNotify($notify);
                            return to_route('user.plan.bstl_payment')->withNotify($notify);
                        }
                    }
                } else {


                    $user_members_defaulter_check = checkUserHasEmiDefaulterInHisTeam($user);
                    // dd(checkUserHasEmiDefaulterInHisTeam($user));

                    // $all_user_reffered = DB::table('users')
                    //     ->select('id')
                    //     ->where('ref_by', $user->id)
                    //     ->where('status', 1)
                    //     ->get();



                    // $is_user_has_emi_defaulter_member = false;
                    // $number_of_loan_users_has_helped_in_team = 0;

                    // foreach ($all_user_reffered as  $all_user_reffered_item) {

                    //     $loan_applications = DB::table('loan_applications')
                    //         ->where('is_application_approved', 1)
                    //         ->where('user_id', $all_user_reffered_item->id)
                    //         ->get();

                    //     foreach ($loan_applications as  $loan_applications_item) {

                    //         $loan_approved = DB::table('loan_approved')
                    //             // ->where('is_loan_closed', 0)
                    //             ->where('loan_application_id', $loan_applications_item->id)
                    //             ->get();

                    //         foreach ($loan_approved as  $loan_approved_item) {
                    //             $number_of_loan_users_has_helped_in_team++;
                    //             $loan_repayments_count = DB::table('loan_repayments')
                    //                 ->where('is_emi_paid', 2)
                    //                 ->where('is_active', 1)
                    //                 ->where('loan_id', $loan_approved_item->id)
                    //                 ->count();
                    //             if ($loan_repayments_count > 0) {
                    //                 $is_user_has_emi_defaulter_member = true;
                    //             }
                    //         }
                    //     }
                    // }
                    if ($user_members_defaulter_check['is_user_has_emi_defaulter_member']) {
                        if ($user_members_defaulter_check['number_of_loan_users_has_helped_in_team'] >= 6) {
                        } else {
                            if ($request->method() == 'GET') {

                                if (!in_array($request->route()->getName(), [
                                    // 'user.plan.bstl_payment',
                                    // 'user.my.rstl_closing',
                                    // 'user.my.rstl_emi_repayment',
                                    // 'user.deposit.insert',
                                    // 'user.deposit.update',
                                    // 'user.deposit.manual.confirm',
                                    // 'user.deposit.confirm',
                                    // 'user.deposit.history',
                                    'user.balance.transfer',
                                    'user.home',
                                ])) {
                                    $notify[] = ['error', 'Please ask your team member to pay his EMI'];
                                    // return back()->withNotify($notify);
                                    // return to_route('user.plan.bstl_payment')->withNotify($notify);
                                    return to_route('user.home')->withNotify($notify);
                                }
                            }
                        }
                    }
                    // dd([
                    //     '$number_of_loan_users_has_helped_in_team' => $number_of_loan_users_has_helped_in_team,
                    //     '$is_user_has_emi_defaulter_member' => $is_user_has_emi_defaulter_member,
                    // ]);
                }

                return $next($request);
            } else {
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first.';
                    return response()->json([
                        'remark' => 'unverified',
                        'status' => 'error',
                        'message' => ['error' => $notify],
                        'data' => [
                            'is_ban' => $user->status,
                            'email_verified' => $user->ev,
                            'mobile_verified' => $user->sv,
                            'twofa_verified' => $user->tv,
                        ],
                    ]);
                } else {
                    return to_route('user.authorization');
                }
            }
        }
        abort(403);
    }
}
