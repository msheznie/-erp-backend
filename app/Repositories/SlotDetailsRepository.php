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
                return ['success' => false, 'message' => trans('srm_supplier_management.slot_detail_not_found')];
            }

            $confirmedAppointment = Appointment::checkConfirmedAppointment($slotDetailID);
            if ($confirmedAppointment) {
                return ['success' => false, 'message' => trans('srm_supplier_management.slot_detail_cannot_be_deleted_because_a_confirmed_or_approved_delivery_appointment_exists')];
            }

            $slotDetail->delete();

            return ['success' => true, 'message' => trans('srm_supplier_management.slot_detail_successfully_deleted')];
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
                return ['status' => true, 'message' => trans('srm_supplier_management.slots_are_not_available_for_this_date_range')];
            }

            if ($slotDetails->pluck('appointment')->flatten()->pluck('confirmed_yn')->contains(true)) {
                return [
                    'status' => false,
                    'message' => trans('srm_supplier_management.cannot_delete_the_slots_because_there_are_appointments_pending_for_approval_or_approved')
                ];
            }

            SlotDetails::whereIn('id', $slotDetails->pluck('id'))->delete();

            return ['status' => true, 'message' => trans('srm_supplier_management.slots_deleted_successfully')];
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
