@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __('Reward Income') }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-12">

            @if (1)
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            <th>@lang('Designation')</th>
                            <th>@lang('Pair')</th>
                            <th>@lang('Reward')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Status')</th>

                        </tr>
                    </thead>
                    <tbody>

  @forelse($all_clubs as $user_club_data_row)
                       <tr>
                            <td class="">
                                {{$user_club_data_row->designation}}
                            </td>
                            <td class="">
                                {{$user_club_data_row->pair}}
                            </td>
                            <td>${{$user_club_data_row->reward}}</td>
                            
                            @if(isset($user_club_data_row->user_club_status) && isset($user_club_data_row->user_club_status->id))
                            <td>{{$user_club_data_row->user_club_status->created_at}}</td>
                            <td>Acheived</td>
                            
                            @else
                            <td></td>
                            <td></td>
                            @endif
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
            
            @endif
           
        </div>
    </div>
    <br />

</div>
@endsection