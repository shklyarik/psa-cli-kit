<?php

namespace Psa\CliKit;

class Select
{
    public function __construct(
        private array $options,
        private string $message = 'Select a option:',
    )
    {
    }

    public function index()
    {
        $currentChoice = 0;
        $optionKeys = array_keys($this->options);
        $maxIndex = count($this->options) - 1;

        function displayOptions($title, $options, $currentChoice) {
            system('clear');
            echo "$title\n--------------------------\n";
            $index = 0;
            foreach ($options as $key => $value) {
                if ($index === $currentChoice) {
                    echo "👉 $value\n";
                } else {
                    echo "   $value\n";
                }
                $index++;
            }
        }

        system('stty -icanon -echo');

        displayOptions($this->message, $this->options, $currentChoice);

        while (true) {
            $char = fread(STDIN, 3);
            if ($char === "\e[A") {
                if ($currentChoice > 0) $currentChoice--;
            } elseif ($char === "\e[B") {
                if ($currentChoice < $maxIndex) $currentChoice++;
            } elseif ($char === "\n" || $char === "\r") {
                break;
            }

            displayOptions($this->message, $this->options, $currentChoice);
        }

        system('stty sane');

        return $optionKeys[$currentChoice];
    }

    public function value()
    {
        return $this->options[$this->index()];
    }
}
