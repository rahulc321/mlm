@extends($activeTemplate . 'layouts.master')
@section('content')
@php
$kycInfo = getContent('kyc_info.content', true);
$notice = getContent('notice.content', true);
@endphp
<?php error_reporting(0); ?>
<div class="dashboard-inner">
    @if ($user->kv == 0)
    <div class="alert border--info border" role="alert">
        <div class="alert__icon d-flex align-items-center text--info">
            <i class="fas fa-file-signature"></i>
        </div>
        <p class="alert__message">
            <span class="fw-bold">@lang('KYC Verification Required')</span>
            <br>
            <small><i>{{ __($kycInfo->data_values->verification_content) }} <a class="link-color" href="{{ route('user.kyc.form') }}">@lang('Click Here to Verify')</a></i></small>
        </p>
    </div>

    <script>
        var alertList = document.querySelectorAll('.alert');
        alertList.forEach(function(alert) {
            new bootstrap.Alert(alert)
        })
    </script>
    @elseif($user->kv == 2)
    <div class="alert border--warning border" role="alert">
        <div class="alert__icon d-flex align-items-center text--warning">
            <i class="fas fa-user-check"></i>
        </div>
        <p class="alert__message">
            <span class="fw-bold">@lang('KYC Verification Pending')</span>
            <br>
            <small><i>{{ __($kycInfo->data_values->pending_content) }} <a class="link-color" href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a></i></small>
        </p>
    </div>
    @endif

    @if (@$notice->data_values->notice_content != null && !$user->plan_id)
    <div class="card custom--card">
        <div class="card-header">
            <h5>@lang('Notice')</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                {{ __($notice->data_values->notice_content) }}
            </p>
        </div>
    </div>
    @endif

    <div class="row g-3 mt-3 mb-12">
        <div class="col-lg-4">
        <h4 class="mb-2 usd-balance text--base mb-2 fs--30">Bonus : {{ showAmount($user->total_bonus) }} <sub class="top-0 fs--13px">USD</sub></h4>
    
        </div>

    </div>
    <div class="row g-3 mt-3 mb-4 itemsdash">

        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Order Form')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ $total_form }}</h3>
                <div class="widget-lists" style="visibility: hiddenx;">
                    <div class="row">
                    <!--    <div class="col-6">
                            <p class="fw-bold">@lang('Total Plan Purchased')</p>
                            <span>{{ ($user_total_purchased_plans) }}</span>
                        </div>
                       -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Pending Orders')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ $pending_total }}  </h3>
                <div class="widget-lists" style="display:none;">
                    <div class="row">
                        <div class="col-4">
                            <p class="fw-bold">@lang('Submitted')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($submittedDeposit) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Pending')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($pendingDeposit) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Rejected')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($rejectedDeposit) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
         <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Delivered Orders')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ $delivered }} </h3>
                <div class="widget-lists">
                    <div class="row">
                        <!-- <div class="col-4">
                            <p class="fw-bold">@lang('Generated')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($totalGeneratedAmount) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Sent')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($totalSentAmount) }}</span>
                        </div> -->
                        <!-- <div class="col-4">
                            <p class="fw-bold">@lang('Rejected')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($rejectedDeposit) }}</span>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
         
        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Stop Orders')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ $stop }} </h3>
                <div class="widget-lists">
                    <div class="row">
                        <!-- <div class="col-4">
                            <p class="fw-bold">@lang('Submitted')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($submittedDeposit) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Pending')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($pendingDeposit) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Rejected')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($rejectedDeposit) }}</span>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Bonus Income')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ showAmount($totalWithdraw) }} {{ __($general->cur_text) }}</h3>
                <div class="widget-lists">
                    <div class="row">
                        <div class="col-4">
                            <p class="fw-bold">@lang('Silver')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($submittedWithdraw) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Gold')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($pendingWithdraw) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Diamond')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($rejectWithdraw) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Team Referral Commission')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ showAmount($user->total_ref_com) }} {{ __($general->cur_text) }}
                </h3>
                <div class="widget-lists">
                    <div class="row">
                        <div class="col-4">
                            <p class="fw-bold">@lang('Pending')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($pending_total)*$direct_income }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Return')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($return)*$direct_income }}</span>
                        </div>
                       <!--  <div class="col-4">
                            <p class="fw-bold">@lang('Right')</p>
                            <span>{{ $totalRight }}</span>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4" style="display:none">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Invest')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ showAmount($user->total_invest) }} {{ __($general->cur_text) }}
                </h3>
            </div>
        </div>

        <div class="col-lg-4" style="display:none">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Binary Commission')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ showAmount($user->total_binary_com) }}
                    {{ __($general->cur_text) }}
                </h3>
            </div>
        </div>

        <div class="col-lg-4" style="display:none">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total BV')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ $totalBv }}</h3>
                <div class="widget-lists">
                    <div class="row">
                        <div class="col-4">
                            <p class="fw-bold">@lang('Left BV')</p>
                            <span>{{ getAmount($user->userExtra->bv_left) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Right BV')</p>
                            <span>{{ getAmount($user->userExtra->bv_right) }}</span>
                        </div>
                        <div class="col-4">
                            <p class="fw-bold">@lang('Total Bv Cut')</p>
                            <span>{{ getAmount($totalBvCut) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--
        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Left BV')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ getAmount($user->userExtra->bv_left) }}</h3>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Right BV')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ getAmount($user->userExtra->bv_right) }}</h3>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Bv Cut')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ getAmount($totalBvCut) }}</h3>
            </div>
        </div>
-->

        <div class="col-lg-4" style="display:none">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Income')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ showAmount($total_paid_bv+$total_paid_daily_trading_income+$total_paid_level_income+$total_referral_commission) }} {{ __($general->cur_text) }}</h3>
                <div class="widget-lists">
                    <div class="row">
                        <div class="col-6">
                            <p class="fw-bold">@lang('Matching')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($total_paid_bv) }}</span>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold">@lang('Trading')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($total_paid_daily_trading_income) }}</span>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold">@lang('Level')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($total_paid_level_income) }}</span>
                        </div> 
                               <div class="col-6">
                            <p class="fw-bold">@lang('Referral')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($total_referral_commission) }}</span>
                        </div> 
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4" style="display:none">
            <div class="dashboard-widget">
                <div class="d-flex justify-content-between">
                    <h5 class="text-secondary">@lang('Total Defaulted BV')</h5>
                </div>
                <h3 class="text--secondary my-4">{{ showAmount($current_loop_user_left_downline_info['defaulters_total_bv_sum']+$current_loop_user_right_downline_info['defaulters_total_bv_sum']) }} {{ __($general->cur_text) }}</h3>
                <div class="widget-lists">
                    <div class="row">
                        <div class="col-6">
                            <p class="fw-bold">@lang('Left BV')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($current_loop_user_left_downline_info['defaulters_total_bv_sum']) }}</span>
                        </div>
                        
                        <div class="col-6">
                            <p class="fw-bold">@lang('Right BV')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($current_loop_user_right_downline_info['defaulters_total_bv_sum']) }}</span>
                        </div>

                          <div class="col-6">
                            <p class="fw-bold">@lang('Total Left Defaulters Count')</p>
                            <span>{{ ($current_loop_user_left_downline_info['defaulters_total_count']) }}</span>
                        </div>
                        
                        <div class="col-6">
                            <p class="fw-bold">@lang('Total Right Defaulters Count')</p>
                            <span>{{ ($current_loop_user_right_downline_info['defaulters_total_count']) }}</span>
                        </div>

                        
                        <div class="col-12">
                            <p class="fw-boldx">Open <a href="/user/team"><b>My Team</b></a> page to view the defaulters</p>
                            
                        </div>
                        <!-- <div class="col-4">
                            <p class="fw-bold">@lang('Rejected')</p>
                            <span>{{ $general->cur_sym }}{{ showAmount($rejectedDeposit) }}</span>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>







    </div>

    <div class="mb-4">
        <h4 class="mb-2">@lang('Binary Summery')</h4>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table--responsive--md table">
                            <thead>
                                <tr>
                                    <th>@lang('Form Id')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Date & Time')</th>
                                    <th>@lang('Phone Number')</th>
                                     
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayData as $data)
                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td> 
                                    @php
                                    // Map statuses to badge classes
                                    $statusClasses = [
                                    'pending' => 'bg-secondary',
                                    'accept' => 'bg-success',
                                    'reject' => 'bg-danger',
                                    'return' => 'bg-warning text-dark',
                                    'delivered' => 'bg-primary',
                                    'dispatched' => 'bg-info text-dark',
                                    'proceed' => 'bg-success',
                                    'stop' => 'bg-dark',
                                    'claim' => 'bg-info',
                                    ];
                                    @endphp

                                    <span class="badge {{ $statusClasses[$data->status] ?? 'bg-secondary' }}">
                                        {{ ucfirst($data->status) }}
                                    </span>


                                    </td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>{{ $data->phone }}</td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@if(isset($user_members_defaulter_check['members_user_not_paid_emi_in_team_details']) && !empty($user_members_defaulter_check['members_user_not_paid_emi_in_team_details']))
