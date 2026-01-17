<?php

// Test file for ForbiddenCurlFunctionsRule - these should NOT trigger errors

// Using file_get_contents is fine (not curl)
$response = file_get_contents('https://example.com');

// Regular functions that happen to contain "curl" in their name are fine
function my_curly_function(): void
{
}

my_curly_function();

// Class methods are also fine
class HttpClient
{
    public function get(string $url): string
    {
        return '';
    }
}

$client = new HttpClient();
$client->get('https://example.com');
