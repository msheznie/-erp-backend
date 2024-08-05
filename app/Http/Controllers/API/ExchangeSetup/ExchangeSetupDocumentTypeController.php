<?php

namespace App\Http\Controllers\API\ExchangeSetup;

use App\Http\Controllers\AppBaseController;
use App\Models\ExchangeSetupDocumentType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExchangeSetupDocumentTypeController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documentType = ExchangeSetupDocumentType::all();

        return $this->sendResponse($documentType,'Data Reterived Successfully!');
    }


}
