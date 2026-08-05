<?php

return [
    'plugin' => [
        'name' => 'eRecht24 Rechtstexte',
        'description' => 'Benachrichtigt über Änderungen und aktualisiert bei Bedarf die Rechtstexte',
        'author' => 'Xitara SoftWerX - Manuel Burghammer',
        'icon' => 'icon-balance-scale',
    ],
    'submenu' => [
        'label' => 'eRecht24 Rechtstexte',
        'description' => '',
    ],
    'settings' => [
        'label' => 'eRecht24',
        'section' => [
            'label' => 'Einstellungen für eRecht24 Rechtstexte',
            'comment' => '',
        ],
        'tab' => [
            'section_api' => 'API-EInstellungen',
            'section_poll' => 'Polling',
            'section_push' => 'Push-Service',
        ],
        'section_api' => [
            'label' => 'Grundeinstellungen',
            'comment' => 'Diese Einstellungen sind grundlegend nötig für den Betrieb des Plugins',
        ],
        'use_demo_credentials' => [
            'label' => 'Demo-Schlüssel verwenden',
            'comment' => 'Verwendet die von eRecht24 dokumentierten Demo-Zugangsdaten für reguläre Abrufe, Push-Registrierungen und testPush. Der konfigurierte API-Key wird dabei ignoriert; importierte Demo-Inhalte dürfen nicht produktiv veröffentlicht werden.',
        ],
        'apikey' => [
            'label' => 'API-Key',
            'comment' => 'Der API-Key muss auf der Seite von eRecht24 angelegt werden und wird verwendet, wenn der Demo-Schalter deaktiviert ist.',
        ],
        'test_apikey' => [
            'label' => 'API-Key testen',
            'button' => 'Test starten',
            'comment' => 'Sendet eine Test-Anfrage an den Server um festzustellen ob der API-Key funktioniert.',
        ],
        'docs' => [
            'label' => 'Dokumente',
            'comment' => 'Auswählen, welchen Dokumente importiert werden sollen.',
        ],
        'langs' => [
            'label' => 'Sprachen',
            'comment' => 'Auswählen, in welchen Sprachen die Dokumente importiert werden sollen.',
        ],
        'section_poll' => [
            'label' => 'Regelmässiger Abruf der Texte einstellen',
            'comment' => 'In diesem Interval werden die Texte neu abgerufen und aktualisiert.',
        ],
        'interval' => [
            'label' => 'Interval',
            'comment' => '',
        ],
        'pull_now' => [
            'label' => 'Import jetzt starten',
            'button' => 'Import starten',
            'comment' => 'Der Import wird sofort ausgeführt, unabhängig der Interval-Einstellung ob ob das Interval aktiv ist oder nicht',
        ],
        'is_pull_active' => [
            'label' => 'Aktiv',
            'comment' => 'Aktiviert oder deaktiviert das automatische Polling',
        ],
        'section_push' => [
            'label' => 'Push-Service',
            'comment' => 'Registriert diese Installation bei eRecht24 und aktualisiert Rechtstexte nach einer authentifizierten Push-Benachrichtigung automatisch.',
        ],
        'push_uri' => [
            'label' => 'Öffentliche Push-URL',
            'comment' => 'Muss von eRecht24 per POST über das Internet erreichbar sein. HTTPS benötigt ein öffentlich vertrauenswürdiges Zertifikat. Speichere die Einstellungen vor der Registrierung.',
        ],
        'is_push_active' => [
            'label' => 'Push-Service aktiv',
            'comment' => 'Wird nach erfolgreicher Registrierung automatisch aktiviert.',
        ],
        'push_client_id' => [
            'label' => 'eRecht24 Client-ID',
            'comment' => 'Wird von eRecht24 bei der Registrierung vergeben.',
        ],
        'push_test_type' => [
            'label' => 'Test-Push-Typ',
            'comment' => 'Ping prüft nur Erreichbarkeit und Authentifizierung. Rechtstext-Tests verwenden den gewählten Schlüsselmodus, speichern die abgerufenen Inhalte aber nicht.',
        ],
        'push_test_types' => [
            'ping' => 'Ping',
            'message' => 'Nachricht',
            'imprint' => 'Impressum',
            'privacy_policy' => 'Datenschutzerklärung',
            'privacy_policy_social_media' => 'Datenschutzerklärung Social-Media',
        ],
        'credential_modes' => [
            'configured' => 'konfigurierter API-Key',
            'demo' => 'Demo-Schlüssel',
        ],
        'registered_clients' => [
            'label' => 'Registrierte Clients',
            'comment' => 'Zeigt den zuletzt über „Clients aktualisieren“ abgerufenen Stand für den gewählten Schlüsselmodus. eRecht24 erlaubt maximal drei Clients pro Projekt.',
            'not_loaded' => 'Für den Modus „:mode“ wurde noch keine Client-Liste abgerufen.',
            'none' => 'Für den Modus „:mode“ sind keine Clients registriert.',
            'summary' => ':count von maximal :limit Clients im Modus „:mode“:',
            'line' => '#:id | :method | :uri | CMS: :cms | Plugin: :plugin',
        ],
        'push_actions' => [
            'label' => 'Push-Service verwalten',
            'comment' => 'Registrierung und Aktualisierung verwenden den im API-Tab gewählten Schlüsselmodus.',
            'register' => 'Registrieren / aktualisieren',
            'registered' => 'Push-Client wurde registriert oder aktualisiert.',
            'test' => 'testPush ausführen',
            'unregister' => 'Abmelden',
            'unregistered' => 'Push-Client wurde abgemeldet.',
            'unregister_confirm' => 'Soll dieser Push-Client wirklich bei eRecht24 abgemeldet werden?',
            'refresh_clients' => 'Clients aktualisieren',
            'clients_refreshed' => ':count registrierte Clients wurden geladen.',
            'unregister_all' => 'Alle Clients abmelden',
            'unregister_all_confirm' => 'Sollen wirklich ALLE Clients des gewählten Schlüsselmodus bei eRecht24 abgemeldet werden? Dies kann andere Installationen betreffen und lässt sich nicht rückgängig machen.',
            'unregister_all_success' => ':count Clients wurden abgemeldet.',
            'unregister_all_partial' => ':deleted Clients wurden abgemeldet; :remaining Clients konnten nicht entfernt werden.',
            'client_limit' => 'Das eRecht24-Limit ist erreicht: :count von maximal :limit Clients sind bereits registriert. Es wurde kein weiterer Client angelegt.',
            'test_notice' => 'testPush verwendet den im API-Tab gewählten Schlüsselmodus. Ein passender registrierter Plugin-Client wird limitneutral wiederverwendet; andernfalls wird bei freiem Platz ein temporärer Client angelegt.',
            'mode_changed' => 'Der vorhandene Push-Client wurde mit einem anderen Schlüsselmodus registriert. Melde ihn zuerst ab und registriere ihn danach erneut.',
        ],
        'daily' => 'täglich',
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
        'error_getting_privacy_policy_social_media' => 'Fehler beim Abrufen der Social-Media-Datenschutzrichtlinie',
        'error_getting_message' => 'Fehler beim Abrufen der eRecht24-Nachricht',
        'missing_document_language' => 'Die angeforderte Sprachfassung fehlt in der API-Antwort',
        'push_processing_failed' => 'Die Push-Benachrichtigung konnte nicht verarbeitet werden',
        'no_error' => 'kein Fehler',
    ],
    'documents' => [
        'imprint' => 'Impressum',
        'privacy_policy' => 'Datenschutzerklärung',
        'privacy_policy_social_media' => 'Datenschutzerklärung Social-Media',
    ],
    'languages' => [
        'de' => 'Deutsch',
        'en' => 'Englisch',
    ],
    'dashboard' => [
        'widget_title' => 'Status der eRecht24-Rechtstexte',
        'current' => ':document ist aktuell',
        'warning' => ':document muss geprüft werden',
        'warning_changed' => 'Der Rechtstext wurde automatisch aktualisiert und sollte geprüft werden.',
        'warning_missing' => 'Der Rechtstext wurde noch nicht importiert.',
        'provider_message' => 'Hinweis von eRecht24',
        'inspect' => 'Untersuchen',
        'warnings_title' => 'eRecht24-Rechtstexte prüfen',
        'warnings_description' => 'Folgende Rechtstexte oder Hinweise erfordern Aufmerksamkeit:',
        'acknowledge' => 'Änderungen als geprüft markieren',
        'acknowledged' => 'Änderungshinweise wurden als geprüft markiert.',
        'no_documents' => 'Es sind keine Rechtstexte und Sprachen konfiguriert.',
        'open_link' => 'Hinweis öffnen',
    ],
];
