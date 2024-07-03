<?php

namespace App\Http\Controllers\API\ExchangeSetup;

use App\Http\Controllers\AppBaseController;
use App\Services\ExchangeSetup\ExchangSetupConfigurationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class ExchangeSetupConfigurationController extends AppBaseController
{

    private $exchangSetupConfigurationService;

    public function __construct(ExchangSetupConfigurationService $exchangSetupConfigurationService)
    {
        $this->exchangSetupConfigurationService = $exchangSetupConfigurationService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $input = $request->input();
        $input['createdBy'] = ($user && $user->employee) ? $user->employee->employeeSystemID : null;

        $validator = \Validator::make($input, [
            'createdBy' => 'required|numeric',
            'companyId' => 'required|numeric',
            'exchangeSetupDocumentTypeId' => 'required|numeric',
            'allowErChanges' => 'required',
            'allowGainOrLossCal' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $result = $this->exchangSetupConfigurationService->setConfiguration($input);

        if(!$result)
            return $this->sendError(500,"Cannot create exchange setup configuration!");

        return $this->sendResponse($result,'Exchange setup configuration created');


    }

}
