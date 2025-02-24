@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table--light style--two table">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Price')</th>

                                <th>@lang('participants')</th>

                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                                <th>@lang('Created')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bonuses as $key => $bonus)
                            <tr>
                                <td>{{ __($bonus->name) }}</td>
                                <td>{{ showAmount($bonus->amount)}}</td>
                                <td>{{ $bonus->participants }}</td>
                                <td><?php
                                    if ($bonus->status == 1) {
                                        echo "Active";
                                    } else {
                                        echo "Inactive";
                                    }

                                    ?></td>
                                <td>{{ $bonus->created_at }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip" data-id="{{ $bonus->id }}" data-name="{{ $bonus->name }}" data-status="{{ $bonus->status }}" data-amount="{{ getAmount($bonus->amount) }}" data-participants="{{ getAmount($bonus->participants) }}" data-original-title="@lang('Edit')" type="button">
                                        <i class="la la-pencil"></i> @lang('Edit')
                                    </button>

                                    @if ($bonus->status == Status::DISABLE)
                                    <!-- <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="@lang('Are you sure to enable this plan?')" data-action="{{ route('admin.plan.status', $bonus->id) }}">
                                        <i class="la la-eye"></i> @lang('Enable')
                                    </button> -->


                                    @else
                                    <!-- <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to disable this plan?')" data-action="{{ route('admin.plan.status', $bonus->id) }}">
                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                    </button> -->
                                    @endif
                                    <!-- <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to delete this plan?')" data-action="{{ route('admin.plan.deleteplan', $bonus->id) }}">
                                        <i class="la la-trash"></i> Delete
                                    </button> -->
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
            @if ($bonuses->hasPages())
            <div class="card-footer py-4">
                @php echo paginateLinks($bonuses) @endphp
            </div>
            @endif
        </div>
    </div>
</div>

{{-- edit modal --}}
<div class="modal fade" id="edit-plan" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Plan')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.plan.save_bonus') }}">
                @csrf
                <div class="modal-body">
                    <input class="form-control plan_id" name="id" type="hidden">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Name')</label>
                            <input class="form-control name" name="name" type="text" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control amount" name="amount" type="number" step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Participants </label>
                            <div class="input-group"> <span class="input-group-text"><i class="fas fa-user text--gray"></i></span> <input class="form-control participants" name="participants" type="number" step="any" required> </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label> Status</label>

                            <select class="form-control status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add-plan" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Bonus')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.plan.save_bonus') }}">
                @csrf
                <div class="modal-body">

                    <input class="form-control plan_id" name="id" type="hidden">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control" name="amount" type="number" step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Participants </label>
                            <div class="input-group"> <span class="input-group-text"><i class="fas fa-user text--gray"></i></span> <input class="form-control" name="participants" type="number" step="any" required> </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label> Status</label>

                            <select class="form-control" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

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

@push('breadcrumb-plugins')
<button class="btn btn-sm btn-outline--primary add-plan" type="button">
    <i class="la la-plus"></i>@lang('Add New')
</button>
@endpush

@push('script')
<script>
    "use strict";
    (function($) {
        $('.edit').on('click', function() {
            var modal = $('#edit-plan');
            modal.find('.name').val($(this).data('name'));
            modal.find('.participants').val($(this).data('participants'));
            modal.find('.status').val($(this).data('status'));
            modal.find('.amount').val($(this).data('amount'));

            modal.find('input[name=id]').val($(this).data('id'));
            modal.modal('show');
        });

        $('.add-plan').on('click', function() {
            var modal = $('#add-plan');
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush