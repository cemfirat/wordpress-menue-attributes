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
3. Check PHP syntax and the focused contracts locally:

   ```bash
   php -l menu-item-extended-fields.php
   php -l uninstall.php
   php -l tests/attribute-contract.php
   php tests/attribute-contract.php
   node --test tests/dom-patcher.test.cjs
   ```

4. Obtain an independent review of the complete candidate and resolve findings.
5. Push the tested candidate and wait until the **WordPress plugin blueprint** branch workflow is green for that exact commit.
6. Open or reopen the existing pull request only after that branch check has passed. Preserve an existing task/PR instead of creating a duplicate. Its own pull-request workflow must also pass.
7. Before merging, recheck the base, candidate, review and required checks. Use the repository's normal merge path and verify the integrated file tree against the checked candidate. Record the resulting commit and checks in the task/PR.

The tests execute the actual PHP callbacks and JavaScript, with explicit WordPress and DOM test doubles. They do not establish live WordPress, role or theme acceptance. `PHP_BIN=/path/to/php` selects an isolated PHP executable for the Node test. CI uses PHP 8.3 and Node.js 22; preserve the plugin's PHP 7.0 syntax contract when changing runtime code.

This repository follows the Cem Firat WordPress plugin blueprint (`simple` profile, version 0.2.0). `assets/logo.svg` and `LICENSE` are exact-managed files; do not rewrite them locally.

Describe pending behavior changes under `Unreleased` in `CHANGELOG.md` and `readme.txt`; change the plugin version only as part of an authorized version change. Keep the project handoff in `README.md` and the concrete task/PR current. Distinguish prepared, implemented, checked, integrated, published, installed and accepted states with their own evidence.
