# Contributing

Thanks for considering an improvement to Menüfeld Erweiterung.

## Scope

Useful contributions are small and focused, for example:

- correcting menu-item field behavior
- tightening validation without discarding saved values unnecessarily
- correcting documentation

Do not commit secrets, credentials, customer data, or private operational details.

## Pull requests

1. Create a branch from `main`.
2. Keep the change focused.
3. Check PHP syntax locally:

   ```bash
   php -l menu-item-extended-fields.php
   php -l uninstall.php
   ```

4. Push the branch and wait until the **WordPress plugin blueprint** workflow is green.
5. Open a pull request only after that branch check has passed.

This repository follows the Cem Firat WordPress plugin blueprint (`simple` profile, version 0.2.0). `assets/logo.svg` and `LICENSE` are exact-managed files; do not rewrite them locally.

Describe behavior changes in `CHANGELOG.md` and `readme.txt` when the plugin version changes.
