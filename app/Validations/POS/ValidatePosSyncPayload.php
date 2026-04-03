<?php

namespace App\Validations\POS;

use App\Http\Requests\POS\PosShiftSyncRequest;
use Illuminate\Support\Facades\Validator;

class ValidatePosSyncPayload
{

    public static function validate(array $data): array
    {
        $rules = PosShiftSyncRequest::rulesForPayload($data);

        $validator = Validator::make(
            $data,
            $rules,
            [],
            PosShiftSyncRequest::attributeLabels()
        );

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->toArray() as $field => $msgs) {
                $errors[] = [
                    'field' => $field,
                    'message' => array_values((array) $msgs),
                ];
            }
        }

        return [
            'status' => $validator->passes(),
            'errors' => $errors,
        ];
    }
}
