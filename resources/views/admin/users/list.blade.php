@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Email-Phone')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                            </span>
                                        </td>

                                        <td>
                                            {{ $user->email }}<br>{!!$user->mobile!!}
                                        </td>
                                        <td>
                                            <span class="fw-bold" title="{{ @$user->address->country }}">{{ $user->country_code }}</span>
                                        </td>

                                        <td>
                                            {{ showDateTime($user->created_at) }} <br> {{ diffForHumans($user->created_at) }}
                                        </td>

                                        <td>
                                            <span class="fw-bold">

                                                {{ $general->cur_sym }}{{ showAmount($user->balance) }}
                                            </span>
                                        </td>

                                        <td>
                                            <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.users.detail', $user->id) }}">
                                                <i class="las la-desktop text--shadow"></i> @lang('Details')
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($users->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($users) }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
@endpush
