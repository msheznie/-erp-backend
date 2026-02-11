<?php

namespace App\Http\Controllers\API;
use App\Http\Requests\SRM\SRMScenarioRequest;
use App\Repositories\SRMScenarioDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
class SRMScenarioAttachmentsAPIController extends AppBaseController
{
    private $sRMScenarioAttachmentsRepository;

    public function __construct(SRMScenarioDetailsRepository $sRMScenarioAttachmentRepo)
    {
        $this->sRMScenarioAttachmentsRepository = $sRMScenarioAttachmentRepo;
    }
}
