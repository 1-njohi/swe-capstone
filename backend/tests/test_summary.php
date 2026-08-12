<?php
// Display test results in a nice format
$resultsFile = __DIR__ . '/test_results.json';

if (!file_exists($resultsFile)) {
    echo "No test results found. Run the tests first.\n";
    exit;
}

$results = json_decode(file_get_contents($resultsFile), true);

echo "\n" . str_repeat("=", 60) . "\n";
echo "      TEST RESULTS\n";
echo str_repeat("=", 60) . "\n";
echo "📅 Run at: " . $results['timestamp'] . "\n";
echo "📊 Total: " . $results['total'] . " tests\n";
echo "✅ Passed: " . $results['passed'] . "\n";
echo "❌ Failed: " . $results['failed'] . "\n";
echo "📈 Success Rate: " . round($results['passed'] / $results['total'] * 100, 1) . "%\n";
echo str_repeat("=", 60) . "\n\n";

if ($results['failed'] > 0) {
    echo "❌ Failed Tests:\n";
    foreach ($results['results'] as $test) {
        if ($test['status'] !== 'PASS') {
            echo "   - " . $test['name'] . "\n";
            if (isset($test['message'])) {
                echo "     Error: " . $test['message'] . "\n";
            }
        }
    }
} else {
    echo "🎉 ALL TESTS PASSED!\n";
}
?>