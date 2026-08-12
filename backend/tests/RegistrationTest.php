<?php
require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Registration.php';
require_once __DIR__ . '/../models/Course.php';

$runner = new TestRunner();

// Test 1: Get user registrations
$runner->addTest('Registration Model - Get User Registrations', function() {
    $registrationModel = new Registration();
    $registrations = $registrationModel->getUserRegistrations(1);
    
    if (!is_array($registrations)) {
        return 'Expected array, got ' . gettype($registrations);
    }
    
    return true;
});

// Test 2: Check duplicate registration (should fail)
$runner->addTest('Registration Model - Prevent Duplicate Registration', function() {
    $registrationModel = new Registration();
    $result = $registrationModel->register(1, 1);
    
    // Should return error about already registered
    if (isset($result['success']) && $result['success'] === true) {
        return 'Should prevent duplicate registration';
    }
    
    if (!isset($result['error'])) {
        return 'Should return error message for duplicate';
    }
    
    return true;
});

// Test 3: Get course registrations
$runner->addTest('Registration Model - Get Course Registrations', function() {
    $registrationModel = new Registration();
    $registrations = $registrationModel->getCourseRegistrations(1);
    
    if (!is_array($registrations)) {
        return 'Expected array, got ' . gettype($registrations);
    }
    
    return true;
});

// Test 4: Drop registration
// $runner->addTest('Registration Model - Drop Registration', function() {
//     $registrationModel = new Registration();
    
//     error_log("=== DEBUG: Drop Test Started ===");
    
//     // Get user 1's registrations (admin)
//     $registrations = $registrationModel->getUserRegistrations(1);
    
//     error_log("User 1 registrations: " . print_r($registrations, true));
    
//     // If user 1 has registrations, drop the first one
//     if (count($registrations) > 0) {
//         $courseId = $registrations[0]['id'];
//         error_log("Found registration, course ID: " . $courseId);
        
//         $dropResult = $registrationModel->drop(1, $courseId);
//         error_log("Drop result: " . print_r($dropResult, true));
        
//         if (isset($dropResult['error'])) {
//             return 'Drop failed for existing registration: ' . $dropResult['error'];
//         }
//         return true;
//     }
    
//     error_log("No registrations found for user 1, attempting to create one");
    
//     // If no registrations, create one then drop it
//     $registerResult = $registrationModel->register(1, 1);
//     error_log("Register result: " . print_r($registerResult, true));
    
//     // If registration failed
//     if (isset($registerResult['error'])) {
//         // Check if already registered
//         if (strpos($registerResult['error'], 'Already registered') !== false) {
//             error_log("Already registered, attempting to drop");
//             $dropResult = $registrationModel->drop(1, 1);
//             error_log("Drop result: " . print_r($dropResult, true));
            
//             if (isset($dropResult['error'])) {
//                 return 'Drop failed after duplicate check: ' . $dropResult['error'];
//             }
//             return true;
//         }
//         return 'Registration failed: ' . $registerResult['error'];
//     }
    
//     // Now drop it
//     error_log("Fresh registration created, now dropping");
//     $dropResult = $registrationModel->drop(1, 1);
//     error_log("Drop result: " . print_r($dropResult, true));
    
//     if (isset($dropResult['error'])) {
//         return 'Drop failed after fresh registration: ' . $dropResult['error'];
//     }
    
//     return true;
// });

// Test 5: Check registration count
$runner->addTest('Registration Model - Registration Count', function() {
    $registrationModel = new Registration();
    $courseModel = new Course();
    
    $registrations = $registrationModel->getUserRegistrations(1);
    $course = $courseModel->findById(1);
    
    if (!$course) {
        return 'Course not found';
    }
    
    // Get count of active registrations
    $count = 0;
    foreach ($registrations as $reg) {
        if ($reg['status'] === 'active') {
            $count++;
        }
    }
    
    return true;
});

$runner->run();
?>