@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">
        <h3 class="mb-2">{{ __('Reward Income') }}</h3>
    </div>
    <div class="row">
        <div class="col-lg-12">

            @if (!blank($user_club_data) || 1)
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
                       <tr>
                            <td class="">
                                Executive         
                            </td>
                            <td class="">
                            25
                            </td>
                            <td>$ 100</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>
                         <tr>
                            <td class="">
                                Ass. Manager         
                            </td>
                            <td class="">
                            50
                            </td>
                            <td>$ 200</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>
                        <tr>
                            <td class="">
                                 Manager         
                            </td>
                            <td class="">
                            100
                            </td>
                            <td>$ 400</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>
                            <tr>
                            <td class="">
                               Zonal  Manager         
                            </td>
                            <td class="">
                            200
                            </td>
                            <td>$ 800</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>
                          <tr>
                            <td class="">
                               Diamond        
                            </td>
                            <td class="">
                            500
                            </td>
                            <td>$ 2000</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>
                          <tr>
                            <td class="">
                              Diamond Star
                            </td>
                            <td class="">
                            1000
                            </td>
                            <td>$ 4000</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>


<tr>
                            <td class="">
                             Sapphire Diamond 
                            </td>
                            <td class="">
                            2000
                            </td>
                            <td>$ 8000</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>




<tr>
                            <td class="">
                             Sapphire Diamond Executive
                            </td>
                            <td class="">
                            4000
                            </td>
                            <td>$ 16000</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>





<tr>
                            <td class="">
                             Ambassador
                            </td>
                            <td class="">
                            8000
                            </td>
                            <td>$ 32000</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>




                        


<tr>
                            <td class="">
                             Black Diamond
                            </td>
                            <td class="">
                            16000
                            </td>
                            <td>$ 64000</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>


                        

<tr>
                            <td class="">
                             Black Diamond Executive
                            </td>
                            <td class="">
                            32000
                            </td>
                            <td>$ 128000</td>
                            <td></td>
                            <td>Pending</td>
                        </tr>

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
    <br />

</div>
@endsection