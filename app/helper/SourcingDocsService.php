<?php
namespace App\helper;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Repositories\GRVMasterRepository;

class SourcingDocsService
{
    protected $grvMasterRepo;

    /**
     * Inject the GRV Master repository via constructor
     */
    public function __construct(GRVMasterRepository $grvMasterRepo)
    {
        $this->grvMasterRepo = $grvMasterRepo;
    }

    /**
     * Send delivery appointment confirmation email
     *
     * @param array $data
     * @return bool
     */
    public static function sendDeliveryAppointmentConfirmationMail($grvMaster)
    {
        $grvRepo = app(GRVMasterRepository::class);

        $data = [
            'grvAutoID' => $grvMaster->grvAutoID,
            'supplierID' => $grvMaster->supplierID,
            'companySystemID' => $grvMaster->companySystemID
        ];

        $grvRepo->sendAppointmentConfirmationEmail($data);
        return true;
    }
}
