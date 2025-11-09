<?php

namespace Psa\CliKit;

/**
 * Select - Provides an interactive command line selector.
 *
 * This class creates an interactive selector that allows users to choose
 * one option from a list using arrow keys. The selected option can be
 * retrieved either as a key or as a value.
 */
class Select
{
    /**
     * Constructor for Select.
     *
     * @param array  $options List of selectable items (keys are preserved).
     * @param string $message Prompt shown above the list (default: 'Select an option:').
     */
    public function __construct(
        private array $options,
        private string $message = 'Select an option:',
    ) {
    }

    /**
     * Render (or re-render) the option list.
     *
     * @param string $title          Heading text (usually the prompt).
     * @param array  $options        Items to display.
     * @param int    $currentChoice  Index of the currently highlighted item.
     * @param int    $previousLines  How many lines to move up before redrawing (default: 0).
     */
    private function displayOptions(string $title, array $options, int $currentChoice, int $previousLines = 0): void
    {
        // Move cursor up to overwrite previous output
        if ($previousLines > 0) {
            echo "\033[{$previousLines}A";
        }

        $output = "{$title}\n--------------------------\n";
        foreach ($options as $index => $value) {
            $output .= $index === $currentChoice ? "👉 {$value}\n" : "   {$value}\n";
        }

        echo $output;
    }

    /**
     * Run the interactive selector and return the KEY of the chosen option.
     *
     * This method initiates the interactive selection process, allowing the user
     * to navigate through options with arrow keys and select one with Enter.
     * It temporarily modifies terminal settings to capture keystrokes directly.
     *
     * @return int|string  Array key that corresponds to the selected item.
     */
    public function index(): int|string
    {
        $currentChoice = 0;
        $optionKeys    = array_keys($this->options);
        $maxIndex      = count($this->options) - 1;

        // Disable canonical mode and echo so keystrokes are captured immediately
        system('stty -icanon -echo');

        $this->displayOptions($this->message, $this->options, $currentChoice);
        $totalLines = count($this->options) + 2; // message + divider + options

        while (true) {
            $char = fread(STDIN, 3);

            if ($char === "\e[A") {          // Up arrow
                if ($currentChoice > 0) --$currentChoice;
            } elseif ($char === "\e[B") {    // Down arrow
                if ($currentChoice < $maxIndex) ++$currentChoice;
            } elseif ($char === "\n" || $char === "\r") { // Enter
                break;
            }

            $this->displayOptions($this->message, $this->options, $currentChoice, $totalLines);
        }

        // Restore terminal settings
        system('stty sane');

        // Clear the menu and show the final choice
        echo "\033[{$totalLines}A"; // move up
        echo "\033[0J";             // erase below
        echo "{$this->message} : {$this->options[$currentChoice]}\n";

        return $optionKeys[$currentChoice];
    }

    /**
     * Run the interactive selector and return the VALUE of the chosen option.
     *
     * This method calls index() internally to get the selected key,
     * then returns the corresponding value from the options array.
     *
     * @return mixed The selected array value.
     */
    public function value(): mixed
    {
        return $this->options[$this->index()];
    }
}
