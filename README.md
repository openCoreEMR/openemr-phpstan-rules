# PHPStan Rules for OpenEMR

Composer-installable PHPStan rules for OpenEMR core and module development. Enforces modern coding patterns and best practices.

## Installation

```bash
composer require --dev opencoreemr/openemr-phpstan-rules
```

The rules are automatically loaded via [phpstan/extension-installer](https://github.com/phpstan/extension-installer). No manual configuration needed.

**Important:** Do not manually include `extension.neon` in your phpstan configuration. The extension-installer handles this automatically. Adding a manual include will cause "File included multiple times" warnings.

## Bundled Extensions

This package includes and configures these PHPStan extensions:

- **[spaze/phpstan-disallowed-calls](https://github.com/spaze/phpstan-disallowed-calls)** - Forbids legacy function calls
- **[phpstan/phpstan-deprecation-rules](https://github.com/phpstan/phpstan-deprecation-rules)** - Reports usage of deprecated code

## Rules

### Why Custom Rules Instead of Just `@deprecated`?

This package provides custom rules that forbid specific functions by name (e.g., `sqlQuery()`, `call_user_func()`). You might wonder why we don't just mark these functions as `@deprecated` in OpenEMR and rely on `phpstan-deprecation-rules`.

**The reason: module analysis without OpenEMR loaded.**

When running PHPStan on a standalone OpenEMR module, OpenEMR core may not be installed as a dependency or autoloaded. PHPStan's deprecation rules require the actual function/class definitions to read `@deprecated` annotations. If OpenEMR isn't available at scan-time, those annotations can't be read.

Our custom rules work by **function name matching**, so they catch forbidden calls even when the function definitions aren't available. This ensures modules get the same static analysis protection whether they're analyzed standalone or within a full OpenEMR installation.

### Database Rules

**Disallowed SQL Functions** (via spaze/phpstan-disallowed-calls)
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

### Globals Rules

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

### Testing Rules

**NoCoversAnnotationRule**
- **Forbids:** `@covers` annotations on test methods
- **Rationale:** Excludes transitively used code from coverage reports

**NoCoversAnnotationOnClassRule**
- **Forbids:** `@covers` annotations on test classes
- **Rationale:** Same as above - incomplete coverage tracking

### HTTP Rules

**ForbiddenCurlFunctionsRule**
- **Forbids:** Raw `curl_*` functions (`curl_init`, `curl_exec`, `curl_setopt`, etc.)
- **Requires:** PSR-18 HTTP client
- **Example:**
  ```php
  // ❌ Forbidden
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);

  // ✅ Required - use a PSR-18 HTTP client
  $response = $httpClient->sendRequest($request);
  ```

### Legacy PHP Rules

**Disallowed call_user_func** (via spaze/phpstan-disallowed-calls)
- **Forbids:** `call_user_func()` and `call_user_func_array()`
- **Requires:** First-class callables (PHP 8.1+)
- **Example:**
  ```php
  // ❌ Forbidden
  call_user_func([$object, 'method'], $arg1, $arg2);
  call_user_func_array('someFunction', $args);

  // ✅ Required - first-class callable syntax
  $callable = $object->method(...);
  $callable($arg1, $arg2);

  $callable = someFunction(...);
  $callable(...$args);

  // Static methods
  $callable = SomeClass::staticMethod(...);
  $callable($arg);
  ```

### Exception Handling Rules

**CatchThrowableNotExceptionRule**
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

### Controller Rules

**NoSuperGlobalsInControllersRule**
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

**NoLegacyResponseMethodsRule**
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

**ControllersMustReturnResponseRule**
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

## Baselines

If you're adding these rules to an existing codebase, generate a baseline to exclude existing violations:

```bash
vendor/bin/phpstan analyze --generate-baseline
```

New code will still be checked against all rules.

## Development

### Running Tests

```bash
composer install
vendor/bin/phpunit
```

## License

GNU General Public License v3.0 or later. See LICENSE

## Authors

- Michael A. Smith <michael@opencoreemr.com>

## Links

- [OpenEMR](https://www.open-emr.org)
- [OpenCoreEMR](https://opencoreemr.com)
- [PHPStan Documentation](https://phpstan.org)
