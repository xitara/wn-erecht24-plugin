<?php

return [
    'plugin'   => [
        'name'        => 'eRecht24 Rechtstexte',
        'description' => 'Benachrichtigt über Änderungen und aktualisiert bei Bedarf die Rechtstexte',
        'author'      => 'Xitara SoftWerX - Manuel Burghammer',
        'icon'        => 'icon-balance-scale',
    ],
    'submenu'  => [
        'label'       => 'E-Recht24 Rechtstexte',
        'description' => '',
    ],
    'settings' => [
        'section'      => [
            'label'   => 'Einstellungen für E-Recht24 Rechtstexte',
            'comment' => '',
        ],
        'tab'          => [
            'section_api'  => 'API-EInstellungen',
            'section_poll' => 'Polling',
            'section_push' => 'Push-Service',
        ],
        'section_api'  => [
            'label'   => 'Grundeinstellungen',
            'comment' => 'Diese Einstellungen sind grundlegend nötig für den Betrieb des Plugins',
        ],
        'apikey'       => [
            'label'   => 'API-Key',
            'comment' => 'Der API-Key muss auf der Seite von E-Recht24 angelegt werden.',
        ],
        'test_apikey'  => [
            'label'   => 'API-Key testen',
            'button' => 'Test starten',
            'comment' => 'Sendet eine Test-Anfrage an den Server um festzustellen ob der API-Key funktioniert.',
        ],
        'docs'       => [
            'label'   => 'Dokumente',
            'comment' => 'Auswählen, welchen Dokumente importiert werden sollen.',
        ],
        'langs'       => [
            'label'   => 'Sprachen',
            'comment' => 'Auswählen, in welchen Sprachen die Dokumente importiert werden sollen.',
        ],
        'section_poll' => [
            'label'   => 'Regelmässiger Abruf der Texte einstellen',
            'comment' => 'In diesem Interval werden die Texte neu abgerufen und aktualisiert.',
        ],
        'interval'     => [
            'label'   => 'Interval',
            'comment' => '',
        ],
        'pull_now'  => [
            'label'   => 'Import jetzt starten',
            'button' => 'Import starten',
            'comment' => 'Der Import wird sofort ausgeführt, unabhängig der Interval-Einstellung ob ob das Interval aktiv ist oder nicht',
        ],
        'is_pull_active'  => [
            'label'   => 'Aktiv',
            'comment' => 'Aktiviert oder deaktiviert das automatische Polling',
        ],
        'section_push' => [
            'label'   => 'Push-Service',
            'comment' => 'dieser Service ist noch nicht implementiert, bitte das Polling nutzen',
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
        'daily'   => 'täglich',
        'weekly' => 'wöchentlich',
        'monthly' => 'monatlich',
    ],
    'flash' => [
        'success' => 'Abfrage erfolgreich',
        'error' => 'Es ist ein Fehler aufgetreten',
        'not_active' => 'Polling ist deaktiviert',
    ],
    'error' => [
        'no_api_key_found_in_post_data' => 'kein Api-Schlüssel in den Postdaten gefunden',
        'no_response_or_wrong_api_key' => 'keine Antwort oder falscher Api-Schlüssel',
        'error_getting_imprint' => 'Fehler beim Abrufen des Impressums',
        'error_getting_privacy_policy' => 'Fehler beim Abrufen der Datenschutzrichtlinie',
        'no_error' => 'kein Fehler',
    ],
];
