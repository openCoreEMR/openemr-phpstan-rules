<?php

// Test file for DuplicateGlobalFunctionRule: same namespace as shadowed-function-exists.php

namespace OpenCoreEMR\PHPStan\Tests\Rules\Functions\Shadowed;

if (!function_exists('OpenCoreEMR\PHPStan\Tests\Rules\Functions\Shadowed\shadowedHelper')) {
    function shadowedHelper()
    {
    }
}
