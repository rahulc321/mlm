    <div class="modal fade" id="bvInfoModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">@lang('Trading Profit(ROI)')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="text--danger">@lang('When someone buys this plan, He will get ROI as per the Plan package').</span>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark w-100" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="refComInfoModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">@lang('Referral Commission info')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="text--danger">@lang('When Your Direct-Referred/Sponsored  User Subscribe in') <b> @lang('ANY PLAN') </b>,
                        @lang('You will get this amount').</span>
                    <br>
                    <br>
                    <span class="text--success"> @lang('This is the reason You should Choose a Plan With Bigger Referral Commission').</span>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark w-100" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="treeComInfoModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">@lang('Commission to tree info')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="text--danger">@lang('When someone from your below tree matching Business volume this plan, You will get this amount as Tree Commission.   (tree commission )'). </span>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark w-100" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
    @auth
        <div class="modal fade" id="purchaseModal">
            <div class="modal-dialog" role="dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title m-0">@lang('Subscribe to the plan')</h5>
                        <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('user.plan.purchase') }}" method="post">
                        @csrf
                        <input name="amount" type="hidden" value="0">
                        <input name="id" type="hidden">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>@lang('Select Method')</label>
                                <select class="form-control form--control form-select" name="payment_method" required>
                                    <option value="">@lang('Select One')</option>
                                    @if (auth()->user()->balance > 0)
                                        <option value="balance">@lang('Balance - ' . $general->cur_sym . showAmount(auth()->user()->balance))</option>
                                    @endif
                                    @foreach ($gatewayCurrency as $data)
                                        <option data-gateway="{{ $data }}" value="{{ $data->id }}" @selected(old('payment_method') == $data->method_code)>{{ $data->name }}</option>
                                    @endforeach
                                </select>
                                <code class="gateway-info d-none"><span class="rate-info">@lang('Rate'): 1
                                        {{ __($general->cur_text) }} = <span class="gateway-rate"></span> <span
                                            class="method_currency"></span>.</span> @lang('Charge'): <span
                                        class="charge"></span> {{ __($general->cur_text) }}. @lang('Total amount'): <span
                                        class="total"></span> {{ __($general->cur_text) }}. </code>
                                <code class="gateway-limit"></code>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="loginModal" role="dialog" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title m-0">@lang('Confirmation Alert!')</h5>
                        <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span class="text-center">@lang('Please login to subscribe plans.')</span>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark h-auto w-auto" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                        <a class="btn btn--base w-auto" href="{{ route('user.login') }}">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>

    @endauth
    @push('script')
        <script>
            (function($) {
                "use strict";
                $('.subscribeBtn').click(function() {
                    var modal = $('#purchaseModal');
                    var price = $(this).data('amount');
                    var id = $(this).data('id');
                    modal.find('[name=amount]').val(price);
                    modal.find('[name=id]').val(id);
                    modal.modal('show');
                    $('[name=payment_method]').trigger('change');
                });

                $(document).on('change', '[name=payment_method]', function() {
                    var amount = $('[name=amount]').val();
                    if ($(this).val() != 'balance' && amount) {
                        var resource = $('select[name=payment_method] option:selected').data('gateway');
                        var max_amount = parseFloat(resource.max_amount);
                        var min_amount = parseFloat(resource.min_amount);
                        var fixed_charge = parseFloat(resource.fixed_charge);
                        var percent_charge = parseFloat(resource.percent_charge);
                        var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                        $('.charge').text(charge);
                        $('.gateway-rate').text(parseFloat(resource.rate));
                        $('.gateway-info').removeClass('d-none');
                        $('.gateway-limit').addClass('d-none');
                        $('.rate-info').removeClass('d-none');
                        if (resource.currency == '{{ __($general->cur_text) }}') {
                            $('.rate-info').addClass('d-none');
                        }
                        if (amount < min_amount || amount > max_amount) {
                            $('.gateway-limit').text(`${resource.name} cannot process ${amount} {{ $general->cur_text }} due to transaction limit. Gateway limit: ${min_amount} {{ $general->cur_text }} - ${max_amount} {{ $general->cur_text }}`);
                            $('.gateway-info').addClass('d-none');
                            $('.gateway-limit').removeClass('d-none');
                        }
                        $('.method_currency').text(resource.currency);
                        $('.total').text(parseFloat(charge) + parseFloat(amount));
                    } else {
                        $('.gateway-info').addClass('d-none');
                        $('.gateway-limit').addClass('d-none');
                    }
                });
            })(jQuery)
        </script>
    @endpush
