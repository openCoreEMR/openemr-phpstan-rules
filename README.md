# PHPStan Rules for OpenEMR

Composer-installable PHPStan rules for OpenEMR core and module development. Enforces modern coding patterns and best practices.

## Installation

```bash
composer require --dev openemr/phpstan-openemr-rules
```

## Usage

### For OpenEMR Core Development

Include the core ruleset in your `phpstan.neon`:

```neon
includes:
    - vendor/openemr/phpstan-openemr-rules/core.neon
```

### For OpenEMR Module Development

Include the module ruleset in your `phpstan.neon`:

```neon
includes:
    - vendor/openemr/phpstan-openemr-rules/module.neon
```

## Rules

### Core Rules (for OpenEMR Core and Modules)

#### Database Rules

**ForbiddenFunctionsRule**
- **Forbids:** Legacy `sql.inc.php` functions (`sqlQuery`, `sqlStatement`, `sqlInsert`, etc.)
- **Requires:** `QueryUtils` methods instead
- **Example:**
  ```php
  // ❌ Forbidden
  $result = sqlStatement($sql, $binds);

  // ✅ Required
  $records = QueryUtils::fetchRecords($sql, $binds);
  ```

**ForbiddenClassesRule**
- **Forbids:** Laminas-DB classes (`Laminas\Db\Adapter`, `Laminas\Db\Sql`, etc.)
- **Requires:** `QueryUtils` or `DatabaseQueryTrait`

#### Globals Rules

**ForbiddenGlobalsAccessRule**
- **Forbids:** Direct `$GLOBALS` array access
- **Requires:** `OEGlobalsBag::getInstance()`
- **Example:**
  ```php
  // ❌ Forbidden
  $value = $GLOBALS['some_setting'];

  // ✅ Required
  $globals = OEGlobalsBag::getInstance();
  $value = $globals->get('some_setting');
  ```

#### Testing Rules

**NoCoversAnnotationRule**
- **Forbids:** `@covers` annotations on test methods
- **Rationale:** Excludes transitively used code from coverage reports

**NoCoversAnnotationOnClassRule**
- **Forbids:** `@covers` annotations on test classes
- **Rationale:** Same as above - incomplete coverage tracking

### Module-Specific Rules (for OpenEMR Modules Only)

These additional rules enforce Symfony-inspired MVC patterns in OpenEMR modules.

#### CatchThrowableNotExceptionRule
- **Forbids:** `catch (\Exception $e)`
- **Requires:** `catch (\Throwable $e)`
- **Rationale:** Catches both exceptions and errors (`TypeError`, `ParseError`, etc.)
- **Example:**
  ```php
  // ❌ Forbidden
  try {
      $service->doSomething();
  } catch (\Exception $e) {
      // Misses TypeError, ParseError, etc.
  }

  // ✅ Required
  try {
      $service->doSomething();
  } catch (\Throwable $e) {
      // Catches everything
  }
  ```

#### NoSuperGlobalsInControllersRule
- **Forbids:** `$_GET`, `$_POST`, `$_FILES`, `$_SERVER` in Controller classes
- **Requires:** Symfony `Request` object methods
- **Example:**
  ```php
  // ❌ Forbidden in controllers
  $name = $_POST['name'];
  $filter = $_GET['filter'];

  // ✅ Required
  $request = Request::createFromGlobals();
  $name = $request->request->get('name');
  $filter = $request->query->get('filter');
  ```

#### NoLegacyResponseMethodsRule
- **Forbids:** `header()`, `http_response_code()`, `die()`, `exit`, direct `echo` in controllers
- **Requires:** Symfony `Response` objects
- **Example:**
  ```php
  // ❌ Forbidden in controllers
  header('Location: /some/path');
  http_response_code(404);
  echo json_encode($data);
  die('Error');

  // ✅ Required
  return new RedirectResponse('/some/path');
  return new Response($content, 404);
  return new JsonResponse($data);
  throw new ModuleException('Error');
  ```

#### ControllersMustReturnResponseRule
- **Forbids:** Controller methods returning `void` or no return type
- **Requires:** Return type declaration of `Response` or subclass
- **Example:**
  ```php
  // ❌ Forbidden
  public function handleRequest(): void
  {
      // ...
  }

  // ✅ Required
  public function handleRequest(): Response
  {
      return new Response($content);
  }
  ```

## Rule Configuration

You can selectively enable rules by creating your own configuration:

```neon
# Custom phpstan.neon
services:
    # Just database rules
    - class: OpenEMR\PHPStan\Rules\Database\ForbiddenFunctionsRule
      tags:
          - phpstan.rules.rule

    # Just module controller rules
    - class: OpenEMR\PHPStan\Rules\Module\NoSuperGlobalsInControllersRule
      tags:
          - phpstan.rules.rule
```

## Baselines

If you're adding these rules to an existing codebase, generate a baseline to exclude existing violations:

```bash
vendor/bin/phpstan analyze --generate-baseline
```

New code will still be checked against all rules.

## Migration Guides

See [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) for detailed migration patterns for each rule.

## Development

### Running Tests

```bash
# Install dependencies
composer install

# Run PHPStan on the rules themselves
vendor/bin/phpstan analyze
```

## Contributing

Contributions are welcome! Please:

1. Follow existing code style and patterns
2. Add tests for new rules
3. Update documentation

## License

GNU General Public License v3.0 or later. See OpenEMR's main repository for full license text.

## Authors

- Michael A. Smith <michael@opencoreemr.com>

## Links

- [OpenEMR](https://www.open-emr.org)
- [OpenCoreEMR](https://opencoreemr.com)
- [PHPStan Documentation](https://phpstan.org)
