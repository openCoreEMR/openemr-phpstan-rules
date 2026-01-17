<?php

// Test file for ForbiddenMethodsRule - these should NOT trigger errors

class UserService
{
    public function generateId(): int
    {
        return 1;
    }

    public function getId(): int
    {
        return 1;
    }
}

$service = new UserService();
$id = $service->generateId();
$id = $service->getId();
