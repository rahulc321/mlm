<?php

return [

    // All the sections for the settings page
    'sections' => [
        'plan_related_ettings' => [
            'title' => 'Plan Related Settings',
            'descriptions' => 'Application general settings.', // (optional)
            'icon' => 'fa fa-cog', // (optional)

            'inputs' => [
                [
                    'name' => 'prs_minimum_plan_amount', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Minimum Plan Amount', // label for input
                    // optional properties
                    'placeholder' => 'Minimum Plan Amount', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required|min:1|max:20', // validation rules for this input
                    'value' => '10', // any default value
                    'hint' => 'You can set the minimum plan amount name here' // help block text for input
                ],
                [
                    'name' => 'prs_minimum_plant_update_amount', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Minimum Plan Update Amount', // label for input
                    // optional properties
                    'placeholder' => 'Minimum Plan Update Amount', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required|min:1|max:20', // validation rules for this input
                    'value' => '100', // any default value
                    'hint' => 'You can set the minimum plan update amount name here' // help block text for input
                ],
                // [
                //     'name' => 'logo',
                //     'type' => 'image',
                //     'label' => 'Upload logo',
                //     'hint' => 'Must be an image and cropped in desired size',
                //     'rules' => 'image|max:500',
                //     'disk' => 'public', // which disk you want to upload
                //     'path' => 'app', // path on the disk,
                //     'preview_class' => 'thumbnail',
                //     'preview_style' => 'height:40px'
                // ]
            ]
        ],
        'turnover_income_related_ettings' => [
            'title' => 'Turnover Income Related Settings',
            // 'descriptions' => 'Application general settings.', // (optional)
            'icon' => 'fa fa-cog', // (optional)

            'inputs' => [
                [
                    'name' => 'tirs_percentage_of_turnover_income_to_distribute', // unique key for setting
                    'type' => 'number', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Percentage Of turnover income to distribute', // label for input
                    // optional properties
                    // 'placeholder' => 'Minimum Plan Amount', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required|min:1|max:20', // validation rules for this input
                    'value' => '10', // any default value
                    // 'hint' => 'You can set the minimum plan amount name here' // help block text for input
                ],
                // [
                //     'name' => 'logo',
                //     'type' => 'image',
                //     'label' => 'Upload logo',
                //     'hint' => 'Must be an image and cropped in desired size',
                //     'rules' => 'image|max:500',
                //     'disk' => 'public', // which disk you want to upload
                //     'path' => 'app', // path on the disk,
                //     'preview_class' => 'thumbnail',
                //     'preview_style' => 'height:40px'
                // ]
            ]
        ],
        // 'user_related_settings' => [
        //     'title' => 'User Related Settings',
        //     'descriptions' => '',
        //     'icon' => 'fa fa-envelope',

        //     'inputs' => [
        //         [
        //             'name' => 'urs_minimum_age_for_a_user',
        //             'type' => 'number',
        //             'label' => 'Number of users allowed',
        //             // optional fields
        //             'data_type' => 'int',
        //             'min' => 5,
        //             'max' => 100,
        //             'rules' => 'required|min:5|max:100',
        //             'placeholder' => 'Number of users allowed',
        //             'class' => 'form-control',
        //             'style' => 'color:red',
        //             'value' => 5,
        //             'hint' => 'You can set the number of users allowed to be added.'
        //         ],
        //         // [
        //         //     'name' => 'from_name',
        //         //     'type' => 'text',
        //         //     'label' => 'Email from Name',
        //         //     'placeholder' => 'Email from Name',
        //         // ]
        //     ]
        // ],
        'website_content_related_settings' => [
            'title' => 'Website Content Text Related Settings',
            'descriptions' => '',
            'icon' => 'fa fa-envelope',

            'inputs' => [
                [
                    'name' => 'wcrs_footer_copyright_company_text',
                    'type' => 'text',
                    'label' => 'Footer Copyright Text Company Text',
                    // optional fields
                    // 'data_type' => 'string',
                    // 'min' => 5,
                    // 'max' => 100,
                    'rules' => 'required',
                    // 'placeholder' => 'Number of users allowed',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    'value' => 5,
                    // 'hint' => 'You can set the number of users allowed to be added.'
                ],
                // [
                //     'name' => 'from_name',
                //     'type' => 'text',
                //     'label' => 'Email from Name',
                //     'placeholder' => 'Email from Name',
                // ]
            ]
        ],
        'payment_gateway_related_settings' => [
            'title' => 'Payment Related Settings',
            'descriptions' => '',
            'icon' => 'fa fa-envelope',

            'inputs' => [
                [
                    'name' => 'pgrs_rstl_payment_gateway_id_text',
                    'type' => 'text',
                    'label' => 'Payment Gateway Id to be used on RSTL page',
                    // optional fields
                    // 'data_type' => 'string',
                    // 'min' => 5,
                    // 'max' => 100,
                    'rules' => 'required',
                    // 'placeholder' => 'Number of users allowed',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    // 'value' => 52,
                    // 'hint' => 'You can set the number of users allowed to be added.'
                ],
                [
                    'name' => 'pgrs_rstl_deposit_approved_amount_for_package',
                    'type' => 'text',
                    'label' => 'Plan amount to generate when user deposit is approved',
                    // optional fields
                    // 'data_type' => 'string',
                    // 'min' => 5,
                    // 'max' => 100,
                    'rules' => 'required',
                    // 'placeholder' => 'Number of users allowed',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    // 'value' => 52,
                    // 'hint' => 'You can set the number of users allowed to be added.'
                ],
                [
                    'name' => 'pgrs_rstl_payment_gateway_closing_loan_id_text',
                    'type' => 'text',
                    'label' => 'Payment Gateway Id to be used on RSTL Closing page',
                    // optional fields
                    // 'data_type' => 'string',
                    // 'min' => 5,
                    // 'max' => 100,
                    'rules' => 'required',
                    // 'placeholder' => 'Number of users allowed',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    // 'value' => 52,
                    // 'hint' => 'You can set the number of users allowed to be added.'
                ],
                [
                    'name' => 'pgrs_rstl_payment_gateway_emi_repayment_loan_id_text',
                    'type' => 'text',
                    'label' => 'Payment Gateway Id to be used on RSTL Emi Repayment page',
                    // optional fields
                    // 'data_type' => 'string',
                    // 'min' => 5,
                    // 'max' => 100,
                    'rules' => 'required',
                    // 'placeholder' => 'Number of users allowed',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    'value' => 'Pending EMI',
                    // 'hint' => 'You can set the number of users allowed to be added.'
                ],
                [
                    'name' => 'pgrs_rstl_payment_amount_round_off_limit',
                    'type' => 'text',
                    'label' => 'Payment Digits after decimal point in amounts',
                    // optional fields
                    // 'data_type' => 'string',
                    // 'min' => 5,
                    // 'max' => 100,
                    'rules' => 'required',
                    // 'placeholder' => 'Number of users allowed',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    // 'value' => 52,
                    // 'hint' => 'You can set the number of users allowed to be added.'
                ],
                [
                    'name' => 'pgrs_rstl_payment_amount_loan_fees',
                    'type' => 'text',
                    'label' => 'Loan Fees',
                    // optional fields
                    // 'data_type' => 'string',
                    // 'min' => 5,
                    // 'max' => 100,
                    'rules' => 'required',
                    // 'placeholder' => 'Number of users allowed',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    'value' => 7.23,
                    // 'hint' => 'You can set the number of users allowed to be added.'
                ],

                // [
                //     'name' => 'from_name',
                //     'type' => 'text',
                //     'label' => 'Email from Name',
                //     'placeholder' => 'Email from Name',
                // ]
            ]
        ]
        // 'email' => [
        //     'title' => 'Email Settings',
        //     'descriptions' => 'How app email will be sent.',
        //     'icon' => 'fa fa-envelope',

        //     'inputs' => [
        //         [
        //             'name' => 'from_email',
        //             'type' => 'email',
        //             'label' => 'From Email',
        //             'placeholder' => 'Application from email',
        //             'rules' => 'required|email',
        //         ],
        //         [
        //             'name' => 'from_name',
        //             'type' => 'text',
        //             'label' => 'Email from Name',
        //             'placeholder' => 'Email from Name',
        //         ]
        //     ]
        // ]
    ],

    // Setting page url, will be used for get and post request
    'url' => 'app-settings',

    // Any middleware you want to run on above route
    'middleware' => [],

    // Route Names
    'route_names' => [
        'index' => 'settings.index',
        'store' => 'settings.store',
    ],

    // View settings
    // 'setting_page_view' => 'app_settings::settings_page',
    'setting_page_view' => 'admin.setting.app_settings',
    // 'setting_page_view' => 'admin.setting.general',
    'flash_partial' => 'app_settings::_flash',

    // Setting section class setting
    'section_class' => 'card mb-3',
    'section_heading_class' => 'card-header',
    'section_body_class' => 'card-body',

    // Input wrapper and group class setting
    'input_wrapper_class' => 'form-group',
    'input_class' => 'form-control',
    'input_error_class' => 'has-error',
    'input_invalid_class' => 'is-invalid',
    'input_hint_class' => 'form-text text-muted',
    'input_error_feedback_class' => 'text-danger',

    // Submit button
    'submit_btn_text' => 'Save Settings',
    'submit_success_message' => 'Settings has been saved.',

    // Remove any setting which declaration removed later from sections
    'remove_abandoned_settings' => false,

    // Controller to show and handle save setting
    // 'controller' => '\QCod\AppSettings\Controllers\AppSettingController',
    'controller' => 'App\Http\Controllers\Admin\AppSettingsController',

    // settings group
    'setting_group' => function () {
        // return 'user_'.auth()->id();
        return 'default';
    }
];
