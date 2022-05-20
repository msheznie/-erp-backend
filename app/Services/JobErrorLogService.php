<?php

namespace App\Services;

use App\Models\JobErrorLog;
use App\Jobs\JobErrorLogInsert;

class JobErrorLogService
{
	public static function storeError($dataBase, $documentSystemID, $documentSystemCode, $tag, $errorType = 2, $errorMessage, $line = null)
	{
        $errorData = [
            'documentSystemID' => $documentSystemID,
            'documentSystemCode' => $documentSystemCode,
            'tag' => $tag,
            'errorType' => $errorType,
            'errorMessage' => $errorMessage,
            'error' => $line
        ];

        // JobErrorLog::create($errorData);
        JobErrorLogInsert::dispatch($errorData, $dataBase);
	}
}