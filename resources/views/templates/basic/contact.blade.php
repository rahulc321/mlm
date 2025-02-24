@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contact = getContent('contact_us.content', true);
    @endphp
    <div class="contact-info padding-top">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-sm-10 col-md-6 col-lg-4">
                    <div class="contact--item">
                        <div class="contact-item">
                            <div class="contact-thumb">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="contact-content">
                                <h6 class="title">@lang('Email Address')</h6>
                                <ul>
                                    <li>
                                        <a href="Mailto:{{ @$contact->data_values->email_address }}">{{ @$contact->data_values->email_address }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-10 col-md-6 col-lg-4">
                    <div class="contact--item">
                        <div class="contact-item">
                            <div class="contact-thumb">
                                <i class="fa fa-building"></i>
                            </div>
                            <div class="contact-content">
                                <h6 class="title">@lang('Office Address')</h6>
                                <ul>
                                    <li>
                                        {{ __(@$contact->data_values->contact_details) }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-10 col-md-6 col-lg-4">
                    <div class="contact--item">
                        <div class="contact-item">
                            <div class="contact-thumb">
                                <img style="    padding: 8px;" src="data:image/png;base64,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
"/>
                                {{-- <i class="fa fa-phone-square"></i> --}}
                            </div>
                            <div class="contact-content">
                                <h6 class="title">@lang('Phone Number')</h6>
                                <ul>
                                    <li>
                                        <a href="Tel:{{ @$contact->data_values->contact_number }}">{{ @$contact->data_values->contact_number }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="contact-section padding-top padding-bottom">
        <div class="container">
            <div class="row">

                <div class="col-lg-7">

                    <div class="contact-form-wrapper rounded bg-white shadow-sm">
                        <div class="section-header left-style mb-4">
                            <h2 class="title mb-4">{{ __(@$contact->data_values->heading) }}</h2>
                            <p>{{ __(@$contact->data_values->short_details) }}</p>
                        </div>
                        <form class="contact-form verify-gcaptcha" method="post" action="{{ route('contact') }}">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Name')</label>
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Subject')</label>
                                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Email')</label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="form-label" for="name">@lang('Message')</label>
                                    <textarea id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                                </div>

                                <div class="col-lg-12">
                                    <x-captcha isCustom="true" />
                                </div>

                                <div class="col-lg-12 form-group">
                                    <input class="cmn-btn" type="submit" value="@lang('Submit Now')">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <img class="wow slideInRight" src="{{ getImage('assets/images/frontend/contact_us/' . @$contact->data_values->background_image, '650x780') }}" alt="contact">
                </div>
            </div>
        </div>
    </section>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
