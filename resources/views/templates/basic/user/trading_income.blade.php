@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __($pageTitle) }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-12">

            @if (!blank($trading_income))
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            <th>@lang('User ID')</th>
                            <th>@lang('Plan Package')</th>
                            <th>@lang('Slab')</th>
                            <th>@lang('Income')</th>
                            <th>@lang('Remaining Income')</th>
                            <th>@lang('Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trading_income as $trading_income_row)
                        <tr>
                            <td class="">
                                {{$trading_income_row->user_id}}
                            </td>
                            <td class="">
                                {{$trading_income_row->plan_package}}
                            </td>
                            <td class="">
                                {{$trading_income_row->slab}}
                            </td>
                            <td class="budget">
                                {{$trading_income_row->income}}
                            </td>
                            <td class="">
                                {{$trading_income_row->remaining_income}}
                            </td>
                            <td class="">
                                {{$trading_income_row->created_at}}
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
            @if ($trading_income->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($trading_income) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection