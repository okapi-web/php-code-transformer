# Mago checks

Mago is pinned to [1.59.0](https://github.com/carthage-software/mago/releases/tag/1.59.0)
as a development dependency. Install dependencies with `composer install`, then run:

```sh
composer check         # formatting, linter, and analyzer for source and tests
composer format-check  # check formatting without changing files
composer lint
composer analyze
composer format        # apply formatting
```

`mago.toml` checks both `src` and `tests`, targeting PHP 8.1, the minimum supported
version. Dependencies provide type information but are not
checked. Generated files in `tests/cache`, `tests/coverage`, and `tests/tmp` are
excluded.

The linter settings follow PHP-AOP: strict types, named arguments, boolean flags,
and `isset` retain the existing API conventions. Aggregate complexity metrics are
left to review, and the parameter-count threshold is 10. Analyzer missing-type
checks are enabled, and checks fail on findings at note severity or higher.

Formatting, lint, and analyzer checks pass without a baseline or vendor type patch.
`Metadata` has one documented `too-many-properties` expectation because its public
fields mirror PHP stream metadata. All other findings are addressed in source and
tests. The CI quality job runs `composer check`.

The scripts invoke the project-local PHP launcher. Its first invocation downloads
the matching native binary; CI supplies `GITHUB_TOKEN` for that download. On
Windows, the launcher needs PHP's ZIP extension and a trusted CA certificate
bundle configured for HTTPS downloads.
