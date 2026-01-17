<?php

// Test file for ForbiddenMethodsRule - these should trigger errors

class DatabaseConnection
{
    public function GenID(string $sequence): int
    {
        return 1;
    }
}

$db = new DatabaseConnection();
$id = $db->GenID('users');
