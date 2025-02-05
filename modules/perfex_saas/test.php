<?php

defined('BASEPATH') or exit('No direct script access allowed');

require('tests/PerfexSaasTest.php');

$CI->load->library('unit_test');
$CI->unit->use_strict(TRUE);

// List of test class
$testClasses = [
    //'GeneralTest',
    'HelpersTest',
];

// Get all test classes and run
foreach ($testClasses as $class) {
    require_once("tests/$class.php");
}

echo "<style>table{margin-bottom:50px !important;}</style>";
echo "<div class='tw-bg-white'>";

foreach ($testClasses as $testClass) {

    echo "<h2>Starting $testClass</h2>";
    $test = new $testClass($CI);
    $reflection = new ReflectionClass($testClass);

    $methods = $reflection->getMethods();
    foreach ($methods as $method) {
        $methodName = $method->getName();
        if (str_starts_with($methodName, 'test')) {
            echo "<h3> Running $methodName -- $testClass</h3>";
            $test->$methodName();
            // Display the test results
            echo $CI->unit->report();
            // Reset test list
            $CI->unit->results = [];
        }
    }

    echo "<h2>End of $testClass</h2>";
    echo "</div>";
    echo "<br/><br/><br/>";
}
exit;
