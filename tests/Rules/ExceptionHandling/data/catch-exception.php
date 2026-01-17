<?php

// Test file for CatchThrowableNotExceptionRule - these should trigger errors

try {
    riskyOperation();
} catch (Exception $e) {
    // Bad: catching Exception
}

try {
    anotherOperation();
} catch (\Exception $e) {
    // Bad: catching \Exception (fully qualified)
}
