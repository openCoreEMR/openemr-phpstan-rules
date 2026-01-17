<?php

namespace TestFixtures\Module;

/**
 * Controller class that should NOT use superglobals.
 */
class UserController
{
    public function index(): void
    {
        $query = $_GET['q'];
        $name = $_POST['name'];
        $file = $_FILES['upload'];
        $host = $_SERVER['HTTP_HOST'];
    }
}
