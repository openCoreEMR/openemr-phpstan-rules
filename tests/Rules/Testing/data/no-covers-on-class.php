<?php

namespace TestFixtures;

/**
 * Test class without the problematic annotation - should NOT trigger error.
 */
class GoodUserServiceTest
{
    public function testCreate(): void
    {
        // Test code
    }
}

class AnotherTest
{
    // No docblock on class
    public function testSomething(): void
    {
        // Test code
    }
}
