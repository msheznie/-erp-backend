<?php

namespace App\Http\Controllers\API\ExchangeSetup;

use App\Http\Controllers\AppBaseController;
use App\Models\ExchangeSetupConfiguration;
use App\Models\ExchangeSetupDocument;
use App\Models\ExchangeSetupDocumentType;
use App\Services\ExchangeSetup\ExchangSetupConfigurationService;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;
class ExchangeSetupDocumentController extends AppBaseController
{
    protected $exchangSetupConfigurationService;

    public function __construct(ExchangSetupConfigurationService $exchangSetupConfigurationService)
    {
        $this->exchangSetupConfigurationService = $exchangSetupConfigurationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $exchangeSetupDocuments =  ExchangeSetupDocument::orderBy('sort')->get();

       $data = collect($exchangeSetupDocuments)->map(function($exchangeSetupDocument)  {
           return [
               'id' => $exchangeSetupDocument->id,
               'documentType' => ($exchangeSetupDocument->master) ? $exchangeSetupDocument->master->documentDescription : null
           ];
        });
       return $this->sendResponse($data,'Data reterived successfully!');
    }

    public function getTypesOfDocument(Request $request,int $id)
    {

        if(!isset($id)) {
            return $this->sendError("Document id not found!",422);
        }

       $exchangeDocument = ExchangeSetupDocument::find($id);
       $documentTypes = $exchangeDocument->types;
       $data = $this->exchangSetupConfigurationService->mapTypesWithExchangeSetupConfig($documentTypes);

       return $this->sendResponse($data,'Document Types Reterived Successfully!');
    }
}
