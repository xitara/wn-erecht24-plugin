**English version below**

# Winter-CMS-Plugin für eRecht24

Mit diesem Plugin kannst du Rechtstexte über die eRecht24-API abrufen und auf deiner Website verwenden.

## Installation

### Download

Lade das Plugin herunter und entpacke es in das Plugin-Verzeichnis deiner Winter-CMS-Installation:

```text
plugins/xitara/erecht24
```

### Git

Führe im Stammverzeichnis deiner Winter-CMS-Installation folgenden Befehl aus:

```bash
git clone https://github.com/xitara/wn-erecht24-plugin.git plugins/xitara/erecht24
```

Führe anschließend die Migrationen aus:

```bash
php artisan winter:up
```

### Composer

Führe im Stammverzeichnis deiner Winter-CMS-Installation folgende Befehle aus:

```bash
composer require xitara/wn-erecht24-plugin
php artisan winter:up
```

## Konfiguration

Öffne die Einstellungen unter `Einstellungen → E-Recht24 → E-Recht24 Rechtstexte`.

Trage im Einstellungsdialog deinen eRecht24-API-Schlüssel ein. Du findest ihn wie folgt:

1. Öffne den [eRecht24 Premium Projekt Manager](https://www.e-recht24.de/mitglieder/tools/projekt-manager/).
2. Klicke beim gewünschten Projekt auf das Zahnradsymbol „Synchronisation“. Dort kannst du einen neuen API-Schlüssel erzeugen oder den vorhandenen API-Schlüssel kopieren.

Der Schalter „Demo-Schlüssel verwenden“ wechselt alle regulären API-Abrufe, neue Push-Registrierungen und `testPush` auf die in der eRecht24-Dokumentation veröffentlichten Demo-Zugangsdaten. Der konfigurierte API-Key wird in diesem Modus ignoriert. Demo-Importe werden wie reguläre Importe lokal gespeichert und dürfen daher nicht auf einer produktiven Website veröffentlicht werden.

Über die Schaltfläche „Test starten“ kannst du die aktuell gewählte Zugangsdatenart prüfen.

**WICHTIG:** Für den Test wird derzeit das deutsche Impressum abgerufen. Damit der Test erfolgreich ist, muss es bei eRecht24 eingerichtet sein.

Bei den Dokumenten und Sprachen muss jeweils mindestens eine Option ausgewählt werden.

Im Tab „Polling“ können die ausgewählten Dokumente in die Datenbank importiert werden. Der automatische Abruf in Intervallen ist derzeit noch nicht vollständig verifiziert.

## Push-Service

Der Push-Service aktualisiert Rechtstexte automatisch, sobald eRecht24 eine Änderung meldet:

1. Speichere zuerst Schlüsselmodus, API-Key, Dokumente und Sprachen.
2. Prüfe im Tab „Push-Service“ die öffentliche Push-URL. Standardmäßig lautet der Pfad `/api/erecht24/push`; die URL muss von eRecht24 per POST über das Internet erreichbar sein. Bei HTTPS muss die vollständige Zertifikatskette öffentlich vertrauenswürdig sein.
3. Klicke auf „Registrieren / aktualisieren“. Das Plugin speichert die von eRecht24 vergebene Client-ID und das Push-Secret in den Winter-Systemeinstellungen.
4. Mit „Clients aktualisieren“ wird die registrierte Client-Liste des gewählten Schlüsselmodus geladen. eRecht24 erlaubt maximal drei Clients pro Projekt; das Plugin prüft diese Grenze vor jeder neuen Registrierung.
5. Mit „testPush ausführen“ kann die Erreichbarkeit geprüft werden. `POST /clients/{client_id}/testPush` verwendet denselben, im API-Tab gewählten Schlüsselmodus wie „Registrieren / aktualisieren“. Bei deaktiviertem Demo-Schalter wird somit der konfigurierte API-Key verwendet. Eine passende vorhandene Plugin-Registrierung wird ohne zusätzlichen Client-Platz wiederverwendet; nur andernfalls ist ein freier Platz für einen temporären Client nötig. Rechtstext-Tests rufen den Inhalt zur Ablaufprüfung ab, speichern ihn aber nicht in den lokalen Rechtstexten.

Eingehende Push-Anfragen werden anhand des Secrets authentifiziert. Ein Rechtstext wird pro Benachrichtigung nur einmal von der API abgerufen; alle ausgewählten Sprachfassungen werden aus derselben Antwort aktualisiert. Nachrichten von eRecht24 werden als Hinweis im Dashboard-Widget angezeigt.

Die Push-Registrierung wird an den beim Registrieren gewählten Schlüsselmodus gebunden. Soll der Modus bei einem bereits registrierten Client wechseln, melde den Client zuerst im Tab „Push-Service“ ab, speichere den neuen Modus und registriere ihn erneut. Bis dahin werden eingehende Pushes weiterhin mit den Zugangsdaten ihrer bestehenden Registrierung verarbeitet.

Das schreibgeschützte Feld „Registrierte Clients“ zeigt den zuletzt abgerufenen Stand. „Alle Clients abmelden“ entfernt nach einer Sicherheitsabfrage sämtliche Clients des gewählten eRecht24-Projekts. Diese Aktion kann andere Installationen betreffen und lässt sich nicht rückgängig machen.

Externe API-Aufrufe werden strukturiert im Winter-Systemlog protokolliert. Die Einträge enthalten Operation, Schlüsselmodus, Statuscode und unkritische Metadaten, aber weder API-Key noch Push-Secret oder Rechtstextinhalt.

## Dashboard-Widget

Füge dem Backend-Dashboard über „Widget hinzufügen“ das Widget „Status der eRecht24-Rechtstexte“ hinzu. Vorhandene und aktuelle Texte erscheinen mit grünem Haken. Fehlende Texte, Abruffehler, Anbieterwarnungen und automatisch geänderte Texte werden als Warnung mit „Untersuchen“ markiert. Automatisch aktualisierte Texte können im Hinweisfenster als geprüft bestätigt werden und wechseln danach wieder auf grün.

## In eine Seite einbinden

Du kannst die Rechtstexte auf zwei Arten in eine Seite einbinden:

1. **Als Komponente in einer CMS-Seite:** Ziehe die Komponente `ERECHT24 RECHTSTEXTE → eRecht 24 Ausgabe` im Editor an die gewünschte Position. Wähle anschließend in den Einstellungen die Sprache und den Dokumenttyp aus.

2. **Als Snippet in einer statischen Seite:** Öffne die Seite und positioniere den Cursor an der Stelle, an der das Snippet eingefügt werden soll. Wähle anschließend `Snippets → eRecht 24 Ausgabe` und lege in den Einstellungen die Sprache und den Dokumenttyp fest.

---

# Winter CMS Plugin for eRecht24

This plugin lets you retrieve legal texts through the eRecht24 API and use them on your website.

## Installation

### Download

Download the plugin and extract it to the plugin directory of your Winter CMS installation:

```text
plugins/xitara/erecht24
```

### Git

Run the following command from the root directory of your Winter CMS installation:

```bash
git clone https://github.com/xitara/wn-erecht24-plugin.git plugins/xitara/erecht24
```

Then run the migrations:

```bash
php artisan winter:up
```

### Composer

Run the following commands from the root directory of your Winter CMS installation:

```bash
composer require xitara/wn-erecht24-plugin
php artisan winter:up
```

## Configuration

Open `Settings → E-Recht24 → E-Recht24 Legal Texts`.

Enter your eRecht24 API key in the settings dialog. You can find it as follows:

1. Open the [eRecht24 Premium Project Manager](https://www.e-recht24.de/mitglieder/tools/projekt-manager/).
2. Click the gear icon labeled “Synchronization” for the relevant project. From there, you can generate a new API key or copy the existing one.

The “Use demo key” switch changes all regular API requests, new push registrations, and `testPush` to the demo credentials published in the eRecht24 documentation. The configured API key is ignored in this mode. Demo imports are stored locally like regular imports and must therefore not be published on a production website.

Use the “Start Test” button to verify the currently selected credential type.

**IMPORTANT:** The test currently retrieves the German imprint. To complete the test successfully, the German imprint must be configured in eRecht24.

At least one document and one language must be selected.

You can import the selected documents into the database from the “Polling” tab. Automatic interval retrieval has not yet been fully verified.

## Push service

The push service automatically updates legal texts when eRecht24 reports a change:

1. Save the credential mode, API key, documents, and languages first.
2. Check the public push URL on the “Push Service” tab. Its default path is `/api/erecht24/push`; eRecht24 must be able to reach it over the internet using POST. With HTTPS, the complete certificate chain must be publicly trusted.
3. Click “Register / update”. The plugin stores the client ID and push secret returned by eRecht24 in the Winter system settings.
4. Use “Refresh clients” to load the registered client list for the selected credential mode. eRecht24 allows at most three clients per project; the plugin checks this limit before every new registration.
5. Use “Run testPush” to verify reachability. `POST /clients/{client_id}/testPush` uses the same credential mode selected on the API tab as “Register / update”. When the demo switch is disabled, the configured API key is therefore used. A matching existing plugin registration is reused without consuming another client slot; only otherwise is a free slot required for a temporary client. Legal-text tests retrieve the content to verify the workflow but do not store it in the local legal texts.

Incoming push requests are authenticated with the secret. Each notification fetches the affected document only once; all selected language variants are updated from that response. Messages from eRecht24 are shown in the dashboard widget.

The push registration remains bound to the credential mode selected during registration. To change the mode of an existing client, unregister it on the “Push Service” tab, save the new mode, and register it again. Until then, incoming pushes continue to use the credentials of their existing registration.

The read-only “Registered clients” field shows the most recently retrieved state. After a confirmation prompt, “Unregister all clients” removes every client in the selected eRecht24 project. This may affect other installations and cannot be undone.

External API calls are recorded as structured entries in the Winter system log. Entries contain the operation, credential mode, status code, and non-sensitive metadata, but never the API key, push secret, or legal-text content.

## Dashboard widget

Use “Add widget” in the backend dashboard to add “eRecht24 legal-text status”. Existing and current texts have a green check mark. Missing texts, fetch errors, provider warnings, and automatically changed texts are marked with a warning and an “Inspect” action. Automatically updated texts can be marked as reviewed in the dialog and then return to green.

## Integrating the Texts into a Page

You can integrate the legal texts into a page in two ways:

1. **As a component on a CMS page:** Drag the `ERECHT24 LEGAL TEXTS → eRecht 24 Output` component to the desired position in the editor. Then select the language and document type in the settings.

2. **As a snippet on a static page:** Open the page and place the cursor where you want to insert the snippet. Then select `Snippets → eRecht 24 Output` and choose the language and document type in the settings.
