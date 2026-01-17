<?php

// Test file for ForbiddenClassesRule - these imports should trigger errors

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\TableGateway;
