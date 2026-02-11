<?php

namespace App\Repositories;

use App\Http\Requests\SRM\SRMScenarioRequest;
use App\Models\SRMScenarioAttachments;
use App\Models\SRMScenarioDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SRMScenarioAttachmentRepository extends BaseRepository
{

    public function model()
    {
        return SRMScenarioAttachments::class;
    }

    /**
     * @return array{success: bool, message: string}
     */

}
