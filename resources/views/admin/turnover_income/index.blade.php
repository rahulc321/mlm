@extends('admin.layouts.app')

@section('panel')
    <?php
    $months = ['Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>

                                    <th>@lang('Amount')</th>
                                    <th>@lang('Percentage')</th>
                                    <th>@lang('Funds')</th>
                                    <th>@lang('Month/Year')</th>

                                    {{-- <th>@lang('A.Fund')</th>
                                    <th>@lang('C.Fund')</th>
                                    <th>@lang('H.Fund')</th>
                                    <th>@lang('F.Fund')</th> --}}


                                    {{-- <th>@lang('Created')</th> --}}
                                    <th>@lang('Processed')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (!empty($club_generations) && $club_generations->count() > 0)
                                    @forelse($club_generations as $key => $bonus)
                                        <tr>

                                            <td>{{ showAmount($bonus->turnover_amount) }}</td>
                                            <td> {{ showAmount($bonus->percentage_amount_to_distribute) }} <br />
                                                ({{ $bonus->percentage_to_distribute }}% of
                                                {{ showAmount($bonus->turnover_amount) }})
                                            </td>
                                            <td style="text-align: left">
                                                @forelse($bonus->distributed_amount as $gc)
                                                    <strong>{{ $gc->club_name }}</strong>
                                                    {{ showAmount($bonus->amount_allocated_for_each_club) }}<br />
                                                @endforeach
                                                <?php
                                                
                                                ?>
                                                {{-- P.Bonus {{ showAmount($bonus->amount_allocated_for_each_club) }}<br />
                                        A.Fund {{ showAmount($bonus->amount_allocated_for_each_club) }}<br />
                                        C.Fund {{ showAmount($bonus->amount_allocated_for_each_club) }}<br />
                                        H.Fund {{ showAmount($bonus->amount_allocated_for_each_club) }}<br />
                                        F.Fund {{ showAmount($bonus->amount_allocated_for_each_club) }}</td> --}}

                                            <td>{{ $months[$bonus->month - 1] }}/{{ $bonus->year }}</td>
                                            {{-- <td>{{ $bonus->created_at }}</td> --}}
                                            <td>{{ $bonus->is_processed == 1 ? 'Processed' : 'Pending' }}</td>
                                            <td>{{ $bonus->is_disabled == 1 ? 'Inactive' : 'Active' }}</td>
                                            <td>

                                                @if ($bonus->is_processed === 1)
                                                @else
                                                    @if ($bonus->is_disabled === 1)
                                                        {{-- <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                    data-question="@lang('Are you sure to enable this ?')"
                                                    data-action="{{ route('admin.plan.status_turnover_income', $bonus->id) }}">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button> --}}
                                                    @else
                                                        <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                            data-question="@lang('Are you sure to disable this?')"
                                                            data-action="{{ route('admin.plan.disable_turnover_income', $bonus->id) }}">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to delete this plan?')" data-action="{{ route('admin.plan.delete_turnover_income', $bonus->id) }}">
                                                        <i class="la la-trash"></i> Delete
                                                    </button>
                                                @endif



                                                {{-- 
                                                <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip"
                                                    data-id="{{ $bonus->id }}" data-year="{{ $bonus->year }}"
                                                    data-month="{{ $bonus->month }}"
                                                    data-is_disabled="{{ $bonus->is_disabled }}"
                                                    data-amount="{{ $bonus->turnover_amount }}"
                                                    data-original-title="@lang('Edit')" type="button">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button> --}}
                                            </td>


                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ 'No Records' }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ 'No Records' }}</td>
                                    </tr>
                                @endif


                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($club_generations->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($club_generations) @endphp
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
                <form method="post" action="{{ route('admin.plan.generate_turnover_income') }}">
                    @csrf
                    <div class="modal-body">
                        <input class="form-control plan_id" name="id" type="hidden">
                        <div class="row">

                            <div class="form-group col-md-12">
                                <label>Total Turnover Income </label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $general->cur_sym }}</span>
                                    <input class="form-control amount" name="amount" type="number" step="any"
                                        required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Month </label>
                                <div class="input-group">

                                    <select class="form-control month" name="month">
                                        <?php
                                       
    $month=date('n');
    for($m=1;$m<=12;$m++){
    // if($m>=$month){break;}
    ?>
                                        <option value="{{ $m }}">{{ $months[$m - 1] }}</option>
                                        <?php
    
    }
                                        ?>
                                        <!--    <option value="1">Jan</option>
                                                                                                                                    <option value="2">Feb</option>
                                                                                                                                    <option value="3">March</option>
                                                                                                                                    <option value="4">April</option>
                                                                                                                                    <option value="5">May</option>
                                                                                                                                    <option value="6">June</option>
                                                                                                                                    <option value="7">July</option>
                                                                                                                                    <option value="8">August</option>
                                                                                                                                    <option value="9">September</option>
                                                                                                                                    <option value="10">October</option>
                                                                                                                                    <option value="11">November</option>
                                                                                                                                    <option value="12">December</option>-->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Year </label>
                                <div class="input-group">

                                    <select class="form-control year" name="year">
                                        <?php
    $year=date('Y')-1;
    for($y=1;$y<30;$y++){
    ?>
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        <?php
    $year++;
    }
                                        ?>
                                    </select>
                                </div>
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
                    <h5 class="modal-title">@lang('Turnover Income')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.plan.generate_turnover_income') }}">
                    @csrf
                    <div class="modal-body">

                        <input class="form-control plan_id" name="id" type="hidden">
                        <div class="row">

                            <div class="form-group col-md-12">
                                <label>Total Turnover Income </label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $general->cur_sym }}</span>
                                    <input class="form-control" name="amount" type="number" step="any" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Month </label>
                                <div class="input-group">

                                    <select class="form-control month" name="month">
                                        <?php
                                       
    $month=date('n');
    for($m=1;$m<=12;$m++){
    // if($m>=$month){break;}
    ?>
                                        <option value="{{ $m }}">{{ $months[$m - 1] }}</option>
                                        <?php
    
    }
                                        ?>
                                        <!--    <option value="1">Jan</option>
                                                                                                                                    <option value="2">Feb</option>
                                                                                                                                    <option value="3">March</option>
                                                                                                                                    <option value="4">April</option>
                                                                                                                                    <option value="5">May</option>
                                                                                                                                    <option value="6">June</option>
                                                                                                                                    <option value="7">July</option>
                                                                                                                                    <option value="8">August</option>
                                                                                                                                    <option value="9">September</option>
                                                                                                                                    <option value="10">October</option>
                                                                                                                                    <option value="11">November</option>
                                                                                                                                    <option value="12">December</option>-->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Year </label>
                                <div class="input-group">

                                    <select class="form-control year" name="year">
                                        <?php
    $year=date('Y')-1;
    for($y=1;$y<30;$y++){
    ?>
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        <?php
    $year++;
    }
                                        ?>
                                    </select>
                                </div>
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
                modal.find('.month').val($(this).data('month'));
                modal.find('.year').val($(this).data('year'));
                modal.find('.is_disabled').val($(this).data('is_disabled'));
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
