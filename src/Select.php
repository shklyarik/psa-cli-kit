<?php

namespace Psa\CliKit;

class Select
{
    public function __construct(
        private array $options,
        private string $message = 'Select an option:',
    )
    {
    }

    private function displayOptions($title, $options, $currentChoice, $previousLines = 0)
    {
        // Поднимаемся на количество строк, чтобы перерисовать выбор
        if ($previousLines > 0) {
            echo "\033[" . $previousLines . "A";
        }

        $output = "$title\n--------------------------\n";
        $index = 0;
        foreach ($options as $value) {
            if ($index === $currentChoice) {
                $output .= "👉 $value\n";
            } else {
                $output .= "   $value\n";
            }
            $index++;
        }

        echo $output;
    }

    public function index()
    {
        $currentChoice = 0;
        $optionKeys = array_keys($this->options);
        $maxIndex = count($this->options) - 1;

        system('stty -icanon -echo');

        $this->displayOptions($this->message, $this->options, $currentChoice);
        $totalLines = count($this->options) + 2; // message + divider + options

        while (true) {
            $char = fread(STDIN, 3);
            if ($char === "\e[A") {
                if ($currentChoice > 0) $currentChoice--;
            } elseif ($char === "\e[B") {
                if ($currentChoice < $maxIndex) $currentChoice++;
            } elseif ($char === "\n" || $char === "\r") {
                break;
            }

            $this->displayOptions($this->message, $this->options, $currentChoice, $totalLines);
        }

        system('stty sane');

        // Стираем предыдущие строки и выводим результат
        echo "\033[" . $totalLines . "A"; // подняться
        echo "\033[0J"; // очистить всё ниже
        echo "{$this->message} : {$this->options[$currentChoice]}\n";

        return $optionKeys[$currentChoice];
    }

    public function value()
    {
        return $this->options[$this->index()];
    }
}
