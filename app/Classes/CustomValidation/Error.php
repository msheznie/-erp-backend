<?php

namespace App\Classes\CustomValidation;

class Error implements \JsonSerializable
{
    public $field;
    public $message;
    private $index;

    public function __construct(
        $field,
        $message,
        $index = null
    )
    {
        $this->field = $field;
        $this->message= $message;
        $this->index = $index;
    }

    public function jsonSerialize()
    {
        return [
            'field' => $this->field,
            'message' => $this->message
        ];
    }
}
