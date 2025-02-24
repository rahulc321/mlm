@extends($activeTemplate . 'layouts.master')
@section('content')
<style>
.badge {
    border-radius: 18px;
    padding: 2px 15px 3px;
    font-weight: 600;
    font-size: 17px;
}
</style>
<div class="dashboard-inner">
    <div class="mb-4">
        <div class="d-flex justify-content-between">
            <h3 class="mb-2">{{ __($pageTitle) }}</h3>
            <span>
                <button class="btn btn--base btn--smd add-plan" type="button">
                    <i class="la la-plus"></i>@lang('Add New')
                </button>
            </span>
        </div>
    </div>
    <div class="row">

        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET" action="">
                    <div class="input-group">
                        <select name="status" class="form-select">
                            <option value="">@lang('All Statuses')</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                @lang('Pending')</option>
                            <option value="accept" {{ request('status') == 'accept' ? 'selected' : '' }}>@lang('Accept')
                            </option>
                            <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>@lang('Reject')
                            </option>
                            <option value="return" {{ request('status') == 'return' ? 'selected' : '' }}>@lang('Return')
                            </option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                                @lang('Delivered')</option>
                            <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>
                                @lang('Dispatched')</option>
                            <option value="proceed" {{ request('status') == 'proceed' ? 'selected' : '' }}>
                                @lang('Proceed')</option>
                            <option value="stop" {{ request('status') == 'stop' ? 'selected' : '' }}>@lang('Stop')
                            </option>
                            <option value="claim" {{ request('status') == 'claim' ? 'selected' : '' }}>@lang('Claim')
                            </option>
                        </select>
                        <button type="submit" class="btn btn--primary">@lang('Filter')</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="col-lg-12">

            <div class="accordion table--acordion" id="transactionAccordion">
                @forelse($orders as $order)

                @php
                // Map statuses to badge classes
                $statusClasses = [
                'pending' => 'bg-secondary',
                'accept' => 'bg-success',
                'reject' => 'bg-danger',
                'return' => 'bg-warning text-dark',
                'delivered' => 'bg-primary',
                'dispatched' => 'bg-info text-dark',
                'proceed' => 'bg-success',
                'stop' => 'bg-dark',
                'claim' => 'bg-info',
                ];
                @endphp
                <div class="accordion-item transaction-item">
                    <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#c-{{ $loop->iteration }}" aria-expanded="false" aria-controls="c-1">
                            <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                <div class="left">

                                    <div class="content">
                                        <h6 class="trans-title">{{$order->name}}</h6>
                                        <span
                                            class="text-muted font-size--14px mt-2">{{ showDateTime($withdraw->created_at, 'M d Y @g:i:a') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper mt-sm-0 mt-3">
                                <p class="text-muted font-size--14px"><b>#{{$order->id}} : <span
                                            class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst($order->status) }}
                                        </span></b></p>
                            </div>
                            <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                <p><b>{{showAmount($order->booking_amt)}}</b></p>
                            </div>
                        </button>
                    </h2>
                    <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" aria-labelledby="h-1"
                        data-bs-parent="#transactionAccordion">
                        <div class="accordion-body">
                            <ul class="caption-list">
                                <li>
                                    <span class="caption">Phone</span>
                                    <span class="value">{{$order->phone}}</span>
                                </li>
                                <li>
                                    <span class="caption">Email</span>
                                    <span class="value">{{$order->email}}</span>
                                </li>
                                <li>
                                    <span class="caption">Address</span>
                                    <span class="value">{{$order->address}}</span>
                                </li>
                                <li>
                                    <span class="caption">Pre-Paid</span>
                                    <span class="value">{{$order->booking_amt}}</span>
                                </li>

                                <li>
                                    <span class="caption">Pending Amt</span>
                                    <span class="value">{{$order->order_price - $order->booking_amt}}</span>
                                </li>
                                @if($order->tracking_id)
                                <li>
                                    <span class="caption">Tracking Id</span>
                                    <span class="value">{{$order->tracking_id}}</span>
                                </li>
                                @endif

                                <li>
                                    <span class="caption">Remark</span>
                                    <span class="value">{{$order->remark}}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Status')</span>
                                    <span class="value">


                                        <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                </li>
                                @if($order->xcel)
                                <li>
                                    <span class="caption">Regular (Pices) Xcel</span>
                                    <span class="value">
                                        {{$order->xcel}}


                                    </span>
                                </li>

                                @endif


                                @if($order->xxcel)
                                <li>
                                    <span class="caption">Regular (Pices) XXCel</span>
                                    <span class="value">
                                        {{$order->xxcel}}


                                    </span>
                                </li>

                                @endif

                                <li>
                                    <span class="caption">Action</span>
                                    <span class="value">




                                        @if($order->status == 'stop')
                                        <a href="javascript:;" class="stmodel" rel="{{$key}}"><span
                                                class="badge bg-info">Claim</a></a>
                                        <div class="modal fade" id="status_model{{$key}}" role="dialog" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Remark</h5>
                                                        <button class="close" data-bs-dismiss="modal" type="button"
                                                            aria-label="Close">
                                                            <i class="las la-times"></i>
                                                        </button>
                                                    </div>
                                                    <form method="post"
                                                        action="{{ route('user.plan.updateOrderStatus',[$order->id]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <input class="form-control plan_id" name="id" type="hidden">
                                                            <div class="row">

                                                                <div class="form-group col-md-12">
                                                                    <label>@lang('Remark')</label>
                                                                    <input type="hidden" name="status" value="claim">
                                                                    <textarea class="form-control"
                                                                        name="remark"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn--primary w-100 h-45"
                                                                type="submit">@lang('Submit')</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                </li>

                            </ul>
                        </div>
                    </div>
                </div><!-- transaction-item end -->
                @empty
                <div class="accordion-body bg-white text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                </div>
                @endforelse
            </div>

            @if ( $orders->hasPages() )
            <div class="mt-3">
                {{ paginateLinks($withdraws) }}
            </div>
            @endif

        </div>
    </div>
