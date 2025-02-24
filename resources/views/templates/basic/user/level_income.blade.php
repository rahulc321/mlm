@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __($pageTitle) }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-12">


            <div class="col-lg-12">
            

                <h4> <strong> Select Level</strong> <select onchange="window.location.href='?level='+this.value;" style="    display: inline-block;width:140px" name="plan_type" class="form-control form--control" id="level">
                    <option value="">Select Level</option>
                 <?php
             for ($ilevel=1; $ilevel<=11 ; $ilevel++) { 
             ?>
             <option <?php if($level_selected==$ilevel){ echo " selected ";} ?> value="{{$ilevel}}">{{$ilevel}}</option>
             <?php
             }
                 ?>
                 
                                                     
                                             </select>  </h4> 
                                             <br/>                                                                                                                                                      
             
                     </div>

            @if (!blank($trading_income))
            <div class="table-responsive">
                <table class="table--responsive--md table">
                    <thead>
                        <tr>
                            <th>@lang('From User')</th>
                            
                            
                            
                            <th>@lang('Income')</th>
                            <th>@lang('Level')</th>
                            
                            <th>@lang('Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trading_income as $trading_income_row)
                        <tr>
                            <td class="">
                                {{$trading_income_row->from_user_id}}
                                <br/>{{$trading_income_row->from_user->fullname}}
                            </td>
                            
                            <td class="budget">
                                {{showAmount($trading_income_row->income)}}
                            </td>
                          
                            <td class="">
                                {{$trading_income_row->level}}
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