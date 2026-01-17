<?php

namespace TestFixtures\Module;

/**
 * Controller class that should NOT use legacy response methods.
 */
class LegacyController
{
    public function methodWithHeader(): void
    {
        header('Location: /home');
    }

    public function methodWithHttpResponseCode(): void
    {
        http_response_code(302);
    }

    public function methodWithDie(): void
    {
        die();
    }

    public function methodWithExit(): void
    {
        exit(1);
    }

    public function methodWithEcho(): void
    {
        echo 'Hello World';
    }
}
