@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $content = getContent('login.content', true);
    @endphp
    <section class="account-section">
        <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="common-form-style login-box p-4 text-white">
                    <h4 class="text-center">{{ __(@$content->data_values->heading) }}</h4>
                    <p class="text-center">{{ __(@$content->data_values->short_details) }}</p>

                    <form class="create-account-form verify-gcaptcha" method="post" action="{{ route('user.login') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">@lang('Email or Username')</label>
                            <input name="username" type="text" value="{{ old('username') }}" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Password')</label>
                            <input id="myInputThree" name="password" type="password" required class="form-control">
                        </div>

                        <x-captcha isCustom="true" />

                        <div class="form-group d-flex justify-content-between flex-wrap">
                           <!--  <div class="form-check">
                                <input class="form-check-input" id="remember" name="remember" type="checkbox">
                                <label class="form-check-label" for="remember">@lang('Remember Me')</label>
                            </div> -->
                            <a class="text-white-50" href="{{ route('user.password.request') }}">@lang('Forgot Password?')</a>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary w-100">@lang('Login')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

<style>
    .bg_img{
        display:none;
    }
/* Fullscreen Background */
.account-section {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
background: linear-gradient(to right, rgba(255, 0, 0, 0), #0f1932);;
}

/* Login Box */
.login-box {
        background: transparent;
    border-radius: 10px;
    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
}

/* Form Styling */
.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.5);
    color: white;
    transition: all 0.3s ease-in-out;
}

/* Input Hover Effect */
.form-control:focus {
    background: rgba(255, 255, 255, 0.2);
    border-color: #007bff;
}

/* Placeholder Styling */
.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

/* Remember Me & Forgot Password */
.form-check-label, .text-white-50 {
    color: rgba(255, 255, 255, 0.8);
}

/* Login Button */
.btn-primary {
    background: #007bff;
    border: none;
    padding: 10px;
    border-radius: 5px;
    font-size: 16px;
    transition: all 0.3s ease-in-out;
}

/* Button Hover */
.btn-primary:hover {
    background: #0056b3;
}
</style>

@endsection
