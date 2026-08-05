<?php

return [
    'plugin' => [
        'name' => 'eRecht24 Legal Texts',
        'description' => 'Notifies about changes and updates the legal texts as needed',
        'author' => 'Xitara SoftWerX - Manuel Burghammer',
        'icon' => 'icon-balance-scale',
    ],
    'submenu' => [
        'label' => 'eRecht24 Legal Texts',
        'description' => '',
    ],
    'settings' => [
        'label' => 'eRecht24',
        'section' => [
            'label' => 'Settings for eRecht24 Legal Texts',
            'comment' => '',
        ],
        'tab' => [
            'section_api' => 'API Settings',
            'section_poll' => 'Polling',
            'section_push' => 'Push Service',
        ],
        'section_api' => [
            'label' => 'Basic Settings',
            'comment' => 'These settings are essential for the plugin to function properly.',
        ],
        'use_demo_credentials' => [
            'label' => 'Use demo key',
            'comment' => 'Uses the demo credentials documented by eRecht24 for regular requests, push registrations, and testPush. The configured API key is ignored; imported demo content must not be published in production.',
        ],
        'apikey' => [
            'label' => 'API Key',
            'comment' => 'The API key must be created on the eRecht24 website and is used when the demo switch is disabled.',
        ],
        'test_apikey' => [
            'label' => 'Test API Key',
            'button' => 'Start Test',
            'comment' => 'Sends a test request to the server to check if the API key works.',
        ],
        'docs' => [
            'label' => 'Documents',
            'comment' => 'Select which documents should be imported.',
        ],
        'langs' => [
            'label' => 'Languages',
            'comment' => 'Select in which languages the documents should be imported.',
        ],
        'section_poll' => [
            'label' => 'Set Regular Text Retrieval',
            'comment' => 'The texts will be retrieved and updated at this interval.',
        ],
        'interval' => [
            'label' => 'Interval',
            'comment' => '',
        ],
        'pull_now' => [
            'label' => 'Start Import Now',
            'button' => 'Start Import',
            'comment' => 'The import will be performed immediately, regardless of the interval setting and whether the interval is active or not.',
        ],
        'is_pull_active' => [
            'label' => 'Active',
            'comment' => 'Enables or disables automatic polling.',
        ],
        'section_push' => [
            'label' => 'Push Service',
            'comment' => 'Registers this installation with eRecht24 and automatically updates legal texts after an authenticated push notification.',
        ],
        'push_uri' => [
            'label' => 'Public push URL',
            'comment' => 'Must be reachable by eRecht24 via POST over the internet. HTTPS requires a publicly trusted certificate. Save the settings before registration.',
        ],
        'is_push_active' => [
            'label' => 'Push service active',
            'comment' => 'Automatically enabled after successful registration.',
        ],
        'push_client_id' => [
            'label' => 'eRecht24 client ID',
            'comment' => 'Assigned by eRecht24 during registration.',
        ],
        'push_test_type' => [
            'label' => 'Test push type',
            'comment' => 'Ping only checks reachability and authentication. Legal-text tests use the selected credential mode but do not store the retrieved content.',
        ],
        'push_test_types' => [
            'ping' => 'Ping',
            'message' => 'Message',
            'imprint' => 'Imprint',
            'privacy_policy' => 'Privacy policy',
            'privacy_policy_social_media' => 'Social media privacy policy',
        ],
        'credential_modes' => [
            'configured' => 'configured API key',
            'demo' => 'demo key',
        ],
        'registered_clients' => [
            'label' => 'Registered clients',
            'comment' => 'Shows the most recent state retrieved with “Refresh clients” for the selected credential mode. eRecht24 allows at most three clients per project.',
            'not_loaded' => 'No client list has been retrieved for “:mode” yet.',
            'none' => 'No clients are registered for “:mode”.',
            'summary' => ':count of at most :limit clients in “:mode”:',
            'line' => '#:id | :method | :uri | CMS: :cms | Plugin: :plugin',
        ],
        'push_actions' => [
            'label' => 'Manage push service',
            'comment' => 'Registration and updates use the credential mode selected on the API tab.',
            'register' => 'Register / update',
            'registered' => 'Push client was registered or updated.',
            'test' => 'Run testPush',
            'unregister' => 'Unregister',
            'unregistered' => 'Push client was unregistered.',
            'unregister_confirm' => 'Do you really want to unregister this push client from eRecht24?',
            'refresh_clients' => 'Refresh clients',
            'clients_refreshed' => ':count registered clients were loaded.',
            'unregister_all' => 'Unregister all clients',
            'unregister_all_confirm' => 'Do you really want to unregister ALL clients for the selected credential mode from eRecht24? This may affect other installations and cannot be undone.',
            'unregister_all_success' => ':count clients were unregistered.',
            'unregister_all_partial' => ':deleted clients were unregistered; :remaining clients could not be removed.',
            'client_limit' => 'The eRecht24 limit has been reached: :count of at most :limit clients are already registered. No additional client was created.',
            'test_notice' => 'testPush uses the credential mode selected on the API tab. A matching registered plugin client is reused without consuming a slot; otherwise a temporary client is created when a slot is available.',
            'mode_changed' => 'The existing push client was registered with a different credential mode. Unregister it first, then register it again.',
        ],
        'daily' => 'daily',
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
        'error_getting_privacy_policy_social_media' => 'Error getting the social media privacy policy',
        'error_getting_message' => 'Error getting the eRecht24 message',
        'missing_document_language' => 'The requested language is missing from the API response',
        'push_processing_failed' => 'The push notification could not be processed',
        'no_error' => 'No error',
    ],
    'documents' => [
        'imprint' => 'Imprint',
        'privacy_policy' => 'Privacy policy',
        'privacy_policy_social_media' => 'Social media privacy policy',
    ],
    'languages' => [
        'de' => 'German',
        'en' => 'English',
    ],
    'dashboard' => [
        'widget_title' => 'eRecht24 legal-text status',
        'current' => ':document is current',
        'warning' => ':document needs attention',
        'warning_changed' => 'The legal text was updated automatically and should be reviewed.',
        'warning_missing' => 'The legal text has not been imported yet.',
        'provider_message' => 'Message from eRecht24',
        'inspect' => 'Inspect',
        'warnings_title' => 'Review eRecht24 legal texts',
        'warnings_description' => 'The following legal texts or messages need attention:',
        'acknowledge' => 'Mark changes as reviewed',
        'acknowledged' => 'Change notifications were marked as reviewed.',
        'no_documents' => 'No legal texts and languages are configured.',
        'open_link' => 'Open message',
    ],
];
