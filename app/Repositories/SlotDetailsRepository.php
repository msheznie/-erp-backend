<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Company;
use App\Models\SlotDetails;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class SlotDetailsRepository
 * @package App\Repositories
 * @version November 11, 2021, 8:46 am +04
 *
 * @method SlotDetails findWithoutFail($id, $columns = ['*'])
 * @method SlotDetails find($id, $columns = ['*'])
 * @method SlotDetails first($columns = ['*'])
 */
class SlotDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'date',
        'slot_master_id',
        'status',
        'time_from',
        'time_to'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SlotDetails::class;
    }
    public function deleteSlotDetail($slotDetailID)
    {
        return DB::transaction(function () use ($slotDetailID) {
            $slotDetail = $this->findWithoutFail($slotDetailID);

            if (empty($slotDetail)) {
                return ['success' => false, 'message' => 'Slot detail not found'];
            }

            $confirmedAppointment = Appointment::checkConfirmedAppointment($slotDetailID);
            if ($confirmedAppointment) {
                return ['success' => false, 'message' => 'Slot detail cannot be deleted because a confirmed or approved delivery appointment exists'];
            }

            $slotDetail->delete();

            return ['success' => true, 'message' => 'Slot detail deleted successfully'];
        });
    }

    public function removeMultipleSlots(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $formattedFromDate = Carbon::parse($request->input('dateFrom', ''));
            $formattedToDate = Carbon::parse($request->input('dateTo', ''));
            $company_id = $request->input('companyID', 0);
            $warehouse_id = $request->input('wareHouse', 0);

            $slotDetails = SlotDetails::getSlotDetails($formattedFromDate, $formattedToDate, $company_id, $warehouse_id);
            if ($slotDetails->isEmpty()) {
                return ['status' => true, 'message' => 'Slots are not available for this date range.'];
            }

            if ($slotDetails->pluck('appointment')->flatten()->pluck('confirmed_yn')->contains(true)) {
                return [
                    'status' => false,
                    'message' => 'Cannot delete the slots because there are appointment/s pending for approval or approved.'
                ];
            }

            SlotDetails::whereIn('id', $slotDetails->pluck('id'))->delete();

            return ['status' => true, 'message' => 'Slots deleted successfully'];
        });
    }

    public function getSlotDetailsFormData($companyID){
        $subCompanies = \Helper::checkIsCompanyGroup($companyID)
            ? \Helper::getGroupCompany($companyID)
            : [$companyID];
        return [
            'isGroupCompany' => \Helper::checkIsCompanyGroup($companyID),
            'company' => Company::getCompanyList($subCompanies)
        ];
    }
}
