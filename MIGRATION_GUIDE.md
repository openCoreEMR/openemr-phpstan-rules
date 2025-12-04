# Migration Guide: OpenEMR PHPStan Rules

This guide provides migration patterns for each rule in the package.

## Table of Contents

- [Database Rules](#database-rules)
- [Globals Rules](#globals-rules)
- [Testing Rules](#testing-rules)
- [Module Rules](#module-rules)

---

## Database Rules

### ForbiddenFunctionsRule: Migrate from Legacy SQL Functions

**Why?** Legacy `sql.inc.php` functions lack proper error handling and type safety.

#### sqlStatement → QueryUtils::fetchRecords()

```php
// ❌ Before
$result = sqlStatement($sql, $binds);
while ($row = sqlFetchArray($result)) {
    echo $row['name'];
}

// ✅ After
$records = QueryUtils::fetchRecords($sql, $binds);
foreach ($records as $row) {
    echo $row['name'];
}
```

#### sqlQuery → QueryUtils::querySingleRow()

```php
// ❌ Before
$row = sqlQuery($sql, $binds);
$name = $row['name'];

// ✅ After
$row = QueryUtils::querySingleRow($sql, $binds);
$name = $row['name'];
```

#### sqlInsert → QueryUtils::sqlStatementThrowException()

```php
// ❌ Before
sqlInsert($sql);

// ✅ After
QueryUtils::sqlStatementThrowException($sql);
```

#### Transactions

```php
// ❌ Before
sqlBeginTrans();
// ... operations ...
sqlCommitTrans();
// ... or ...
sqlRollbackTrans();

// ✅ After
QueryUtils::startTransaction();
// ... operations ...
QueryUtils::commitTransaction();
// ... or ...
QueryUtils::rollbackTransaction();
```

---

## Globals Rules

### ForbiddenGlobalsAccessRule: Migrate from $GLOBALS

**Why?** `OEGlobalsBag` provides testability, type safety, and dependency injection support.

#### Basic Access

```php
// ❌ Before
$siteName = $GLOBALS['sitename'];
$timeout = $GLOBALS['timeout'];

// ✅ After
use OpenEMR\Core\OEGlobalsBag;

$globals = OEGlobalsBag::getInstance();
$siteName = $globals->get('sitename');
$timeout = $globals->get('timeout');
```

#### With Default Values

```php
// ❌ Before
$timezone = $GLOBALS['gbl_time_zone'] ?? 'UTC';

// ✅ After
use OpenEMR\Core\OEGlobalsBag;

$globals = OEGlobalsBag::getInstance();
$timezone = $globals->get('gbl_time_zone', 'UTC');
```

#### Setting Values

```php
// ❌ Before
$GLOBALS['some_setting'] = 'new value';

// ✅ After
use OpenEMR\Core\OEGlobalsBag;

$globals = OEGlobalsBag::getInstance();
$globals->set('some_setting', 'new value');
```

#### Checking Key Existence

```php
// ❌ Before
if (isset($GLOBALS['some_key'])) {
    $value = $GLOBALS['some_key'];
}

// ✅ After
use OpenEMR\Core\OEGlobalsBag;

$globals = OEGlobalsBag::getInstance();
if ($globals->has('some_key')) {
    $value = $globals->get('some_key');
}
```

#### Dependency Injection (Preferred)

```php
// ✅ Best Practice
class MyService
{
    public function __construct(
        private readonly OEGlobalsBag $globals
    ) {}

    public function doSomething(): void
    {
        $setting = $this->globals->get('some_setting');
    }
}

// Usage
$globals = OEGlobalsBag::getInstance();
$service = new MyService($globals);
```

---

## Testing Rules

### NoCoversAnnotationRule: Remove @covers from Tests

**Why?** `@covers` annotations exclude transitively used code from coverage reports, giving incomplete coverage information.

```php
// ❌ Before
/**
 * @covers \OpenEMR\Services\SomeService
 */
public function testSomeMethod(): void
{
    $service = new SomeService();
    $result = $service->process();
    $this->assertTrue($result);
}

// ✅ After
public function testSomeMethod(): void
{
    $service = new SomeService();
    $result = $service->process();
    $this->assertTrue($result);
}
```

### NoCoversAnnotationOnClassRule: Remove @covers from Test Classes

```php
// ❌ Before
/**
 * @covers \OpenEMR\Services\SomeService
 */
class SomeServiceTest extends TestCase
{
    // tests
}

// ✅ After
class SomeServiceTest extends TestCase
{
    // tests
}
```

---

## Module Rules

These rules apply to OpenEMR module development using the Symfony-inspired MVC pattern.

### CatchThrowableNotExceptionRule: Catch Errors Too

**Why?** `\Throwable` catches both `\Exception` and `\Error` (including `TypeError`, `ParseError`, etc.).

```php
// ❌ Before - Misses errors
try {
    $this->service->doSomething();
} catch (\Exception $e) {
    $this->logger->error($e->getMessage());
}

// ✅ After - Catches everything
try {
    $this->service->doSomething();
} catch (\Throwable $e) {
    $this->logger->error($e->getMessage());
}
```

### NoSuperGlobalsInControllersRule: Use Request Object

**Why?** Symfony Request object provides better testability and type safety.

#### GET Parameters

```php
// ❌ Before
public function showList(): Response
{
    $filter = $_GET['filter'] ?? '';
    $page = (int)($_GET['page'] ?? 1);
}

// ✅ After
public function showList(Request $request): Response
{
    $filter = $request->query->get('filter', '');
    $page = (int)$request->query->get('page', 1);
}
```

#### POST Parameters

```php
// ❌ Before
public function handleSubmit(): Response
{
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
}

// ✅ After
public function handleSubmit(Request $request): Response
{
    $name = (string)$request->request->get('name', '');
    $email = (string)$request->request->get('email', '');
}
```

#### File Uploads

```php
// ❌ Before
public function handleUpload(): Response
{
    $file = $_FILES['document'];
    $filePath = $file['tmp_name'];
}

// ✅ After
public function handleUpload(Request $request): Response
{
    $uploadedFile = $request->files->get('document');
    if ($uploadedFile && $uploadedFile->isValid()) {
        $filePath = $uploadedFile->getPathname();
    }
}
```

#### Check HTTP Method

```php
// ❌ Before
public function handleForm(): Response
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // process
    }
}

// ✅ After
public function handleForm(Request $request): Response
{
    if ($request->isMethod('POST')) {
        // process
    }
}
```

#### Controller Dispatch Pattern

```php
// ✅ Standard Pattern
public function dispatch(string $action): Response
{
    $request = Request::createFromGlobals();

    return match ($action) {
        'create' => $this->handleCreate($request),
        'view' => $this->showView($request),
        'list' => $this->showList($request),
        default => $this->showList($request),
    };
}
```

### NoLegacyResponseMethodsRule: Use Response Objects

**Why?** Response objects enable proper testing and follow Symfony best practices.

#### Redirects

```php
// ❌ Before
public function afterSubmit(): void
{
    header('Location: /some/path');
    exit;
}

// ✅ After
public function afterSubmit(): Response
{
    return new RedirectResponse('/some/path');
}
```

#### JSON Responses

```php
// ❌ Before
public function apiEndpoint(): void
{
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// ✅ After
public function apiEndpoint(): Response
{
    return new JsonResponse(['status' => 'success']);
}
```

#### Error Responses

```php
// ❌ Before
public function handleError(): void
{
    http_response_code(404);
    echo "Not found";
    die();
}

// ✅ After
public function handleError(): Response
{
    throw new ModuleNotFoundException("Resource not found");
    // Or return error response:
    // return new Response("Not found", Response::HTTP_NOT_FOUND);
}
```

#### File Downloads

```php
// ❌ Before
public function downloadFile(): void
{
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="file.pdf"');
    readfile($filePath);
    exit;
}

// ✅ After
public function downloadFile(): Response
{
    $response = new BinaryFileResponse($filePath);
    $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'file.pdf'
    );
    return $response;
}
```

### ControllersMustReturnResponseRule: Declare Return Types

**Why?** Explicit return types improve type safety and IDE support.

```php
// ❌ Before - No return type
public function handleRequest()
{
    $content = $this->twig->render('template.html.twig', []);
    return new Response($content);
}

// ❌ Before - Void return type
public function handleRequest(): void
{
    echo $this->twig->render('template.html.twig', []);
}

// ✅ After - Explicit Response type
public function handleRequest(): Response
{
    $content = $this->twig->render('template.html.twig', []);
    return new Response($content);
}
```

#### Common Response Types

```php
// HTML Response
public function showPage(): Response
{
    $content = $this->twig->render('page.html.twig', $data);
    return new Response($content);
}

// JSON Response
public function apiEndpoint(): JsonResponse
{
    return new JsonResponse(['key' => 'value']);
}

// Redirect Response
public function afterAction(): RedirectResponse
{
    return new RedirectResponse($url);
}

// File Download
public function download(): BinaryFileResponse
{
    return new BinaryFileResponse($filePath);
}
```

---

## Common Patterns

### Complete Controller Example

```php
<?php

namespace OpenCoreEMR\Modules\MyModule\Controller;

use OpenCoreEMR\Modules\MyModule\Service\MyService;
use OpenEMR\Common\Csrf\CsrfUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

class MyController
{
    public function __construct(
        private readonly MyService $service,
        private readonly Environment $twig
    ) {}

    public function dispatch(string $action): Response
    {
        $request = Request::createFromGlobals();

        return match ($action) {
            'create' => $this->handleCreate($request),
            'list' => $this->showList($request),
            default => $this->showList($request),
        };
    }

    private function showList(Request $request): Response
    {
        $filter = $request->query->get('filter', '');
        $items = $this->service->getItems($filter);

        $content = $this->twig->render('list.html.twig', [
            'items' => $items,
            'csrf_token' => CsrfUtils::collectCsrfToken(),
        ]);

        return new Response($content);
    }

    private function handleCreate(Request $request): Response
    {
        if (!$request->isMethod('POST')) {
            return new RedirectResponse($request->server->get('SCRIPT_NAME'));
        }

        if (!CsrfUtils::verifyCsrfToken($request->request->get('csrf_token', ''))) {
            throw new ModuleAccessDeniedException("CSRF verification failed");
        }

        try {
            $name = (string)$request->request->get('name', '');
            $this->service->create(['name' => $name]);

            return new RedirectResponse($request->server->get('SCRIPT_NAME'));
        } catch (\Throwable $e) {
            throw new ModuleException("Error creating item: " . $e->getMessage());
        }
    }
}
```

---

## Gradual Migration

You don't need to migrate everything at once:

1. **New code**: Always follow these patterns
2. **Modified code**: When touching a file, migrate violations in that section
3. **Existing code**: Generate a baseline to exclude existing violations

```bash
vendor/bin/phpstan analyze --generate-baseline
```

## Questions?

See the [README.md](README.md) for rule descriptions and links to documentation.
