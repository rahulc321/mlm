@extends($activeTemplate.'layouts.master')

@section('content')
<style>
    #repayment_id{display:none}
    label[for="repayment_id"]{display:none}
</style>

    <div class="dashboard-inner">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="text-center">{{__($pageTitle)}}</h5>
                    </div>
                    <div class="card-body  ">
                        <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <p class="text-center mt-2">@lang('You have requested') <b class="text--success">{{ showAmount($data['amount'])  }} {{__($general->cur_text)}}</b> , @lang('Please pay')
                                        <b class="text--success">{{showAmount($data['final_amo']) .' '.$data['method_currency'] }} </b> @lang('for successful payment')
                                    </p>
                                    <h4 class="text-center mb-4">@lang('Please follow the instruction below')</h4>

                                    <p class="my-4 text-center">@php echo  $data->gateway->description @endphp</p>

                                </div>

                                <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Pay Now')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        $(document).ready(function(){
            $('#repayment_id').val({{$rstl_loan_repayment_id}});
            $('#repayment_id').attr('data-rstl_loan_repayment_id',{{$rstl_loan_repayment_id}});
        });
        
        $('#repayment_id').val({{$rstl_loan_repayment_id}});
        $('#repayment_id').attr('data-rstl_loan_repayment_id',{{$rstl_loan_repayment_id}});



 $(document).on('change','#payment_mode',function(){
            if($('#payment_mode').val()=='Wallet' || $('#payment_mode').val()=='wallet'){
                $('#utr_number').removeAttr('required');
                $('#utr_number').parent().hide();
                $('#payment_slip').removeAttr('required');
                $('#payment_slip').parent().hide();
                $('#file_payment_slip').removeAttr('required');
                $('#file_payment_slip').parent().hide();
            }else{
                $('#utr_number').attr('required',1);
                $('#utr_number').parent().show();
                $('#payment_slip').attr('required',1);
                $('#payment_slip').parent().show();
                $('#file_payment_slip').attr('required',1);
                $('#file_payment_slip').parent().show();
            }
        });
 $(document).on('change','#mode_of_paymnet',function(){
            if($('#mode_of_paymnet').val()=='Wallet' || $('#mode_of_paymnet').val()=='wallet'){
                $('#utr_number').removeAttr('required');
                $('#utr_number').parent().hide();
                $('#payment_slip').removeAttr('required');
                $('#payment_slip').parent().hide();
            }else{
                $('#utr_number').attr('required',1);
                $('#utr_number').parent().show();
                $('#payment_slip').attr('required',1);
                $('#payment_slip').parent().show();
            }
        });


        


        $(document).ready(function(){
            $('#mode_of_paymnet').trigger('change');
            $('#payment_mode').trigger('change');
            $('#loan_account_number').val({{session()->get("rstl_user_loan_number","")}});
        
        });
        
        $('#loan_account_number').val({{session()->get("rstl_user_loan_number","")}});
        


    })(jQuery);
</script>
@endpush