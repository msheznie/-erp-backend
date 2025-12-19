<?php

namespace App\Http\Controllers\API;

use App\Classes\AccountsPayable\SupplierDirectInvoiceDetails;
use App\Classes\AccountsPayable\SupplierInvoice;
use App\Commands\AddDebitNoteDetails;
use App\Commands\CreateDebitNote;
use App\enums\accountsPayable\SupplierInvoiceType;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\VRF\GenerateDocumentApiRequest;
use App\Models\Company;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DirectInvoiceDetails;
use App\Models\DocumentApproved;
use App\Models\Tax;
use App\Models\VatReturnFillingMaster;
use App\Services\DocumentAutoApproveService;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use mysql_xdevapi\Exception;

class VRFDocumentGenerateController extends AppBaseController
{
    public $db;
    public function store(GenerateDocumentApiRequest $request)
    {
        $input = $request->only(['VRFId','companySystemId','isGenerateDebitNote','confirmGenDocWithoutPrevGen']);
        $VRFId = $input['VRFId'];
        $this->db = isset($request->db) ? $request->db : "";
        $isGenerateDebitNote = (bool) $input['isGenerateDebitNote'];
        $vatReturnFillingMaster = VatReturnFillingMaster::find($VRFId,['returnFillingCode','date','companySystemID','masterDocumentAutoID','masterDocumentTypeID','id']);

//        if($vatReturnFillingMaster->isPreviousVRFHasDocument())
//            return $this->sendError("In the previous VAT return filing, the related supplier invoice/debit note has not been generated. Are you sure you want to proceed?",400,array('type' => 1));

        if($vatReturnFillingMaster->isDocumentGenerated())
            return $this->sendError(trans('custom.supplier_invoice_debit_note_generated_for_vrf'),400,array('type' => 2));

        $tax = Tax::where('taxCategory',2)->where('isActive',true)->where('isDefault',true)->first();


        if(empty($tax->inputVatGLAccountAutoID) && empty($tax->outputVatGLAccountAutoID))
            return  $this->sendError(trans('custom.input_output_vat_gl_account_not_configured'),500);

        if(empty($tax->inputVatGLAccountAutoID))
            return  $this->sendError(trans('custom.input_vat_gl_account_not_configured'),500);

        if(empty($tax->outputVatGLAccountAutoID))
            return  $this->sendError(trans('custom.output_vat_gl_account_not_configured'),500);

        if(empty($tax->authorityAutoID))
            return  $this->sendError(trans('custom.supplier_not_assigned_tax_authority'),500);

        if($isGenerateDebitNote)
        {
            $result = $this->generateDebitNote($vatReturnFillingMaster,$tax);
            $msg = trans('custom.debit_note_generated_successfully');
        }else {
            $result = $this->generateSupplierInvoice($vatReturnFillingMaster,$tax);
            $msg = trans('custom.supplier_invoice_generated_successfully');
        }

        if(!$result['success'])
            return $this->sendError($result['message'],500);

        $result['message'] = $msg;
        return $result;
    }

    public function generateSupplierInvoice(VatReturnFillingMaster $request,$tax)
    {

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
            $supplierInvoice->setNarration(trans('custom.bsi_created_by_vat_return_filling')." ".$request->returnFillingCode);

            $storeSupplierInvoice = $supplierInvoice->store();

            switch ($storeSupplierInvoice['data']['documentType']) {
                case SupplierInvoiceType::SUPPLIER_DIRECT_INVOICE :
                    $glAccounts= [
                        'InputVATGLAccount','OutputVATGLAccount'
                    ];
                    foreach ($glAccounts as $glAccountType)
                    {
                        $details = new SupplierDirectInvoiceDetails($storeSupplierInvoice['data']);
                        $details->setVATReturnFillingMaster($request);
                        $details->setGlAccountDetails($glAccountType);
                        $details->setCurrenciesAndExchagneRate();
                        $details->setAmount($details->getAmount());
                        $details->setAdditionalDetatils();
                        $details->setDefaultValues();
                        $details->details->save();
                    }
                    $storeSupplierInvoice['data']->updateBookingAmount(($supplierInvoice->getBookingAmount($request)));
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

    public function generateDebitNote(VatReturnFillingMaster  $request, $tax)
    {
        try {
            $data = new \App\Classes\AccountsPayable\DebitNote(
                $request->companySystemID,
                $request->date,
                $request->returnFillingCode
            );
            $data->setSupplierDetails($tax->authorityAutoID);
            $data->setSystemCreatedUserDetails();

            if(!isset($data->master) && $data->master instanceof  DebitNote)
                throw  new \Exception(trans('custom.data_not_found'));

            $debitNote = new CreateDebitNote($data->master);
            $storeDebitNote = $debitNote->execute();

            if(!$storeDebitNote)
                throw new Exception(trans('custom.cannot_create_debit_note_from_vrf'));

            $glAccounts= [
                'InputVATGLAccount','OutputVATGLAccount'
            ];
            foreach ($glAccounts as $glAccountType)
            {
                $newDetails = new \App\Classes\AccountsPayable\DebitNoteDetails($storeDebitNote);
                $newDetails->setVATReturnFillingMaster($request);
                $newDetails->setGlAccountDetails($glAccountType);
                $newDetails->setCurrenciesAndExchagneRate();
                $newDetails->setAmount($newDetails->getAmount());
                $newDetails->setDefaultValues();
                $newDetails->setAdditionalDetatils();

                $newDetails->details->save();
            }
            $storeDebitNote->updateNetAmount(abs($data->getNetAmount($request)));

            $confirmDoc = ($this->confirmDocument($storeDebitNote));
            if(isset($confirmDoc['success']) && $confirmDoc['success'])
            {
                $request->attachGeneratedDocument($storeDebitNote->getKey(),15);
                return $confirmDoc;
            }
        }catch (\Exception $exception)
        {
            return ['success' => false, 'message' => $exception->getMessage()];
        }

    }

    public function confirmDocument($master)
    {
        $autoID = $master->getKey();
        $params = array('autoID' => $autoID,
            'company' => $master->companySystemID,
            'document' => $master->documentSystemID,
            'segment' => '',
            'category' => '',
            'amount' => '',
            'receipt' => true,
            'sendMail' => false,
            'sendNotication' => false,
            'employee_id' =>  $master->createdUserSystemID,
            'isAutoCreateDocument' => true
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

        $approveData = DocumentAutoApproveService::getAutoApproveParams( $master->documentSystemID,  $master->getKey());
        $approveData['approvedComments'] = trans('custom.system_auto_generated');
        $approveData['supplierPrimaryCode'] = $master->supplierID;
        $approveData['db'] = $this->db;
        $approval = \Helper::approveDocument($approveData);

        if(!$approval['success'])
            throw new \Exception($approval['message']);



        return ['success' => true , 'message' => trans('custom.document_successfully_approved')];

    }
}


