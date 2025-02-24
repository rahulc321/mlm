@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __($pageTitle) }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-12">

            @if (!blank($directIncome))
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            <th>@lang('User ID')</th>
                            <th>@lang('Income')</th>
                             <th>@lang('Ref By')</th>
                            <th>@lang('Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($directIncome as $direct_imcome)
                        <tr>
                            <td class="">
                                {{$direct_imcome->user_id}}
                            </td>
                            <td class="">
                                {{$direct_imcome->amount}}
                            </td>
                            
                            <td class="">
                                {{$direct_imcome->details}}
                            </td>
                            <td class="">
                                {{$direct_imcome->created_at}}
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
            @if ($directIncome->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($directIncome) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection