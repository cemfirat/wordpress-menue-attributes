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

Gespeicherte Werte werden bereinigt. Der Filter nav_menu_link_attributes schreibt sie auf den Menülink. Themes, die diesen Filter nicht verwenden, erhalten dieselben gespeicherten Werte über ein kleines Frontend-Skript.

Erlaubt sind IDs mit einem führenden Buchstaben, durch Leerzeichen getrennte Klassennamen sowie die Attribute role, rel, target, download und Namen mit den Präfixen aria-, data- und uk-. target ist auf _self, _blank, _parent und _top beschränkt.

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

role, rel, target, download sowie Attributnamen, die mit aria-, data- oder uk- beginnen. Andere Namen werden beim Speichern verworfen.

= Was passiert beim Deinstallieren? =

uninstall.php entfernt die drei gespeicherten Meta-Schlüssel aller Menüeinträge.

== Changelog ==

= 1.5.5 =
* ID-, Class- und Attributfelder im klassischen Menü-Editor.
* Bereinigung von IDs, Klassen und der erlaubten Attributliste beim Speichern.
* Ausgabe über nav_menu_link_attributes und ein Frontend-Skript, das CF_MEF_DATA erhält.
* Entfernen der gespeicherten Menü-Meta-Daten in uninstall.php.
