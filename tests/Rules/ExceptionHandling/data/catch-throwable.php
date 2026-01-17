<?php

// Test file for CatchThrowableNotExceptionRule - these should NOT trigger errors

try {
    riskyOperation();
} catch (\Throwable $e) {
    // Good: catching Throwable
}

try {
    anotherOperation();
} catch (Throwable $e) {
    // Good: catching Throwable (unqualified)
}

try {
    specificOperation();
} catch (\InvalidArgumentException $e) {
    // Good: catching specific exception subclass is allowed
}

try {
    runtimeOperation();
} catch (\RuntimeException $e) {
    // Good: catching specific exception subclass is allowed
}
