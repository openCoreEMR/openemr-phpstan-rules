<?php

// Test file for ForbiddenFunctionsRule - these should NOT trigger errors

// Regular function calls that are allowed
strlen('test');
array_map(fn($x) => $x * 2, [1, 2, 3]);
json_encode(['key' => 'value']);

// Method calls are allowed (only global functions are forbidden)
$db->sqlQuery('SELECT * FROM users');
$utils->sqlStatement('UPDATE users SET name = ?', ['John']);
