@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">

    <?php
if(empty($gatewayCurrency)){

    ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-body text-center">
            <h4 class="text--muted"><i class="far fa-frown"></i> {{ __('Sorry! You are not eligible for the Business Support Loan') }}</h4>
        </div>
        </div>
        </div>


    <?php
}else{
    ?>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between mb-3 flex-wrap gap-1 text-end">
                <h3 class="dashboard-title">@lang('RSTL Apply Loan Closing') <i class="fas fa-question-circle text-muted text--small" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Add funds using our system\'s gateway. The deposited amount will be credited to the account balance.')"></i></h3>
                {{-- <a class="btn btn--base btn--smd" href="{{ route('user.deposit.history') }}">@lang('Deposit History')</a> --}}
            </div>
            <form action="{{ route('user.deposit.insert') }}" method="post">
                @csrf
                <input name="method_code" type="hidden">
                <input name="currency" type="hidden">
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="form-group" >
                            <label class="form-label" style="display: none">@lang('Select Gateway')</label>
                            <select class="form--control form-select" style="display: none" name="gateway" required>
                                {{-- <option value="">@lang('Select One')</option> --}}
                                @foreach ($gatewayCurrency as $data)
                                <option selected data-gateway="{{ $data }}" value="{{ $data->method_code }}" @selected(old('gateway')==$data->method_code)>{{ $data->name }}</option>
                                @endforeach
                            </select>
                            <p class="text--danger limit-error"></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <input class="form-control form--control" readonly name="amount" type="number" value="{{ $loan_repayment->emi_amount }}" step="any" autocomplete="off" required>
                                <span class="input-group-text">{{ $general->cur_text }}</span>
                            </div>
                        </div>
                        <div class="preview-details d-none mt-3">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Limit')</span>
                                    <span><span class="min fw-bold">0</span> {{ __($general->cur_text) }} - <span class="max fw-bold">0</span> {{ __($general->cur_text) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Charge')</span>
                                    <span><span class="charge fw-bold">0</span> {{ __($general->cur_text) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Payable')</span> <span><span class="payable fw-bold"> 0</span>
                                        {{ __($general->cur_text) }}</span>
                                </li>
                                <li class="list-group-item justify-content-between d-none rate-element">

                                </li>
                                <li class="list-group-item justify-content-between d-none in-site-cur">
                                    <span>@lang('In') <span class="method_currency"></span></span>
                                    <span class="final_amo fw-bold">0</span>
                                </li>
                                <li class="list-group-item justify-content-center crypto_currency d-none">
                                    <span>@lang('Conversion with') <span class="method_currency"></span>
                                        @lang('and final value will Show on next step')</span>
                                </li>
                            </ul>
                        </div>
                        <button class="btn btn--base w-100 submitBtn mt-3" type="submit">@lang('Submit')</button>
                    </div>
                </div>
                <br />

            </form>
        </div>


        <div>
            <div class="card custom--card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6 col-md-8 mb-8">
                            <div class="card custom--card dashboard-plan-card">
                                <div class="card-body">
                                    <div class="pricing-table mb-4 text-center">
                                        <h3 class="package-name mb-2 text-center">
                                            <strong>USDT Deposit Address</strong>
                                        </h3>
                                        <img src="{{ getImage('assets/images/payments/qr.png')}}" alt="QR" style="width: 80%;">
                                        <hr>
                                        <ul class="package-features-list mt-3">
                                            <li>

                                                <span>Network</span>

                                            </li>

                                        </ul>
                                        <h6 class="package-name mb-2 text-center">
                                            <strong> TRON(TRC20)</strong>
                                        </h6>
                                        <!-- <span class="price text--dark fw-bold d-block">
                                            TRON(TRC20)
                                        </span> -->
                                        <hr>
                                        <h6 class="package-name mb-2 text-center">
                                            <strong>TEoQrSdzDfv4VgJ42E4RFUjyDMVdw6AGXB</strong>
                                        </h6>
                                        
                                    </div>

                                </div>

                            </div><!-- card end -->
                        </div>

                        <div class="col-xl-6 col-md-6 mb-6">
                            <div class="card custom--card dashboard-plan-card">
                                <div class="card-body">
                                <div class="pricing-table mb-4 text-center">
                                        <h3 class="package-name mb-2 text-center">
                                            <strong>Bank Details</strong>
                                        </h3>
                                        <img src="{{ getImage('assets/images/payments/qr2rsindia.jpg')}}" alt="QR" style="width: 80%;">
                                        <hr>
                                        <ul class="package-features-list mt-3">
                                            <li>

                                                <!-- <span>Network</span> -->

                                            </li>

                                        </ul>
                                        <h6 class="package-name mb-2 text-center">
                                            <strong> 3rd Party Vendor For INR Deposit</strong>
                                        </h6>
                                        <!-- <span class="price text--dark fw-bold d-block">
                                            TRON(TRC20)
                                        </span> -->
                                        <!-- <hr> -->
                                        <!-- <h6 class="package-name mb-2 text-center">
                                            <strong>TEoQrSdzDfv4VgJ42E4RFUjyDMVdw6AGXB</strong>
                                        </h6> -->
                                        
                                    </div>

                                    <!-- <button class="btn btn--success w-100 disabled mt-2" type="button">
                                        Cureent Plan </button> -->
                                </div>

                            </div><!-- card end -->
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
    <?php
}

?>


</div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        

        $('select[name=gateway]').change(function() {
            if (!$('select[name=gateway]').val()) {
                $('.preview-details').addClass('d-none');
                return false;
            }
            var resource = $('select[name=gateway] option:selected').data('gateway');
            var max_amount = parseFloat(resource.max_amount);
            var min_amount = parseFloat(resource.min_amount);
            var fixed_charge = parseFloat(resource.fixed_charge);
            var percent_charge = parseFloat(resource.percent_charge);
            var rate = parseFloat(resource.rate)
            if (resource.method.crypto == 1) {
                var toFixedDigit = 8;
                $('.crypto_currency').removeClass('d-none');
            } else {
                var toFixedDigit = 2;
                $('.crypto_currency').addClass('d-none');
            }
            $('.min').text(parseFloat(resource.min_amount).toFixed(2));
            $('.max').text(parseFloat(resource.max_amount).toFixed(2));
            var amount = parseFloat($('input[name=amount]').val());
            if (!amount) {
                amount = 0;
            }
            if (amount <= 0) {
                $('.preview-details').addClass('d-none');
                return false;
            }
            if (amount < min_amount || amount > max_amount) {
                $('.submitBtn').prop('disabled', true);
                $('.limit-error').text(`@lang('Deposit amount crossed gateway limit')`)
            } else {
                $('.submitBtn').prop('disabled', false);
                $('.limit-error').text('')
            }
            $('.preview-details').removeClass('d-none');
            var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
            $('.charge').text(charge);
            var payable = parseFloat((parseFloat(amount) + parseFloat(charge))).toFixed(2);
            $('.payable').text(payable);
            var final_amo = (parseFloat((parseFloat(amount) + parseFloat(charge))) * rate).toFixed(
                toFixedDigit);
            $('.final_amo').text(final_amo);
            if (resource.currency != '{{ $general->cur_text }}') {
                var rateElement =
                    `<span class="fw-bold">@lang('Conversion Rate')</span> <span><span  class="fw-bold">1 {{ __($general->cur_text) }} = <span class="rate">${rate}</span>  <span class="method_currency">${resource.currency}</span></span></span>`;
                $('.rate-element').html(rateElement)
                $('.rate-element').removeClass('d-none');
                $('.in-site-cur').removeClass('d-none');
                $('.rate-element').addClass('d-flex');
                $('.in-site-cur').addClass('d-flex');
            } else {
                $('.rate-element').html('')
                $('.rate-element').addClass('d-none');
                $('.in-site-cur').addClass('d-none');
                $('.rate-element').removeClass('d-flex');
                $('.in-site-cur').removeClass('d-flex');
            }
            $('.method_currency').text(resource.currency);
            $('input[name=currency]').val(resource.currency);
            $('input[name=method_code]').val(resource.method_code);
            $('input[name=amount]').on('input');
        });
        $('input[name=amount]').on('input', function() {
            $('select[name=gateway]').change();
            $('.amount').text(parseFloat($(this).val()).toFixed(2));
        });

        $('select[name=gateway]').trigger('change');
        $('select[name=gateway]').change();;
    })(jQuery);
</script>
@endpush