</div>



{{-- APPROVE MODAL --}}
<div id="detailModal" class="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Details')</h5>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <div class="modal-body">
                <ul class="list-group userData">

                </ul>
                <div class="feedback"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add-plan" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Form</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('user.plan.client_form_store') }}">
                @csrf
                <div class="modal-body">
                    <input class="form-control plan_id" name="id" type="hidden">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Phone')</label>
                            <input class="form-control" name="phone" type="text" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Email')</label>
                            <input class="form-control" name="email" type="email" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Address')</label>
                            <input class="form-control" name="address" type="text">
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Booking Amount')</label>
                            <input class="form-control" name="booking_amt" type="number" required value="30">
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('User Code')</label>
                            <input class="form-control" name="user_code" type="text" value="{{\Auth::user()->username}}"
                                readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Regular (Pices) Xcel')</label>
                            <input class="form-control" name="xcel" type="number">
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('Regular (Pices) XXCeL')</label>
                            <input class="form-control" name="xxcel" type="number">
                        </div>
                        <!-- <div class="form-group col-md-6">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" name="remark"></textarea>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('script')
<script>
(function($) {
    "use strict";
    $('.detailBtn').on('click', function() {
        var modal = $('#detailModal');
        var userData = $(this).data('user_data');
        var html = ``;
        userData.forEach(element => {
            if (element.type != 'file') {
                html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
            }
        });
        modal.find('.userData').html(html);

        if ($(this).data('admin_feedback') != undefined) {
            var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
        } else {
            var adminFeedback = '';
        }

        modal.find('.feedback').html(adminFeedback);

        modal.modal('show');
    });
})(jQuery);
</script>

<script>
"use strict";
(function($) {
    $(document).ready(function() {
        // Initialize DataTable


        $('.edit').on('click', function() {
            var modal = $('#edit-plan');
            modal.find('.level').val($(this).data('level'));
            modal.find('.members').val($(this).data('members'));
            modal.find('.income').val($(this).data('income'));
            modal.find('.is_disabled').val($(this).data('is_disabled'));

            modal.find('input[name=id]').val($(this).data('id'));
            modal.modal('show');
        });

        $('.add-plan').on('click', function() {
            var modal = $('#add-plan');
            modal.modal('show');
        });
        $('.stmodel').on('click', function() {
            var id = $(this).attr('rel');

            var modal = $('#status_model' + id);
            modal.modal('show');
        });
    });
})(jQuery);
</script>
@endpush