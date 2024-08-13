<?php

namespace App\Http\Controllers\API;

use App\Classes\AccountsPayable\SupplierDirectInvoiceDetails;
use App\Classes\AccountsPayable\SupplierInvoice;
use App\enums\accountsPayable\SupplierInvoiceType;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\VRF\GenerateDocumentApiRequest;
use App\Models\Company;
use App\Models\DirectInvoiceDetails;
use App\Models\DocumentApproved;
use App\Models\Tax;
use App\Models\VatReturnFillingMaster;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VRFDocumentGenerateController extends AppBaseController
{
    public function store(GenerateDocumentApiRequest $request)
    {
        $input = $request->only(['VRFId','companySystemId','isGenerateDebitNote','confirmGenDocWithoutPrevGen']);
        $VRFId = $input['VRFId'];
        $isGenerateDebitNote = (bool) $input['isGenerateDebitNote'];
        $vatReturnFillingMaster = VatReturnFillingMaster::find($VRFId,['returnFillingCode','date','companySystemID','masterDocumentAutoID','masterDocumentTypeID','id']);

        if($vatReturnFillingMaster->isPreviousVRFHasDocument())
            return $this->sendError("In the previous VAT return filing, the related supplier invoice/debit note has not been generated. Are you sure you want to proceed?",400,array('type' => 1));

        if($vatReturnFillingMaster->isDocumentGenerated())
            return $this->sendError("Supplier Invoice/Debit Note  generated for VAT return filing document",400,array('type' => 2));

        if(!$isGenerateDebitNote)
        {
            $result = $this->generateDebitNote($vatReturnFillingMaster);
            $msg = "Debit Note generated successfully";
        }else {
            $result = $this->generateSupplierInvoice($vatReturnFillingMaster);
            $msg = "Supplier Invoice generated successfully";
        }

        if(!$result['success'])
            return $this->sendError($result['message'],500);

        $result['message'] = $msg;
        return $result;
    }

    public function generateSupplierInvoice(VatReturnFillingMaster $request)
    {
        $tax = Tax::where('taxCategory',2)->where('isActive',true)->where('isDefault',true)->first();

        if(!isset($tax->authorityAutoID))
            return ['success' => false , "message" => "The supplier is not assigned in the tax setup (tax authority)"];

        try {
            $supplierInvoice = new SupplierInvoice(
                                    $request->companySystemID,
                                    $request->date
                                );

            $supplierInvoice->setReferenceNo($request->returnFillingCode);
            $supplierInvoice->setSupplierInvoiceNo($request->returnFillingCode);
            $supplierInvoice->setInvoiceType(SupplierInvoiceType::SUPPLIER_DIRECT_INVOICE);
            $supplierInvoice->setSupplier($tax->authorityAutoID);
            $supplierInvoice->setSupplierDetails($tax->authorityAutoID);
            $supplierInvoice->setSystemCreatedUserDetails();
            $supplierInvoice->setNarration("BSI created BY VAT return filling ".$request->returnFillingCode);

            $storeSupplierInvoice = $supplierInvoice->store();

            switch ($storeSupplierInvoice['data']['documentType']) {
                case SupplierInvoiceType::SUPPLIER_DIRECT_INVOICE :
                    $data = array();
                    $glAccounts= [
                        'InputVATGLAccount','OutputVATGLAccount'
                    ];
                    foreach ($glAccounts as $glAccountType)
                    {
                        $details = new SupplierDirectInvoiceDetails($storeSupplierInvoice['data']);
                        $details->setVATReturnFillingMaster($request);
                        $details->setGlAccountDetails($glAccountType);
                        $details->setAmount($details->getAmount());
                        $details->setCurrenciesAndExchagneRate();
                        $details->details->save();
                    }
                    $storeSupplierInvoice['data']->updateBookingAmount(abs($supplierInvoice->getBookingAmount($request)));
                    $confirmDoc = ($this->confirmDocument($storeSupplierInvoice['data']));

                    if(isset($confirmDoc['success']) && $confirmDoc['success'])
                    {
                        $request->attachGeneratedDocument($storeSupplierInvoice['data']['bookingSuppMasInvAutoID'],11);
                        return $confirmDoc;
                    }
                    break;
            }
        }catch (\Exception $exception)
        {
            return ['success' => false, 'message' => $exception->getMessage()];
        }

    }

    public function generateDebitNote(VatReturnFillingMaster  $request)
    {
        throw new \Exception("Debit Note Generatation not yet implemented, only supplier invoice generation implemented!");
    }

    public function confirmDocument($master)
    {
        $autoID = $master->bookingSuppMasInvAutoID;
        $params = array('autoID' => $autoID,
            'company' => $master->companySystemID,
            'document' => $master->documentSystemID,
            'segment' => '',
            'category' => '',
            'amount' => '',
            'receipt' => true,
            'sendMail' => false,
            'sendNotication' => false,
            'employee_id' =>  $master->createdUserSystemID
        );

        $confirmation = \Helper::confirmDocument($params);
        if($confirmation['success'])
        {
            return $this->approveDocument($master);
        }else {
            throw new \Exception($confirmation['message']);
        }
    }

    public function approveDocument($master)
    {
        $documentApproveds = DocumentApproved::where('documentSystemCode', $master->bookingSuppMasInvAutoID)->where('documentSystemID', $master->documentSystemID)->get();
        foreach ($documentApproveds as $documentApproved) {
            $documentApproved["approvedComments"] = "Generated Customer Invoice through API";
            $documentApproved["db"] = "gears-erp-gutech";
            $documentApproved['empID'] = $master->createdUserSystemID;
            $documentApproved['documentSystemID'] = $master->documentSystemID;
            $documentApproved['approvedDate'] = $master->approvedDate;
            $documentApproved['sendMail'] = false;
            $documentApproved['sendNotication'] = false;
            $documentApproved['isCheckPrivilages'] = false;


            $approval = \Helper::approveDocument($documentApproved);

            if(!$approval['success'])
                throw new \Exception($approval['message']);

        }

        return ['success' => true , 'message' => 'Document successfully approved'];

    }
}


