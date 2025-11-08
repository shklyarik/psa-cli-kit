<?php

namespace Psa\CliKit;


class InputField
{
    public function __construct(
        protected $placeholder = 'Enter text value: ',
        protected $print = true
    )
    {
    }

    public function value(): string
    {
        $value = trim(readline($this->placeholder));
        echo "\033[A\033[2K"; // clear previous line
        if ($this->print) {
            echo $this->placeholder . $value . PHP_EOL;
        }
        return $value;
    }
}
