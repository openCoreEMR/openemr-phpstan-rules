<?php

// Test file for DuplicateGlobalFunctionRule: same namespace as namespaced-file.php

namespace OpenCoreEMR\PHPStan\Tests\Rules\Functions\Data;

function sharedHelper()
{
}

function onlyInFirstFile()
{
}
