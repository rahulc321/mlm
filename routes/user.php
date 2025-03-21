<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->group(function () {

    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });
    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});

Route::middleware('auth')->name('user.')->group(function () {
    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');
        Route::post('get-user-name-by-user-id', 'User\UserController@getUserNameByUserID')->name('get_user_name_by_user_id');

        Route::middleware('registration.complete')->namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');
                Route::get('login/history', 'userLoginHistory')->name('login.history');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                Route::get('/transfer', 'indexTransfer')->name('balance.transfer');
                Route::post('/transfer', 'balanceTransfer')->name('balance.transfer.post');
                Route::post('/search-user', 'searchUser')->name('search.user');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');

                Route::get('bv-log', 'bvLog')->name('bv.log');
                Route::get('referrals', 'myReferralLog')->name('my.referral');
                Route::get('team', 'myTeam')->name('my.team');
                Route::get('my-level-team', 'myTeamByLevel')->name('my.team_by_level');
                Route::get('/tree/{user?}', 'binaryTree')->name('binary.tree');


                Route::any('/rstl-deposit', 'rstlDeposit')->name('my.rstl_depoist');
                Route::any('/rstl-deposit-closing', 'rstlDepositClosing')->name('my.rstl_closing');
                Route::any('/rstl-emi-repayment/{repayment_id?}', 'rstlEmiRepayment')->name('my.rstl_emi_repayment');

                Route::middleware('kyc')->group(function () {
                    Route::get('transfer-balance', 'balanceTransfer')->name('balance.transfer');
                    Route::post('transfer-balance', 'transferConfirm');
                });
            });

            Route::controller('PlanController')->prefix('plan')->name('plan.')->group(function () {
                Route::get('/', 'planIndex')->name('index');
                Route::post('/', 'planPurchase')->name('purchase');
                Route::post('/activationTopUpSave', 'activationTopUpSave')->name('activationTopUpSave');
                Route::get('/activation', 'activation')->name('activation');
                Route::get('/trading_income', 'tradingIncome')->name('trading_income');


                Route::get('/direct_income', 'direct_income')->name('direct_income');



                Route::get('/bsl-payment', 'bstlPayment')->name('bstl_payment');
                Route::get('/level-income', 'levelIncome')->name('level_income');
                Route::get('/turnover-income', 'turnoverIncome')->name('turnover_income');
                Route::get('/reward-income', 'turnoverIncome')->name('turnover_income');
                // Route::post('/activation', 'activation')->name('activation');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });


            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });
        });

        // Payment
        Route::middleware('registration.complete')->prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            // Route::any('/rstl', 'rstlDeposit')->name('index_rstl');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
