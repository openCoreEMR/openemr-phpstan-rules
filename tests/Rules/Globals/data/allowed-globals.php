<?php

// Test file for ForbiddenGlobalsAccessRule - these should NOT trigger errors

// Using superglobals other than $GLOBALS is allowed by this rule
$query = $_GET['q'];
$name = $_POST['name'];

// Regular array access is allowed
$arr = ['key' => 'value'];
$value = $arr['key'];

// Variable named GLOBALS (not superglobal) is allowed
$notGlobals = 'test';
