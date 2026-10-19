<?php

// Test file for DuplicateGlobalFunctionRule: a namespace's own function_exists()

namespace OpenCoreEMR\PHPStan\Tests\Rules\Functions\Shadowed;

function function_exists(string $name): bool
{
    return false;
}
