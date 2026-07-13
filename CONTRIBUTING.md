# Contributing

Contributions are more than **welcome**.

We accept contributions via Pull Requests on [GitHub](https://github.com/kununu/collections).

## Development Setup

Install the dependencies:

```bash
composer install
```

The required PHP version is pinned in `composer.json` (`require.php`). Development tooling (PHPUnit, PHPStan, Rector, and the kununu coding standards) is installed as dev dependencies.

## Testing

Run the test suite:

```bash
composer test
```

Run the tests with a coverage report:

```bash
composer test-coverage
```

## Quality Gates

The following checks run in CI and must pass before a pull request can be merged. They can be run locally through the corresponding Composer scripts:

- `composer cs` — PHP CS Fixer with the kununu coding standards
- `composer sniffer` — PHP_CodeSniffer (`composer sniffer-fix` to auto-fix)
- `composer phpstan` — PHPStan static analysis
- `composer rector` — Rector in dry-run mode (`composer rector-fix` to apply)
- `composer test` — PHPUnit test suite

See the `scripts` section of `composer.json` for the full list. CI additionally runs `composer-dependency-analyser`, `composer-require-checker`, and `composer-normalize`.

## Pull Requests

- **[kununu Coding Standards](https://github.com/kununu/code-tools)** — The kununu coding standards are an extension of the [PSR-2](https://github.com/php-fig/fig-standards/blob/main/accepted/PSR-2-coding-style-guide.md). The [kununu/code-tools](https://github.com/kununu/code-tools) package is already a dev dependency and exposes the commands you need to meet our standards.

- **Add tests!** — To ensure a high quality code base, your code patch can't be accepted if it does not have tests.

- **Document any change in behaviour** — Make sure the `CHANGELOG.md`, `README.md`, and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** — We use semantic versioning ([SemVer v2.0.0](https://semver.org/)). Changes to the API must be done with great consideration, and prevented if at all possible.

- **Create feature branches** — `main` is the stable branch, create a new branch for each feature.

- **One pull request per feature** — If you want to do more than one thing, send multiple pull requests.

- **Be respectful** — Be excellent to other contributors. See our [Code of Conduct](CODE_OF_CONDUCT.md).

**Happy coding**!
