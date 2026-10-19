<?php

// Test file for DuplicateGlobalFunctionRule, read together with first-file.php

function sharedHelper()
{
}

if (!function_exists('guardedInBoth')) {
    function guardedInBoth()
    {
    }
}

function guardedHereOnly()
{
}

function mixedcasename()
{
}

if (!function_exists('somethingElse')) {
    function guardNamesAnotherFunction()
    {
    }
}

if (PHP_VERSION_ID >= 80000) {
    function declaredTwiceInOneFile()
    {
    }
} else {
    function declaredTwiceInOneFile()
    {
    }
}

if (!function_exists('compoundGuarded') and PHP_VERSION_ID >= 80000) {
    function compoundGuarded()
    {
    }
}

if (!function_exists('eitherOrGuarded') || PHP_VERSION_ID >= 80000) {
    function eitherOrGuarded()
    {
    }
}
