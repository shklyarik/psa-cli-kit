<?php

namespace Psa\CliKit;


class InputField
{
    public function __construct(
        protected $placeholder = 'Enter text value: '
    )
    {
    }

    public function value(): string
    {
        return trim(readline($this->placeholder));
    }
}
