<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\PurchaseOrderDetails;
use App\Models\GRVDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AppointmentRepository
 * @package App\Repositories
 * @version November 12, 2021, 3:35 pm +04
 *
 * @method Appointment findWithoutFail($id, $columns = ['*'])
 * @method Appointment find($id, $columns = ['*'])
 * @method Appointment first($columns = ['*'])
*/
class AppointmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'slot_detail_id',
        'status',
        'supplier_id',
        'tenat_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Appointment::class;
    }

    public function getServiceLineSystemIDs($request)
    {
        try {
            $appointmentId = $request->input('appointmentId');

            $serviceLineSystemIDs = (new Appointment())->getDeliveryAppointmentDetails($appointmentId);
            $uniqueServiceLineSystemIDs = $serviceLineSystemIDs->pluck('po_master.serviceLineSystemID')->unique();

            if ($uniqueServiceLineSystemIDs->count() === 1) {
                return $uniqueServiceLineSystemIDs->first();
            }

            return null;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    public function validateAppointmentQuantities($appointmentId)
    {
        try {
            $appointment = Appointment::getAppointmentData($appointmentId);

            if (!$appointment) {
                return [
                    'success' => false,
                    'message' => 'Appointment not found'
                ];
            }

            $validationErrors = [];
            foreach ($appointment->detail as $appointmentDetail) {
                if (!$appointmentDetail->po_detail_id) {
                    continue;
                }

                $poDetail = PurchaseOrderDetails::find($appointmentDetail->po_detail_id);
                if (!$poDetail) {
                    continue;
                }

                $receivedQtySum = GRVDetails::getDirectPOGrv($appointmentDetail->po_detail_id);

                $receivedQty = $receivedQtySum->totalReceivedQty ?? 0;
                $poQty = $poDetail->noQty ?? 0;
                $appointmentQty = $appointmentDetail->qty ?? 0;
                $availableQty = $poQty - $receivedQty;

                if ($appointmentQty > $availableQty) {
                    $itemCode = $poDetail->itemPrimaryCode ?? 'N/A';
                    $validationErrors[] = trans('srm_supplier_management.item_appointment_quantity_exceeds_available_quantity', [
                        'itemCode' => $itemCode,
                        'appointmentQty' => $appointmentQty,
                        'availableQty' => $availableQty,
                        'poQty' => $poQty,
                        'receivedQty' => $receivedQty
                    ]);
                }
            }

            if (!empty($validationErrors)) {
                $errorMessage = trans('srm_supplier_management.grv_qty_can_not_exceed_validation');
                $errorMessage .= "." . implode(",", $validationErrors);
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
                
            }

            return [
                'success' => true,
                'message' => 'success'
            ];

        } catch (\Exception $e) {
            
            return [
                'success' => false,
                'message' => trans('srm_supplier_management.something_went_wrong') . ' ' . $e->getMessage()
            ];
        }
    }
}
