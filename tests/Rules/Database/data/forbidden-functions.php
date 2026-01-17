<?php

// Test file for ForbiddenFunctionsRule - these should all trigger errors

sqlQuery('SELECT * FROM users');
sqlStatement('UPDATE users SET name = ?', ['John']);
sqlInsert('INSERT INTO users (name) VALUES (?)', ['Jane']);
sqlFetchArray($result);
sqlBeginTrans();
sqlCommitTrans();
sqlRollbackTrans();
