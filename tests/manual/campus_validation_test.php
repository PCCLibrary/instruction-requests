<?php

use App\Http\Requests\UpdateCampusRequest;
use App\Http\Requests\CreateCampusRequest;

/**
 * Manual test script to verify campus validation fixes
 *
 * Run with: php artisan tinker
 * Then: include 'tests/manual/campus_validation_test.php';
 */

echo "=== Campus Validation Fix Test ===\n";

// Test 1: Valid calendar ID should pass
echo "\nTest 1: Valid calendar ID format\n";
$validCalendarId = 'c_abc123def456@group.calendar.google.com';

try {
    $request = new UpdateCampusRequest();
    $rules = $request->rules();

    echo "✅ UpdateCampusRequest rules loaded successfully\n";
    echo "gcal validation rules: " . json_encode($rules['gcal']) . "\n";

    // Check if regex rule exists
    $hasRegex = false;
    foreach ($rules['gcal'] as $rule) {
        if (is_string($rule) && strpos($rule, 'regex:') === 0) {
            $hasRegex = true;
            echo "✅ Regex validation rule found: $rule\n";
        }
    }

    if (!$hasRegex) {
        echo "❌ Missing regex validation rule\n";
    }

} catch (Exception $e) {
    echo "❌ Error loading UpdateCampusRequest: " . $e->getMessage() . "\n";
}

// Test 2: Check CreateCampusRequest consistency
echo "\nTest 2: CreateCampusRequest consistency\n";

try {
    $createRequest = new CreateCampusRequest();
    $createRules = $createRequest->rules();

    echo "✅ CreateCampusRequest rules loaded successfully\n";
    echo "gcal validation rules: " . json_encode($createRules['gcal']) . "\n";

    // Compare rules
    $updateRules = (new UpdateCampusRequest())->rules();
    $gcalRulesMatch = $updateRules['gcal'] == $createRules['gcal'];

    if ($gcalRulesMatch) {
        echo "✅ gcal validation rules match between Create and Update requests\n";
    } else {
        echo "❌ gcal validation rules differ between Create and Update requests\n";
    }

} catch (Exception $e) {
    echo "❌ Error loading CreateCampusRequest: " . $e->getMessage() . "\n";
}

// Test 3: Check custom error messages
echo "\nTest 3: Custom error messages\n";

try {
    $updateRequest = new UpdateCampusRequest();
    $messages = $updateRequest->messages();

    if (isset($messages['gcal.regex'])) {
        echo "✅ Custom regex error message found: " . $messages['gcal.regex'] . "\n";
    } else {
        echo "❌ Missing custom regex error message\n";
    }

    // Check old url message is removed
    if (isset($messages['gcal.url'])) {
        echo "❌ Old 'gcal.url' message still exists (should be removed)\n";
    } else {
        echo "✅ Old 'gcal.url' message successfully removed\n";
    }

} catch (Exception $e) {
    echo "❌ Error checking messages: " . $e->getMessage() . "\n";
}

// Test 4: Test regex pattern directly
echo "\nTest 4: Test regex pattern directly\n";

$pattern = '/^[a-z0-9._]+@group\.calendar\.google\.com$/';
$testCases = [
    ['c_abc123@group.calendar.google.com', true, 'Valid c_ prefixed format'],
    ['c_gcjdt32rchu9uk1i259k5nngdc@group.calendar.google.com', true, 'Valid c_ with mixed chars'],
    ['pcc.edu_rmshf5pbpi5qar07jvnu5eph8c@group.calendar.google.com', true, 'Valid domain prefixed format'],
    ['4pp219iaud759ol1k3l6r62tic@group.calendar.google.com', true, 'Valid number prefixed format'],
    ['test.calendar_123@group.calendar.google.com', true, 'Valid mixed format with dots and underscores'],
    ['c_@group.calendar.google.com', false, 'Empty ID part'],
    ['abc123@group.calendar.google.com', true, 'Valid simple format'],
    ['c_abc123@calendar.google.com', false, 'Missing group subdomain'],
    ['https://calendar.google.com/...', false, 'URL format (old)'],
    ['', false, 'Empty string'],
    ['c_ABC123@group.calendar.google.com', false, 'Uppercase characters (should fail)'],
    ['c_abc123xyz@group.calendar.google.com', true, 'Letters and numbers (should pass now)'],
];

foreach ($testCases as [$input, $expected, $description]) {
    $result = preg_match($pattern, $input);
    $passed = ($result === 1) === $expected;

    $status = $passed ? '✅' : '❌';
    $expectedStr = $expected ? 'PASS' : 'FAIL';
    $actualStr = $result === 1 ? 'PASS' : 'FAIL';

    echo "$status $description: '$input' -> Expected: $expectedStr, Got: $actualStr\n";
}

echo "\n=== Test Complete ===\n";
