@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $planContent = getContent('plan.content', true);
    @endphp
    <section class="blog-section padding-top padding-bottom">
        <div class="container">
            <div class="row mb-30-none">

                <div class="col-lg-3 col-md-6 col-sm-10 mb-30">
                    <div class="plan-card bg_img text-center" data-background="https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg" style="background-image: url(&quot;https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg&quot;);">
                        <h4 class="plan-card__title text--base mb-2">Star Package&nbsp;</h4>
                        <div class="price-range mt-5 text-white"> {{(setting('prs_minimum_plan_amount', 1))}} - 499.00
                            USD </div>
                        <ul class="plan-card__features mt-4">
                            <li> Trading Profit : <span class="amount">4</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#bvInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                            <li>
                                Referral Commission : <span class="amount">$5.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#refComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
    
                            <li>
                                Commission To Tree : <span class="amount">$10.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#treeComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                        </ul>
    
                        <button class="custom-button theme mt-3 w-auto text-white" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Subscribe now </button>
    
                    </div>
                </div>
    
    
                <div class="col-lg-3 col-md-6 col-sm-10 mb-30">
                    <div class="plan-card bg_img text-center" data-background="https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg" style="background-image: url(&quot;https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg&quot;);">
                        <h4 class="plan-card__title text--base mb-2">Silver Package</h4>
                        <div class="price-range mt-5 text-white"> 500.00 - 999.00
                            USD </div>
                        <ul class="plan-card__features mt-4">
                            <li> Trading Profit : <span class="amount">5</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#bvInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                            <li>
                                Referral Commission : <span class="amount">$5.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#refComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
    
                            <li>
                                Commission To Tree : <span class="amount">$10.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#treeComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                        </ul>
    
                        <button class="custom-button theme mt-3 w-auto text-white" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Subscribe now </button>
    
                    </div>
                </div>
    
    
                <div class="col-lg-3 col-md-6 col-sm-10 mb-30">
                    <div class="plan-card bg_img text-center" data-background="https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg" style="background-image: url(&quot;https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg&quot;);">
                        <h4 class="plan-card__title text--base mb-2">Gold Package</h4>
                        <div class="price-range mt-5 text-white"> 1,000.00 - 9,999.00
                            USD </div>
                        <ul class="plan-card__features mt-4">
                            <li> Trading Profit : <span class="amount">6</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#bvInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                            <li>
                                Referral Commission : <span class="amount">$5.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#refComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
    
                            <li>
                                Commission To Tree : <span class="amount">$10.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#treeComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                        </ul>
    
                        <button class="custom-button theme mt-3 w-auto text-white" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Subscribe now </button>
    
                    </div>
                </div>
    
                <div class="col-lg-3 col-md-6 col-sm-10 mb-30">
                    <div class="plan-card bg_img text-center" data-background="https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg" style="background-image: url(&quot;https://www.returnsuretrade.com/assets/images/frontend/plan/637da77c41ab91669179260.jpg&quot;);">
                        <h4 class="plan-card__title text--base mb-2">Diamond Package</h4>
                        <div class="price-range mt-5 text-white"> 10,000.00 and Above
                            USD </div>
                        <ul class="plan-card__features mt-4">
                            <li> Trading Profit : <span class="amount">7</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#bvInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                            <li>
                                Referral Commission : <span class="amount">$5.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#refComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
    
                            <li>
                                Commission To Tree : <span class="amount">$10.00</span>
                                <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#treeComInfoModal"><i class="fas fa-question-circle"></i></span>
                            </li>
                        </ul>
    
                        <button class="custom-button theme mt-3 w-auto text-white" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Subscribe now </button>
    
                    </div>
                </div>
    


                {{-- @foreach ($plans as $plan)
                    <div class="col-lg-3 col-md-6 col-sm-10 mb-30">
                        <div class="plan-card bg_img text-center" data-background="{{ asset(getImage('assets/images/frontend/plan/' . @$planContent->data_values->background_image, '700x480')) }}">
                            <h4 class="plan-card__title text--base mb-2">{{ __(@$plan->name) }}</h4>
                            <div class="price-range mt-5 text-white"> {{ showAmount($plan->price) }}  - {{ showAmount($plan->maxprice) }}
                                {{ __($general->cur_text) }} </div>
                            <ul class="plan-card__features mt-4">
                                <li> Trading Profit : <span class="amount">{{ $plan->bv }}</span>
                                    <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#bvInfoModal"><i
                                            class="fas fa-question-circle"></i></span>
                                </li>
                                <li>
                                    @lang('Referral Commission') : <span
                                        class="amount">{{ $general->cur_sym }}{{ showAmount($plan->ref_com) }}</span>
                                    <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#refComInfoModal"><i
                                            class="fas fa-question-circle"></i></span>
                                </li>

                                <li>
                                    @lang('Commission To Tree') : <span
                                        class="amount">{{ $general->cur_sym }}{{ showAmount($plan->tree_com) }}</span>
                                    <span class="icon float-right" data-bs-toggle="modal" data-bs-target="#treeComInfoModal"><i
                                            class="fas fa-question-circle"></i></span>
                                </li>
                            </ul>

                            @auth
                                @if (@auth()->user()->plan->price > $plan->price)
                                    <button class="custom-button theme disabled mt-3 w-auto text-white" type="button">
                                        @lang('Unavailable')
                                    </button>
                                @elseif (auth()->user()->plan_id != $plan->id)
                                    <button class="subscribeBtn custom-button theme mt-3 w-auto text-white" data-amount="{{ getAmount($plan->price) }}" data-id="{{ $plan->id }}" type="button">
                                        @lang('Subscribe Now')
                                    </button>
                                @else
                                    <button class="custom-button btn--success disabled mt-3 w-auto" type="button">
                                        @lang('Cureent Plan')
                                    </button>
                                @endif
                            @else
                                <button class="custom-button theme mt-3 w-auto text-white" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    @lang('Subscribe now')
                                </button>
                            @endauth

                        </div>
                    </div>
                @endforeach --}}
            </div>

            {{-- @if ($plans->hasPages())
                {{ paginateLinks($plans) }}
            @endif --}}
        </div>
    </section>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
    @include($activeTemplate . 'partials.plan_modals')
@endsection
