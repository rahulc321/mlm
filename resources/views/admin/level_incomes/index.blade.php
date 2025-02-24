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
                                <th>@lang('Level')</th>
                                <th>@lang('Members')</th>
                                <th>@lang('Income')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($level_incomes as $key => $bonus)
                            <tr>
                                <td>{{ ($bonus->level) }}</td>
                                <td>{{ ($bonus->members)}}</td>
                                <td>{{ $bonus->income }}</td>
                                <td>
                                    @if ($bonus->is_disabled == 0)
                                    <button class="btn btn-sm btn-outline--success " >
                                        </i> @lang('Active')
                                    </button> 


                                    @else
                                 <button class="btn btn-sm btn-outline--danger ms-1 ">
                                         @lang('Inactive')
                                    </button> 
                                    @endif
                                    <!-- <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to delete this plan?')" data-action="{{ route('admin.plan.deleteplan', $bonus->id) }}">
                                        <i class="la la-trash"></i> Delete
                                    </button> -->
                                    <?php
                                    // if ($bonus->is_disabled == 0) {
                                    //     echo "Active";
                                    // } else {
                                    //     echo "Inactive";
                                    // }

                                    ?></td>
                                
                                <td>
                                    <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip" data-id="{{ $bonus->id }}" data-level="{{ $bonus->level }}" data-members="{{ $bonus->members }}" data-income="{{ ($bonus->income) }}" data-is_disabled="{{ ($bonus->is_disabled) }}" data-original-title="@lang('Edit')" type="button">
                                        <i class="la la-pencil"></i> @lang('Edit')
                                    </button>

                                
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
            @if ($level_incomes->hasPages())
            <div class="card-footer py-4">
                @php echo paginateLinks($level_incomes) @endphp
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
            <form method="post" action="{{ route('admin.plan.save_level_income') }}">
                @csrf
                <div class="modal-body">
                    <input class="form-control plan_id" name="id" type="hidden">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Level')</label>
                            <input class="form-control level" name="level" type="number" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Members </label>
                            <div class="input-group">
                                
                                <input class="form-control members" name="members" type="number" step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Income </label>
                            <div class="input-group"> <span class="input-group-text"><i class="fas fa-user text--gray"></i></span>
                                 <input class="form-control income" name="income" type="number" step="any" required> </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label> Status</label>

                            <select class="form-control is_disabled" name="is_disabled">
                                <option value="0">Active</option>
                                <option value="1">Inactive</option>
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
            <form method="post" action="{{ route('admin.plan.save_level_income') }}">
                @csrf
                <div class="modal-body">

                    <input class="form-control plan_id" name="id" type="hidden">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>@lang('Level')</label>
                            <input class="form-control" name="level" type="number" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Members </label>
                            <div class="input-group">
                                
                                <input class="form-control" name="members" type="number" step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Income </label>
                            <div class="input-group"> <span class="input-group-text"><i class="fas fa-user text--gray"></i></span> 
                                <input class="form-control" name="income" type="number" step="any" required> </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label> Status</label>

                            <select class="form-control" name="is_disabled">
                                <option value="0">Active</option>
                                <option value="1">Inactive</option>
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
    })(jQuery);
</script>
@endpush