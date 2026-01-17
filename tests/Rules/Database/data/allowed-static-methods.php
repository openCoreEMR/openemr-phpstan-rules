<?php

// Test file for ForbiddenStaticMethodsRule - these should NOT trigger errors

namespace OpenEMR\Common\Database;

class QueryUtils
{
    public static function inTransaction(callable $callback): mixed
    {
        return $callback();
    }

    public static function fetchRecords(string $sql): array
    {
        return [];
    }
}

// These are allowed
QueryUtils::inTransaction(function () {
    // do stuff
});

QueryUtils::fetchRecords('SELECT * FROM users');

// Other classes are not affected
class OtherClass
{
    public static function startTransaction(): void
    {
    }
}

OtherClass::startTransaction();
