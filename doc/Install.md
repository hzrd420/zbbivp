# Installationsanleitung für das Programm ZBB-IVP (ZBB Interessentenportal)

## Systemvoraussetzungen:

+ PHP 8
+ Apache2
+ PDO mit MySQL-Treiber
+ MySQL oder MariaDB

## Installation

1. Importieren Sie den SQL-Dump aus der Datei "db.sql" im Hauptverzeichnis der Anwendung in eine Datenbank (MySQL oder MariaDB).
2. Platzieren Sie den Inhalt des Hauptverzeichnisses in das Wurzelverzeichnis ihres Servers (web root).
3. Kopieren Sie die Datei "app/config.ini.dist" nach "app/config.ini".
4. Bearbeiten Sie die Datei "app/config.ini", um Datenbankparameter, Anmeldeeinstellungen, Paginierungseinstellungen und die notwendigen Daten für das Versenden von E-Mails (SMTP, E-Mail-Adressen) zu konfigurieren.
5. Erstellen Sie einen Cronjob, welcher einmal Täglich, am besten vor Arbeitsbeginn, die Route /steps/remind aufruft. Dies wird für das Senden von E-Mail-Erinnerungen benötigt (fällige / überfällige Schritte)
6. Rufen Sie die Web-Adresse Ihres Servers auf und Testen Sie, ob das Programm erfolgreich startet.
7. Um sich anzumelden, verwenden sie Folgende Benutzerdaten (ohne Anführungszeichen): Benutzername: "admin", Passwort: "Test123!"
  Achtung: Bitte ändern Sie sofort nach der ersten erfolgreichen Anmeldung das Passwort und den Benutzernamen über die Benutzerverwaltung, dort können Sie auch zusätzliche Benutzer anlegen.