# Changelog

## 1.5.5

- Add ID, class, and extra-attribute fields to classic navigation menu items.
- Sanitize IDs, class lists, and the allowed attribute set before they are stored.
- Apply saved values through `nav_menu_link_attributes`, with a front-end fallback that receives `CF_MEF_DATA`.
- Remove the stored menu-item meta keys on uninstall.
