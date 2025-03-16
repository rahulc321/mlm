<div class="dashboard-sidebar" id="dashboard-sidebar">
    <button class="btn-close dash-sidebar-close d-xl-none"></button>
    <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_dark.png')) }}" alt="images">
    </a>
    <div class="bg--lights">
        <div class="profile-info">
            <p class="fs--13px mb-3 fw-bold">@lang('ACCOUNT BALANCE')</p>
            <h4 class="usd-balance text--base mb-2 fs--30">
                {{ showAmount(auth()->user()->balance) }} 
                <sub class="top-0 fs--13px">{{ __($general->cur_text) }}</sub>
            </h4>
            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('user.deposit.index') }}" class="btn btn--base btn--smd d-none">@lang('Deposit')</a>
                <a href="{{ route('user.withdraw') }}" class="btn btn--secondary btn--smd d-none">@lang('Withdraw')</a>
            </div>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('user.home') }}" class="{{ menuActive('user.home') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/dashboard.png') }}" alt="icon"> @lang('Dashboard')
            </a>
        </li>
        <?php

$user_info_curr = auth()->user();
?>
<style>
    .hide_1{
        display:none !important;
    }
</style>
@if($user_info_curr->plan_id==0)
        <li>
            <a href="{{ route('user.my.rstl_depoist') }}" class="{{ menuActive('user.my.rstl_depoist') }} hide_1">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/wallet.png') }}" alt="icon"><strong> @lang('RSTL Apply Loan')</strong>
            </a>
        </li>
        @endif

        @if($user_info_curr->anyLoanGoingOn)
   <li>
            <a href="{{ route('user.my.rstl_closing') }}" class="{{ menuActive('user.my.rstl_closing') }} hide_1">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/wallet.png') }}" alt="icon"><strong> @lang('RSTL Loan Closer')</strong>
            </a>
        </li>        
        {{-- <li>
            <a href="{{ route('user.my.rstl_emi_repayment') }}" class="{{ menuActive('user.my.rstl_emi_repayment') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/wallet.png') }}" alt="icon"><strong> @lang('RSTL EMI Repayment')</strong>
            </a>
        </li>         --}}
        {{-- <li>
            <a href="{{ route('user.plan.bstl_payment') }}" class="{{ menuActive('user.plan.bstl_payment') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/wallet.png') }}" alt="icon"><strong> @lang('BSL Payment')</strong>
            </a>
        </li> --}}
        @endif



        @if($user_info_curr->anyLoanApplied)
     
             <li>
                 <a href="{{ route('user.plan.bstl_payment') }}" class="{{ menuActive('user.plan.bstl_payment') }}">
                     <img src="{{ asset($activeTemplateTrue.'users/images/icon/wallet.png') }}" alt="icon"><strong> @lang('BSL Payment')</strong>
                 </a>
             </li>
             @endif


        <li>
            <a href="{{ route('user.plan.index') }}" class="{{ menuActive('user.plan.index') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Plan')
            </a>
        </li>
        <!-- New changes -->
        <li>
            <a href="" class="{{ menuActive('user.plan.client_form') }} d-none">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Orders')
            </a>
        </li>

        <!-- New changes -->
        <li>
            <a href="{{ route('user.plan.activation') }}" class="{{ menuActive('user.plan.activation') }} hide_1">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Activation')
            </a>
        </li>
        <li>
            <a href="{{ route('user.plan.trading_income') }}" class="{{ menuActive('user.plan.trading_income') }} hide_1">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Trading Income')
            </a>
        </li>

            <!-- Direct Income -->
            <li>
                <a href="{{ route('user.plan.direct_income') }}" class="{{ menuActive('user.plan.direct_income') }} d-none" >
                    <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Direct Income')
                </a>
            </li>

       <!--  <li>
            <a href="{{ route('user.plan.level_income') }}" class="{{ menuActive('user.plan.level_income') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Level Income')
            </a>
        </li> -->
        <!--<li>
            <a href="{{ route('user.plan.turnover_income') }}" class="{{ menuActive('user.plan.turnover_income') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Turnover Income')
            </a>
        </li>-->
        <li>
            <a href="{{ route('user.plan.turnover_income') }}" class="{{ menuActive('user.plan.turnover_income') }} hide_1">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/plans.png') }}" alt="icon"> @lang('Reward Income')
            </a>
        </li>


        <li>
            <a href="{{ route('user.bv.log') }}" class="{{ menuActive('user.bv.log') }} hide_1">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/bv_log.png') }}" alt="icon"> @lang('Bv Log')
            </a>
        </li>
        <li>
            <a href="{{ route('user.my.referral') }}" class="{{ menuActive('user.my.referral') }}">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/referral.png') }}" alt="icon"> @lang('My Teams')
            </a>
        </li>
        <li>
            <a href="{{ route('user.my.team_by_level') }}" class="{{ menuActive('user.my.team_by_level') }} hide_1">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/referral.png') }}" alt="icon"> @lang('My Level Team')
            </a>
        </li>
        <li>
            <a href="{{ route('user.my.team') }}" class="{{ menuActive('user.my.team') }} hide_1">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/referral.png') }}" alt="icon"> @lang('My Team')
            </a>
        </li>
        <li>
            <a href="{{ route('user.binary.tree') }}" class="{{ menuActive('user.binary.tree') }} hide_1">
                <img src="{{ asset($activeTemplateTrue . 'users/images/icon/tree.png') }}" alt="icon"> @lang('My Tree')
            </a>
        </li>
        <li>
            <a href="{{ route('user.deposit.index') }}" class="{{ menuActive('user.deposit*') }} hide_1">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/wallet.png') }}" alt="icon"> @lang('Deposit')
            </a>
        </li>
      
        <li>
            <a href="{{ route('user.withdraw') }}" class="{{ menuActive('user.withdraw*') }} d-none">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/withdraw.png') }}" alt="icon"> @lang('Withdraw')
            </a>
        </li>
        @if ($general->balance_transfer == Status::ENABLE)
            <li>
                <a href="{{ route('user.balance.transfer') }}" class="{{ menuActive('user.balance.transfer') }} d-none">
                    <img src="{{ asset($activeTemplateTrue.'users/images/icon/transfer.png') }}" alt="icon"> @lang('Balance Transfer')
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('user.transactions') }}" class="{{ menuActive('user.transactions') }} d-none">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/transactions.png') }}" alt="icon"> @lang('Transactions')
            </a>
        </li>
        <li>
            <a href="{{ route('ticket.index') }}" class="{{ menuActive(['ticket.index', 'ticket.view', 'ticket.open']) }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/ticket.png') }}" alt="icon"> @lang('Support Ticket')
            </a>
        </li>
        <li>
            <a href="{{ route('user.twofactor') }}" class="{{ menuActive('user.twofactor') }} d-none">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/2fa.png') }}" alt="icon"> @lang('2FA')
            </a>
        </li>
        <li>
            <a href="{{ route('user.profile.setting') }}" class="{{ menuActive('user.profile.setting') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/profile.png') }}" alt="icon"> @lang('Profile')
            </a>
        </li>
        <li>
            <a href="{{ route('user.change.password') }}" class="{{ menuActive('user.change.password') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/password.png') }}" alt="icon"> @lang('Change Password')
            </a>
        </li>
        <li>
            <a href="{{ route('user.logout') }}" class="{{ menuActive('user.logout') }}">
                <img src="{{ asset($activeTemplateTrue.'users/images/icon/logout.png') }}" alt="icon"> @lang('Logout')
            </a>
        </li>
    </ul>
</div>
<style type="text/css">
    .dashboard-sidebar {
    position: fixed;
    top: 0;
    width: 310px;
    height: 100%;
    padding: 20px;
    border-right: 1px solid hsl(var(--border));
    /* background-color: hsl(0deg 32.19% 48.84%); */
    overflow-y: auto;
    background: rgb(60 48 48);
    background: linear-gradient(0deg, rgb(40 54 54) 0%, rgb(193 186 170) 100%);
}

.sidebar-menu li a{

    text-decoration: none;
    color: hsl(0deg 0% 97.64%);
 
}
.sidebar-menu li a.active, .sidebar-menu li a:hover {
    color: hsl(0deg 0% 100%);
    background-color: hsl(var(--base) / 0.1);
}
</style>
