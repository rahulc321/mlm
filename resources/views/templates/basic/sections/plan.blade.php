@php
$planContent = getContent('plan.content', true);
$plans = \App\Models\Plan::where('status', Status::ENABLE)->get();
$gatewayCurrency = \App\Models\GatewayCurrency::whereHas('method', function ($gate) {
$gate->where('status', Status::ENABLE);
})
->with('method')
->orderby('name')
->get();
@endphp
<section class="pricing-section padding-bottom padding-topx">
    <div class="container">
        <div class="section-header">
            <h2 class="title">{{ __(@$planContent->data_values->heading) }}</h2>
            <p>{{ __(@$planContent->data_values->sub_heading) }}</p>
        </div>

        <div class="row justify-content-center mb-30-none">

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



        </div>

    </div>
</section>

@include($activeTemplate . 'partials.plan_modals')