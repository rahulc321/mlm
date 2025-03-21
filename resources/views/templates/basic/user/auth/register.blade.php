@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
        $content = getContent('register.content', true);
    @endphp
    <section class="account-section padding-bottom padding-top">
        <div class="container">
            <div class="account-wrapper">
                <div class="login-area account-area">
                    <div class="row m-0">
                        <div class="col-lg-4 p-0">
                            <div class="change-catagory-area bg_img" data-background="{{ getImage('assets/images/frontend/register/' . @$content->data_values->background_image, '450x970') }}">
                                <h4 class="title">@lang('Welcome To') {{ __($general->site_name) }}</h4>
                                <p>@lang('Already have an account?')</p>
                                <a class="custom-button account-control-button" href="{{ route('user.login') }}">@lang('Login')</a>
                            </div>
                        </div>
                        <div class="col-lg-8 p-0">
                            <div class="common-form-style bg-one create-account">
                                <h4 class="title">{{ __(@$content->data_values->heading) }}</h4>
                                <p class="mb-sm-4 mb-3">{{ __(@$content->data_values->short_details) }}</p>
                                <form class="create-account-form row verify-gcaptcha" method="post" action="{{ route('user.register') }}">
                                    @csrf
                                    @if ($refUser == null)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Referral Username')</label>
                                                <input class="referral ref_id" name="referral" type="text" value="{{ old('referral') }}" autocomplete="off" required>
                                                <div id="ref"></div>
                                                <span id="referral"></span>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Place ID')</label>
                                                <input class="referral_place_id place_id" name="place_id" type="text" value="{{ old('place_id') }}" autocomplete="off" required>
                                                <div id="place_id_msg_1"></div>
                                                <span id="place_id_msg_2"></span>
                                            </div>
                                        </div> -->
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Position')</label>
                                                <select class="position" id="position" name="position" required disabled>
                                                    <option value="">@lang('Select position')*</option>
                                                    @foreach (mlmPositions() as $k => $v)
                                                        <option value="{{ $k }}">{{ __($v) }}</option>
                                                    @endforeach
                                                </select>
                                                <span id="position-test"><span
                                                        class="text--danger"></span></span>
                                            </div>
                                        </div> -->
                                    @else
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Referral Username')</label>
                                                <input class="referral" name="referral" type="text" value="{{ $refUser->username }}" required readonly>
                                            </div>
                                            <input name="referrer_id" type="hidden" value="{{ $refUser->id }}">
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Position')</label>
                                                <select class="position" id="position" required disabled>
                                                    <option value="">@lang('Select position')*</option>
                                                    @foreach (mlmPositions() as $k => $v)
                                                        <option value="{{ $k }}" @if ($pos == $k) selected @endif>{{ __($v) }}</option>
                                                    @endforeach
                                                </select>
                                                <input name="position" type="hidden" value="{{ $pos }}">
                                                <strong class='text--success'>@lang('Your are joining under') {{ $joining }} @lang('at')
                                                    {{ $position }} </strong>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <?php //$userId_gen = '2RS'.random_int(100000, 999999); ?>
                                            <label class="form-label">@lang('Username')</label>
                                            <input class="checkUser" name="username" type="text" value="{{ old('username',$userId_gen) }}" required readonly>
                                            <small class="text--danger usernameExist"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Email')</label>
                                            <input class="checkUser" name="email" type="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Country')</label>
                                            <select name="country">
                                                @foreach ($countries as $key => $country)
                                                    <option data-mobile_code="{{ $country->dial_code }}" data-code="{{ $key }}" value="{{ $country->country }}">
                                                        {{ __($country->country) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Mobile')</label>
                                            <div class="input-group">
                                                <span class="input-group-text mobile-code">

                                                </span>
                                                <input name="mobile_code" type="hidden">
                                                <input name="country_code" type="hidden">
                                                <input class="form-control" name="mobile" type="text" value="{{ old('mobile') }}" placeholder="@lang('Your Phone Number')" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Password')</label>
                                            <input name="password" type="password" required>
                                            @if ($general->secure_password)
                                                <div class="input-popup">
                                                    <p class="error lower">@lang('1 small letter minimum')</p>
                                                    <p class="error capital">@lang('1 capital letter minimum')</p>
                                                    <p class="error number">@lang('1 number minimum')</p>
                                                    <p class="error special">@lang('1 special character minimum')</p>
                                                    <p class="error minimum">@lang('6 character password')</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Confirm password')</label>
                                            <input name="password_confirmation" type="password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <x-captcha isCustom="true" />
                                    </div>

                                    @if ($general->agree)
                                        <div class="col-md-12 mb-3">
                                            <div class="form--check">
                                                <input class="form-check-input" id="agree" name="agree" type="checkbox" @checked(old('agree')) required>
                                                <label class="form-check-label" for="agree">
                                                    @lang('I agree with')
                                                </label>
                                                <span class="ms-1">
                                                    @foreach ($policyPages as $policy)
                                                        <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}" target="_blank">{{ __($policy->data_values->title) }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </span>
                                            </div>

                                        </div>
                                    @endif
                                    <div class="form-group col-md-12">
                                        <input class="w-100" type="submit" value="@lang('Create an Account')">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="existModalCenter" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('You are with us')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="h5 text-center">@lang('You already have an account please Login ')</h5>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark h-auto w-auto" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                    <a class="btn btn--base" href="{{ route('user.login') }}">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .country-code .input-group-text {
            background: #fff !important;
        }

        .country-code select {
            border: none;
        }

        .country-code select:focus {
            border: none;
            outline: none;
        }
    </style>
