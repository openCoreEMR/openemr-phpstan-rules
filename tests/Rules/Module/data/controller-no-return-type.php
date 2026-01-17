<?php

namespace TestFixtures\Module;

/**
 * Controller with missing return type.
 */
class NoReturnTypeController
{
    public function index()
    {
        return 'Hello';
    }
}
