@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">

        <h3 class="mb-2">@lang('My Team')


 <a class="btn btn--base btn--smd <?php if ($view_team_position_side == 'LEFT_DEFAULTERS') {
                                                    echo ' view-team-left  view-team-current-side';
                                                } ?>" href="?view_members_side=LEFT_DEFAULTERS"><?php if ($view_team_position_side == 'LEFT_DEFAULTERS') {
                                                                                                                                                                                echo ' <u>';
                                                                                                                                                                            } ?>LEFT DEFAULTERS<?php if ($view_team_position_side == 'LEFT_DEFAULTERS') {
                                                                                                                                                                                                                                                echo ' </u>';
                                                                                                                                                                                                                                            } ?></a>
            <a class="btn btn--base btn--smd <?php if ($view_team_position_side == 'LEFT') {
                                                    echo ' view-team-left  view-team-current-side';
                                                } ?>" href="?view_members_side=LEFT"><?php if ($view_team_position_side == 'LEFT') {
                                                                                                                                                                                echo ' <u>';
                                                                                                                                                                            } ?>Left All<?php if ($view_team_position_side == 'LEFT') {
                                                                                                                                                                                                                                                echo ' </u>';
                                                                                                                                                                                                                                            } ?></a>
            <a class="btn btn--base btn--smd <?php if ($view_team_position_side == 'RIGHT') {
                                                    echo ' view-team-left  view-team-current-side';
                                                } ?>" href="?view_members_side=RIGHT"><?php if ($view_team_position_side == 'RIGHT') {
                                                                                                                                                                                    echo ' <u>';
                                                                                                                                                                                } ?>Right All<?php if ($view_team_position_side == 'RIGHT') {
                                                                                                                                                                                                                                                    echo ' </u>';
                                                                                                                                                                                                                                                } ?></a>

                                                                                                                                                                                                                                                 <a class="btn btn--base btn--smd <?php if ($view_team_position_side == 'RIGHT_DEFAULTERS') {
                                                    echo ' view-team-left  view-team-current-side';
                                                } ?>" href="?view_members_side=RIGHT_DEFAULTERS"><?php if ($view_team_position_side == 'RIGHT_DEFAULTERS') {
                                                                                                                                                                                    echo ' <u>';
                                                                                                                                                                                } ?>RIGHT DEFAULTERS<?php if ($view_team_position_side == 'RIGHT_DEFAULTERS') {
                                                                                                                                                                                                                                                    echo ' </u>';
                                                                                                                                                                                                                                                } ?></a>
        </h3>
    </div>
    <div class="row">

        <div class="col-lg-12">
        </div>


        <div class="col-lg-12">
            <div class="card custom--card">
                @if (!blank($team))
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table--responsive--md table">
                            <thead>
                                <tr>
                                    <th>@lang('Username')</th>

                                    <th>@lang('Position')</th>
                                    <th>@lang('Referrer')</th>
                                    <th>@lang('Place')</th>

                                    <th>@lang('Package')</th>
                                    <th>@lang('Created')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($team as $data)
                                <tr>
                                    <td>{{ $data->username }}<br />

                                        <?php if ($data->firstname) {
                                        ?>
                                            ({{ $data->firstname }} {{ $data->lastname }})
                                            <br />
                                        <?php
                                        } ?>

                                        {{ $data->user_account_status }}
                                    </td>
                                    <td>{{ $data->left_right_position }}</td>
                                    @if(isset($data->under_who_place_user->firstname))
                                    <td>{{$data->under_who_place_user->username}} ({{ $data->under_who_place_user->firstname }} {{ $data->under_who_place_user->lastname }})</td>
                                    @else
                                    <td>{{$data->under_who_place_user->username}}</td>
                                    @endif




                                    <td>L:{{ $data->left_direct_team_member_count}}/ R:{{$data->right_direct_team_member_count }}</td>
                                    <td>T:{{ showAmount($data->total_package_purchased_sum_amount) }}/ R: {{ showAmount($data->user_current_plane_amount_remaining) }}</td>
                                    <td>
                                        @if ($data->created_at != '')
                                        {{ showDateTime($data->created_at) }}
                                        @else
                                        @lang('Not Assign')
                                        @endif
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
                </div>
                @else
                <div class="card-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                </div>
                @endif
                @if ($team->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($team) }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection