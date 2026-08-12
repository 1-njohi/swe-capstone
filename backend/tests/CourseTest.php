<?php
require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Course.php';

$runner = new TestRunner();

// Test 1: Get all courses
$runner->addTest('Course Model - Get All Courses', function() {
    $courseModel = new Course();
    $courses = $courseModel->getAll();
    
    if (!is_array($courses)) {
        return 'Expected array, got ' . gettype($courses);
    }
    
    if (count($courses) < 3) {
        return 'Expected at least 3 courses, got ' . count($courses);
    }
    
    return true;
});

// Test 2: Find course by ID
$runner->addTest('Course Model - Find Course by ID', function() {
    $courseModel = new Course();
    $course = $courseModel->findById(1);
    
    if (!$course) {
        return 'Course with ID 1 not found';
    }
    
    if (!isset($course['code']) || empty($course['code'])) {
        return 'Course missing code field';
    }
    
    return true;
});

// Test 3: Check course capacity
$runner->addTest('Course Model - Capacity Check', function() {
    $courseModel = new Course();
    $course = $courseModel->findById(1);
    
    if (!$course) {
        return 'Course not found';
    }
    
    if (!isset($course['capacity']) || !isset($course['enrolled'])) {
        return 'Course missing capacity or enrolled fields';
    }
    
    if ($course['enrolled'] > $course['capacity']) {
        return 'Enrolled (' . $course['enrolled'] . ') exceeds capacity (' . $course['capacity'] . ')';
    }
    
    return true;
});

// Test 4: Get available spots
$runner->addTest('Course Model - Available Spots', function() {
    $courseModel = new Course();
    $course = $courseModel->findById(1);
    
    if (!$course) {
        return 'Course not found';
    }
    
    $availableSpots = $course['capacity'] - $course['enrolled'];
    
    if ($availableSpots < 0) {
        return 'Available spots cannot be negative';
    }
    
    return true;
});

$runner->run();
?>