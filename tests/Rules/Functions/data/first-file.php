<?php

// Test file for DuplicateGlobalFunctionRule, read together with second-file.php

function sharedHelper()
{
}

if (!function_exists('guardedInBoth')) {
    function guardedInBoth()
    {
    }
}

if (!function_exists('guardedHereOnly')) {
    function guardedHereOnly()
    {
    }
}

function MixedCaseName()
{
}

function onlyInFirstFile()
{
}

function guardNamesAnotherFunction()
{
}
