<?php

// Test file for ForbiddenGlobalsAccessRule - these should trigger errors

$value = $GLOBALS['site_id'];
$GLOBALS['setting'] = 'new_value';

class SomeService
{
    public function getSetting(): string
    {
        return $GLOBALS['setting'];
    }
}
