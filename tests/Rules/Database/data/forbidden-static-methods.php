<?php

// Test file for ForbiddenStaticMethodsRule - these should trigger errors

namespace OpenEMR\Common\Database;

class QueryUtils
{
    public static function startTransaction(): void
    {
    }

    public static function commitTransaction(): void
    {
    }

    public static function rollbackTransaction(): void
    {
    }

    public static function inTransaction(callable $callback): mixed
    {
        return $callback();
    }
}

// These should trigger errors
QueryUtils::startTransaction();
QueryUtils::commitTransaction();
QueryUtils::rollbackTransaction();
