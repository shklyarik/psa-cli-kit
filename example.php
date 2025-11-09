<?php

/**
 * Example usage of Psa\CliKit library
 */

require_once __DIR__ . '/vendor/autoload.php';

use Psa\CliKit\InputField;
use Psa\CliKit\Select;

echo "=== Psa CliKit Examples ===\n\n";

// Example 1: Using InputField
echo "1. Using InputField:\n";
$input = new InputField('Enter your name: ');
$name = $input->value();
echo "Hello, {$name}!\n\n";

// Example 2: Using Select
echo "2. Using Select:\n";
$options = [
    'php' => 'PHP Programming Language',
    'python' => 'Python Programming Language',
    'javascript' => 'JavaScript Programming Language',
    'java' => 'Java Programming Language',
    'go' => 'Go Programming Language',
];

$select = new Select($options, 'Choose your favorite programming language:');
$choiceKey = $select->index();
$choiceValue = $select->value();

echo "You selected key: {$choiceKey}\n";
echo "You selected value: {$choiceValue}\n\n";

echo "Done!\n";