<?php


use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});
// Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Route::get('cron', 'CronController@cron')->name('cron');
Route::get('daily_trading_income', 'CronController@dailyTradingIncome')->name('dailyTradingIncome');
Route::get('super_income', 'CronController@super_income')->name('super_income');
Route::get('club_achievers', 'CronController@club_achievers')->name('daily_club_achievers');
Route::get('club_achievers_distribute_income', 'CronController@club_achievers_distribute_income')->name('club_achievers_distribute_income');
Route::get('process_loan_repayments', 'CronController@processLoanRepayments')->name('process_loan_repayments');
Route::get('generate_loan_repayments_schedule', 'CronController@generateLoanRepaymentsSchedule')->name('generateLoanRepaymentsSchedule');
// Route::get('cron_update_bv_manually', 'CronController@updateBvOfUser')->name('cron_update_bv_manually');
//Route::get('cron_update_plan_loan', 'CronController@givePlanToUser')->name('cron_update_plan_loan');
Route::get('reward_achievers_generator', 'CronController@reward_achievers_generator')->name('reward_achievers_generator');


// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});

Route::controller('SiteController')->group(function () {

    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::post('subscriber', 'subscriberStore')->name('subscribe');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::post('/check/referral', 'CheckUsername')->name('check.referral');

    Route::get('plan', 'plan')->name('plan');

    Route::get('blog', 'blog')->name('blog');
    Route::get('blog/{id}/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');


    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