@endpush
@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
@push('script')
    <script>
        "use strict";
        (function($) {

          
        // Handler for .load() called.
        @if(request()->has('ref'))
        $( window ).on("load", function() {
                $('.ref_id').val('{{request()->get("ref")}}').queue(function(){
                    @if(request()->has('place'))
                
                $('.referral_place_id')
                .val('{{request()->get("place")}}').queue(function(){
                    $('.referral_place_id').trigger('change');
                    $('.referral_place_id').trigger('focusout');
                    // alert("asdas");
                });
                @endif
              
              
                
            });
            });
            

            
           
            
          
               
            
            @endif
          



            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });

            var not_select_msg = $('#position-test').html();

            var positionDetails = null;

            $('.ref_id').on('focusout', function() {

                var ref_id = $('.ref_id').val();

                $('#position').val('');
                            $('#place_id').val('');
                            $('#position-test').text('');
                            $('#place_id_msg_1').text('');
                // $('#position').val('');
                // $('#place_id').val('');
                // $('#place_id').trigger('change');
                // $('#place_id').trigger('focusout');

                if (ref_id) {
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ route('check.referral') }}",
                        data: {
                            'ref_id': ref_id,
                            '_token': token
                        },
                        success: function(data) {
                            
                            if (data.success) {
                                $("#place_id").prop("disabled",false);
                                // $("#position").prop("disabled",false);
                                // $("#position").val("");
                                // $('select[name=position]').removeAttr('disabled');
                                // $('#position-test').text('');
                                $("#ref").html(
                                    `<span class="help-block"><strong class="text--success">@lang('Referrer username matched')</strong></span>`);
                            } else {
                                $("#place_id").val("");
                                $("#position").val("");
                                $("#place_id").prop("disabled",true);
                                
                                $("#position").prop("disabled",true);

                                $('select[name=position]').attr('disabled', true);
                                $('#position-test').html(not_select_msg);
                                $("#ref").html(
                                    `<span class="help-block"><strong class="text--danger">@lang('Referrer username not found')</strong></span>`
                                );
                            }
                            positionDetails = data;
                            // updateHand();
                        }
                    });
                } else {
                    $("#position-test").html(`<span class="help-block"><strong class="text--danger">@lang('Enter referral username first')</strong></span>`);
                }
            });


            $('.place_id').on('focusout', function() {

var ref_id = $('.place_id').val();
$('#position').val('');

if (ref_id) {
    var token = "{{ csrf_token() }}";
    $.ajax({
        type: "POST",
        url: "{{ route('check.referral') }}",
        data: {
            'ref_id': ref_id,
            '_token': token
        },
        success: function(data) {
            if (data.success) {
                $('select[name=position]').removeAttr('disabled');
                $('#position-test').text('');
                $("#place_id_msg_1").html(
                    `<span class="help-block"><strong class="text--success">@lang('Place ID found')</strong></span>`);
            } else {
                $('select[name=position]').attr('disabled', true);
                $('#position-test').html(not_select_msg);
                $("#place_id_msg_1").html(
                    `<span class="help-block"><strong class="text--danger">@lang('Place ID not found')</strong></span>`
                );
            }
            positionDetails = data;
            updateHand();
        }
    });
} else {
    $("#position-test").html(`<span class="help-block"><strong class="text--danger">@lang('Enter place id first')</strong></span>`);
}
});


            $('#position').on('change', function() {
                updateHand();
            });

            function updateHand() {

                // console.log(positionDetails);
                $("#position option[value='1']").prop("disabled",true);
                $("#position option[value='2']").prop("disabled",true);

              var  is_left_or_right_available=false;
                if(positionDetails.position[1]== $('.place_id').val() ){
                    $("#position option[value='1']").prop("disabled",false);
                    is_left_or_right_available=true;
                }

                if(positionDetails.position[2]== $('.place_id').val() ){
                    $("#position option[value='2']").prop("disabled",false);
                    is_left_or_right_available=true;
                }


                var pos = $('#position').val(),
                    className = null,
                    text = null;

                    if(!is_left_or_right_available){
                        className = 'text--danger';
                        text = `@lang('No place available')`;
                    }else{
                        if (pos && positionDetails.success == true) {
                    className = 'text--success';
                    text = `<span class="help-block"><strong class="text--success">Your are joining under ${positionDetails.position[pos]} at ${pos==1?'left':'right'} </strong></span>`;
                } else {
                    className = 'text--danger';
                    if (positionDetails.success == true) text = `@lang('Select your position')`;
                    else if ($('.ref_id').val()) text = `@lang('Please enter a valid referral username')`;
                    else text = `@lang('Enter referral username first')`;

                }
                    }


                
                $("#position-test").html(`<span class="help-block"><strong class="${className}">${text}</strong></span>`)
            }

            function updateHandX() {
                var pos = $('#position').val(),
                    className = null,
                    text = null;
                if (pos && positionDetails.success == true) {
                    className = 'text--success';
                    text = `<span class="help-block"><strong class="text--success">Your are joining under ${positionDetails.position[pos]} at ${pos==1?'left':'right'} </strong></span>`;
                } else {
                    className = 'text--danger';
                    if (positionDetails.success == true) text = `@lang('Select your position')`;
                    else if ($('.ref_id').val()) text = `@lang('Please enter a valid referral username')`;
                    else text = `@lang('Enter referral username first')`;

                }
                $("#position-test").html(`<span class="help-block"><strong class="${className}">${text}</strong></span>`)
            }
            @if (old('position'))
                $(`select[name=position]`).val('{{ old('position') }}');
                $(`select[name=referral]`).change();
            @endif

         
        })(jQuery);
    </script>
@endpush
