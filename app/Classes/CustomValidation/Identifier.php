<?php

namespace App\Classes\CustomValidation;

class Identifier
{
    public $uniqueKey;
    public $index;
    public function __construct($uniqueKey,$index)
    {
        $this->uniqueKey = $uniqueKey;
        $this->index = $index;
    }

}
