<?php

namespace App\Lib;

use App\Models\BvLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserExtra;
use Illuminate\Support\Facades\DB;

class Mlm
{
    /**
     * User who subscribe a plan
     *
     * @var object
     */
    public $user;

    /**
     * Plan which subscribed by the user
     *
     * @var object
     */
    public $plan;

    /**
     * General setting
     *
     * @var object
     */
    public $setting;

    /**
     * Transaction number of whole process
     *
     * @var string
     */
    public $trx;

    /**
     * Initialize some global properties
     *
     * @param object $user
     * @param object $plan
     * @param string $trx
     * @return void
     */
    public function __construct($user = null, $plan = null, $trx = null)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->trx = $trx;
        $this->setting = gs();
    }

    /**
     * Get the positioner user object
     *
     * @param object $positioner
     * @param int $position
     * @return object;
     */
    public static function getPositioner($positioner, $position)
    {
        $getPositioner = $positioner;
        while (0 == 0) {
            $getUnder = User::where('pos_id', $positioner->id)->where('position', $position)->first(['id', 'pos_id', 'position', 'username']);
            if ($getUnder) {
                $positioner = $getUnder;
                $getPositioner = $getUnder;
            } else {
                break;
            }
        }
        return $getPositioner;
    }


    public static function getAllActivePaidUsersRefferedByThisUser($user)
    {
        return User::where('ref_by', $user->id)
            ->where('plan_id', '>', 0)
            ->where('status', '=', 1)
            ->get();
    }

    public static function getAllActivePaidUserOfUser($positioner, $view_position = null)
    {

        $members = [];

        if ($view_position == 1) {
            $members[] = $u = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();
            if (isset($u) && !empty($u)) {
                $positioner = $u;
            }
        } else if ($view_position == 2) {
            $members[] = $u = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();
            if (isset($u) && !empty($u)) {
                $positioner = $u;
            }
        } else {
            $members[] = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();
            $members[] = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();
        }



        $user_to_skip_ids = [];
        if (isset($members[0])) {
            $user_to_skip_ids[] = $members[0]->id;
        }
        if (isset($members[1])) {
            $user_to_skip_ids[] = $members[1]->id;
        }


        while (0 == 0) {

            if ($view_position > 0) {
                if (!empty($user_to_skip_ids)) {
                    $getUnder = User::where('pos_id', $positioner->id)->where('status', 1)->whereNotIn('id', $user_to_skip_ids)->first();
                } else {
                    $getUnder = User::where('pos_id', $positioner->id)->where('status', 1)->first();
                }
            } else {
                if (!empty($user_to_skip_ids)) {
                    $getUnder = User::where('ref_by', $positioner->id)->where('status', 1)->whereNotIn('id', $user_to_skip_ids)->first();
                } else {
                    $getUnder = User::where('ref_by', $positioner->id)->where('status', 1)->first();
                }
            }


            if ($getUnder) {
                $members[] = $getUnder;
                $positioner = $getUnder;
            } else {
                break;
            }
        }
        return $members;
    }


    public static function getAllActivePaidUserOfUserDirectIndirect($positioner, $view_position = null)
    {

        // $members = self::getAllActivePaidUserOfUserDirect($positioner);
        $members = [];
        $left = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();
        $right = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        $user_to_skip_ids = [];
        if (isset($left->id)) {
            $members[] = $left;
            $user_to_skip_ids[] = $left->id;

            $usert = $left;
            while (1) {
                $tm = User::where('ref_by', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
            $usert = $left;
            while (1) {
                $tm = User::where('pos_id', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
        }

        if (isset($right->id)) {
            $members[] = $right;
            $user_to_skip_ids[] = $right->id;

            $usert = $right;
            while (1) {
                $tm = User::where('ref_by', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
            $usert = $right;
            while (1) {
                $tm = User::where('pos_id', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
        }




        $members = collect($members);
        $unique = $members->unique('id');

        return $unique->values()->reverse()->all();
        // dd($members);
        return $members;
        // $dmembers  = User::where('pos_id', $positioner->id)->where('status', 1)->get();;
        // $imembers  = User::where('ref_by', $positioner->id)->where('status', 1)->get();;




        // while (0 == 0) {

        //     $getUnder = User::where('pos_id', $positioner->id)->where('status', 1)->first();


        //     if ($getUnder) {
        //         $members[] = $getUnder;
        //         $positioner = $getUnder;
        //     } else {
        //         break;
        //     }
        // }
        // return $members;
    }
    public static function getAllActivePaidUserOfUserDirectIndirectBK($positioner, $view_position = null)
    {

        // $members = self::getAllActivePaidUserOfUserDirect($positioner);
        $members = [];
        $left = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();
        $right = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        $user_to_skip_ids = [];
        if (isset($left->id)) {
            $members[] = $left;
            $user_to_skip_ids[] = $left->id;
        }
        if (isset($right->id)) {
            $members[] = $right;
            $user_to_skip_ids[] = $right->id;
        }

        $getUnder = [];
        if (!empty($user_to_skip_ids)) {
            $getUnder = User::where('ref_by', $positioner->id)->where('status', 1)->whereNotIn('id', $user_to_skip_ids)->get();
        } else {
            $getUnder = User::where('ref_by', $positioner->id)->where('status', 1)->get();
        }

        if (!empty($getUnder)) {
            foreach ($getUnder as $m) {
                $members[] = $m;
            }
            // $members = array_merge($members,$getUnder->toArray());
        }

        return $members;
        // $dmembers  = User::where('pos_id', $positioner->id)->where('status', 1)->get();;
        // $imembers  = User::where('ref_by', $positioner->id)->where('status', 1)->get();;




        // while (0 == 0) {

        //     $getUnder = User::where('pos_id', $positioner->id)->where('status', 1)->first();


        //     if ($getUnder) {
        //         $members[] = $getUnder;
        //         $positioner = $getUnder;
        //     } else {
        //         break;
        //     }
        // }
        // return $members;
    }
    public static function getAllActivePaidUserOfUserDirectIndirectLeft($positioner)
    {
        $members = [];
        $left = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();
        // $right = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        $user_to_skip_ids = [];
        if (isset($left->id)) {
            $members[] = $left;
            $user_to_skip_ids[] = $left->id;

            $usert = $left;
            while (1) {
                $tm = User::where('ref_by', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
            $usert = $left;
            while (1) {
                $tm = User::where('pos_id', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
        }




        $members = collect($members);
        $unique = $members->unique('id');

        return $unique->values()->reverse()->all();
        // dd($members);


    }

    public static function getAllLeftRightDirectActivePaidUsersOfUser($positioner)
    {


        $users = User::where('ref_by', $positioner->id)
            ->where('plan_id', '>', 0)
            ->where('status', 1)
            ->get();

        $all_lefts = [];
        $all_rights = [];

        foreach ($users as $u) {
            $tmp = $u;
            while (1) {
                $parent_user = User::where('id', $tmp->pos_id)->first();

                if ($parent_user->id == $positioner->id) {
                    if ($tmp->position == 1) {
                        $all_lefts[] = $u;
                    } else {
                        $all_rights[] = $u;
                    }
                    break;
                } else {
                    $tmp = $parent_user;
                }
            }
        }

        // dd([$all_lefts, $all_rights]);
        return [$all_lefts, $all_rights];

        $members = [];
        // $left = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();
        $left = User::where('ref_by', $positioner->id)
            ->where('plan_id', '>', 0)
            ->where('position', 1)
            ->where('status', 1)->first();
        // $right = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        $user_to_skip_ids = [];
        if (isset($left->id)) {
            $members[] = $left;
            $user_to_skip_ids[] = $left->id;

            $usert = $left;
            while (1) {
                $tm = User::where('ref_by', $usert->id)->where('status', 1)->where('plan_id', '>', 0)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
        }




        $members = collect($members);
        $unique = $members->unique('id');

        return $unique->values()->reverse()->all();
        // dd($members);


    }
    public static function getAllActivePaidUserOfUserDirectIndirectLeftX($positioner)
    {

        // $members = self::getAllActivePaidUserOfUserDirect($positioner);
        $members = [];
        $left_user = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();




        if (isset($left_user->id)) {

            $members[] = $left_user;


            $left = User::where('pos_id', $left_user->id)->where('position', 1)->where('status', 1)->first();
            $right = User::where('pos_id', $left_user->id)->where('position', 2)->where('status', 1)->first();




            $user_to_skip_ids = [];
            if (isset($left->id)) {
                $members[] = $left;
                $user_to_skip_ids[] = $left->id;
            }
            if (isset($right->id)) {
                $members[] = $right;
                $user_to_skip_ids[] = $right->id;
            }

            $getUnder = [];
            if (!empty($user_to_skip_ids)) {
                $getUnder = User::where('ref_by', $left_user->id)->where('status', 1)->whereNotIn('id', $user_to_skip_ids)->get();
            } else {
                $getUnder = User::where('ref_by', $left_user->id)->where('status', 1)->get();
            }

            if (!empty($getUnder)) {
                foreach ($getUnder as $m) {
                    $members[] = $m;
                }
                // $members = array_merge($members,$getUnder->toArray());
            }

            // return $members;

            ////









        }







        return $members;
        // $dmembers  = User::where('pos_id', $positioner->id)->where('status', 1)->get();;
        // $imembers  = User::where('ref_by', $positioner->id)->where('status', 1)->get();;




        // while (0 == 0) {

        //     $getUnder = User::where('pos_id', $positioner->id)->where('status', 1)->first();


        //     if ($getUnder) {
        //         $members[] = $getUnder;
        //         $positioner = $getUnder;
        //     } else {
        //         break;
        //     }
        // }
        // return $members;
    }


    public static function getAllActivePaidUserOfUserDirectIndirectRight($positioner)
    {
        $members = [];

        $right = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        if (isset($right->id)) {
            $members[] = $right;
            $user_to_skip_ids[] = $right->id;

            $usert = $right;
            while (1) {
                $tm = User::where('ref_by', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
            $usert = $right;
            while (1) {
                $tm = User::where('pos_id', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
        }




        $members = collect($members);
        $unique = $members->unique('id');

        return $unique->values()->reverse()->all();
        // dd($members);
    }
    public static function getAllActivePaidUserOfUserDirectIndirectRightX($positioner)
    {

        // $members = self::getAllActivePaidUserOfUserDirect($positioner);
        $members = [];
        $left_user = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        if (isset($left_user->id)) {

            $members[] = $left_user;


            $left = User::where('pos_id', $left_user->id)->where('position', 1)->where('status', 1)->first();
            $right = User::where('pos_id', $left_user->id)->where('position', 2)->where('status', 1)->first();




            $user_to_skip_ids = [];
            if (isset($left->id)) {
                $members[] = $left;
                $user_to_skip_ids[] = $left->id;
            }
            if (isset($right->id)) {
                $members[] = $right;
                $user_to_skip_ids[] = $right->id;
            }

            $getUnder = [];
            if (!empty($user_to_skip_ids)) {
                $getUnder = User::where('ref_by', $left_user->id)->where('status', 1)->whereNotIn('id', $user_to_skip_ids)->get();
            } else {
                $getUnder = User::where('ref_by', $left_user->id)->where('status', 1)->get();
            }

            if (!empty($getUnder)) {
                foreach ($getUnder as $m) {
                    $members[] = $m;
                }
                // $members = array_merge($members,$getUnder->toArray());
            }

            // return $members;

            ////









        }







        return $members;
        // $dmembers  = User::where('pos_id', $positioner->id)->where('status', 1)->get();;
        // $imembers  = User::where('ref_by', $positioner->id)->where('status', 1)->get();;




        // while (0 == 0) {

        //     $getUnder = User::where('pos_id', $positioner->id)->where('status', 1)->first();


        //     if ($getUnder) {
        //         $members[] = $getUnder;
        //         $positioner = $getUnder;
        //     } else {
        //         break;
        //     }
        // }
        // return $members;
    }
    public static function getAllActivePaidUserOfUserDirect($positioner, $view_position = null)
    {

        $members = [];


        while (0 == 0) {

            $getUnder = User::where('pos_id', $positioner->id)->where('status', 1)->first();


            if ($getUnder) {
                $members[] = $getUnder;
                $positioner = $getUnder;
            } else {
                break;
            }
        }
        return $members;
    }
    public static function getAllActivePaidUserOfUserIndirect($positioner, $view_position = null)
    {

        $members = [];


        while (0 == 0) {

            $getUnder = User::where('ref_by', $positioner->id)->where('status', 1)->first();


            if ($getUnder) {
                $members[] = $getUnder;
                $positioner = $getUnder;
            } else {
                break;
            }
        }
        return $members;
    }


    public static function getLeftActivePaidUser($positioner)
    {
        $members = [];
        $left = User::where('pos_id', $positioner->id)->where('position', 1)->where('status', 1)->first();
        // $right = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        $user_to_skip_ids = [];
        if (isset($left->id)) {
            $members[] = $left;
            $user_to_skip_ids[] = $left->id;

            $usert = $left;
            while (1) {
                $tm = User::where('ref_by', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
            $usert = $left;
            while (1) {
                $tm = User::where('pos_id', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
        }




        $members = collect($members);
        $unique = $members->unique('id');

        // return $unique->values()->reverse()->all();



        $unique_users_only = $unique->values()->reverse()->all();

        $current_loop_user_right_active_member = 0;
        foreach ($unique_users_only as   $unique_users_only_item) {
            if ($unique_users_only_item['status'] == 1 && $unique_users_only_item['plan_id'] > 0) {

                // dd($unique_users_only_item);
                $current_loop_user_right_active_member = $current_loop_user_right_active_member + 1;
            }
        }

        // dd($current_loop_user_right_active_member);
        return $current_loop_user_right_active_member;
    }

    public static function getLeftActivePaidUserBkp27April2024($positioner)
    {
        $getPositioner = $positioner;
        $current_loop_user_left_active_member = 0;
        while (0 == 0) {
            $getUnder = User::where('pos_id', $positioner->id)->where('position', '=', 1)->first(['id', 'status', 'pos_id', 'position', 'plan_id', 'username']);


            if ($getUnder) {

                if ($getUnder['status'] == 1 && $getUnder['plan_id'] > 0) {

                    if ($getUnder['position'] == 1) {
                        $current_loop_user_left_active_member = $current_loop_user_left_active_member + 1;
                    } else {
                    }
                }
                $positioner = $getUnder;
                $getPositioner = $getUnder;
            } else {
                break;
            }
        }
        return $current_loop_user_left_active_member;
    }


    public static function getLeftActivePaidUserBusiness($positioner)
    {
        $tmppositioner = $positioner;

        $business_amount = 0;
        while (0 == 0) {
            $getUnder = User::where('pos_id', $positioner->id)->where('position', '=', 1)->first(['id', 'status', 'pos_id', 'position', 'plan_id', 'username']);


            if ($getUnder) {

                if ($getUnder['status'] == 1 && $getUnder['plan_id'] > 0) {

                    if ($getUnder['position'] == 1) {
                        $user_current_plan = Plan::where('id',  $getUnder->plan_id)
                            ->where('plan_created_by_user_id',  $getUnder->id)
                            ->latest('created_at')
                            ->first();
                        $business_amount = $business_amount + $user_current_plan->price;
                    } else {
                    }
                }
                $positioner = $getUnder;
            } else {
                break;
            }
        }
        return ($business_amount);
    }

    public static function getRightActivePaidUserBusiness($positioner)
    {
        $tmppositioner = $positioner;

        $business_amount = 0;
        while (0 == 0) {
            $getUnder = User::where('pos_id', $positioner->id)->where('position', '=', 2)->first(['id', 'status', 'pos_id', 'position', 'plan_id', 'username']);


            if ($getUnder) {

                if ($getUnder['status'] == 1 && $getUnder['plan_id'] > 0) {

                    if ($getUnder['position'] == 2) {

                        $user_current_plan = Plan::where('id',  $getUnder->plan_id)
                            ->where('plan_created_by_user_id',  $getUnder->id)
                            ->latest('created_at')
                            ->first();
                        $business_amount = $business_amount + $user_current_plan->price;
                    } else {
                    }
                }
                $positioner = $getUnder;
            } else {
                break;
            }
        }
        return ($business_amount);
    }

    public static function isStarID($user)
    {

        if ($user->is_start_id == 1) {
            return true;
        }

        // dd($user);

        $user_current_plan = Plan::where('id', $user->plan_id)
            ->where('plan_created_by_user_id', $user->id)
            ->latest('created_at')
            ->first();


        if ($user_current_plan) {




            // if ($user->is_start_id != 1) {
            // if (empty($user->user_income_week_start_date) && empty($user->user_income_week_end_date)) {
            //     $carbon_plan_created_date = now()->parse($plan->created_at);
            //     $user->user_income_week_start_date = $carbon_plan_created_date->format('Y-m-d H:i:s');;

            //     $carbon_plan_created_date->addDays(7);
            //     $user->user_income_week_end_date =  $carbon_plan_created_date->format('Y-m-d H:i:s');
            // }
            // }




            $user_income_week_start_date = now()->parse($user->user_income_week_start_date);
            $user_income_week_end_date = now()->parse($user->user_income_week_end_date);


            $plan_week_start_date = $user_income_week_start_date->format('Y-m-d H:i:s');;
            $plan_week_end_date =  $user_income_week_end_date->format('Y-m-d H:i:s');



            $currren_date = now();
            $currren_date_format =  $currren_date->format('Y-m-d H:i:s');


            if ($plan_week_end_date < $currren_date_format) {
                //week finished
                $user->user_income_week_start_date = $user_income_week_end_date->format('Y-m-d H:i:s');
                $user->user_income_week_end_date = $user_income_week_end_date->addDays(7)->format('Y-m-d H:i:s');


                $user_income_week_start_date = now()->parse($user->user_income_week_start_date);
                $user_income_week_end_date = now()->parse($user->user_income_week_end_date);


                $plan_week_start_date = $user_income_week_start_date->format('Y-m-d H:i:s');;
                $plan_week_end_date =  $user_income_week_end_date->format('Y-m-d H:i:s');

                // $user->save();
            } else {

                // $user->user_income_week_start_date = $user_income_week_end_date->format('Y-m-d H:i:s');
                // $user->user_income_week_end_date = $user_income_week_end_date->addDays(7)->format('Y-m-d H:i:s');
            }

            $user_chain_current_business_left = BvLog::where('user_id',  $user->id)
                ->whereBetween('created_at',  [$plan_week_start_date, $plan_week_end_date])
                ->where('position', 1)
                ->sum('amount');
            $user_chain_current_business_right = BvLog::where('user_id',  $user->id)
                ->whereBetween('created_at',  [$plan_week_start_date, $plan_week_end_date])
                ->where('position', 2)
                ->sum('amount');


            echo '<br/> $$$currren_date_format:' . ($currren_date_format);
            echo '<br/> $$plan_week_start_date:' . ($plan_week_start_date);
            echo '<br/> $$plan_week_end_date:' . ($plan_week_end_date);

            echo '<br/> $user_chain_current_business_left:' . ($user_chain_current_business_left);
            echo '<br/> $user_chain_current_business_right:' . ($user_chain_current_business_right);


            if ($user_chain_current_business_left >= 1000 &&  $user_chain_current_business_right >= 1000) {
                $user->is_start_id = 1;
                return true;
            }
            return false;
        }
    }

    public static function getRightActivePaidUser($positioner)
    {
        $members = [];
        $current_loop_user_right_active_member = 0;

        $right = User::where('pos_id', $positioner->id)->where('position', 2)->where('status', 1)->first();




        if (isset($right->id)) {
            $members[] = $right;
            $user_to_skip_ids[] = $right->id;

            $usert = $right;
            while (1) {
                $tm = User::where('ref_by', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
            $usert = $right;
            while (1) {
                $tm = User::where('pos_id', $usert->id)->where('status', 1)->first();
                if (!isset($tm->id)) {
                    break;
                } else {
                    $members[] = $usert = $tm;
                }
            }
        }




        $members = collect($members);
        $unique = $members->unique('id');

        $unique_users_only = $unique->values()->reverse()->all();

        $current_loop_user_right_active_member = 0;
        foreach ($unique_users_only as   $unique_users_only_item) {
            if ($unique_users_only_item['status'] == 1 && $unique_users_only_item['plan_id'] > 0) {

                // dd($unique_users_only_item);
                $current_loop_user_right_active_member = $current_loop_user_right_active_member + 1;
            }
        }

        // dd($current_loop_user_right_active_member);
        return $current_loop_user_right_active_member;

        // dd($unique_users_only);
    }



    public static function getRightActivePaidUserBkp27April2024($positioner)
    {
        $getPositioner = $positioner;
        $current_loop_user_right_active_member = 0;
        while (0 == 0) {
            $getUnder = User::where('pos_id', $positioner->id)->where('position', '=', 2)->first(['id', 'status', 'pos_id', 'position', 'plan_id', 'username']);


            if ($getUnder) {

                if ($getUnder['status'] == 1 && $getUnder['plan_id'] > 0) {

                    if ($getUnder['position'] == 2) {
                        $current_loop_user_right_active_member = $current_loop_user_right_active_member + 1;
                    } else {
                    }
                }
                $positioner = $getUnder;
                $getPositioner = $getUnder;
            } else {
                break;
            }
        }
        return $current_loop_user_right_active_member;
    }
    public static function isUserEligibleToGetBinaryIncome($user)
    {
    }


    public static function getPositionerNew($positioner, $position)
    {
        // return 1;
        $userplace = null;
        $userplace = UserExtra::where('user_id', $positioner->id)->first(['id', 'free_left', 'free_right']);
        if ($position == 1) {
            $placeval = $userplace->free_left;
        } else {
            $placeval = $userplace->free_right;
        }
        if ($placeval == 0) {
            $getPositioner = $positioner;
            $getUnder = User::where('id', $positioner->id)->where('position', $position)->first(['id', 'pos_id', 'position', 'username']);
            if ($getUnder) {

                $getPositioner = $getUnder;
            }
            return $getPositioner;
        } else {
            return null;
        }
        //  }

    }

    /**
     * Give BV to upper positioners
     *
     * @return void
     */
    public function updateBvBKP()
    {
        $user = $this->user;
        $bv = $this->plan->bv;
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
            $bvlog->details = 'BV from ' . auth()->user()->username;
            $bvlog->save();

            $user = $upper;
        }
    }
    public function updateBv()
    {
        $user = $this->user;
        // $bv = $this->plan->bv;
        $bv = $this->plan->price;
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
            $bvlog->details = 'BV from ' . auth()->user()->username;
            $bvlog->save();

            $user = $upper;
        }
    }
    public function updateBvForSomeone($plan, $buy_plan_for_a_different_user)
    {
        $user = $buy_plan_for_a_different_user;
        // $bv = $this->plan->bv;
        $bv = $plan->price;
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
            $bvlog->details = 'BV from ' . auth()->user()->username;
            $bvlog->save();

            $user = $upper;
        }
    }
    public function updateBvForSomeoneAsLoan($plan, $buy_plan_for_a_different_user)
    {
        $user = $buy_plan_for_a_different_user;
        // $bv = $this->plan->bv;
        $bv = $plan->price;
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
    /**
     * Give referral commission to immediate referrer
     *
     * @return void
     */
    public function referralCommission($plan_purchased_price = null)
    {
        $user = $this->user;
        $referrer = $user->referrer;

        //dd($referrer);
        $plan = @$referrer->plan;
        if (!empty($plan) && $plan && isset($plan->price) && $plan_purchased_price > 0) {

            //$amount = return2sureRoundAmount(($plan_purchased_price * $plan->ref_com) / 100, 4);
            $gs = DB::table('general_settings')->first()->direct_income ?? 0;
            $amount = $gs;

            $referrer->balance += $amount;


            $referrer->total_ref_com += $amount;
            $referrer->save();

            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $referrer->id;
            // $transaction->amount = $amount;

            if ($plan_purchased_price > 0) {
                // $transaction->amount = return2sureRoundAmount($plan_purchased_price * $amount / 100, 0);
                $transaction->amount = $amount;
            } else {
                $transaction->amount = $amount;
            }

            $transaction->post_balance = $referrer->balance;


            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Direct referral commission from ' . $user->username;
            $transaction->trx =  $trx;
            $transaction->remark = 'referral_commission';
            $transaction->save();

            notify($referrer, 'REFERRAL_COMMISSION', [
                'amount' => showAmount($amount),
                'username' => $user->username,
                'post_balance' => $referrer->balance,
                'trx' => $trx
            ]);
        }
    }
    public function referralCommissionForSomeone($plan_purchased_price = null, $buy_plan_for_a_different_user)
    {
        // $user = $this->user;
        $user = $buy_plan_for_a_different_user;
        $referrer = $user->referrer;
        $plan = @$referrer->plan;
        if (!empty($plan) && $plan && isset($plan->price) && $plan_purchased_price > 0) {

            // $amount = $plan->ref_com;
            // if ($plan_purchased_price > 0) {
            //     $referrer->balance += return2sureRoundAmount($plan_purchased_price * $amount / 100, 0);
            // } else {
            //     $referrer->balance += $amount;
            // }


            $amount = return2sureRoundAmount(($plan_purchased_price * $plan->ref_com) / 100, 4);

            $referrer->balance += $amount;


            // $referrer->balance += $amount;
            $referrer->total_ref_com += $amount;
            $referrer->save();

            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $referrer->id;
            // $transaction->amount = $amount;

            if ($plan_purchased_price > 0) {
                //$transaction->amount = return2sureRoundAmount($plan_purchased_price * $amount / 100, 0);
                // $transaction->amount = return2sureRoundAmount($plan_purchased_price * $amount / 100, 4);
                $transaction->amount = $amount;
            } else {
                $transaction->amount = $amount;
            }

            $transaction->post_balance = $referrer->balance;


            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Direct referral commission from ' . $user->username;
            $transaction->trx =  $trx;
            $transaction->remark = 'referral_commission';
            $transaction->save();

            notify($referrer, 'REFERRAL_COMMISSION', [
                'amount' => showAmount($amount),
                'username' => $user->username,
                'post_balance' => $referrer->balance,
                'trx' => $trx
            ]);
        }
    }

    /**
     * Give tree commission to upper positioner
     *
     * @return void
     */
    public function treeCommission()
    {
        $user = $this->user;
        $amount = $this->plan->tree_com;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }
            if ($upper->plan_id == 0) {
                $user = $upper;
                continue;
            }
            $upper->balance += $amount;
            $upper->total_binary_com += $amount;
            $upper->save();

            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $upper->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $upper->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Tree commission';
            $transaction->remark = 'binary_commission';
            $transaction->trx =  $trx;
            $transaction->save();

            notify($upper, 'TREE_COMMISSION', [
                'amount' => showAmount($amount),
                'post_balance' => $upper->balance,
            ]);

            $user = $upper;
        }
    }
    public function treeCommissionForSomeone($buy_plan_for_a_different_user)
    {
        // $user = $this->user;
        $user = $buy_plan_for_a_different_user;
        $amount = $this->plan->tree_com;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }
            if ($upper->plan_id == 0) {
                $user = $upper;
                continue;
            }
            $upper->balance += $amount;
            $upper->total_binary_com += $amount;
            $upper->save();

            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $upper->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $upper->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Tree commission';
            $transaction->remark = 'binary_commission';
            $transaction->trx =  $trx;
            $transaction->save();

            notify($upper, 'TREE_COMMISSION', [
                'amount' => showAmount($amount),
                'post_balance' => $upper->balance,
            ]);

            $user = $upper;
        }
    }

    /**
     * Update paid count users to upper positioner when user subscribe a plan
     *
     * @return void
     */
    public function updatePaidCount()
    {
        $user = $this->user;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            // dd($upper);
            if (!$upper) {
                break;
            }

            $extra = $upper->userExtra;
            if ($user->position == 1) {
                $extra->free_left -= 1;
                $extra->paid_left += 1;
            } else {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
            }
            $extra->save();
            $user = $upper;
        }
    }

    public function updatePaidCountForSomeone($buy_plan_for_a_different_user)
    {
        // $user = $this->user;
        $user = $buy_plan_for_a_different_user;
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }

            $extra = $upper->userExtra;
            if ($user->position == 1) {
                $extra->free_left -= 1;
                $extra->paid_left += 1;
            } else {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
            }
            $extra->save();
            $user = $upper;
        }
    }

    /**
     * Update free count users to upper positioner when user register to this system
     *
     * @param object $user
     * @return void
     */
    public static function updateFreeCount($user)
    {
        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();
            if (!$upper) {
                break;
            }

            $extra = $upper->userExtra;
            if (!is_null($extra)) {
                if ($user->position == 1) {
                    $extra->free_left += 1;
                } else {
                    $extra->free_right += 1;
                }
                $extra->save();
            }
            $user = $upper;
        }
    }

    /**
     * Check the time for giving the matching bonus
     *
     * @return boolean
     */
    public function checkTime()
    {
        $general = $this->setting;
        $times = [
            'H' => 'daily',
            'D' => 'weekly',
            'd' => 'monthly',
        ];
        foreach ($times as $timeKey => $time) {
            if ($general->matching_bonus_time == $time) {
                $day = Date($timeKey);
                if (strtolower($day) != $general->matching_when) {
                    return false;
                }
            }
        }
        if (now()->toDateString() == now()->parse($general->last_paid)->toDateString()) {
            return false;
        }
        return true;
    }

    /**
     * Update the user BV after getting bonus
     *
     * @param object $general
     * @param object $uex
     * @param integer $paidBv
     * @param float $weak
     * @param float $bonus
     * @return void
     */
    public function updateUserBv($uex, $paidBv, $weak, $bonus)
    {
        $general = $this->setting;
        $user = $uex->user;
        //cut paid bv from both
        if ($general->cary_flash == 0) {
            $uex->bv_left -= $paidBv;
            $uex->bv_right -= $paidBv;
            $lostl = 0;
            $lostr = 0;
        }

        //cut only weaker bv from both
        if ($general->cary_flash == 1) {
            $uex->bv_left -= $weak;
            $uex->bv_right -= $weak;
            $lostl = $weak - $paidBv;
            $lostr = $weak - $paidBv;
        }

        //cut all bv from both
        if ($general->cary_flash == 2) {
            $uex->bv_left = 0;
            $uex->bv_right = 0;
            $lostl = $uex->bv_left - $paidBv;
            $lostr = $uex->bv_right - $paidBv;
        }
        $uex->save();
        $bvLog = null;
        if ($paidBv != 0) {
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 1,
                'amount' => $paidBv,
                'trx_type' => '-',
                'details' => 'Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 2,
                'amount' => $paidBv,
                'trx_type' => '-',
                'details' => 'Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
        }
        if ($lostl != 0) {
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 1,
                'amount' => $lostl,
                'trx_type' => '-',
                'details' => 'Flush ' . showAmount($lostl) . ' BV after Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
        }
        if ($lostr != 0) {
            $bvLog[] = [
                'user_id' => $user->id,
                'position' => 2,
                'amount' => $lostr,
                'trx_type' => '-',
                'details' => 'Flush ' . showAmount($lostr) . ' BV after Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
        }

        if ($bvLog) {
            BvLog::insert($bvLog);
        }
    }


    /**
     * Get the under position user
     *
     * @param integer $id
     * @param integer $position
     * @return object
     */

    protected function getPositionUser($id, $position)
    {
        return User::where('pos_id', $id)->where('position', $position)->with('referrer', 'plan', 'userExtra')->first();
    }


    /**
     * Get the under position user
     *
     * @param object $user
     * @return array
     */
    public function showTreePage($user, $isAdmin = false)
    {
        if (!$isAdmin) {
            if ($user->username != @auth()->user()->username) {
                $this->checkMyTree($user);
            }
        }
        $hands = array_fill_keys($this->getHands(), null);
        $hands['a'] = $user;
        $hands['b'] = $this->getPositionUser($user->id, 1);
        if ($hands['b']) {
            $hands['d'] = $this->getPositionUser($hands['b']->id, 1);
            $hands['e'] = $this->getPositionUser($hands['b']->id, 2);
        }
        if ($hands['d']) {
            $hands['h'] = $this->getPositionUser($hands['d']->id, 1);
            $hands['i'] = $this->getPositionUser($hands['d']->id, 2);
        }
        if ($hands['e']) {
            $hands['j'] = $this->getPositionUser($hands['e']->id, 1);
            $hands['k'] = $this->getPositionUser($hands['e']->id, 2);
        }
        $hands['c'] = $this->getPositionUser($user->id, 2);
        if ($hands['c']) {
            $hands['f'] = $this->getPositionUser($hands['c']->id, 1);
            $hands['g'] = $this->getPositionUser($hands['c']->id, 2);
        }
        if ($hands['f']) {
            $hands['l'] = $this->getPositionUser($hands['f']->id, 1);
            $hands['m'] = $this->getPositionUser($hands['f']->id, 2);
        }
        if ($hands['g']) {
            $hands['n'] = $this->getPositionUser($hands['g']->id, 1);
            $hands['o'] = $this->getPositionUser($hands['g']->id, 2);
        }
        return $hands;
    }

    /**
     * Get single user in tree
     *
     * @param object $user
     * @return string
     */
    public function showSingleUserinTree($user, $isAdmin = false)
    {
        $html = '';
        // print_r($user->userExtra);
        // die;
        if ($user) {
            if ($user->plan_id == 0) {
                $userType = "free-user";
                $stShow = "Free";
                $planName = '';
            } else {
                $userType = "paid-user";
                $stShow = "Paid";
                $planName = @$user->plan->name;
            }

            $img = getImage(getFilePath('userProfile') . '/' . $user->image, false, true);
            $refby = @$user->referrer->fullname ?? '';

            if ($isAdmin) {
                $hisTree = route('admin.users.binary.tree', $user->username);
            } else {
                $hisTree = route('user.binary.tree', $user->username);
            }

            $extraData = " data-name=\"$user->fullname\"";
            $extraData .= " data-username=\"$user->username\"";
            $extraData .= " data-treeurl=\"$hisTree\"";
            $extraData .= " data-status=\"$stShow\"";
            $extraData .= " data-plan=\"$planName\"";
            $extraData .= " data-image=\"$img\"";
            $extraData .= " data-refby=\"$refby\"";
            $extraData .= " data-lpaid=\"" . @$user->userExtra->paid_left . "\"";
            $extraData .= " data-rpaid=\"" . @$user->userExtra->paid_right . "\"";
            $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
            $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
            $extraData .= " data-lbv=\"" . showAmount(@$user->userExtra->bv_left) . "\"";
            $extraData .= " data-rbv=\"" . showAmount(@$user->userExtra->bv_right) . "\"";
            // $extraData .= " data-rbv=\"" . showAmount(@$user->total_invest) . "\"";

            $html .= "<div class=\"user showDetails\" type=\"button\" $extraData>";
            $html .= "<img src=\"$img\" alt=\"*\"  class=\"$userType\">";
            $html .= "<p class=\"user-name\"><a class='viewteammember' style='    z-index: 99;position: relative;' href='?view_team_member=$user->username'>$user->username</a></p>";
        } else {
            $img = getImage('assets/images/nouser.png');

            $html .= "<div class=\"user\" type=\"button\">";
            $html .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
            $html .= "<p class=\"user-name\">No User</p>";
        }

        $html .= " </div>";
        $html .= " <span class=\"line\"></span>";

        return $html;
    }


    /**
     * Get the mlm hands for tree
     *
     * @return array
     */
    public function getHands()
    {
        return ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    }

    /**
     * Check the user is in my tree or not
     *
     * @param object $user
     * @return bool
     */
    protected function checkMyTree($user)
    {
        $topUser = User::where('id', $user->pos_id)->first(['id', 'pos_id']);
        if (!$topUser) {
            abort(401);
        }
        if ($topUser->id == auth()->user()->id) {
            return true;
        }
        $this->checkMyTree($topUser);
    }

    /**
     * Plan subscribe logic
     *
     * @return void
     */
    public function purchasePlanBKP()
    {
        $user = $this->user;
        $plan = $this->plan;
        $trx = $this->trx;

        $oldPlan = $user->plan_id;
        $user->plan_id = $plan->id;
        $user->balance -= $plan->price;
        $user->total_invest += $plan->price;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $plan->price;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased ' . $plan->name;
        $transaction->remark = 'purchased_plan';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->save();

        notify($user, 'PLAN_PURCHASED', [
            'plan_name' => $plan->name,
            'price' => showAmount($plan->price),
            'trx' => $transaction->trx,
            'post_balance' => showAmount($user->balance)
        ]);

        if ($oldPlan == 0) {
            $this->updatePaidCount($user->id);
        }

        if ($plan->bv) {
            $this->updateBV();
        }

        if ($plan->tree_com > 0) {
            // $this->treeCommission();//TODO: Temporary disabled binary income
        }

        $this->referralCommission($plan->price);
    }

    public function purchasePlan()
    {
        $user = $this->user;
        $plan = $this->plan;
        $trx = $this->trx;

        $oldPlan = $user->plan_id;
        $user->plan_id = $plan->id;
        $user->is_payment = 1;


        // if ($plan->is_bonus_used_on_plan > 0) {
        //     $user->balance -= $plan->plan_price_after_bonus;
        // } else {
        //     $user->balance -= $plan->price;
        // }



        // $user->balance -= $plan->price;
        $user->total_invest += $plan->price;



        // if ($user->is_start_id != 1) {
        //     if (empty($user->user_income_week_start_date) && empty($user->user_income_week_end_date)) {
        //         $carbon_plan_created_date = now()->parse($plan->created_at);
        //         $user->user_income_week_start_date = $carbon_plan_created_date->format('Y-m-d H:i:s');;

        //         $carbon_plan_created_date->addDays(7);
        //         $user->user_income_week_end_date =  $carbon_plan_created_date->format('Y-m-d H:i:s');
        //     }
        // }




        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $plan->price;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased ' . $plan->name;
        $transaction->remark = 'purchased_plan';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->save();

        notify($user, 'PLAN_PURCHASED', [
            'plan_name' => $plan->name,
            'price' => showAmount($plan->price),
            'trx' => $transaction->trx,
            'post_balance' => showAmount($user->balance)
        ]);

        if ($oldPlan == 0) {
            // $this->updatePaidCount($user->id);
            $this->updatePaidCount();
        }

        if ($plan->bv) {
            $this->updateBV();
        }

        if ($plan->tree_com > 0) {
            // $this->treeCommission();//TODO: Temporary disabled binary income
        }

        $this->referralCommission($plan->price);
    }
    public function purchasePlanForSomeone($buy_plan_for_a_different_user, $plan, $trx, $current_user)
    {

        $user = $buy_plan_for_a_different_user;
        $plan = $this->plan;
        $trx = $this->trx;

        $oldPlan = $user->plan_id;
        $user->plan_id = $plan->id;
        // $user->balance -= $plan->price;
        // $user->total_invest += $plan->price;




        if ($user->is_start_id != 1) {
            if (empty($user->user_income_week_start_date) && empty($user->user_income_week_end_date)) {
                $carbon_plan_created_date = now()->parse($plan->created_at);
                $user->user_income_week_start_date = $carbon_plan_created_date->format('Y-m-d H:i:s');;

                $carbon_plan_created_date->addDays(7);
                $user->user_income_week_end_date =  $carbon_plan_created_date->format('Y-m-d H:i:s');
            }
        }




        $user->save();





        $user = $current_user;
        // $plan = $this->plan;
        $trx = $this->trx;

        // $oldPlan = $user->plan_id;
        // $user->plan_id = $plan->id;


        if ($plan->is_bonus_used_on_plan > 0) {
            $user->balance -= $plan->plan_price_after_bonus;
        } else {
            $user->balance -= $plan->price;
        }



        // $user->balance -= $plan->price;
        $user->total_invest += $plan->price;
        $user->save();











        // $user = $buy_plan_for_a_different_user;

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $plan->price;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased ' . $plan->name . ' for userID ' . $buy_plan_for_a_different_user->username;
        $transaction->remark = 'purchased_plan';
        $transaction->trx = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->save();


        notify($user, 'PLAN_PURCHASED', [
            'plan_name' => $plan->name,
            'price' => showAmount($plan->price),
            'trx' => $transaction->trx,
            'post_balance' => showAmount($user->balance)
        ]);

        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $buy_plan_for_a_different_user->id;
        $transaction->amount = $plan->price;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased By ' . $user->username . ' ' . $plan->name;
        $transaction->remark = 'purchased_plan';
        $transaction->trx = $trx;
        $transaction->post_balance = $buy_plan_for_a_different_user->balance;
        $transaction->save();





        if ($oldPlan == 0) {
            $this->updatePaidCountForSomeone($buy_plan_for_a_different_user);
        }

        if ($plan->bv) {
            $this->updateBvForSomeone($plan, $buy_plan_for_a_different_user);
        }

        if ($plan->tree_com > 0) {
            // $this->treeCommission();
            // $this->treeCommissionForSomeone($buy_plan_for_a_different_user);
        }

        // $this->referralCommission($plan->price);
        $this->referralCommissionForSomeone($plan->price, $buy_plan_for_a_different_user);
    }

    public function purchasePlanForSomeoneAsLoan($buy_plan_for_a_different_user, $plan, $trx, $current_user)
    {

        $user = $buy_plan_for_a_different_user;
        $plan = $this->plan;
        $trx = $this->trx;

        $oldPlan = $user->plan_id;
        $user->plan_id = $plan->id;
        // $user->balance -= $plan->price;
        // $user->total_invest += $plan->price;




        if ($user->is_start_id != 1) {
            if (empty($user->user_income_week_start_date) && empty($user->user_income_week_end_date)) {
                $carbon_plan_created_date = now()->parse($plan->created_at);
                $user->user_income_week_start_date = $carbon_plan_created_date->format('Y-m-d H:i:s');;

                $carbon_plan_created_date->addDays(7);
                $user->user_income_week_end_date =  $carbon_plan_created_date->format('Y-m-d H:i:s');
            }
        }




        $user->save();





        // $user = $current_user;
        // $plan = $this->plan;
        $trx = $this->trx;

        // $oldPlan = $user->plan_id;
        // $user->plan_id = $plan->id;


        if ($plan->is_bonus_used_on_plan > 0) {
            $user->balance -= $plan->plan_price_after_bonus;
        } else {
            $user->balance -= $plan->price;
        }



        // $user->balance -= $plan->price;
        $user->total_invest += $plan->price;
        $user->save();











        // $user = $buy_plan_for_a_different_user;

        // $transaction = new Transaction();
        // $transaction->user_id = $user->id;
        // $transaction->amount = $plan->price;
        // $transaction->trx_type = '-';
        // $transaction->details = 'Purchased ' . $plan->name . ' for userID ' . $buy_plan_for_a_different_user->username;
        // $transaction->remark = 'purchased_plan';
        // $transaction->trx = $trx;
        // $transaction->post_balance = $user->balance;
        // $transaction->save();


        // notify($user, 'PLAN_PURCHASED', [
        //     'plan_name' => $plan->name,
        //     'price' => showAmount($plan->price),
        //     'trx' => $transaction->trx,
        //     'post_balance' => showAmount($user->balance)
        // ]);

        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $buy_plan_for_a_different_user->id;
        $transaction->amount = $plan->price;
        $transaction->trx_type = '-';
        $transaction->details = 'Loan Approved to ' . $user->username . ' For Plan ' . $plan->name;
        $transaction->remark = 'purchased_plan';
        $transaction->trx = $trx;
        $transaction->post_balance = $buy_plan_for_a_different_user->balance;
        $transaction->save();


        notify($user, 'PLAN_PURCHASED', [
            'plan_name' => $plan->name,
            'price' => showAmount($plan->price),
            'trx' => $transaction->trx,
            'post_balance' => showAmount($user->balance)
        ]);



        if ($oldPlan == 0) {
            $this->updatePaidCountForSomeone($buy_plan_for_a_different_user);
        }

        if ($plan->bv) {
            // $this->updateBvForSomeone($plan, $buy_plan_for_a_different_user);
            $this->updateBvForSomeoneAsLoan($plan, $buy_plan_for_a_different_user);
        }

        if ($plan->tree_com > 0) {
            // $this->treeCommission();
            // $this->treeCommissionForSomeone($buy_plan_for_a_different_user);
        }

        // $this->referralCommission($plan->price);
        $this->referralCommissionForSomeone($plan->price, $buy_plan_for_a_different_user);
    }



    public static function getUserDownline($positioner, $leg = 'ALL', $status_active = 1, $is_paid = 1, $is_non_defaulter = 1, $specific_response_type = [])
    {

        $members = $tmp = [];
        if ($leg == 'ALL') {
            $left_right = User::where('pos_id', $positioner->id)->get();
        } elseif ($leg == 'LEFT') {
            $left_right = User::where('pos_id', $positioner->id)->where('position', 1)->get();
        } elseif ($leg == 'RIGHT') {
            $left_right = User::where('pos_id', $positioner->id)->where('position', 2)->get();
        } else {
            return $members;
        }

        foreach ($left_right as $uu => $kk) {
            // echo  "<br/>" . $kk->username;
            $members[] = $tmp[] = $kk;
        }


        $tmp1 = [];
        foreach ($tmp as $uu => $kk) {
            $left_right = User::where('pos_id', $kk->id)->get();
            foreach ($left_right as $uu => $kk) {
                // echo  "<br/>" . $kk->username;
                $members[] = $tmp1[] = $kk;
            }
        }


        // $tmp2 = [];
        // foreach ($tmp1 as $uu => $kk) {
        //     $left_right = User::where('pos_id', $kk->id)->get();
        //     foreach ($left_right as $uu => $kk) {
        //         $members[] = $tmp2[] = $kk;
        //     }
        // }

        $ccc = 2;
        while (1) {
            ${"tmp$ccc"} = [];
            foreach (${"tmp" . ($ccc - 1)} as $uu => $kk) {
                $left_right = User::where('pos_id', $kk->id)->get();
                foreach ($left_right as $uu => $kk) {
                    // echo  "<br/>" . $kk->username;
                    $members[] = ${"tmp$ccc"}[] = $kk;
                }
            }

            if (empty(${"tmp$ccc"})) {
                break;
            }
            $ccc++;
        }

        $final_members = [];
        foreach ($members as $md => $ndm) {
            $send_members = true;

            if ($status_active > 0) {
                if ($ndm->status != $status_active) {
                    $send_members = false;
                }
            }
            if ($is_paid === 1) {
                if ($ndm->plan_id > 0) {
                } else {
                    $send_members = false;
                }
            }


            if ($is_non_defaulter === 1) {
                $is_defaulter = DB::table('loan_repayments')
                    ->where('user_id', $ndm->id)
                    ->where('is_emi_paid', 2)
                    ->where('is_active', 1)
                    ->count();

                if ($is_defaulter == 0) {
                    $user_members_defaulter_check = checkUserHasEmiDefaulterInHisTeam($ndm);
                    if ($user_members_defaulter_check['is_user_has_emi_defaulter_member']) {
                        if ($user_members_defaulter_check['number_of_loan_users_has_helped_in_team'] >= 6) {
                        } else {
                            $send_members = false;
                        }
                    }
                } else {
                    $send_members = false;
                }
            } else {
            }





            if ($send_members) {
                $final_members[] = $ndm;
            }
        }


        if (!empty($specific_response_type)) {
            $specific_response_type = array_flip($specific_response_type);
            $specific_response_type = array_fill_keys(array_keys($specific_response_type), '');

            if (isset($specific_response_type['non_defaulters'])) {
                $specific_response_type['non_defaulters'] = [];
            }

            if (isset($specific_response_type['defaulters_total_bv_sum'])) {
                $specific_response_type['defaulters_total_bv_sum'] = 0;
            }

            if (isset($specific_response_type['defaulters_total_count'])) {
                $specific_response_type['defaulters_total_count'] = 0;
            }
            if (isset($specific_response_type['non_defaulters_total_count'])) {
                $specific_response_type['non_defaulters_total_count'] = 0;
            }

            if (isset($specific_response_type['defaulters'])) {
                $specific_response_type['defaulters'] = [];
            }



            if (isset($specific_response_type['defaulters_emi_loan_user_only'])) {
                $specific_response_type['defaulters_emi_loan_user_only'] = [];
            }

            foreach ($final_members as $md => $ndm) {

                $is_defaulted = false;

                $is_defaulter = DB::table('loan_repayments')
                    ->where('user_id', $ndm->id)
                    ->where('is_emi_paid', 2)
                    ->where('is_active', 1)
                    ->count();

                if ($is_defaulter == 0) {
                    // $user_members_defaulter_check = checkUserHasEmiDefaulterInHisTeam($ndm);
                    // if ($user_members_defaulter_check['is_user_has_emi_defaulter_member']) {
                    //     if ($user_members_defaulter_check['number_of_loan_users_has_helped_in_team'] >= 6) {
                    //     } else {
                    //         $is_defaulted = true;
                    //     }
                    // }
                } else {
                    if (isset($specific_response_type['defaulters_emi_loan_user_only'])) {

                        $specific_response_type['defaulters_emi_loan_user_only'][] = $ndm;
                    }
                    $is_defaulted = true;
                }

                if ($is_defaulted) {

                    $ndm->defaulted_emis = DB::table('loan_repayments')
                        ->where('user_id', $ndm->id)
                        ->where('is_emi_paid', 2)
                        ->where('is_active', 1)
                        ->get();

                    if (isset($specific_response_type['defaulters'])) {

                        $specific_response_type['defaulters'][] = $ndm;
                    }

                    if (isset($specific_response_type['defaulters_total_count'])) {

                        $specific_response_type['defaulters_total_count']++;
                    }

                    if (isset($specific_response_type['defaulters_total_bv_sum'])) {




                        $specific_response_type['defaulters_total_bv_sum'] += Plan::where('plan_created_by_user_id', $ndm->id)->sum('price');
                    }
                } else {
                    if (isset($specific_response_type['non_defaulters'])) {

                        $specific_response_type['non_defaulters'][] = $ndm;
                    }
                    if (isset($specific_response_type['non_defaulters_total_count'])) {

                        $specific_response_type['non_defaulters_total_count']++;
                    }
                }
            }

            $final_members = $specific_response_type;
        }



        return ($final_members);
    }
}
