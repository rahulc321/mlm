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
                                <th>@lang('Min')</th>
                                <th>@lang('Max')</th>

                                <th>@lang('Percentage')</th>
                                <th>@lang('Days')</th>

                                {{-- <th>@lang('Status')</th> --}}
                                <th>@lang('Action')</th>
                                {{-- <th>@lang('Created')</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bonuses as $key => $bonus)
                            <tr>
                                     
                                <td>{{ showAmount($bonus->min_range)}}</td>
                                <td>{{ showAmount($bonus->max_range)}}</td>
                                <td>{{ $bonus->return_income_percentage }}</td>
                                <td>{{ $bonus->days }}</td>
                             
                                <td>
                                    <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip" data-id="{{ $bonus->id }}" data-min_range="{{ $bonus->min_range }}" data-max_range="{{ $bonus->max_range }}" data-return_income_percentage="{{ ($bonus->return_income_percentage) }}" 
                                    
                                    data-days="{{ (@$bonus->days) }}"

                                    data-original-title="@lang('Edit')" type="button">
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
                <h5 class="modal-title">@lang('Edit Slab')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ route('admin.plan.save_income_slabs') }}">
                @csrf
                <div class="modal-body">
                    <input class="form-control plan_id" name="id" type="hidden">
                    <div class="row">
                      
                       
                        <div class="form-group col-md-6">
                            <label>Min </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control min_range" name="min_range" type="number" step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Max </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input class="form-control max_range" name="max_range" type="number" step="any" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Percentage </label>
                            <div class="input-group"> <span class="input-group-text"><i class="fas fa-user text--gray"></i></span> <input class="form-control return_income_percentage" name="return_income_percentage" type="number" step="any" required> </div>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label>Days </label>
                            <div class="input-group"> <span class="input-group-text"><i class="fas fa-user text--gray"></i></span> <input class="form-control days" name="days" type="number" step="any" required> </div>
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
{{-- <button class="btn btn-sm btn-outline--primary add-plan" type="button">
    <i class="la la-plus"></i>@lang('Add New')
</button> --}}
@endpush

@push('script')
<script>
    "use strict";
    (function($) {
        $('.edit').on('click', function() {
            var modal = $('#edit-plan');
            modal.find('.min_range').val($(this).data('min_range'));
            modal.find('.max_range').val($(this).data('max_range'));
            modal.find('.return_income_percentage').val($(this).data('return_income_percentage'));
            modal.find('.days').val($(this).data('days'));
            // modal.find('.amount').val($(this).data('amount'));

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