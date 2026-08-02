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

Über die Schaltfläche „Test starten“ kannst du prüfen, ob der API-Schlüssel funktioniert.

**WICHTIG:** Für den Test wird derzeit das deutsche Impressum abgerufen. Damit der Test erfolgreich ist, muss es bei eRecht24 eingerichtet sein.

Bei den Dokumenten und Sprachen muss jeweils mindestens eine Option ausgewählt werden.

Im Tab „Polling“ können die ausgewählten Dokumente in die Datenbank importiert werden. Der automatische Abruf in Intervallen ist derzeit noch nicht funktionsfähig. Auch die Einstellung im Tab „Push-Service“ hat noch keine Funktion.

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

Use the “Start Test” button to verify that the API key works.

**IMPORTANT:** The test currently retrieves the German imprint. To complete the test successfully, the German imprint must be configured in eRecht24.

At least one document and one language must be selected.

You can import the selected documents into the database from the “Polling” tab. Automatic retrieval at configured intervals is not yet functional. The settings on the “Push Service” tab are not functional yet either.

## Integrating the Texts into a Page

You can integrate the legal texts into a page in two ways:

1. **As a component on a CMS page:** Drag the `ERECHT24 LEGAL TEXTS → eRecht 24 Output` component to the desired position in the editor. Then select the language and document type in the settings.

2. **As a snippet on a static page:** Open the page and place the cursor where you want to insert the snippet. Then select `Snippets → eRecht 24 Output` and choose the language and document type in the settings.
