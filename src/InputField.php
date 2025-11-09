<?php

namespace Psa\CliKit;

/**
 * InputField - Provides an interactive command line input field.
 *
 * This class creates an input field in the command line that allows users
 * to enter text values. It includes features to clear the input line
 * and optionally display the entered value.
 */
class InputField
{
    /**
     * Constructor for InputField.
     *
     * @param string $message The prompt message to display to the user (default: 'Enter text value: ')
     * @param bool $print Whether to print the entered value after input (default: true)
     */
    public function __construct(
        protected string $message = 'Enter text value: ',
        protected bool $print = true
    ) {
    }

    /**
     * Gets the value entered by the user.
     *
     * This method displays the input prompt and waits for user input.
     * It then clears the input line and optionally prints the entered
     * value followed by a newline.
     *
     * @return string The value entered by the user
     */
    public function value(): string
    {
        $value = trim(readline($this->message));
        echo "\033[A\033[2K"; // clear previous line
        if ($this->print) {
            echo $this->message . $value . PHP_EOL;
        }
        return $value;
    }
}
