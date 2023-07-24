**English version below**

# Winter CMS Plugin für e-recht24

Mit diesem Plugin kannst du Rechtstexte von e-recht24 über die API abrufen. Du kannst die Texte dann auf deiner Website verwenden.

## Installation
### Per Download
Lade das Plugin herunter und entpacke es in dein Winter CMS Plugin-Verzeichnis: `plugins/xitara/erecht24`

### Mit Git
Im Hauptverzeichnis deiner Winter CMS Installation führe folgenden Befehl aus:

`git clone https://github.com/xitara/wn-erecht24-plugin.git plugins/xitara/erecht24`

Migration starten mit

`php artisan winter:up`

### Per Composer
Im Hauptverzeichnis deiner Winter CMS Installation führe folgenden Befehl aus:

composer require xitara/wn-erecht24-plugin

## Konfiguration
Rufe die Einstellungen unter `Einstellungen -> E-Recht24 -> E-Recht24 Rechtstexte` auf.

Gib im geöffneten Installationsdialog deinen eRecht24 API-Schlüssel ein. Du findest diesen wie folgt:

1. Rufe den [eRecht24 Premium Projekt Manager](https://www.e-recht24.de/mitglieder/tools/projekt-manager/) auf.
2. Klicke beim entsprechenden Projekt auf das Zahnrad-Symbol "Synchronisation". Nun kannst du entweder einen neuen API-Schlüssel generieren oder den vorhandenen API-Schlüssel kopieren.

Mit dem Button "Test starten" kannst du den API-Schlüssel auf Funktionalität testen.

**WICHTIG:** Aktuell wird für den Test das deutsche Impressum abgerufen. Damit der Test nicht fehlschlägt, muss dieses zwingend eingerichtet sein.

Bei den Dokumenten und Sprachen muss mindestens jeweils ein Punkt ausgewählt sein.

Im Tab "Polling" kann dann der eigentliche Import der ausgewählten Dokumente in die Datenbank erfolgen. Der automatische Abruf per Intervall ist aktuell noch nicht funktionsfähig. Ebenso hat die Einstellung im Tab "Push-Service" noch keine Funktionalität.

## Einbinden in eine Seite
Es stehen zwei Möglichkeiten zur Verfügung, um die Rechtstexte in die Seite einzubinden.

1. Per Komponente in eine CMS-Seite: Ziehe einfach die Komponente `ERECHT24 RECHTSTEXTE -> eRecht 24 Ausgabe` in den Editor an die gewünschte Stelle und wähle in den Einstellungen Sprache und Art des Dokuments aus.

2. Per Snippet in eine statische Seite: Öffne die Seite, klicke mit der Maus an die Position, an der das Snippet eingefügt werden soll, und klicke dann auf `Snippets -> eRecht 25 Ausgabe`. Anschließend kannst du in den Einstellungen Sprache und Art des Dokuments auswählen.

---

# Winter CMS Plugin for e-recht24

This plugin allows you to retrieve legal texts from e-recht24 via the API. You can then use these texts on your website.

## Installation
### Download
Download the plugin and unzip it into your Winter CMS plugin directory: `plugins/xitara/erecht24`

### Using Git
In the main directory of your Winter CMS installation, execute the following command:

`git clone https://github.com/xitara/wn-erecht24-plugin.git plugins/xitara/erecht24`

Start migration with

`php artisan winter:up`

### Using composer
In the main directory of your Winter CMS installation, execute the following command:

composer require xitara/wn-erecht24-plugin

## Configuration
Go to the settings under `Settings -> E-Recht24 -> E-Recht24 Legal Texts`.

In the opened installation dialog, enter your eRecht24 API key. You can find it as follows:

1. Visit the [eRecht24 Premium Project Manager](https://www.e-recht24.de/mitglieder/tools/projekt-manager/).
2. Click the gear icon "Synchronization" for the corresponding project. You can then either generate a new API key or copy the existing API key.

Use the "Start Test" button to test the API key's functionality.

**IMPORTANT:** Currently, the German imprint is fetched for the test. To ensure the test does not fail, it must be set up accordingly.

For the documents and languages, at least one point must be selected.

In the "Polling" tab, the actual import of the selected documents into the database can be done. The automatic retrieval per interval is currently not functional. Similarly, settings in the "Push-Service" tab do not have any functionality yet.

## Integrating into a Page
Two methods are available to integrate the legal texts into a page.

1. Using a component in a CMS page: Simply drag the `ERECHT24 LEGAL TEXTS -> eRecht 24 Output` component into the editor at the desired location and choose the language and type of document in the settings.

2. Using a snippet in a static page: Open the page, click with the mouse at the position where the snippet should be inserted, and then click on `Snippets -> eRecht 25 Output`. You can then select the language and type of document in the settings.
