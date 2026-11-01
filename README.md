<p align="center">
  <img src="https://raw.githubusercontent.com/cemfirat/repository-governance/main/assets/brand-banner.webp" alt="Cem Firat creative consultancy artwork" width="900" />
</p>

# Menüfeld Erweiterung (ID, Class & Attribute)

Fügt ID-, Class- und Attribute-Felder zu Menüeinträgen hinzu.

**Author:** [Cem Firat](https://cemfirat.com/)  
**License:** GPL-2.0-or-later  
**Requirements:** WordPress 5.4+ and PHP 7.0+.

> **Entwicklungsstand:** Version 1.5.5; die laufende Korrektur und ihre Prüfnachweise stehen in [PR #1](https://github.com/cemfirat/wordpress-menue-attributes/pull/1). Es gibt kein automatisches Update aus GitHub Releases.

## What it does

Im klassischen Menü-Editor (**Design → Menüs**) erhält jeder Menüeintrag drei zusätzliche Felder:

- **ID** für das Anker-Element
- **Class** für eine oder mehrere CSS-Klassen
- **Zusätzliche Attribute** aus einer festen Erlaubnisliste

IDs und Klassen werden beim Speichern bereinigt. Der zusätzliche Attributtext bleibt für die Bearbeitung in den vorhandenen Metadaten erhalten; seine Erlaubnisliste und Wertnormalisierung greifen bei der Ausgabe. Es findet keine Datenmigration statt.

Auf der Website setzt `nav_menu_link_attributes` ID, Klassen und erlaubte Attribute auf den Menülink. Für den Frontend-Fallback bereitet PHP dieselben erlaubten und normalisierten Attributpaare vor und übergibt sie als `window.CF_MEF_DATA` an `public/dom-patcher.js`. Das Skript prüft diese Paare vor dem Setzen; es wertet keinen gespeicherten Attributtext mit einem zweiten Parser aus. HTML-Sonderzeichen werden für die Inline-JSON-Übertragung kodiert und kommen als Daten beim Skript an.

## Allowed values

- IDs beginnen mit einem Buchstaben. Erlaubt sind Buchstaben, Ziffern, `-`, `_`, `:` und `.`.
- Klassen sind durch Leerzeichen getrennt. Erlaubt sind Buchstaben, Ziffern, `-` und `_`.
- Zusätzliche Attribute sind auf `role`, `rel`, `target`, `download` sowie Namen mit den Präfixen `aria-`, `data-` und `uk-` beschränkt.
- `target` akzeptiert nur `_self`, `_blank`, `_parent` und `_top` in dieser Schreibweise. Andere oder leere Werte werden zu `_self`.
- `rel` wird in kleingeschriebene Tokens aus ASCII-Buchstaben und Bindestrichen normalisiert; doppelte Tokens werden entfernt.
- Attributwerte werden mit einfachen oder doppelten Anführungszeichen angegeben, etwa `data-label="Menü"`. Bei mehrfach angegebenen Namen gilt der letzte erkannte Wert. Erlaubte Attribute ohne Wert erhalten einen leeren Wert.

Die bestehende WordPress-Ausgabe mit `esc_attr` bleibt erhalten. Die gemeinsamen Attributpaare belegen keine vollständige Gleichheit der fertigen Theme-Ausgabe und des Browser-DOM; eine Prüfung mit WordPress und dem tatsächlich verwendeten Theme bleibt erforderlich.

## Installation

1. Das Plugin-Verzeichnis nach `wp-content/plugins/` legen oder das Verzeichnis als ZIP hochladen.
2. **Menüfeld Erweiterung (ID, Class & Attribute)** aktivieren.
3. **Design → Menüs** öffnen und einen Menüeintrag bearbeiten.
4. ID, Klassen oder zusätzliche Attribute eintragen und das Menü speichern.

Es gibt derzeit kein Release-ZIP und keinen eingebauten Updater. `Update URI` verweist auf dieses GitHub-Repository, damit WordPress das Plugin nicht durch ein gleichnamiges Plugin aus dem WordPress.org-Verzeichnis ersetzt.

## Uninstall

`uninstall.php` löscht die Meta-Schlüssel `_menu_item_custom_id`, `_menu_item_custom_class` und `_menu_item_custom_attr` aus der Beitrags-Meta-Tabelle.

## Development

Dieses Repository nutzt den gemeinsamen Cem-Firat-WordPress-Plugin-Blueprint, Profil `simple`, Version 0.2.0.

```bash
php -l menu-item-extended-fields.php
php -l uninstall.php
php -l tests/attribute-contract.php
php tests/attribute-contract.php
node --test tests/dom-patcher.test.cjs
```

Die fokussierten Regressionen prüfen die tatsächlichen PHP-Callbacks, beide Fallback-Datenwege, die unveränderte Speicherung des Attributtexts und das tatsächliche DOM-Skript mit dem von PHP erzeugten Inline-JSON. WordPress-Funktionen und DOM-Objekte sind ausdrücklich Testdoubles. Diese Tests ersetzen keine Prüfung in einer WordPress-Installation, mit Rollen oder mit einem konkreten Theme. Bei einem abweichenden PHP-Pfad kann der Node-Test mit `PHP_BIN=/pfad/zu/php` gestartet werden.

Der bestehende Workflow **WordPress plugin blueprint** prüft Manifest, Plugin-Header, `readme.txt`, Changelog, Logo und Lizenz gegen die gepinnte Blueprint-Revision und führt die Syntax- und Vertragsprüfungen mit PHP 8.3 und Node.js 22 aus. Diese Testwerkzeuge ändern die Mindestvoraussetzungen des Plugins nicht. Der Workflow enthält keinen Veröffentlichungs- oder Installationsschritt.

## Projektstand und Übergabe

Stand 1. November 2026: Ausgangspunkte dieser Korrektur sind `main` bei `971ca04287658be9a46fe3898ad0c97f8d5d88a5` und der bestehende PR-Branch bei `16f3d07cf2ce56263e71b1d8a225a13d3e32ac8c`. [PR #1](https://github.com/cemfirat/wordpress-menue-attributes/pull/1) führt den konkreten Auftrag und die fortgeschriebenen Nachweise für Kandidat, lokale Prüfungen, unabhängigen Review, Branch-/PR-CI und Integration. Diese datierten Ausgangspunkte sind kein Live-Status.

Die Korrektur betrifft den Speicher-/Ausgabevertrag, die gemeinsame Attributfilterung und ihre Regressionen. Produktumfang und Version bleiben unverändert. Veröffentlichung, Installation und fachliche beziehungsweise sichtbare Abnahme benötigen eigene Nachweise; eine grüne CI belegt sie nicht.

## License

GPL-2.0-or-later. Der vollständige GPL-v2-Text steht in [LICENSE](LICENSE).
