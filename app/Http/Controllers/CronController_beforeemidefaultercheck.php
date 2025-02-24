<?php

namespace App\Http\Controllers;

use App\Lib\Mlm;
use App\Models\BvLog;
use App\Models\Transaction;
use App\Models\UserExtra;
use App\Models\User;
use App\Models\Plan;
use App\Models\IncomeSlabs;
use App\Models\IncomeLosts;
use App\Models\TradingIncome;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LevelIncomes;

class CronController extends Controller
{
    public function cron()
    {

        ob_start();
        echo '<br/> cron function <br/>';
        $mlm = new Mlm();
        $general = gs();
        $general->last_cron = now()->toDateTimeString();
        $general->save();

        $check = $mlm->checkTime();
        if (!$check) {
            // return 0;
        }

        $general->last_paid = now()->toDateString();
        $general->save();

        // $eligibleUsers = UserExtra::where('bv_left', '>=', $general->total_bv)->where('bv_right', '>=', $general->total_bv)->cursor();
        // $eligibleUsers = UserExtra::where('1','=','')->cursor();
        $eligibleUsers = UserExtra::all();


        foreach ($eligibleUsers as $uex) {
            echo '<br/><br/><br/>=============================================<br/>';

            $user = $uex->user;
            echo '<br/>user id beginning:' . $user->username . " ";;
            // if ($user->plan_id > 0) {
            //if ($user->plan_id > 0 && $user->username == '2RS947563') {
            // if ($user->status == 1 && $user->plan_id > 0 && $user->username == '2RS947563') {
            if ($user->status == 1 && $user->plan_id > 0) {




                echo '<br/>user id :' . $user->username . " plan bought , id is active";;
                echo '<br/>user id table id :' . $user->id;;



                $current_loop_user_left_active_member = Mlm::getLeftActivePaidUser($user);
                $current_loop_user_right_active_member = Mlm::getRightActivePaidUser($user);
                $total_active_paid_members = ($current_loop_user_left_active_member + $current_loop_user_right_active_member);
                echo '<br/>user id ' . $user->username . " has " . $total_active_paid_members . "  active paid members ";;

                echo '<br/>user id ' . $user->username . " has " . $current_loop_user_left_active_member . " left active paid member ";;
                echo '<br/>user id ' . $user->username . " has " . $current_loop_user_right_active_member . " left right paid member ";;

                if ($current_loop_user_left_active_member > $current_loop_user_right_active_member) {
                    echo "<br/> income ratio is 2:1";
                } else {
                    if ($current_loop_user_right_active_member > $current_loop_user_left_active_member) {
                        echo "<br/> income ratio is 1:2";
                    } else {
                        echo "<br/> income ratio is equal";
                    }
                }

                if (
                    ($total_active_paid_members  >= 3 &&
                        $current_loop_user_left_active_member >= 1 &&
                        $current_loop_user_right_active_member >= 1) && ($current_loop_user_left_active_member >= 2 || $current_loop_user_right_active_member >= 2)
                ) {

                    $is_star_id = Mlm::isStarID($user);

                    if ($is_star_id) {
                        echo '<br/>user id ' . $user->username . " is star id";;
                    } else {
                        echo '<br/>user id ' . $user->username . " is not star id";;
                    }



                    $user_current_plan = Plan::where('id', $user->plan_id)
                        ->where('plan_created_by_user_id', $user->id)

                        ->orderBy('created_at', 'desc')
                        ->first();

                    $user_plan_sum = Plan::where('plan_created_by_user_id', $user->id)
                        ->where('status', 1)
                        ->sum('price');

                    if (isset($user_current_plan->created_at)) {
                        echo '<br/>user id ' . $user->username . " has bought last plan on " . $user_current_plan->created_at . " of amount " . $user_current_plan->price . '<br/>';;
                    }

                    echo '<br/> total plans sum ' . $user_plan_sum . '<br/>';;
                    // echo '<br/>user id ' . $user->username . " left businesss " . MLM::getLeftActivePaidUserBusiness($user);;
                    // echo '<br/>user id ' . $user->username . " right businesss " . MLM::getRightActivePaidUserBusiness($user);;
                    // echo '<br/>user id ' . $user->username . " right businesss " . MLM::isStarID($user);;

                    // if (isset($user_current_plan->price)) {
                    if (1) {
                        if ($user_plan_sum > 0) {

                            //get BV and bonus
                            $weak = $uex->bv_left < $uex->bv_right ? $uex->bv_left : $uex->bv_right; //500    
                            echo 'weak check :' . $weak . "<br/>";
                            echo 'weak check $uex->bv_left:' . $uex->bv_left . "<br/>";
                            echo 'weak check $uex->bv_right:' . $uex->bv_right . "<br/>";
                            // $percent_of_plan_amount = ($user_current_plan->price * $general->max_bv) / 100;
                            // $percent_of_plan_amount = ($user_plan_sum * $general->max_bv) / 100;
                            $percent_of_plan_amount = return2sureRoundAmount(($user_plan_sum * $general->max_bv) / 100, 4);
                            echo 'weak check $percent_of_plan_amount:' . $percent_of_plan_amount . "<br/>";
                            echo 'weak check $general->max_bv:' . $general->max_bv . "<br/>";
                            $weaker = $weak;
                            echo 'weak check $weaker:' . $weaker . "<br/>";
                            $last_weaker_amount = 0;
                            if ($weaker <= $percent_of_plan_amount) {
                                echo 'weak check : in $weaker <= $percent_of_plan_amount' . "<br/>";
                                // $pair = intval($weaker);
                                $pair = ($weaker);
                                echo 'weak check < case1 $pair:' . $pair . "<br/>";

                                if ($is_star_id) {
                                    $bonus = return2sureRoundAmount(($pair * 10) / 100, 4);
                                    echo 'weak check < case1 $bonus:' . $bonus . "<br/>";
                                } else {
                                    $bonus = return2sureRoundAmount(($pair * $general->bv_price) / 100, 4);
                                    echo 'weak check < case2 $bonus:' . $bonus . "<br/>";
                                    echo 'weak check < case2 $general->bv_price:' . $general->bv_price . "<br/>";
                                }


                                if ($bonus <= 0) {
                                    echo 'weak check ______ 11111111<<<===000 continue:' . $bonus . "<br/>";
                                    continue;
                                }
                                if ($is_star_id) {

                                    $paidBv = return2sureRoundAmount(($pair * 10) / 100, 4);;
                                    echo 'weak check < start11111 paidBv:' . $paidBv . "<br/>";
                                } else {
                                    $paidBv = return2sureRoundAmount(($pair * $general->bv_price) / 100, 4);;
                                    echo 'weak check < start11111 paidBv2222222:' . $paidBv . "<br/>";
                                }
                            } else {

                                echo 'weak check : in $weaker > $percent_of_plan_amount' . "<br/>";
                                // $pair = intval($percent_of_plan_amount);
                                $pair = ($percent_of_plan_amount);
                                echo 'weak check < case1 $pairooooooooo:' . $pair . "<br/>";
                                if ($is_star_id) {
                                    $last_weaker_amount = (($weaker * 10) / 100);
                                    echo 'weak check > case1 $last_weaker_amount22222222:' . $last_weaker_amount . "<br/>";
                                } else {
                                    $last_weaker_amount = (($weaker * $general->bv_price) / 100);
                                    echo 'weak check > case2 $last_weaker_amount3333333:' . $last_weaker_amount . "<br/>";
                                }


                                if ($last_weaker_amount > $percent_of_plan_amount) {
                                    // $pair = intval($percent_of_plan_amount);
                                    $pair = ($percent_of_plan_amount);
                                    echo 'weak check ______ 1111:' . $pair . "<br/>";
                                } else {
                                    // $pair = intval($last_weaker_amount);
                                    $pair = ($last_weaker_amount);
                                    echo 'weak check ______ 22222:' . $pair . "<br/>";
                                }


                                $bonus = $pair;
                                echo 'weak check ______ 3333:' . $bonus . "<br/>";

                                if ($bonus <= 0) {
                                    echo 'weak check ______ 3333 continue:' . $bonus . "<br/>";
                                    continue;
                                }
                                $paidBv = $pair;
                            }

                            // echo '<br/><br/>user id ' . $user->username . "<br/>";;
                            // echo 'user current plan price:' . $user_current_plan->price . "<br/>";
                            echo 'weaker leg from left right:' . $weaker . "<br/>";
                            echo 'ten percent of plan_amount:' . $percent_of_plan_amount . "<br/>";
                            echo 'binary:' . $bonus . "<br/>";
                            // echo '$paidBv:' . $paidBv . "<br/>";
                            echo 'last weaker amount:' . $last_weaker_amount . "<br/>";
                            echo 'paid amount:' . $bonus . "<br/>";
                            echo 'lost amount :' . ($last_weaker_amount - $bonus) . "<br/>";








                            //                           echo "<br/> changes in only echo mode not being saved";
                            //continue;
                            // $output_of_cron = ob_get_clean();
                            // $output_of_cron = str_ireplace('<br/>', "\n", $output_of_cron);
                            // $output_of_cron = str_ireplace('<br>', "\n", $output_of_cron);
                            // Log::channel('cron_bv_income')->info($output_of_cron);

                            // die('x');;
                            $income_lost = $last_weaker_amount - $bonus;
                            if ($income_lost > 0) {

                                IncomeLosts::create([
                                    'user_table_id' => $user->id,
                                    'income_generated' => $last_weaker_amount,
                                    'income_sent' => $bonus,
                                    'income_lost' => $income_lost,
                                ]);
                                // $income_lost = new IncomeLosts();
                                // $income_lost->user_table_id = $user->id;
                                // $income_lost->income_generated = $last_weaker_amount;
                                // $income_lost->income_sent = $user->balance;
                                // $income_lost->income_lost =  $income_lost;

                                // $income_lost->save();
                            }
                            $user->balance += $bonus;
                            $user->save();

                            //create transaction
                            $transaction = new Transaction();
                            $transaction->user_id = $user->id;
                            $transaction->amount = $bonus;
                            $transaction->post_balance = $user->balance;
                            $transaction->charge = 0;
                            $transaction->trx_type = '+';
                            $transaction->remark = 'paid_bv';
                            $transaction->details = 'Paid ' . $bonus . ' ' . $general->cur_text . ' For ' . $paidBv . ' BV.';
                            $transaction->trx =  getTrx();
                            $transaction->save();







                            $mlm->updateUserBv($uex, $paidBv, $weak, $bonus);

                            notify($user, 'MATCHING_BONUS', [
                                'amount' => showAmount($bonus),
                                'paid_bv' => $paidBv,
                                'post_balance' => showAmount($user->balance)
                            ]);
                        }
                    }
                }
            }
        }
        $output_of_cron = ob_get_clean();
        $output_of_cron = str_ireplace('<br/>', "\n", $output_of_cron);
        $output_of_cron = str_ireplace('<br>', "\n", $output_of_cron);
        Log::channel('cron_bv_income')->info($output_of_cron);
    }
    public function dailyTradingIncome()
    {
        ob_start();
        $datetime = now();
        // echo "<br/><br/>";
        $number_of_days_in_current_month = date('t');

        // $eligibleUsers = UserExtra::all();
        $eligibleUsers = User::where('plan_id', '>', 0)
            ->where('status', '=', 1)
            //   ->where('username', '=', '2RS439923')
            ->get();

        $all_slabs = IncomeSlabs::all();


        $trading_incomes_tmp_saved = [];

        foreach ($eligibleUsers as $key => $user) {

            // $user_all_plans_amount_sum = TradingIncome::where('created_at', '=',)->sum('price');
            $user_all_plans_amount_sum = Plan::where('plan_created_by_user_id', $user->id)->sum('price');

            foreach ($all_slabs as  $slab) {

                if ($user_all_plans_amount_sum >= $slab->min_range && $user_all_plans_amount_sum <= $slab->max_range) {

                    $income_percentage = ($user_all_plans_amount_sum * $slab->return_income_percentage) / 100;
                    $daily_trading_package_income = return2sureRoundAmount($income_percentage / $number_of_days_in_current_month, 4);
                    $trading_income_info =  TradingIncome::create([
                        'user_table_id' => $user->id,
                        'user_id' => $user->username,
                        'plan_package' => return2sureRoundAmount($user_all_plans_amount_sum, 0),
                        'slab' => $slab->return_income_percentage,
                        'income' => ($daily_trading_package_income),
                        'remaining_income' => 0,
                    ]);
                    echo 'user:' . $user->username . ", trading income : " . $daily_trading_package_income . "<br/><br/>";





                    $user->balance +=  $daily_trading_package_income;
                    $user->save();
                    $trading_incomes_tmp_saved[$user->id] = [
                        'income' => ($daily_trading_package_income),
                        'user_table_id' => $user->id,
                        'user_id' => $user->username,
                        'trading_income_id' => $trading_income_info->id,
                    ];

                    //create transaction
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->amount =  $daily_trading_package_income;
                    $transaction->post_balance = $user->balance;
                    $transaction->charge = 0;
                    $transaction->trx_type = '+';
                    $transaction->remark = 'paid_bv';
                    $transaction->details = 'Paid ' .  $daily_trading_package_income . ' as trading income';
                    $transaction->trx =  getTrx();
                    $transaction->save();
                    break;
                }
            }
        }



        $all_income_levels = LevelIncomes::where('is_disabled', '=', 0)
            ->orderBy('level', 'asc')
            ->get();
        // $all_income_levels_sum = LevelIncomes::where('is_disabled', '=', 0)
        //     ->sum('income');
        // echo "<pre>";
        //  print_r($trading_incomes_tmp_saved);
        // echo "</pre>";

        foreach ($eligibleUsers as $key => $user) {

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
            echo '<br/><br/>Level income for user:' . $user->username . "<br/>";

            $user_total_business_self = Plan::where('plan_created_by_user_id', $user->id)
                ->sum('price');
            echo 'user_total_business_self:' . $user_total_business_self . "<br/>";

            if ($user_total_business_self >= 100) {

                $all_reffered_users = User::where('ref_by', $user->id)
                    ->where('plan_id', '>', 0)
                    ->where('status', '=', 1)
                    ->get();


                $total_direct_paid_members_100_business = 0;
                foreach ($all_reffered_users as $all_reffered_users_k => $all_reffered_users_v) {
                    $user_total_business = Plan::where('plan_created_by_user_id', $all_reffered_users_v->id)
                        ->sum('price');
                    if ($user_total_business >= 100) {
                        $total_direct_paid_members_100_business++;
                    }
                }
                echo 'total_direct_paid_members_with_any_business:' .  $all_reffered_users->count() . "<br/>";
                echo 'total_direct_paid_members_100 or more _business:' .  $total_direct_paid_members_100_business . "<br/>";

                if ($total_direct_paid_members_100_business >= 2) {

                    $all_users_in_levels = [];

                    foreach ($all_income_levels as  $all_income_level_item) {

                        if ($total_direct_paid_members_100_business >= $all_income_level_item->members) {

                            echo '=================== calculation for level:' . $all_income_level_item->level . "==========<br/>";

                            if ($all_income_level_item->level == 1) {

                                $all_reffered_users_members_tmp = $all_reffered_users_members_tmp_level_next_tmp = User::where('ref_by', $user->id)
                                    ->where('plan_id', '>', 0)
                                    ->where('status', '=', 1)
                                    ->get();

                                $all_users_in_levels[$all_income_level_item->level + 1] = [];
                                foreach ($all_reffered_users_members_tmp as $all_reffered_users_members_tmpk => $all_reffered_users_members_tmpv) {
                                    $all_users_in_levels[$all_income_level_item->level + 1][] = $all_reffered_users_members_tmpv->id;
                                    echo 'member level user id table:' .  $all_reffered_users_members_tmpv->id . "<br/>";
                                    echo 'member level user username table:' .  $all_reffered_users_members_tmpv->username . "<br/>";
                                    if (isset($trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]) && $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['income']) {


                                        $level_income_to_give_final = return2sureRoundAmount((($trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['income'] *  $all_income_level_item->income) / 100), 4);

                                        DB::table('level_income_users')->insertGetId(
                                            [
                                                'from_user_table_id' => $all_reffered_users_members_tmpv->id,
                                                'from_user_id' => $all_reffered_users_members_tmpv->username,
                                                'to_user_table_id' => $user->id,
                                                'to_user_id' => $user->username,
                                                'level' => $all_income_level_item->level,
                                                'level_id' => $all_income_level_item->id,
                                                'trading_income_id' => $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['trading_income_id'],
                                                'income' => ($level_income_to_give_final),
                                                'created_at' => $datetime->format("Y-m-d H:i:s"),
                                                'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                            ]
                                        );

                                        DB::table('users')
                                            ->where('id', $user->id)  // find your user by their email
                                            ->update(array('balance' => return2sureRoundAmount($user->balance + $level_income_to_give_final, 4)));

                                        // $user->balance +=  $daily_trading_package_income;


                                        $transaction = new Transaction();
                                        $transaction->user_id = $user->id;
                                        $transaction->amount =  $level_income_to_give_final;
                                        $transaction->post_balance = return2sureRoundAmount($user->balance + $level_income_to_give_final, 4);
                                        $transaction->charge = 0;
                                        $transaction->trx_type = '+';
                                        // $transaction->remark = 'paid_bv';
                                        $transaction->remark = 'paid_level_income';
                                        $transaction->details = 'Paid ' .  $level_income_to_give_final . ' as level income';
                                        $transaction->trx =  getTrx();
                                        $transaction->save();
                                        // echo 'trading income:' . $daily_trading_package_income . "<br/>";
                                        echo 'level income to give:' . $level_income_to_give_final . "<br/>";
                                        // echo 'of user :' . $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['user_id'] . "<br/>";
                                        // echo 'level :' . $all_income_level_item->level . "<br/>";
                                        // echo 'total_direct_paid_members_100_business :' . $total_direct_paid_members_100_business . "<br/><br/><br/>";
                                    }
                                }
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

                                            //  $all_reffered_users_members_tmp = User::where('ref_by', $all_reffered_users_members_tmpv->id)
                                            //                         ->where('plan_id', '>', 0)
                                            //                         ->where('status', '=', 1)
                                            //                         ->get();

                                            echo 'level user id table:' .  $all_reffered_users_members_tmpv->id . "<br/>";
                                            echo 'level user username table:' .  $all_reffered_users_members_tmpv->username . "<br/>";
                                            if (isset($trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]) && $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['income']) {


                                                $level_income_to_give_final = return2sureRoundAmount((($trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['income'] *  $all_income_level_item->income) / 100), 4);

                                                DB::table('level_income_users')->insertGetId(
                                                    [
                                                        'from_user_table_id' => $all_reffered_users_members_tmpv->id,
                                                        'from_user_id' => $all_reffered_users_members_tmpv->username,
                                                        'to_user_table_id' => $user->id,
                                                        'to_user_id' => $user->username,
                                                        'level' => $all_income_level_item->level,
                                                        'level_id' => $all_income_level_item->id,
                                                        'trading_income_id' => $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['trading_income_id'],
                                                        'income' => ($level_income_to_give_final),
                                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                                    ]
                                                );




                                                DB::table('users')
                                                    ->where('id', $user->id)  // find your user by their email
                                                    ->update(array('balance' => return2sureRoundAmount($user->balance + $level_income_to_give_final, 4)));

                                                // $user->balance +=  $daily_trading_package_income;


                                                $transaction = new Transaction();
                                                $transaction->user_id = $user->id;
                                                $transaction->amount =  $level_income_to_give_final;
                                                $transaction->post_balance = return2sureRoundAmount($user->balance + $level_income_to_give_final, 4);
                                                $transaction->charge = 0;
                                                $transaction->trx_type = '+';
                                                // $transaction->remark = 'paid_bv';
                                                $transaction->remark = 'paid_level_income';
                                                $transaction->details = 'Paid ' .  $level_income_to_give_final . ' as level income';
                                                $transaction->trx =  getTrx();
                                                $transaction->save();

                                                // echo 'trading income:' . $daily_trading_package_income . "<br/>";
                                                echo 'level income to give:' . $level_income_to_give_final . "<br/>";
                                                // echo 'of user :' . $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['user_id'] . "<br/>";
                                                // echo 'level :' . $all_income_level_item->level . "<br/>";
                                                // echo 'total_direct_paid_members_100_business :' . $total_direct_paid_members_100_business . "<br/><br/><br/>";
                                            }
                                        }
                                    }
                                }

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

                                            //  $all_reffered_users_members_tmp = User::where('ref_by', $all_reffered_users_members_tmpv->id)
                                            //                         ->where('plan_id', '>', 0)
                                            //                         ->where('status', '=', 1)
                                            //                         ->get();

                                            echo 'level user id table:' .  $all_reffered_users_members_tmpv->id . "<br/>";
                                            echo 'level user username table:' .  $all_reffered_users_members_tmpv->username . "<br/>";
                                            if (isset($trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]) && $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['income']) {


                                                $level_income_to_give_final = return2sureRoundAmount((($trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['income'] *  $all_income_level_item->income) / 100), 4);

                                                DB::table('level_income_users')->insertGetId(
                                                    [
                                                        'from_user_table_id' => $all_reffered_users_members_tmpv->id,
                                                        'from_user_id' => $all_reffered_users_members_tmpv->username,
                                                        'to_user_table_id' => $user->id,
                                                        'to_user_id' => $user->username,
                                                        'level' => $all_income_level_item->level,
                                                        'level_id' => $all_income_level_item->id,
                                                        'trading_income_id' => $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['trading_income_id'],
                                                        'income' => ($level_income_to_give_final),
                                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                                    ]
                                                );


                                                DB::table('users')
                                                    ->where('id', $user->id)  // find your user by their email
                                                    ->update(array('balance' => return2sureRoundAmount($user->balance + $level_income_to_give_final, 4)));

                                                // $user->balance +=  $daily_trading_package_income;


                                                $transaction = new Transaction();
                                                $transaction->user_id = $user->id;
                                                $transaction->amount =  $level_income_to_give_final;
                                                $transaction->post_balance = return2sureRoundAmount($user->balance + $level_income_to_give_final, 4);
                                                $transaction->charge = 0;
                                                $transaction->trx_type = '+';
                                                // $transaction->remark = 'paid_bv';
                                                $transaction->remark = 'paid_level_income';
                                                $transaction->details = 'Paid ' .  $level_income_to_give_final . ' as level income';
                                                $transaction->trx =  getTrx();
                                                $transaction->save();

                                                // echo 'trading income:' . $daily_trading_package_income . "<br/>";
                                                echo 'level income to give:' . $level_income_to_give_final . "<br/>";
                                                // echo 'of user :' . $trading_incomes_tmp_saved[$all_reffered_users_members_tmpv->id]['user_id'] . "<br/>";
                                                // echo 'level :' . $all_income_level_item->level . "<br/>";
                                                // echo 'total_direct_paid_members_100_business :' . $total_direct_paid_members_100_business . "<br/><br/><br/>";
                                            }
                                        }
                                    }
                                }

                                //  $all_reffered_users_members_tmp = User::where('ref_by', $user->id)
                                //                         ->where('plan_id', '>', 0)
                                //                         ->where('status', '=', 1)
                                //                         ->get();

                            }
                        }
                    }
                }
            }



            //level income

        }

        // dd($eligibleUsers);


        $output_of_cron = ob_get_clean();
        $output_of_cron = str_ireplace('<br/>', "\n", $output_of_cron);
        $output_of_cron = str_ireplace('<br>', "\n", $output_of_cron);
        Log::channel('cron_trading_income')->info($output_of_cron);
    }


    public function club_achievers()
    {
        ob_start();
        $number_of_days_in_current_month = date('t');
        $datetime = now();

        $eligibleUsers = User::where('plan_id', '>', 0)
            ->where('status', '=', 1)
            // ->where('username', '=', 'rootuser')
            ->get();

        // $all_slabs = IncomeSlabs::all();
        $all_clubs =    DB::table('clubs')->get();



        //
        foreach ($eligibleUsers as $key => $user) {


            echo "<br/><br/><br/><br/> Current Uer is : {$user->username}";
            $user_total_business = Plan::where('plan_created_by_user_id', $user->id)

                ->sum('price');


            if ($user_total_business >= 100) {
                echo "<br/> 100+ business : {$user->username}";
            } else {
                echo "<br/>  less than 100$ business of : {$user->username}";
            }


            $users_left_members = Mlm::getAllActivePaidUserOfUserDirectIndirectLeft($user);
            $users_right_members = Mlm::getAllActivePaidUserOfUserDirectIndirectRight($user);
            echo "<br/> left active paid members " . count($users_left_members);
            echo "<br/> right active paid members " . count($users_right_members);
            echo "<br/><br/> All Left users ";
            $left_count = 0;
            foreach ($users_left_members as $key => $users_left_member_item) {


                // echo "<br/> username : {$users_left_member_item->username} plan : {$users_left_member_item->plan_id}";
                // if ($users_left_member_item->plan_id == 0) {


                //     $plan = new Plan();

                //     $plan->name             = '2ReturnSure Trading Package';
                //     $plan->price            = 400;


                //     $plan->plan_created_by_user_id = $users_left_member_item->id;



                //     $plan->bv               = 400;
                //     $plan->ref_com          = 5;
                //     $plan->tree_com         = 5;

                //     // echo '$plan_price_after_bonus:'.$plan_price_after_bonus;
                //     // dd($plan);




                //     // $user = auth()->user();


                //     // dd($user);
                //     $plan->save();
                // }

                $user_total_business = Plan::where('plan_created_by_user_id', $users_left_member_item->id)
                    ->sum('price');
                if ($user_total_business < 100) {
                    $left_count++;
                } else {
                }
            }
            echo "<br/> less than 100$ users count " . ($left_count);
            echo "<br/><br/> All Right users ";
            $right_count = 0;
            foreach ($users_right_members as $key => $users_right_member_item) {


                // if ($users_right_member_item->plan_id == 0) {


                //     $plan = new Plan();

                //     $plan->name             = '2ReturnSure Trading Package';
                //     $plan->price            = 400;


                //     $plan->plan_created_by_user_id = $users_right_member_item->id;



                //     $plan->bv               = 400;
                //     $plan->ref_com          = 5;
                //     $plan->tree_com         = 5;

                //     // echo '$plan_price_after_bonus:'.$plan_price_after_bonus;
                //     // dd($plan);




                //     // $user = auth()->user();


                //     // dd($user);
                //     $plan->save();
                // }

                $user_total_business = Plan::where('plan_created_by_user_id', $users_right_member_item->id)

                    ->sum('price');
                if ($user_total_business < 100) {
                    // dd($user_total_business, $users_right_member_item);
                    $right_count++;
                } else {
                }
            }
            echo "<br/>  less than 100$ users count " . ($right_count);
        }
        // die("<br/><br/><br/>finished");
        //




        foreach ($eligibleUsers as $key => $user) {


            $user_total_business = Plan::where('plan_created_by_user_id', $user->id)

                ->sum('price');


            if ($user_total_business >= 100) {

                foreach ($all_clubs as  $club) {
                    switch ($club->unique_name) {
                        case 'performance_bonus':
                            $user_pb_status = DB::table('club_user_achievement')
                                ->where('club_id', '=', $club->id)
                                ->where('user_id', '=', $user->id)
                                ->count();

                            if ($user_pb_status === 0) {

                                // $users_left_members = Mlm::getAllActivePaidUserOfUserDirectIndirect($user);
                                $users_left_members = Mlm::getAllActivePaidUserOfUserDirectIndirectLeft($user);
                                $users_right_members = Mlm::getAllActivePaidUserOfUserDirectIndirectRight($user);



                                $is_all_members_has_a_specific_amount_of_business = true;
                                foreach ($users_left_members as $key => $users_left_member_item) {
                                    if ($users_left_member_item->plan_id > 0) {
                                        $user_total_business = Plan::where('plan_created_by_user_id', $users_left_member_item->id)

                                            ->sum('price');
                                        if ($user_total_business <= 100) {
                                            $is_all_members_has_a_specific_amount_of_business = false;
                                            break;
                                        }
                                    }
                                }
                                foreach ($users_right_members as $key => $users_right_member_item) {
                                    if ($users_right_member_item->plan_id > 0) {
                                        $user_total_business = Plan::where('plan_created_by_user_id', $users_right_member_item->id)

                                            ->sum('price');
                                        if ($user_total_business <= 100) {
                                            $is_all_members_has_a_specific_amount_of_business = false;
                                            break;
                                        }
                                    }
                                }

                                if ($is_all_members_has_a_specific_amount_of_business) {
                                    if (count($users_left_members) >= 25 && count($users_right_members) >= 25) {
                                        $club_gen_id = DB::table('club_user_achievement')->insertGetId(
                                            [
                                                // 'action' => 'generation',
                                                'user_id' => $user->id,
                                                'club_id' => $club->id,
                                                'created_at' => $datetime->format("Y-m-d H:i:s"),
                                                'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                            ]
                                        );
                                    }
                                }
                            }

                            break;
                    }
                }
            }
        }




        foreach ($eligibleUsers as $key => $user) {

            foreach ($all_clubs as  $club) {
                switch ($club->unique_name) {

                    case 'allowance_fund':
                        $user_fund_status = DB::table('club_user_achievement')
                            ->where('club_id', '=', $club->id)
                            ->where('user_id', '=', $user->id)
                            ->count();

                        if ($user_fund_status === 0) {


                            $ref = Mlm::getAllActivePaidUsersRefferedByThisUser($user);

                            $total_pf_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_pf = DB::table('club_user_achievement')
                                    ->where('club_id', 1)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_pf === 1) {
                                    $total_pf_ac++;
                                }
                            }


                            if ($total_pf_ac >= 2) {
                                $club_gen_id = DB::table('club_user_achievement')->insertGetId(
                                    [
                                        'user_id' => $user->id,
                                        'club_id' => $club->id,
                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                    ]
                                );
                            }
                        }


                        break;
                }
            }
        }

        foreach ($eligibleUsers as $key => $user) {


            foreach ($all_clubs as  $club) {
                switch ($club->unique_name) {

                    case 'car_fund':


                        $user_fund_status = DB::table('club_user_achievement')
                            ->where('club_id', '=', $club->id)
                            ->where('user_id', '=', $user->id)
                            ->count();

                        if ($user_fund_status === 0) {

                            $ref = Mlm::getAllActivePaidUsersRefferedByThisUser($user);



                            $total_pf_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_pf = DB::table('club_user_achievement')
                                    ->where('club_id', 1)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_pf === 1) {
                                    $total_pf_ac++;
                                }
                            }


                            $total_af_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_af = DB::table('club_user_achievement')
                                    ->where('club_id', 2)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_af === 1) {
                                    $total_af_ac++;
                                }
                            }



                            if ($total_pf_ac >= 2 && $total_af_ac >= 1) {
                                $club_gen_id = DB::table('club_user_achievement')->insertGetId(
                                    [
                                        'user_id' => $user->id,
                                        'club_id' => $club->id,
                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                    ]
                                );
                            }
                        }


                        break;
                }
            }
        }


        foreach ($eligibleUsers as $key => $user) {


            foreach ($all_clubs as  $club) {
                switch ($club->unique_name) {

                    case 'house_fund':


                        $user_fund_status = DB::table('club_user_achievement')
                            ->where('club_id', '=', $club->id)
                            ->where('user_id', '=', $user->id)
                            ->count();

                        if ($user_fund_status == 0) {

                            // $ref = User::where('ref_by', $user->id)
                            //     ->where('plan_id', '>', 0)
                            //     ->where('status', '=', 1)
                            //     ->get();
                            $ref = Mlm::getAllActivePaidUsersRefferedByThisUser($user);



                            $total_pf_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_pf = DB::table('club_user_achievement')
                                    ->where('club_id', 1)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_pf === 1) {
                                    $total_pf_ac++;
                                }
                            }


                            $total_af_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_af = DB::table('club_user_achievement')
                                    ->where('club_id', 2)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_af === 1) {
                                    $total_af_ac++;
                                }
                            }


                            $total_cf_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_cf = DB::table('club_user_achievement')
                                    ->where('club_id', 3)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_cf === 1) {
                                    $total_cf_ac++;
                                }
                            }

                            // die("$total_pf_ac");

                            if ($total_pf_ac >= 2 && $total_af_ac >= 2 && $total_cf_ac >= 1) {
                                $club_gen_id = DB::table('club_user_achievement')->insertGetId(
                                    [
                                        'user_id' => $user->id,
                                        'club_id' => $club->id,
                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                    ]
                                );
                            }
                        }


                        break;
                }
            }
        }




        foreach ($eligibleUsers as $key => $user) {


            foreach ($all_clubs as  $club) {
                switch ($club->unique_name) {

                    case 'family_fund':


                        $user_fund_status = DB::table('club_user_achievement')
                            ->where('club_id', '=', $club->id)
                            ->where('user_id', '=', $user->id)
                            ->count();

                        if ($user_fund_status == 0) {

                            $ref = Mlm::getAllActivePaidUsersRefferedByThisUser($user);



                            $total_pf_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_pf = DB::table('club_user_achievement')
                                    ->where('club_id', 1)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_pf === 1) {
                                    $total_pf_ac++;
                                }
                            }


                            $total_af_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_af = DB::table('club_user_achievement')
                                    ->where('club_id', 2)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_af === 1) {
                                    $total_af_ac++;
                                }
                            }


                            $total_cf_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_cf = DB::table('club_user_achievement')
                                    ->where('club_id', 3)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_cf === 1) {
                                    $total_cf_ac++;
                                }
                            }


                            $total_hf_ac = 0;
                            foreach ($ref as $referred_user) {
                                $is_achevied_hf = DB::table('club_user_achievement')
                                    ->where('club_id', 4)
                                    ->where('user_id', $referred_user->id)
                                    ->count();
                                if ($is_achevied_hf === 1) {
                                    $total_hf_ac++;
                                }
                            }

                            // die("$total_pf_ac");

                            if ($total_pf_ac >= 2 && $total_af_ac >= 2 && $total_cf_ac >= 2 && $total_hf_ac >= 1) {
                                $club_gen_id = DB::table('club_user_achievement')->insertGetId(
                                    [
                                        'user_id' => $user->id,
                                        'club_id' => $club->id,
                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                    ]
                                );
                            }
                        }


                        break;
                }
            }
        }

        $output_of_cron = ob_get_clean();
        $output_of_cron = str_ireplace('<br/>', "\n", $output_of_cron);
        $output_of_cron = str_ireplace('<br>', "\n", $output_of_cron);
        Log::channel('cron_club_achievers_checker')->info($output_of_cron);
    }
    public function club_achievers_distribute_income()
    {
        ob_start();
        $number_of_days_in_current_month = date('t');
        $datetime = now();


        $club_user_achievements = DB::table('club_user_achievement')
            ->groupBy('user_id')
            ->get();

        if ($club_user_achievements->count() === 0) {
            echo "No Achievements yet \n";
            // exit;
        } else {

            $all_clubs =    DB::table('clubs')->get();

            $club_income_generations = DB::table('club_income_generations')
                ->where('is_disabled', 0)
                ->where('is_processed', 0)
                ->get();


            if ($club_income_generations->count() === 0) {
                echo "No Turnover set \n";
                // exit;
            } else {
                if ($club_income_generations->count() > 0) {


                    foreach ($club_income_generations as  $club_income_generated) {

                        DB::table('club_income_generations')
                            ->where('id', $club_income_generated->id)  // find your user by their email
                            ->update(array('is_processed' => 1, 'processed_date' => $datetime->format("Y-m-d H:i:s")));

                        if ($club_income_generated->percentage_amount_to_distribute > 0) {

                            $club_total_distribution = DB::table('club_total_distribution')
                                ->where('generation_id', $club_income_generated->id)
                                ->get();

                            foreach ($club_total_distribution as  $distribution_club) {


                                $club_user_achievers = DB::table('club_user_achievement')
                                    ->where('club_id', $distribution_club->club_id)
                                    ->get();


                                $club_info = DB::table('clubs')
                                    ->where('id', $distribution_club->club_id)
                                    ->first();

                                if ($club_user_achievers->count() > 0) {

                                    $user_to_get_paid = [];

                                    foreach ($club_user_achievers as  $club_user_achiever) {


                                        $club_achiever_user_detail = User::where('id', $club_user_achiever->user_id)->first();
                                        if (isset($club_achiever_user_detail->status)) {


                                            $business_LEFT_amount = 0;
                                            $business_RIGHT_amount = 0;

                                            $left_member = User::where('pos_id', $club_user_achiever->user_id)
                                                ->where('position', 1)
                                                ->where('status', 1)
                                                ->first();
                                            $right_member = User::where('pos_id', $club_user_achiever->user_id)
                                                ->where('position', 2)
                                                ->where('status', 1)
                                                ->first();

                                            // dd($club_user_achiever);

                                            if (isset($left_member->id) && isset($right_member->id)) {


                                                $business_to_search_in_users = [];
                                                $business_to_search_in_users[] = $club_achiever_user_detail;
                                                $business_to_search_in_users[] = $left_member;
                                                $business_to_search_in_users[] = $right_member;
                                                // dd($business_to_search_in_users);

                                                foreach ($business_to_search_in_users as  $business_to_search_in_user_item) {

                                                    $left_right_members = MLM::getAllLeftRightDirectActivePaidUsersOfUser($business_to_search_in_user_item);
                                                    // dd([]);
                                                    if (isset($left_right_members[0]) && isset($left_right_members[1]) && !empty($left_right_members[0]) && !empty($left_right_members[1])) {

                                                        //left business
                                                        foreach ($left_right_members[0] as $left_right_member) {
                                                            if ($left_right_member->plan_id > 0) {
                                                                $is_member_is_achiever = DB::table('club_user_achievement')
                                                                    ->where('user_id', $left_right_member->id)
                                                                    ->count();
                                                                if ($is_member_is_achiever === 0) {
                                                                    $business_LEFT_amount += Plan::where('plan_created_by_user_id', $left_right_member->id)
                                                                        ->whereMonth('created_at', $club_income_generated->month)
                                                                        ->whereYear('created_at', $club_income_generated->year)
                                                                        ->sum('price');
                                                                }
                                                            }
                                                        }

                                                        //right business
                                                        foreach ($left_right_members[1] as $left_right_member) {
                                                            $is_member_is_achiever = DB::table('club_user_achievement')
                                                                ->where('user_id', $left_right_member->id)
                                                                ->count();
                                                            if ($is_member_is_achiever === 0) {
                                                                $business_RIGHT_amount += Plan::where('plan_created_by_user_id', $left_right_member->id)
                                                                    ->whereMonth('created_at', $club_income_generated->month)
                                                                    ->whereYear('created_at', $club_income_generated->year)
                                                                    ->sum('price');
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            $club_achiever_user_detail->business_LEFT_amount = $business_LEFT_amount;
                                            $club_achiever_user_detail->business_RIGHT_amount = $business_RIGHT_amount;
                                            $club_user_achiever->club_user_achiever_detail = $club_achiever_user_detail;
                                            // print_r([$club_achiever_user_detail->username,$left_member,$right_member,'business left ' . $business_LEFT_amount, 'business right ' . $business_RIGHT_amount, $club_user_achiever, $club_info]);
                                            // print_r([$club_achiever_user_detail->username,'left '.$left_member->username,'right '.$right_member->username,'business left ' . $business_LEFT_amount, 'business right ' . $business_RIGHT_amount, $club_user_achiever, $club_info]);

                                            if (isset($club_info->unique_name)) {

                                                switch ($club_info->unique_name) {
                                                    case 'performance_bonus':
                                                        if ($business_LEFT_amount >= 1000 && $business_RIGHT_amount >= 1000) {
                                                            $user_to_get_paid[] = $club_user_achiever;
                                                        }
                                                        break;
                                                    case 'allowance_fund':
                                                        if ($business_LEFT_amount >= 2000 && $business_RIGHT_amount >= 2000) {
                                                            $user_to_get_paid[] = $club_user_achiever;
                                                        }
                                                        break;

                                                    case 'car_fund':
                                                        if ($business_LEFT_amount >= 3000 && $business_RIGHT_amount >= 3000) {
                                                            $user_to_get_paid[] = $club_user_achiever;
                                                        }
                                                        break;

                                                    case 'house_fund':
                                                        if ($business_LEFT_amount >= 4000 && $business_RIGHT_amount >= 4000) {
                                                            $user_to_get_paid[] = $club_user_achiever;
                                                        }
                                                        break;

                                                    case 'family_fund':
                                                        if ($business_LEFT_amount >= 5000 && $business_RIGHT_amount >= 5000) {
                                                            $user_to_get_paid[] = $club_user_achiever;
                                                        }
                                                        break;
                                                }
                                            }
                                        }
                                    }

                                    // return;
                                    if (count($user_to_get_paid) > 0) {
                                        $amount_to_distribute = return2sureRoundAmount($distribution_club->amount / count($user_to_get_paid), 2);
                                        if ($amount_to_distribute > 0) {

                                            // dd($user_to_get_paid);
                                            foreach ($user_to_get_paid as $user_to_get_paid_item) {


                                                DB::table('club_user_income')->insertGetId(
                                                    [
                                                        'user_id' => $user_to_get_paid_item->user_id,
                                                        'club_id' => $user_to_get_paid_item->club_id,
                                                        'income' => $amount_to_distribute,
                                                        'club_distribution_id' => $distribution_club->id,
                                                        'club_generation_id' => $club_income_generated->id,
                                                        'created_at' => $datetime->format("Y-m-d H:i:s"),
                                                        'updated_at' => $datetime->format("Y-m-d H:i:s"),
                                                    ]
                                                );

                                                $user_to_update = User::where('id', $user_to_get_paid_item->user_id)

                                                    ->first();


                                                $user_to_update->balance +=  $amount_to_distribute;
                                                $user_to_update->save();

                                                //create transaction
                                                $transaction = new Transaction();
                                                $transaction->user_id = $user_to_update->id;
                                                $transaction->amount =  $amount_to_distribute;
                                                $transaction->post_balance = $user_to_update->balance;
                                                $transaction->charge = 0;
                                                $transaction->trx_type = '+';
                                                $transaction->remark = 'paid_bv';
                                                $transaction->details = 'Paid ' .  $amount_to_distribute . ' as reward income';
                                                $transaction->trx =  getTrx();
                                                $transaction->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $output_of_cron = ob_get_clean();
        $output_of_cron = str_ireplace('<br/>', "\n", $output_of_cron);
        $output_of_cron = str_ireplace('<br>', "\n", $output_of_cron);
        Log::channel('club_achievers_distribute_income')->info($output_of_cron);
    }


    public function processLoanRepayments()
    {
        ob_start();

        echo '<br/><br/>======Start processLoanRepayments======<br/>';

        $datetime = now();
        $current_date = now()->format('Y-m-d');
        $number_of_days_in_current_month = date('t');

        $all_loans = DB::table('loan_approved')
            // ->where('club_id', '=', $club->id)
            ->where('is_loan_closed', '=', 0)
            ->get();

        $all_loans_closed = DB::table('loan_approved')
            // ->where('club_id', '=', $club->id)
            ->where('is_loan_closed', '=', 1)
            ->count();

        echo "Total Loans Approved in System {$all_loans->count()}<br/>";
        echo "Total Loans Closed in System {$all_loans_closed}<br/>";
        echo "Today Cron Date {$current_date}<br/>";


        $total_loan_repayments_processed_today = 0;
        $total_loan_repayments_failed_today = 0;
        foreach ($all_loans as $lkey => $loan) {

            echo "<br/><br/><br/>=========================<br/>";

            if (!isset($loan->loan_application_id)) {
                continue;
            }

            echo "Check Loan {$loan->loan_id}<br/>";

            // if (!isset($loan->created_at)) {
            //     continue;
            // }

            // $loan_approved_date = now()->parse($loan->created_at);


            // $loan_application = DB::table('loan_applications')
            //     ->where('id', '=', $loan->loan_application_id)
            //     ->first();


            // if (!isset($loan_application->deposit_id)) {
            //     continue;
            // }


            // $deposit_info = DB::table('deposits')
            //     ->where('id', '=', $loan_application->deposit_id)
            //     ->first();


            // if (!isset($deposit_info->detail)) {
            //     continue;
            // }

            // $loan_detail = $deposit_info->detail;

            // if (empty($loan_detail)) {
            //     echo "Loan Form Attributes not found<br/>";
            //     continue;
            // }

            // $loan_detail = json_decode($loan_detail);

            // $loan_tenure = null;

            // foreach ($loan_detail as  $loan_detail_item) {
            //     if (isset($loan_detail_item->name)) {
            //         if (preg_match('#tenure#i', $loan_detail_item->name) === 1) {
            //             if (is_array($loan_detail_item->value) && isset($loan_detail_item->value[0])) {
            //                 if (preg_match('#(?<tenure>\d+)#', $loan_detail_item->value[0], $tenmatch) === 1) {

            //                     if (isset($tenmatch['tenure'])) {
            //                         $loan_tenure = $tenmatch['tenure'];
            //                     }
            //                 }
            //             } else {
            //                 if (is_string($loan_detail_item->value) && !empty($loan_detail_item->value)) {
            //                     if (preg_match('#(?<tenure>\d+)#', $loan_detail_item->value, $tenmatch) === 1) {

            //                         if (isset($tenmatch['tenure'])) {
            //                             $loan_tenure = $tenmatch['tenure'];
            //                         }
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // }

            // if (empty($loan_tenure)) {
            //     echo "Loan Tenure Not Found<br/>";
            //     continue;
            // }

            // $current_date = "2024-05-15";

            $loan_repayments = DB::table('loan_repayments')
                ->where('loan_id', $loan->id)
                ->where('is_emi_paid', 0)
                ->where('is_active', 1)
                ->whereDate('emi_to_be_paid_datetime', $current_date)
                ->get();


            foreach ($loan_repayments as  $loan_repayment_item) {

                $user = User::findOrFail($loan_repayment_item->user_id);
                $amount = $loan_repayment_item->emi_amount;

                $notifyTemplate = 'BAL_SUB';

                if ($amount > $user->balance) {
                    $total_loan_repayments_failed_today++;
                    echo "EMI Failed for user:" . $user->username . " of month {$loan_repayment_item->emi_to_be_paid_datetime}<br/>";
                    DB::table('loan_repayments')
                        ->where('id', $loan_repayment_item->id)  // find your user by their email
                        ->update(array('is_emi_paid' => 2, 'emi_failed_datetime' => $datetime->format("Y-m-d H:i:s")));


                    //// $user = User::findOrFail(37);
                    // notify($user, 'DEFAULT', [
                    //     'subject' => 'EMI Payment Failed',
                    //     'message' => "Hello {$user->fullname} your emi payment for your loan {$loan->loan_id} is failed. Please pay your emi.",
                    // ]);

                } else {
                    $total_loan_repayments_processed_today++;
                    echo "EMI Paid for user:" . $user->username . " of month {$loan_repayment_item->emi_to_be_paid_datetime}<br/>";
                    $general = gs();
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
                    $transaction->details = 'Loan EMI for LOAN:' . $loan->loan_id;
                    $transaction->save();

                    DB::table('loan_repayments')
                        ->where('id', $loan_repayment_item->id)  // find your user by their email
                        ->update(array('is_emi_paid' => 1, 'emi_paid_datetime' => $datetime->format("Y-m-d H:i:s")));


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
            }



            $loan_repayments_is_emi_paid = DB::table('loan_repayments')
                ->where('loan_id', $loan->id)
                ->where('is_emi_paid', 1)
                ->where('is_active', 1)
                ->count();

            $loan_repayments_total_count = DB::table('loan_repayments')
                ->where('loan_id', $loan->id)
                ->where('is_active', 1)
                ->count();

            if ($loan_repayments_total_count > 0) {
                if ($loan_repayments_is_emi_paid == $loan_repayments_total_count) {
                    DB::table('loan_approved')
                        ->where('id', $loan->id)  // find your user by their email
                        ->update(array('is_loan_closed' => 1));
                }
            }


            // dd([
            //     $current_date,
            //     $loan,
            //     $loan_repayments
            // ]);
        }

        echo "<br/><br/><br/>=========================<br/>";
        echo "Total EMI Paid :" . $total_loan_repayments_processed_today . "<br/>";
        echo "Total EMI failed:" . $total_loan_repayments_failed_today . "<br/>";



        echo $output_of_cron = ob_get_clean();
        $output_of_cron = str_ireplace('<br/>', "\n", $output_of_cron);
        $output_of_cron = str_ireplace('<br>', "\n", $output_of_cron);
        Log::channel('cron_loan_repayments')->info($output_of_cron);
    }

    public function generateLoanRepaymentsSchedule()
    {
        ob_start();

        echo '<br/><br/>======Start generateLoanRepaymentsSchedule======<br/>';

        $datetime = now();
        $number_of_days_in_current_month = date('t');

        $all_loans = DB::table('loan_approved')
            // ->where('club_id', '=', $club->id)
            ->where('is_loan_closed', '=', 0)
            ->get();








        foreach ($all_loans as $lkey => $loan) {

            $loan_approved_date = now()->parse($loan->created_at);

            $loan_application = DB::table('loan_applications')
                // ->where('club_id', '=', $club->id)
                ->where('id', '=', $loan->loan_application_id)
                ->first();

            $deposit_info = DB::table('deposits')
                // ->where('club_id', '=', $club->id)
                ->where('id', '=', $loan_application->deposit_id)
                ->first();

            $loan_detail = $deposit_info->detail;
            if (empty($loan_detail)) {
                echo "Loan Form Attributes not found<br/>";
                continue;
            }

            $loan_detail = json_decode($loan_detail);

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

            if (empty($loan_tenure)) {
                echo "Loan Tenure Not Found<br/>";
                continue;
            }

            // $loan_application->loan_amount=60;

            // $loan_tenure=6;


            if ($loan_application->loan_amount % $loan_tenure == 0) {
                $loan_emis = $loan_application->loan_amount / $loan_tenure;

                for ($loan_loop_counter = 1; $loan_loop_counter <= $loan_tenure; $loan_loop_counter++) {

                    $loan_approved_date = $loan_approved_date->addDays(30);
                    DB::table('loan_repayments')->insertGetId(
                        [
                            'loan_id' => $loan->id,
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
                            'loan_id' => $loan->id,
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

            // dd($loan_emis);



            // $payment_info = DB::table('gateways')
            //     // ->where('club_id', '=', $club->id)
            //     ->where('id', '=', $loan_application->payment_gateway_id)
            //     ->first();

            // $form_info = DB::table('forms')
            //     // ->where('club_id', '=', $club->id)
            //     ->where('id', '=', $payment_info->form_id)
            //     ->first();
        }



        $output_of_cron = ob_get_clean();
        $output_of_cron = str_ireplace('<br/>', "\n", $output_of_cron);
        $output_of_cron = str_ireplace('<br>', "\n", $output_of_cron);
        Log::channel('cron_loan_generate_repayments_schedule')->info($output_of_cron);
    }

    public function updateBvOfUser()
    {

        // dd(request()->all());

        $user_to_process = 183;
        $plan_price = 34;

        $buy_plan_for_a_different_user = User::where('id', $user_to_process)

            ->first();
        if (!isset($buy_plan_for_a_different_user->id)) {
            echo "No user";
            exit;
        }

        $user = $buy_plan_for_a_different_user;
        // $bv = $this->plan->bv;
        // $bv = $plan->price;
        $bv = $plan_price;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }
            if ($upper->plan_id == 0) {
                $user = $upper;
                continue;
            }

            $bvlog = new BvLog();
            $bvlog->user_id = $upper->id;
            $bvlog->trx_type = '+';
            $extra = $upper->userExtra;
            if ($user->position == 1) {
                $extra->bv_left += $bv;
                $bvlog->position = '1';
            } else {
                $extra->bv_right += $bv;
                $bvlog->position = '2';
            }
            $extra->save();
            $bvlog->amount = $bv;
            if (isset($buy_plan_for_a_different_user->username)) {
                $bvlog->details = 'BV from Loan Approval Of User ID ' . $buy_plan_for_a_different_user->username;
            } else {
                $bvlog->details = 'BV from Loan Approval';
            }

            $bvlog->save();

            $user = $upper;
        }
    }
}
