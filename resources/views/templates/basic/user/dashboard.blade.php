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
            <small><i>{{ __($kycInfo->data_values->verification_content) }} <a class="link-color"
                        href="{{ route('user.kyc.form') }}">@lang('Click Here to Verify')</a></i></small>
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
            <small><i>{{ __($kycInfo->data_values->pending_content) }} <a class="link-color"
                        href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a></i></small>
        </p>
    </div>
    @endif

    @if (@$notice->data_values->notice_content != null && !$user->plan_id)
    <div class="card custom--card">
        <div class="card-header">
            <h5>@lang('Information')</h5>
        </div>
        <div class="card-body">
            <p class="card-text fw-bold text-dark">📧 Email: <span class="text-muted">{{ \Auth::user()->email }}</span>
            </p>
            <p class="card-text fw-bold text-dark">📞 Phone: <span class="text-muted">{{ \Auth::user()->mobile }}</span>
            </p>
        </div>
        <hr>
   
    @endif


    <div class="row g-3 mt-3 mb-4 p-4 itemsdash">
    @php
        $cards = [
            ['title' => 'Total Members', 'value' => '1,250', 'gradient' => 'linear-gradient(135deg, #007bff, #6610f2)'],
            ['title' => 'Direct Referrals', 'value' => '150', 'gradient' => 'linear-gradient(135deg, #ff416c, #ff4b2b)'],
            ['title' => 'Team Growth', 'value' => '12.5%', 'gradient' => 'linear-gradient(135deg, #36d1dc, #5b86e5)'],
            ['title' => 'Total Earnings', 'value' => '$8,500', 'gradient' => 'linear-gradient(135deg, #ff9a44, #ff3c83)'],
            ['title' => 'Monthly Payouts', 'value' => '$1,200', 'gradient' => 'linear-gradient(135deg, #16a085, #f4d03f)'],
            ['title' => 'Pending Withdrawals', 'value' => '$300', 'gradient' => 'linear-gradient(135deg, #8e44ad, #c0392b)'],
            ['title' => 'Active Plans', 'value' => '4', 'gradient' => 'linear-gradient(135deg, #ff5722, #ff9800)'],
            ['title' => 'Total Transactions', 'value' => '3,500', 'gradient' => 'linear-gradient(135deg, #bdc3c7, #2c3e50)'],
            ['title' => 'Next Bonus', 'value' => '$500', 'gradient' => 'linear-gradient(135deg, #1abc9c, #2ecc71)'],
            ['title' => 'Rank Progress', 'value' => 'Gold Level', 'gradient' => 'linear-gradient(135deg, #f1c40f, #e67e22)'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-lg rounded-4 text-white" style="background: {{ $card['gradient'] }};">
                <div class="card-body text-center p-4">
                    <h6 class="card-title fw-bold">{{ $card['title'] }}</h6>
                    <p class="card-text fs-4">{{ $card['value'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

</div>

</div>
@endsection


@push('style')
<style>
.itemsdash .col-lg-4 {
    display: flex;
    grid-template-columns: masonry
}

@media (min-width:320px) {
    .itemsdash .col-lg-4 {
        display: block;
        grid-template-columns: masonry
    }

    /* smartphones, iPhone, portrait 480x320 phones */
}

@media (min-width:481px) {
    .itemsdash .col-lg-4 {
        display: block;
        grid-template-columns: masonry
    }

    /* portrait e-readers (Nook/Kindle), smaller tablets @ 600 or @ 640 wide. */
}

@media (min-width:641px) {
    .itemsdash .col-lg-4 {
        display: flex;
        grid-template-columns: masonry
    }

    /* portrait tablets, portrait iPad, landscape e-readers, landscape 800x480 or 854x480 phones */
}

@media (min-width:961px) {
    .itemsdash .col-lg-4 {
        display: flex;
        grid-template-columns: masonry
    }

    /* tablet, landscape iPad, lo-res laptops ands desktops */
}

@media (min-width:1025px) {
    .itemsdash .col-lg-4 {
        display: flex;
        grid-template-columns: masonry
    }

    /* big landscape tablets, laptops, and desktops */
}

@media (min-width:1281px) {
    .itemsdash .col-lg-4 {
        display: flex;
        grid-template-columns: masonry
    }

    /* hi-res laptops and desktops */
}
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