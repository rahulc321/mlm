@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __($pageTitle) }} <a class="btn btn--base btn--smd" href="/user/deposit/history">Deposit History</a></h3>
    </div>
    <div class="row">
        <div class="col-lg-12">

            @if (!blank($deposits))
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            {{-- <th>@lang('BSL ID')</th> --}}
                            <th>@lang('Fees')</th>
                            <th>@lang('Mode')</th>
                            <th>@lang('UTR')</th>
                            <th>@lang('BSL Amount ')</th>
                            <th>@lang('Slip')</th>
                            <th>@lang('Tenure')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposits_row)
                        <tr>
                            {{-- <td class="">
                                {{$deposits_row->id}}
                            </td> --}}
                            <td class="">
                                {{showAmount($deposits_row->amount)}}
                            </td>
                       
                            
                            <td class="budget">
                                {{$deposits_row->payment_mode}}
                            </td>
                            <td class="">
                                {{$deposits_row->utr}}
                            </td>
                            <td class="">
                                {{showAmount($deposits_row->loan_amount)}}
                            </td>
                            <td class="">
                                {!!($deposits_row->slip)!!}
                            </td>
                            <td class="">
                                {{$deposits_row->tenure}}
                            </td>
                            <td class="">
                                {{$deposits_row->loan_status}}
                            </td>
                            <td class="">
                                {{$deposits_row->created_at }}
                            </td>


                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                        </tr>
                        @endforelse


                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-center">
                <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
            </div>
            @endif
            @if ($deposits->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($deposits) }}
            </div>
            @endif
        </div>
    </div>



<br/>
      <div class="mb-4">
        <h3 class="mb-2">{{ __('BSL EMI Repayment') }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-12">

            @if (!blank($deposits))
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            <th>@lang('BSL ID')</th>
                            <th>@lang('Transaction ID ')</th>
                            {{-- <th>@lang('Mode')</th> --}}
                            
                            <th>@lang('BSL EMI ')</th>
                            
                            
                            <th>@lang('Status')</th>
                            <th>@lang('Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($all_loans_repayments as $deposits_row)
                        <tr>
                        <td class="">
                            {{$deposits_row->loan_id_assigned_to_user}}
                        </td>
                        <td> {{$deposits_row->transaction_id}}</td>
                        {{-- <td></td> --}}
                        
                        <td class="">
                            {{showAmount($deposits_row->emi_amount)}}
                        </td>
                   
                        
                        <td class="budget">
                            {{$deposits_row->emi_paid_status}}
                            <?php
    if($deposits_row->emi_paid_status=='Failed'){
?>
<br/>
<strong><a class="btn btn--base btn--smd" href="{{route('user.my.rstl_emi_repayment',$deposits_row->id)}}">Pay</a></strong>
<?php
    }

?>
                        </td>
                        <td class="">
                            {{$deposits_row->emi_to_be_paid_datetime}}
                        </td>
                       
                    </tr>

                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                        </tr>
                        @endforelse


                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body text-center">
                <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
            </div>
            @endif
            @if ($deposits->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($deposits) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection