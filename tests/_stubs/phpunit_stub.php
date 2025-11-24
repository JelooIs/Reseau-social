<?php
// Minimal PHPUnit TestCase stub to satisfy static analysis when phpunit isn't installed.
// This file is only loaded by tests/bootstrap.php if vendor/autoload.php is missing.

namespace PHPUnit\Framework {
    if (!class_exists('PHPUnit\\Framework\\TestCase')) {
        abstract class TestCase {
            // Provide empty assertion methods used in our tests so static analysis doesn't fail.
            public function assertTrue($condition, $message = '') {}
            public function assertIsArray($var, $message = '') {}
            public function assertEquals($expected, $actual, $message = '') {}
            public function assertNotEmpty($var, $message = '') {}
            public function assertFalse($condition, $message = '') {}
            public function assertCount($expectedCount, $haystack, $message = '') {}
        }
    }
}
