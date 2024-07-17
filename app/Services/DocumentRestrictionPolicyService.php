<?php

namespace App\Services;

use App\Interfaces\DocumentExchangeSetupConfigInterface;
use App\Services\ExchangeSetup\DocumentConfigs\PaymentVoucherDocumentExchangeSetupConfig;

class DocumentRestrictionPolicyService
{
    public $documentMasterId;
    public $documentSystemId;
    public $companyId;
    public function __construct($documentMasterId,$documentSystemId,$companyId)
    {
        $this->companyId = $companyId;
        $this->documentSystemId = $documentSystemId;
        $this->documentMasterId = $documentMasterId;
    }

    public function checkDocumentScenario()
    {
        switch ($this->documentSystemId)
        {
            case 4 :
                $service = new PaymentVoucherDocumentExchangeSetupConfig($this->companyId,$this->documentMasterId);
                return $service->getDocumentExchangeRateConfigAccess();
        }
    }


}
