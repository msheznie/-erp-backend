<?php

namespace App\Classes\CustomValidation;

class Validation implements \JsonSerializable
{

    public $identifier;
    public $fieldErrors = [];

    public $headerData = [];

    public $detailData = [];

    private $identifierKey;
    private $identifierValue;

    private $data;

    public function __construct($data,$key)
    {
        $this->data = $data;

        if(!empty($data))
        {
            if(isset($data['narration']) || isset($data['comments']))
            {
                if(isset($data['narration']))
                    $this->identifierKey = $data['narration'];
                    $this->identifierValue = "narration";

                if(isset($data['comments']))
                    $this->identifierKey = $data['comments'];
                    $this->identifierValue = "comments";
            }else {
                $this->identifierKey = $key+1;
                $this->setFieldErrors();
            }


        }
        $this->identifier = new Identifier($this->identifierKey,$key+1);

    }

    /**
     * @param mixed $fieldErrors
     */
    public function setFieldErrors(): void
    {
        $this->fieldErrors = [
            [
                "field" => $this->identifierKey,
                "message" => "Narration not found"
            ]
        ];
    }


    /**
     * @param array $headerData
     */
    public function setHeaderData($errors): void
    {

        $this->headerData = [
            [
                "status" => empty($errors),
                "errors" =>  $errors
            ]
        ];
    }

    /**
     * @param array $detailData
     */
    public function setDetailData($detailData): void
    {

        $this->detailData = [
            'success' => collect($detailData)->pluck('error')->flatten()->isEmpty(),
            'error' => (collect($detailData)->pluck('error')->flatten()->isEmpty()) ? [] :$detailData
        ];
    }

    public function jsonSerialize()
    {
        return [
            'indentifier' => $this->identifier,
            'fieldErrors' => $this->fieldErrors,
            'headerData' => $this->headerData,
            'detailData' => $this->detailData
        ];
    }

}
