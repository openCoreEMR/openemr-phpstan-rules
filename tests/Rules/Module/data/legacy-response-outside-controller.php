<?php

namespace TestFixtures\Module;

/**
 * Non-controller class - legacy response methods are allowed here.
 */
class LegacyService
{
    public function doSomething(): void
    {
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(['status' => 'ok']);
    }
}

/**
 * Another non-controller class.
 */
class ErrorHandler
{
    public function handleFatal(): void
    {
        die('Fatal error');
    }
}
