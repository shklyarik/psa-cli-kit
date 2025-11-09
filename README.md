# Psa CliKit

A collection of common interactive command line user interfaces.

## Installation

You can install this library using Composer:

```bash
composer require shklyarik/psa-cli-kit
```

## Usage

### InputField

The `InputField` class allows you to create an interactive input field in the command line:

```php
<?php

use Psa\CliKit\InputField;

$input = new InputField('Enter your name: ');
$name = $input->value();
echo "Hello, {$name}!";
?>
```

### Select

The `Select` class provides an interactive selector that allows users to choose from a list of options using arrow keys:

```php
<?php

use Psa\CliKit\Select;

$options = [
    'option1' => 'First Option',
    'option2' => 'Second Option',
    'option3' => 'Third Option',
];

$select = new Select($options, 'Please choose an option:');
$choice = $select->index(); // Returns the key of the selected option
$value = $select->value();  // Returns the value of the selected option

echo "You chose: {$value}";
?>
```

## Classes

### InputField

The `InputField` class creates an interactive input field in the command line.

- `__construct($message = 'Enter text value: ', $print = true)` - Creates a new InputField instance
  - `$message` - The prompt message to display (default: 'Enter text value: ')
  - `$print` - Whether to print the entered value after input (default: true)
- `value(): string` - Gets the value entered by the user

### Select

The `Select` class creates an interactive selector with arrow key navigation.

- `__construct(array $options, string $message = 'Select an option:')` - Creates a new Select instance
  - `$options` - Array of selectable options
  - `$message` - The prompt message to display (default: 'Select an option:')
- `index(): int|string` - Returns the key of the selected option
- `value(): mixed` - Returns the value of the selected option

## License

This library is licensed under the MIT License.