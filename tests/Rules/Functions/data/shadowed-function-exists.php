<?php

// Test file for DuplicateGlobalFunctionRule: in this namespace an unqualified
// function_exists() call resolves to shadowed-declaration.php's function, not
// PHP's, so the if statements here and in shadowed-copy.php are not guards

namespace OpenCoreEMR\PHPStan\Tests\Rules\Functions\Shadowed;

if (!function_exists('OpenCoreEMR\PHPStan\Tests\Rules\Functions\Shadowed\shadowedHelper')) {
    function shadowedHelper()
    {
    }
}
