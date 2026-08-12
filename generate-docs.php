<?php
$output = "# 📚 Course Registration System\n\n";
$output .= "## 🏗️ Architecture\n\n";

// Scan models
$output .= "### Models\n\n";
$modelFiles = glob('backend/models/*.php');
foreach ($modelFiles as $file) {
    $className = basename($file, '.php');
    $content = file_get_contents($file);
    preg_match('/\/\*\*(.*?)\*\//s', $content, $matches);
    $desc = isset($matches[1]) ? trim(str_replace('*', '', $matches[1])) : 'No description';
    $output .= "#### `$className`\n$desc\n\n";
}

// API endpoints
$output .= "## 🔗 API Endpoints\n\n";
$output .= "| Method | Endpoint | Description |\n";
$output .= "|--------|----------|-------------|\n";
$output .= "| POST | `/auth/login` | Login user |\n";
$output .= "| POST | `/auth/register` | Register new user |\n";
$output .= "| GET | `/courses` | Get all courses |\n";
$output .= "| POST | `/registrations/register` | Register for course |\n";
$output .= "| POST | `/registrations/drop` | Drop course |\n";

// Add test results
if (file_exists('backend/tests/test_results.json')) {
    $results = json_decode(file_get_contents('backend/tests/test_results.json'), true);
    $output .= "\n## 🧪 Test Results\n\n";
    $output .= "- Total Tests: " . ($results['total'] ?? 0) . "\n";
    $output .= "- ✅ Passed: " . ($results['passed'] ?? 0) . "\n";
    $output .= "- ❌ Failed: " . ($results['failed'] ?? 0) . "\n";
    $output .= "- 📈 Success Rate: " . round(($results['passed'] ?? 0) / max(1, ($results['total'] ?? 1)) * 100, 1) . "%\n";
}

file_put_contents('README.md', $output);
echo "✅ Documentation generated at README.md\n";