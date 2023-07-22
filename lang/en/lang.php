<?php

return [
    'plugin'   => [
        'name'        => 'eRecht24 Legal Texts',
        'description' => 'Notifies about changes and updates the legal texts as needed',
        'author'      => 'Xitara SoftWerX - Manuel Burghammer',
        'icon'        => 'icon-balance-scale',
    ],
    'submenu'  => [
        'label'       => 'eRecht24 Legal Texts',
        'description' => '',
    ],
    'settings' => [
        'label' => 'eRecht24',
        'section'      => [
            'label'   => 'Settings for eRecht24 Legal Texts',
            'comment' => '',
        ],
        'tab'          => [
            'section_api'  => 'API Settings',
            'section_poll' => 'Polling',
            'section_push' => 'Push Service',
        ],
        'section_api'  => [
            'label'   => 'Basic Settings',
            'comment' => 'These settings are essential for the plugin to function properly.',
        ],
        'apikey'       => [
            'label'   => 'API Key',
            'comment' => 'The API key must be created on the eRecht24 website.',
        ],
        'test_apikey'  => [
            'label'   => 'Test API Key',
            'button' => 'Start Test',
            'comment' => 'Sends a test request to the server to check if the API key works.',
        ],
        'docs'       => [
            'label'   => 'Documents',
            'comment' => 'Select which documents should be imported.',
        ],
        'langs'       => [
            'label'   => 'Languages',
            'comment' => 'Select in which languages the documents should be imported.',
        ],
        'section_poll' => [
            'label'   => 'Set Regular Text Retrieval',
            'comment' => 'The texts will be retrieved and updated at this interval.',
        ],
        'interval'     => [
            'label'   => 'Interval',
            'comment' => '',
        ],
        'pull_now'  => [
            'label'   => 'Start Import Now',
            'button' => 'Start Import',
            'comment' => 'The import will be performed immediately, regardless of the interval setting and whether the interval is active or not.',
        ],
        'is_pull_active'  => [
            'label'   => 'Active',
            'comment' => 'Enables or disables automatic polling.',
        ],
        'section_push' => [
            'label'   => 'Push Service',
            'comment' => 'This service is not yet implemented. Please use polling instead.',
        ],
        'push_uri'     => [
            'label'   => 'push_uri',
            'comment' => '',
        ],
        ''             => [
            'label'   => '',
            'comment' => '',
        ],
        ''             => [
            'label'   => '',
            'comment' => '',
        ],
        ''             => [
            'label'   => '',
            'comment' => '',
        ],
        ''             => [
            'label'   => '',
            'comment' => '',
        ],
        'daily'   => 'daily',
        'weekly' => 'weekly',
        'monthly' => 'monthly',
    ],
    'flash' => [
        'success' => 'Query successful',
        'error' => 'An error occurred',
        'not_active' => 'Polling is disabled',
    ],
    'error' => [
        'no_api_key_found_in_post_data' => 'No API key found in post data',
        'no_response_or_wrong_api_key' => 'No response or wrong API key',
        'error_getting_imprint' => 'Error getting the imprint',
        'error_getting_privacy_policy' => 'Error getting the privacy policy',
        'no_error' => 'No error',
    ],
];
