<?php

namespace App\Mapper;

use App\Classes\CustomValidation\Error;
use App\Classes\CustomValidation\Identifier;
use App\Classes\CustomValidation\Validation;

class LaravelValidationToAPIJSON
{

    public function getMessage($validator)
    {


        $details_errors = array_filter($validator->messages()->getMessages(), function ($key) {
            return strpos($key, '.details.') !== false;
        }, ARRAY_FILTER_USE_KEY);

        $totalErrors = $validator->messages()->getMessages();
        $headerErrors  = $validator->messages()->getMessages();

        foreach ($details_errors as $key => $value) {
            unset($headerErrors[$key]);
        }

        $errorsWithIndex = [];
        foreach ($headerErrors as $field => $messages) {
            if (preg_match('/(\d+)\.(.*)/', $field, $matches)) {
                $index = $matches[1];   // Get the index (0, 1, ...)
                $fieldName = $matches[2]; // Get the field name (paymentMode, payeeType)
                $cleanMessage = str_replace($field,'',$messages);

                // Group messages by index
                $errorsWithIndex[$index][$field] = [
                    'field' => $fieldName,
                    'message' => $cleanMessage[0],
                    'index' => isset($validator->getData()[$index]['narration']) ? $validator->getData()[$index]['narration'] : ""
                ];
            }
        }



        $resultArray = [];

        foreach ($details_errors as $key => $messages) {
            $keyParts = explode('.', $key);
            $parentIndex = $keyParts[0];
            $detailIndex = $keyParts[2];
            $field = end($keyParts);

            foreach ($messages as $key => $message) {

                $wordToRemove = $parentIndex.'.'.$keyParts[1].'.'.$detailIndex.'.';
                $message = str_replace($wordToRemove, "", $message);
                $error = new Error($field,$message);

                // Group the errors by parentIndex
                if (!isset($resultArray[$parentIndex][$detailIndex])) {
                    $resultArray[$parentIndex][$detailIndex] = [
                        'parentIndex' => $parentIndex,
                        'index' => $detailIndex+1,
                        'errors' => []
                    ];
                }
                $resultArray[$parentIndex][$detailIndex]['errors'][] = $error;
            }
        }


        $result = array_map(function($item) {

            $newKey = collect($item)->first()['index'];
            return [
                'index' => $newKey,
                'error' => array_values($item),
            ]; // Get only the values
        }, $errorsWithIndex);

        $data = collect($result)->map(function($res,$key)  use ($resultArray) {
            $errors = [];

            foreach ($res['error'] as $errorArray) {
                $errors[] = new Error($errorArray['field'], $errorArray['message'], $errorArray['index']);
            }

            return [
                "identifier" => new Identifier($res['index'],$key+1),
                "fieldErrors" => [],
                "headerData" => [
                    'status' => false,
                    'errors' =>  $errors
                ],
                "detailData" => empty(array_filter($resultArray, function ($entry) use ($key) {
                    return $entry[0]['parentIndex'] == $key;
                })) ? [
                    'status' => true,
                    'errors' => []
                ] : array_filter($resultArray, function ($entry) use ($key) {
                    return $entry[0]['parentIndex'] == $key;
                })
            ];
        });

        return $data;
    }

}
