@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30 " id="page-app-settings">

        <!-- @ include('app_settings::_settings') -->

        <div class="col-lg-12 col-md-12 mb-30">
            @includeIf(config('app_settings.flash_partial'))
        </div>

        <form method="post" action="{{ config('app_settings.url') }}" class="form-horizontal mb-3"
            enctype="multipart/form-data" role="form">
            {!! csrf_field() !!}




                        @if (isset($settingsUI) && count($settingsUI))
                            @foreach (Arr::get($settingsUI, 'sections', []) as $section => $fields)

            <div class="col-lg-12 col-md-12 mb-30">
                <div class="card">
                    <div class="card-body">


                                @component('app_settings::section', compact('fields'))
                                    <div
                                        class="{{ Arr::get($fields, 'section_body_class', config('app_settings.section_body_class', 'card-body')) }}">
                                        @foreach (Arr::get($fields, 'inputs', []) as $field)
                                            @if (!view()->exists('app_settings::fields.' . $field['type']))
                                                <div
                                                    style="background-color: #f7ecb5; box-shadow: inset 2px 2px 7px #e0c492; border-radius: 0.3rem; padding: 1rem; margin-bottom: 1rem">
                                                    Defined setting <strong>{{ $field['name'] }}</strong> with
                                                    type <code>{{ $field['type'] }}</code> field is not supported.
                                                    <br>
                                                    You can create a
                                                    <code>fields/{{ $field['type'] }}.balde.php</code> to render
                                                    this input however you want.
                                                </div>
                                            @endif
                                            @includeIf('app_settings::fields.' . $field['type'])
                                        @endforeach
                                    </div>
                                @endcomponent


                    </div>
                </div>
            </div>

                            @endforeach
                        @endif





            <div class="row m-b-md">
                <div class="col-md-12">
                    <button class="btn btn--primary w-100 h-45">
                        {{ Arr::get($settingsUI, 'submit_btn_text', 'Save Settings') }}
                    </button>
                </div>
            </div>
        </form>




    </div>
@endsection

@push('style')
    <style>
        #page-app-settings .card-body {}

        #page-app-settings .alert {
            padding: 12px;

        }
    </style>
@endpush
