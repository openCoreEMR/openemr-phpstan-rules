<?php

namespace TestFixtures\Module;

/**
 * Controller with void return type.
 */
class VoidReturnController
{
    public function index(): void
    {
        // Bad: controller returns void
    }
}
