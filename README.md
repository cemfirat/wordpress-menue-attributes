<p align="center">
  <img src="https://raw.githubusercontent.com/cemfirat/repository-governance/main/assets/brand-banner.webp" alt="Cem Firat creative consultancy artwork" width="900" />
</p>

# Menüfeld Erweiterung (ID, Class & Attribute)

Fügt ID-, Class- und Attribute-Felder zu Menüeinträgen hinzu.

**Author:** [Cem Firat](https://cemfirat.com/)  
**License:** GPL-2.0-or-later  
**Requirements:** WordPress 5.4+ and PHP 7.0+.

> **Status:** Version 1.5.5. Es gibt noch kein automatisches Update aus GitHub Releases.

## What it does

Im klassischen Menü-Editor (**Design → Menüs**) erhält jeder Menüeintrag drei zusätzliche Felder:

- **ID** für das Anker-Element
- **Class** für eine oder mehrere CSS-Klassen
- **Zusätzliche Attribute** aus einer festen Erlaubnisliste

Gespeicherte Werte werden vor dem Schreiben bereinigt. Auf der Website setzt der Filter `nav_menu_link_attributes` ID, Klassen und erlaubte Attribute auf den Menülink. Wenn ein Theme diesen Filter nicht verwendet, übergibt das Plugin die gespeicherten Einträge als `window.CF_MEF_DATA` an `public/dom-patcher.js` und trägt sie dort am passenden Link nach.

## Allowed values

- IDs beginnen mit einem Buchstaben. Erlaubt sind Buchstaben, Ziffern, `-`, `_`, `:` und `.`.
- Klassen sind durch Leerzeichen getrennt. Erlaubt sind Buchstaben, Ziffern, `-` und `_`.
- Zusätzliche Attribute sind auf `role`, `rel`, `target`, `download` sowie Namen mit den Präfixen `aria-`, `data-` und `uk-` beschränkt.
- `target` akzeptiert nur `_self`, `_blank`, `_parent` und `_top`.

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
```

Der Workflow **WordPress plugin blueprint** prüft Manifest, Plugin-Header, `readme.txt`, Changelog, Logo und Lizenz gegen die gepinnte Blueprint-Revision.

## License

GPL-2.0-or-later. Der vollständige GPL-v2-Text steht in [LICENSE](LICENSE).
