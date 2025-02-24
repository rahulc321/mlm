@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __($pageTitle) }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-12">

            @if (!blank($user_club_data) || 1)
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            <th>@lang('Club Name')</th>
                            <th>@lang('User ID')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Status')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user_club_data as $user_club_data_row)
                        <tr>
                            <td class="">
                                {{$user_club_data_row->name}}
                            </td>
                            <td class="">
                                {{$user->username}}
                            </td>
                            <td class="">
                                <?php
                                if ($user_club_data_row->club_user_achievement_status == 1) {
                                    echo $user_club_data_row->club_user_achievement_info->created_at;
                                } else {
                                }
                                ?>

                            </td>
                            <td class="budget">
                                <?php
                                if ($user_club_data_row->club_user_achievement_status == 1) {
                                    echo "Achevied";
                                } else {
                                    echo "Pending";
                                }
                                ?>
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
    <br />
    <div class="row">
        <div class="col-lg-12">

            @if (!blank($trading_income) || 1)
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            <th>@lang('User ID')</th>
                            <th>@lang('Club')</th>
                            
                            <th>@lang('Income')</th>
                            
                            <th>@lang('Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trading_income as $trading_income_row)
                        <tr>
                            <td class="">
                                {{$trading_income_row->user->username}}
                            </td>
                            <td class="">
                                {{$trading_income_row->club->name}}
                            </td>
                            <td class="">
                                {{showAmount($trading_income_row->income)}}
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