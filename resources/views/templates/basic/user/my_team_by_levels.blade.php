@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner">
    <div class="mb-4">

        <h3 class="mb-2">@lang('My Level Team')

    
        </h3>
    </div>
    <div class="row">

        <div class="col-lg-12">
            

   <h4> <strong> Select Level</strong> <select onchange="window.location.href='?level='+this.value;" style="    display: inline-block;width:100px" name="plan_type" class="form-control form--control" id="level">
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


        <div class="col-lg-12">
            <div class="card custom--card">
                @if (!blank($team))
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table--responsive--md table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Date')</th>

                                  
                                  
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($team as $data)
                                <tr>
                                    <td>{{ $data->fullname }}</td>
                                    <td>{{ $data->email }}</td>
                                    <td>{{ $data->username }}<br />

                                      
                                      
                                            {{ $data->plan_id>0?'Active':'Inactive' }}
                                        

                                      
                                    </td>
                                    <td>{{ $data->created_at }}</td>
                                




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
                @else
                <div class="card-body text-center">
                    <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                </div>
                @endif
                @if ($team->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($team) }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection