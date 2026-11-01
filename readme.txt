=== Menüfeld Erweiterung (ID, Class & Attribute) ===
Contributors: cemfirat
Tags: menu, navigation, attributes, css, accessibility
Requires at least: 5.4
Requires PHP: 7.0
Stable tag: 1.5.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Fügt ID-, Class- und Attribute-Felder zu Menüeinträgen hinzu.

== Description ==

Menüfeld Erweiterung ergänzt den klassischen Menü-Editor um Felder für eine HTML-ID, CSS-Klassen und eine begrenzte Liste zusätzlicher Attribute.

IDs und Klassen werden beim Speichern bereinigt. Der zusätzliche Attributtext bleibt in den vorhandenen Metadaten für die Bearbeitung erhalten; erlaubte Namen und normalisierte Werte werden erst für die Ausgabe bestimmt. Es findet keine Datenmigration statt.

Der Filter nav_menu_link_attributes schreibt die erlaubten Attributpaare auf den Menülink. Für den Frontend-Fallback bereitet PHP dieselben Paare vor. Das Skript prüft diese Daten vor dem Setzen und verwendet keinen zweiten Parser für den gespeicherten Attributtext. Die Inline-JSON-Übertragung kodiert HTML-Sonderzeichen als Daten. Die bestehende WordPress-Ausgabe bleibt erhalten; die vollständige Theme-/DOM-Ausgabe muss in einer tatsächlichen WordPress-Installation geprüft werden.

Erlaubt sind IDs mit einem führenden Buchstaben, durch Leerzeichen getrennte Klassennamen sowie die Attribute role, rel, target, download und Namen mit den Präfixen aria-, data- und uk-. target akzeptiert _self, _blank, _parent und _top in dieser Schreibweise; andere oder leere Werte werden zu _self. rel wird in kleingeschriebene Tokens aus ASCII-Buchstaben und Bindestrichen normalisiert, ohne doppelte Tokens.

Das Plugin richtet sich an den klassischen Menübildschirm. Es fügt keine Felder zum Block-Navigation-Editor hinzu.

Es gibt keinen eingebauten Update-Mechanismus. Die Update URI zeigt auf das öffentliche GitHub-Repository, damit WordPress.org dieses Plugin nicht ersetzt.

== Installation ==

1. Das Plugin-Verzeichnis nach wp-content/plugins/ legen oder das Verzeichnis als ZIP über Plugins > Installieren > Plugin hochladen installieren.
2. Menüfeld Erweiterung (ID, Class & Attribute) aktivieren.
3. Design > Menüs öffnen.
4. Einen Menüeintrag bearbeiten, ID, Klassen oder zusätzliche Attribute eintragen und das Menü speichern.

== Frequently Asked Questions ==

= Wo erscheinen die Felder? =

Im klassischen Menü-Editor unter Design > Menüs, innerhalb jedes Menüeintrags.

= Welche Attribute sind erlaubt? =

role, rel, target, download sowie Attributnamen, die mit aria-, data- oder uk- beginnen. Andere Namen werden bei der Ausgabe verworfen; der gespeicherte Text bleibt erhalten. Werte stehen in einfachen oder doppelten Anführungszeichen, etwa data-label="Menü". Bei mehrfach angegebenen Namen gilt der letzte erkannte Wert; erlaubte Attribute ohne Wert erhalten einen leeren Wert.

= Was passiert beim Deinstallieren? =

uninstall.php entfernt die drei gespeicherten Meta-Schlüssel aller Menüeinträge.

== Changelog ==

= Unreleased =
* PHP bereitet dieselben erlaubten und normalisierten Attributpaare für den Link-Filter und den Frontend-Fallback vor.
* Der Fallback prüft die vorbereiteten Paare und wertet keinen zusätzlichen Rohtext mehr aus.
* HTML-Sonderzeichen bleiben bei der Inline-JSON-Übertragung Daten; das geänderte Skript erhält einen neuen Cache-Key.
* Fokussierte PHP-/JavaScript-Regressionen ergänzen den bestehenden Blueprint-Workflow. Version 1.5.5 bleibt unverändert.

= 1.5.5 =
* ID-, Class- und Attributfelder im klassischen Menü-Editor.
* Bereinigung von IDs und Klassen beim Speichern; der zusätzliche Attributtext wird unverändert an die vorhandene Metadatenspeicherung übergeben und für die PHP-Ausgabe gefiltert.
* Ausgabe über nav_menu_link_attributes und ein Frontend-Skript, das CF_MEF_DATA erhält.
* Entfernen der gespeicherten Menü-Meta-Daten in uninstall.php.
