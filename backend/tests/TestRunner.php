<?php
// Simple test runner for our Course Registration System

class TestRunner {
    private $tests = [];
    private $passed = 0;
    private $failed = 0;
    private $results = [];

    public function addTest($name, $callback) {
        $this->tests[] = ['name' => $name, 'callback' => $callback];
    }

    public function run() {
        echo "\n🧪 Running Tests...\n";
        echo str_repeat("=", 50) . "\n\n";

        foreach ($this->tests as $test) {
            try {
                $result = call_user_func($test['callback']);
                if ($result === true) {
                    $this->passed++;
                    echo "✅ PASS: " . $test['name'] . "\n";
                    $this->results[] = ['name' => $test['name'], 'status' => 'PASS'];
                } else {
                    $this->failed++;
                    echo "❌ FAIL: " . $test['name'] . "\n";
                    $this->results[] = ['name' => $test['name'], 'status' => 'FAIL', 'message' => $result];
                }
            } catch (Exception $e) {
                $this->failed++;
                echo "❌ ERROR: " . $test['name'] . " - " . $e->getMessage() . "\n";
                $this->results[] = ['name' => $test['name'], 'status' => 'ERROR', 'message' => $e->getMessage()];
            }
        }

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 Results: " . $this->passed . " passed, " . $this->failed . " failed\n";
        echo "✅ " . ($this->passed / count($this->tests) * 100) . "% success rate\n";

        // Save results to JSON
        file_put_contents(__DIR__ . '/test_results.json', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'total' => count($this->tests),
            'passed' => $this->passed,
            'failed' => $this->failed,
            'results' => $this->results
        ], JSON_PRETTY_PRINT));

        return $this->failed === 0;
    }
}
?>