<br/>
    <div class="mb-4">
        <h4 class="mb-2">@lang('Team Loan EMI Defaulters')</h4>
        <p>Please ask your team members to pay their EMIs</p>
        <p><stron><a class="btn btn--base btn--smd" href="{{route('user.balance.transfer')}}">Click here</a></strong> to transfer balance to your team members</p>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table--responsive--md table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('User ID')</th>
                                    <th>@lang('Balance')</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user_members_defaulter_check['members_user_not_paid_emi_in_team_details'] as $udd)
                                <tr>
                                    <td>{{ $udd->fullname }}</td>
                                    <td>{{ $udd->username }}</td>
                                    <td>{{ $udd->balance }}</td>
                                   
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


</div>
@endsection


@push('style')
<style>
.itemsdash .col-lg-4{display:flex;grid-template-columns: masonry
}
@media (min-width:320px)  {.itemsdash .col-lg-4{display:block;grid-template-columns: masonry
} /* smartphones, iPhone, portrait 480x320 phones */ }
@media (min-width:481px)  {.itemsdash .col-lg-4{display:block;grid-template-columns: masonry
} /* portrait e-readers (Nook/Kindle), smaller tablets @ 600 or @ 640 wide. */ }
@media (min-width:641px)  {.itemsdash .col-lg-4{display:flex;grid-template-columns: masonry
} /* portrait tablets, portrait iPad, landscape e-readers, landscape 800x480 or 854x480 phones */ }
@media (min-width:961px)  {.itemsdash .col-lg-4{display:flex;grid-template-columns: masonry
} /* tablet, landscape iPad, lo-res laptops ands desktops */ }
@media (min-width:1025px) {.itemsdash .col-lg-4{display:flex;grid-template-columns: masonry
} /* big landscape tablets, laptops, and desktops */ }
@media (min-width:1281px) {.itemsdash .col-lg-4{display:flex;grid-template-columns: masonry
} /* hi-res laptops and desktops */ }
</style>
@endpush

@push('script')
<script>
/*
    (function($) {
var maxHeight = -1;
$('.itemsdash .dashboard-widget').each(function() {
     console.log($(this).height());
    if ($(this).height() > maxHeight) {
        
        maxHeight = $(this).height();
        console.log(maxHeight);
    }
});
console.log(maxHeight);
$('.itemsdash .dashboard-widget').height(maxHeight);

    })(jQuery);
    */
</script>
@endpush