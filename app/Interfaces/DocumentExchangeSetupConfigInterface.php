<?php

namespace App\Interfaces;

use phpDocumentor\Reflection\Types\Boolean;

interface DocumentExchangeSetupConfigInterface
{
    public function checkDocumentRestricationPolicyAccess();

    public function checkDocumentTypeExchangeSetupConfigAccess();
    public function checkScenarioOne();

    public function checkScenarioTwo();

    public function checkScenarioThree();
    public function checkScenario();

    public function getDocumentExchangeRateConfigAccess();

    public function updateTheExchangeRateDocument($data);


}
