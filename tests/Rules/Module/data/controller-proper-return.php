<?php

namespace TestFixtures\Module;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller with proper return types.
 */
class ProperController
{
    public function index(): Response
    {
        return new Response('Hello');
    }

    public function api(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }

    /**
     * Private methods are exempt from this rule.
     */
    private function helper(): void
    {
        // Private methods can return void
    }

    /**
     * Magic methods are exempt from this rule.
     */
    public function __construct()
    {
        // Constructor is exempt
    }
}

/**
 * Non-controller class - no restrictions.
 */
class SomeService
{
    public function doSomething(): void
    {
        // Non-controllers can return void
    }
}
