@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <style>
    #orders-table td,
    #orders-table th {
        white-space: nowrap;
        /* Prevent text wrapping */
    }

    .form-group label {
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 8px;
        float: left;
    }
    </style>
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
        <div class="col-lg-12">

            <div class="table-responsive">
                <table class="table--responsive--md table" id="orders-table">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Phone</td>
                            <td>Address</td>
                            <td>Pre-Paid</td>
                            <td>Pending Amt</td>
                            <td>Remarks</td>
                            <td>Status</td>
                            <td>Date/Time</td>
                            <td>Action</td>
                            @if(auth()->user()->user_role == 'user')
                            <td>Order</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $key=>$order)
                        <tr>
                            <td>{{$order->name}}</td>
                            <td>{{$order->phone}}</td>
                            <td>{{$order->address}}</td>
                            <td> - </td>
                            <td class="budget">
                                {{showAmount($order->booking_amt)}}
                            </td>
                            <td>{{$order->remark}}</td>
                            <td>
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

                                <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>


                            </td>
                            <td>{{$order->created_at}}</td>
                            <td>

                                @if(auth()->user()->user_role == 'user')
                                    @if($order->status == 'stop')
                                        <a href="javascript:;" class="stmodel" rel="{{$key}}">Claim</a>
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
                                                            <textarea class="form-control" name="remark"></textarea>
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
                                @endif


                                <!-- Change status fro telecaller side -->
                                @if(auth()->user()->user_role == 'telecaller')
                                <a href="javascript:;" class="stmodel" rel="{{$key}}">Change Status</a>
                                <div class="modal fade" id="status_model{{$key}}" role="dialog" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Change Status</h5>
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

                                                        <div class="form-group col-md-6">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control" required>

                                                                <option value="">Select </option>
                                                                <option value="proceed">Proceed </option>
                                                                <option value="stop">Stop </option>


                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label>@lang('Remark')</label>
                                                            <textarea class="form-control" name="remark"></textarea>
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

                            </td>
                            @if(auth()->user()->user_role == 'user')
                            <td>New Order</td>
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
<x-confirmation-modal />
@endsection

@push('style')
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush

@push('script')
<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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