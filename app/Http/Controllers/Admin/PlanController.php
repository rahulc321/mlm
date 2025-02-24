<?php

namespace App\Http\Controllers\Admin;

use App\Models\LevelIncomes;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Bonuses;
use App\Models\IncomeSlabs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{

    public function plan()
    {
        $pageTitle = 'Plans';
        $plans = Plan::orderBy('price', 'asc')->paginate(getPaginate());
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function levelIncome()
    {
        $pageTitle = 'Level Income';
        $level_incomes = LevelIncomes::orderBy('level', 'asc')->paginate(getPaginate());
        // $level_incomes = DB::table('level_incomes')
        //     ->where('is_disabled', 0)
        //     ->orderBy('id', 'desc')->paginate(getPaginate());


        // $bonuses = Plan::orderBy('price', 'asc')->paginate(getPaginate());
        return view('admin.level_incomes.index', compact('pageTitle', 'level_incomes'));
    }

    public function saveLevelIncome(Request $request)
    {

        $this->validate($request, [
            'level'              => 'required',
            'members'             => 'required|numeric|min:1',
            'income'             => 'required|numeric|min:0.5',
            'is_disabled'                => 'required|min:0|integer',

        ]);
        $bonuses = new LevelIncomes();
        if ($request->id) {
            $bonuses = LevelIncomes::findOrFail($request->id);
        }

        $bonuses->level             = $request->level;
        $bonuses->members            = $request->members;
        $bonuses->income            = $request->income;
        $bonuses->is_disabled               = $request->is_disabled;

        $bonuses->save();

        $notify[] = ['success', 'Level saved successfully'];
        return back()->withNotify($notify);
    }

    public function bonus()
    {
        $pageTitle = 'Bonus';
        $bonuses = Bonuses::orderBy('id', 'desc')->paginate(getPaginate());

        // $bonuses = Plan::orderBy('price', 'asc')->paginate(getPaginate());
        return view('admin.bonuses.index', compact('pageTitle', 'bonuses'));
    }
    public function incomeSlabs()
    {
        $pageTitle = 'Income Slabs';
        $bonuses = IncomeSlabs::orderBy('id', 'asc')->paginate(getPaginate());

        // $bonuses = Plan::orderBy('price', 'asc')->paginate(getPaginate());
        return view('admin.income_slabs.index', compact('pageTitle', 'bonuses'));
    }

    public function turnoverIncome()
    {
        $pageTitle = 'Turnover Income';
        // $bonuses = Bonuses::orderBy('id', 'desc')->paginate(getPaginate());
        // $bonuses = Bonuses::orderBy('id', 'desc')->paginate(getPaginate());



        // $club_total_distribution_all = DB::table('club_total_distribution')->where('generation_id',  $club_gen_id)->get();
        // foreach ($club_total_distribution_all as $club_total_distribution_all_item) {
        //     $club_data =    DB::table('clubs')->where('id', $club_total_distribution_all_item->club_id)->first();
        //     $club_total_distribution_all_item->club_data =   $club_data;
        // }
        // foreach ($club_total_distribution_all as $club_total_distribution_all_item) {


        //     switch ($club_total_distribution_all_item->club_data->unique_name) {
        //         case 'performance_bonus':

        //             break;
        //     }
        // }

        // dd([]);


        $club_generations = DB::table('club_income_generations')
            ->where('is_deleted', 0)
            ->orderBy('id', 'desc')->paginate(getPaginate());

        foreach ($club_generations as $club_gen_item) {


            $club_generation_amounts_divisions = DB::table('club_total_distribution')->where('generation_id', $club_gen_item->id)->get();
            foreach ($club_generation_amounts_divisions as $club_generation_amounts_divisions_item) {
                $club_name =    DB::table('clubs')->where('id', $club_generation_amounts_divisions_item->club_id)->first();
                $club_generation_amounts_divisions_item->club_name =   $club_name->name;
            }

            $club_gen_item->distributed_amount = $club_generation_amounts_divisions;
            // $club_gen_item->amount_percent_turnover =  return2sureRoundAmount(($club_gen_item->turnover_amount * 10) / 100, 4);;

            // $club_total_distribution = DB::table('club_total_distribution')->paginate(getPaginate());

            // foreach ($club_total_distribution as $club_total_distribution_item) {
            //     // $club_total_distribution_item->club_total_distribution_item = DB::table('club_total_distribution')->where('generation_id', $club_total_distribution_item->generation_id)->get();
            //     $club_generation_amounts = DB::table('club_total_distribution')->where('generation_id', $club_total_distribution_item->generation_id)->get();

            //     foreach ($club_generation_amounts as $cga) {
            //         $club_name =    DB::table('clubs')->where('id', $cga->club_id)->first();
            //         $cga->club_name =   $club_name->name;
            //     }
            //     $club_total_distribution_item->club_total_distribution_item =  $club_generation_amounts;
            // }
        }

        // dd($club_generations);

        // $bonuses = Plan::orderBy('price', 'asc')->paginate(getPaginate());
        // $emptyMessage='No data';
        return view('admin.turnover_income.index', compact('pageTitle', 'club_generations'));
    }
    public function saveBonus(Request $request)
    {

        $this->validate($request, [
            'name'              => 'required',
            'amount'             => 'required|numeric|min:1',
            'participants'             => 'required|numeric|min:1',
            'status'                => 'required|min:0|integer',

        ]);
        $bonuses = new Bonuses();
        if ($request->id) {
            $bonuses = Bonuses::findOrFail($request->id);
        }

        $bonuses->name             = $request->name;
        $bonuses->amount            = $request->amount;
        $bonuses->participants            = $request->participants;
        $bonuses->status               = $request->status;

        $bonuses->save();

        $notify[] = ['success', 'Bonus saved successfully'];
        return back()->withNotify($notify);
    }
    public function saveIncomeSlabs(Request $request)
    {

        $this->validate($request, [

            'min_range'             => 'required|numeric|min:1',
            'max_range'             => 'required|numeric|min:1',
            'return_income_percentage'             => 'required|numeric|min:0.25',
            // 'status'                => 'required|min:0|integer',

        ]);
        $bonuses = new IncomeSlabs();
        if ($request->id) {
            $bonuses = IncomeSlabs::findOrFail($request->id);
        }

        $bonuses->min_range             = $request->min_range;
        $bonuses->max_range            = $request->max_range;
        $bonuses->return_income_percentage            = $request->return_income_percentage;
        $bonuses->days               = @$request->days;

        $bonuses->save();

        $notify[] = ['success', 'Slab saved successfully'];
        return back()->withNotify($notify);
    }

    public function generateTurnoverIncome(Request $request)
    {

        $this->validate($request, [
            // 'name'              => 'required',
            'amount'             => 'required|numeric|min:1',
            // 'participants'             => 'required|numeric|min:1',
            // 'status'                => 'required|min:0|integer',

        ]);

        $amount = $request->get('amount');


        if ($request->id) {
            $bonuses = Bonuses::findOrFail($request->id);
        }


        $datetime = now();


        $is_turnover_already_set = DB::table('club_income_generations')
            ->where('year',  $request->get('year'))
            ->where('month',  $request->get('month'))
            ->where('is_disabled',  0)
            ->count();

        if ($is_turnover_already_set === 1) {

            $notify[] = ['error', 'Turnover income already set'];
            return back()->withNotify($notify);
        }


        $tirs_percentage_of_turnover_income_to_distribute = setting()->get('tirs_percentage_of_turnover_income_to_distribute');

        // $amount_to_distribute = return2sureRoundAmount(($amount * 10) / 100, 4);
        $amount_to_distribute = return2sureRoundAmount(($amount * $tirs_percentage_of_turnover_income_to_distribute) / 100, 4);


        // $user = DB::table('clubs')->where('name', 'John')->first();
        // $clubs = DB::table('clubs')->first();
        $clubs = DB::table('clubs')->get();
        $amount_per_club = return2sureRoundAmount($amount_to_distribute / $clubs->count(), 4);
        // dd($clubs);
        $club_gen_id = DB::table('club_income_generations')->insertGetId(
            [
                // 'action' => 'generation',
                'turnover_amount' => $amount,
                'percentage_to_distribute' => $tirs_percentage_of_turnover_income_to_distribute,
                // 'status' => 1,
                'month' => $request->get('month'),
                'year' => $request->get('year'),
                'amount_allocated_for_each_club' => $amount_per_club,
                'percentage_amount_to_distribute' =>  $amount_to_distribute,
                'created_at' => $datetime->format("Y-m-d H:i:s"),
                'updated_at' => $datetime->format("Y-m-d H:i:s"),
            ]
        );


        foreach ($clubs as $c) {
            $club_total_distribution = DB::table('club_total_distribution')->insertGetId(
                [
                    'club_id' => $c->id,
                    'generation_id' => $club_gen_id,
                    // 'amount' => $amount_to_distribute,
                    'amount' => $amount_per_club,
                    'created_at' => $datetime->format("Y-m-d H:i:s"),
                    'updated_at' => $datetime->format("Y-m-d H:i:s"),
                ]
            );
        }



        //give income

        // $club_total_distribution_all = DB::table('club_total_distribution')->where('generation_id', 10)->get();
        $club_total_distribution_all = DB::table('club_total_distribution')->where('generation_id',  $club_gen_id)->get();
        foreach ($club_total_distribution_all as $club_total_distribution_all_item) {
            $club_data =    DB::table('clubs')->where('id', $club_total_distribution_all_item->club_id)->first();
            $club_total_distribution_all_item->club_data =   $club_data;
        }


        //give income

        // dd($request->all());

        $notify[] = ['success', 'Turnover income generated successfully'];
        return back()->withNotify($notify);
    }

    public function planSave(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required',
            'price'             => 'required|numeric|min:0',
            'maxprice'             => 'required|numeric|min:1',
            'bv'                => 'required|min:0|integer',
            'ref_com'           => 'required|numeric|min:0',
            'tree_com'          => 'required|numeric|min:0',
        ]);

        $plan = new Plan();
        if ($request->id) {
            $plan = Plan::findOrFail($request->id);
        }

        $plan->name             = $request->name;
        $plan->price            = $request->price;
        $plan->maxprice            = $request->maxprice;
        $plan->bv               = $request->bv;
        $plan->ref_com          = $request->ref_com;
        $plan->tree_com         = $request->tree_com;
        $plan->save();

        $notify[] = ['success', 'Plan saved successfully'];
        return back()->withNotify($notify);
    }

    public function disableTurnoverIncome($id)
    {
        DB::table('club_income_generations')
            ->where('id', $id)  // find your user by their email
            ->update(array('is_disabled' => 1));
        $notify[] = ['success', 'Updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Plan::changeStatus($id);
    }

    public function deleteplan($id)
    {
        // return Plan::changeStatus( $id);

        Plan::where('id', $id)->update(['status' => 3]);

        $message       = ' Plan Deleted successfully';


        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function deleteTurnoverIncome($id)
    {
        // return Plan::changeStatus( $id);
        DB::table('club_income_generations')
            ->where('id', $id)  // find your user by their email
            ->update(array('is_deleted' => 1, 'is_disabled' => 1));

        $message       = '  Deleted successfully';


        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }
}
