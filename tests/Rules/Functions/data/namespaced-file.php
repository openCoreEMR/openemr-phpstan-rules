<?php

// Test file for DuplicateGlobalFunctionRule: a namespaced function is a different name

namespace OpenCoreEMR\PHPStan\Tests\Rules\Functions\Data;

function sharedHelper()
{
}

if (!function_exists('OpenCoreEMR\PHPStan\Tests\Rules\Functions\Data\onlyInFirstFile')) {
    function onlyInFirstFile()
    {
    }
}
