<?php

namespace Psa\CliKit;


class InputField
{
    public function __construct(
        protected $placeholder = 'Enter text value: ',
        protected $printResult = true
    )
    {
    }

    public function value(): string
    {
        $value = trim(readline($this->placeholder));
        if ($this->printResult) {
            echo $this->placeholder . $value . PHP_EOL;
        }
        return $value;
    }
}
