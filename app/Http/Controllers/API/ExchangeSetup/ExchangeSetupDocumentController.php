<?php

namespace App\Http\Controllers\API\ExchangeSetup;

use App\Http\Controllers\AppBaseController;
use App\Models\ExchangeSetupConfiguration;
use App\Models\ExchangeSetupDocument;
use App\Models\ExchangeSetupDocumentType;
use App\Models\PaySupplierInvoiceMaster;
use App\Services\ExchangeSetup\DocumentConfigs\ExchangeSetupDocumentConfigurationService;
use App\Services\ExchangeSetup\ExchangSetupConfigurationService;
use Illuminate\Http\Request;
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
               'documentType' => ($exchangeSetupDocument->master) ? $exchangeSetupDocument->master->documentDescription : null,
               'isActive' => (Boolean) $exchangeSetupDocument->isActive
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

    public function updateDocumentExchangeRate(Request $request)
    {

        $input = $request->all();

        $validator = \Validator::make($input, [
            'companySystemId' => 'required',
            'exchangeRateData' => 'required'
        ]);

        $input['payMasterAutoId'] = $input['exchangeRateData']['PayMasterAutoId'];


        if ($validator->fails())
        {
            return $this->sendError($validator->messages(), 422);
        }

        $service = new ExchangeSetupDocumentConfigurationService();
        $result = $service->updateDocumentExchangeRate($input);

        $result['editedFiles'] = $input['editedFiles'] ?? null;
        if(!$result['success'])
            return $this->sendError($result,400);

        return $this->sendResponse($result['data'],"Exchange rate configuration updated successfully!");
    }

    public function setDefaultExchangeRate(Request $request)
    {
        $input = $request->input();


        $validator = \Validator::make($input, [
            'companySystemId' => 'required',
            'payMasterId' => 'required'
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->messages(), 422);
        }
        $payMasterAutoID = $input['payMasterId'];
        $paymentVoucherMasterOrg = PaySupplierInvoiceMaster::find($payMasterAutoID);
        $changeSimilarCurrencies = PaySupplierInvoiceMaster::find($payMasterAutoID)->only('companyRptCurrencyID','localCurrencyID','BPVbankCurrency','supplierTransCurrencyID','BPVbank','supplierDefCurrencyID');

        $currencyRate = \Helper::currencyConversion($input['companySystemId'], $changeSimilarCurrencies['supplierTransCurrencyID'], $changeSimilarCurrencies['supplierDefCurrencyID'],0);
        $localExchangeRate =  \Helper::currencyConversion($input['companySystemId'], $changeSimilarCurrencies['supplierTransCurrencyID'], $changeSimilarCurrencies['localCurrencyID'], 0);
        $currencyRateBank = \Helper::currencyConversion($input['companySystemId'], $changeSimilarCurrencies['supplierTransCurrencyID'], $changeSimilarCurrencies['BPVbankCurrency'],0);

        $paymentVoucherMasterOrg['companyRptCurrencyER'] = $currencyRate['trasToRptER'];
        $paymentVoucherMasterOrg['localCurrencyER'] = $localExchangeRate['transToDocER'];
        $paymentVoucherMasterOrg['BPVbankCurrencyER'] = $currencyRateBank['transToDocER'];

        $updateDefaultExchangeRate = $paymentVoucherMasterOrg->save();

        if(!$updateDefaultExchangeRate)
            return $this->sendError("Cannot update default exchange rate",500);

        return $this->sendResponse($paymentVoucherMasterOrg,"Default exchange rate updated successfully!");
    }

    public function getExchangeSetupConfigOfCompany(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'companySystemId' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->messages(), 422);
        }

        $id = $input['id'];
        $companySystemId = $input['companySystemId'];

        $exchangeDocument = ExchangeSetupDocument::find($id);
        $documentTypes = $exchangeDocument->types;
        $data = $this->exchangSetupConfigurationService->mapTypesWithExchangeSetupConfig($documentTypes,$companySystemId);

        return $this->sendResponse($data,'Document Types Reterived Successfully!');
    }
}
