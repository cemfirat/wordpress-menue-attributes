# Changelog

## Unreleased

- Prepare the same PHP-filtered attribute pairs for the link filter and the DOM fallback, preserving the existing allowlist, `target` defaults and `rel` normalization.
- Stop interpreting raw saved attribute text in JavaScript; guard the prepared attribute map before DOM writes.
- Encode HTML-sensitive characters in inline JSON and change the updated script's cache key.
- Add focused PHP and JavaScript contract regressions to the existing pinned Blueprint workflow.
- Correct the storage documentation: custom attribute text remains editable in the existing metadata, with filtering at output. No migration or plugin version change.

## 1.5.5

- Add ID, class, and extra-attribute fields to classic navigation menu items.
- Sanitize IDs and class lists before storage; pass custom attribute text to the existing metadata storage and filter it for PHP output.
- Apply saved values through `nav_menu_link_attributes`, with a front-end fallback that receives `CF_MEF_DATA`.
- Remove the stored menu-item meta keys on uninstall.
