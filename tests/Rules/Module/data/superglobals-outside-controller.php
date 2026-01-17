<?php

namespace TestFixtures\Module;

/**
 * Non-controller class - superglobals are allowed here.
 */
class UserService
{
    public function getQueryParam(): string
    {
        return $_GET['q'] ?? '';
    }
}

/**
 * Another non-controller class.
 */
class RequestHelper
{
    public function getPostData(): array
    {
        return $_POST;
    }
}
