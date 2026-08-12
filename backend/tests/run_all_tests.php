<?php
// Run all tests
echo "\n" . str_repeat("=", 60) . "\n";
echo "      COURSE REGISTRATION SYSTEM - TEST SUITE\n";
echo str_repeat("=", 60) . "\n\n";

// Check database connection first
try {
    require_once __DIR__ . '/../config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connected\n\n";
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Run test files
$testFiles = [
    'AuthTest.php',
    'CourseTest.php',
    'RegistrationTest.php'
];

$allPassed = true;
foreach ($testFiles as $file) {
    echo "\n📝 Running " . $file . "\n";
    echo str_repeat("-", 40) . "\n";
    include __DIR__ . '/' . $file;
    echo str_repeat("-", 40) . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ ALL TESTS COMPLETED\n";
echo str_repeat("=", 60) . "\n";

// Show summary
if (file_exists(__DIR__ . '/test_results.json')) {
    $results = json_decode(file_get_contents(__DIR__ . '/test_results.json'), true);
    if ($results) {
        echo "\n📊 Summary:\n";
        echo "   Total Tests: " . $results['total'] . "\n";
        echo "   ✅ Passed: " . $results['passed'] . "\n";
        echo "   ❌ Failed: " . $results['failed'] . "\n";
        echo "   📅 Run at: " . $results['timestamp'] . "\n";
    }
}
?>