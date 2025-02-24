<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Searchable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'ver_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    //mlm
    public function userExtra()
    {
        return $this->hasOne(UserExtra::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function loanRepaymentsPending(): Attribute
    {
        return new Attribute(
            get: function () {
                return $loan_repayments_failed = DB::table('loan_repayments')
                    ->where('user_id', $this->id)
                    ->where('is_emi_paid', 2)
                    ->where('is_active', 1)
                    ->count();
            },
        );
    }
    public function anyLoanGoingOn(): Attribute
    {
        return new Attribute(
            get: function () {
                $loan_application = DB::table('loan_applications')
                    ->where('user_id', $this->id)
                    ->where('is_application_approved', 1)
                    ->first();
                if (isset($loan_application->id) && $loan_application->id) {
                    $loan_approve = DB::table('loan_approved')
                        ->where('loan_application_id', $loan_application->id)
                        ->where('is_loan_closed', 0)
                        ->count();
                    if ($loan_approve > 0) {
                        return true;
                    }
                }



                return false;
            },
        );
    }

    public function anyLoanApplied(): Attribute
    {
        return new Attribute(
            get: function () {
                $loan_applications = DB::table('loan_applications')
                    ->where('user_id', $this->id)
                    // ->where('is_application_approved', 1)
                    ->count();

                if ($loan_applications > 0) {
                    return true;
                }




                return false;
            },
        );
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'ref_by');
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        $query->where('ev', Status::NO);
    }

    public function scopeMobileUnverified($query)
    {
        $query->where('sv', Status::NO);
    }

    public function scopeKycUnverified($query)
    {
        $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        $query->where('balance', '>', 0);
    }

    public function scopePaid($query)
    {
        $query->where('plan_id', '!=', 0);
    }
}
