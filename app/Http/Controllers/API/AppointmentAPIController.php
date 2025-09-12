<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAppointmentAPIRequest;
use App\Http\Requests\API\UpdateAppointmentAPIRequest;
use App\Http\Requests\DeliveryAppointmentRequest;
use App\Models\Appointment;
use App\Models\DocumentAttachments;
use App\Repositories\AppointmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\CompanyFinanceYear;
use Carbon\Carbon;
use App\Models\ApprovalLevel;
use App\Jobs\DeliveryAppoinmentGRV;
use App\Models\AppointmentDetails;

/**
 * Class AppointmentController
 * @package App\Http\Controllers\API
 */

class AppointmentAPIController extends AppBaseController
{
    /** @var  AppointmentRepository */
    private $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->appointmentRepository = $appointmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/appointments",
     *      summary="Get a listing of the Appointments.",
     *      tags={"Appointment"},
     *      description="Get all Appointments",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Appointment")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->appointmentRepository->pushCriteria(new RequestCriteria($request));
        $this->appointmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $appointments = $this->appointmentRepository->all();

        return $this->sendResponse($appointments->toArray(), 'Appointments retrieved successfully');
    }

    /**
     * @param CreateAppointmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/appointments",
     *      summary="Store a newly created Appointment in storage",
     *      tags={"Appointment"},
     *      description="Store Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Appointment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Appointment")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Appointment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAppointmentAPIRequest $request)
    {
        $input = $request->all();

        $appointment = $this->appointmentRepository->create($input);

        return $this->sendResponse($appointment->toArray(), 'Appointment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/appointments/{id}",
     *      summary="Display the specified Appointment",
     *      tags={"Appointment"},
     *      description="Get Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Appointment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Appointment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Appointment $appointment */
        $appointment = $this->appointmentRepository->findWithoutFail($id);

        if (empty($appointment)) {
            return $this->sendError('Appointment not found');
        }

        return $this->sendResponse($appointment->toArray(), 'Appointment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAppointmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/appointments/{id}",
     *      summary="Update the specified Appointment in storage",
     *      tags={"Appointment"},
     *      description="Update Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Appointment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Appointment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Appointment")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Appointment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAppointmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var Appointment $appointment */
        $appointment = $this->appointmentRepository->findWithoutFail($id);

        if (empty($appointment)) {
            return $this->sendError('Appointment not found');
        }

        $appointment = $this->appointmentRepository->update($input, $id);

        return $this->sendResponse($appointment->toArray(), 'Appointment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/appointments/{id}",
     *      summary="Remove the specified Appointment from storage",
     *      tags={"Appointment"},
     *      description="Delete Appointment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Appointment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var Appointment $appointment */
        $appointment = $this->appointmentRepository->findWithoutFail($id);

        if (empty($appointment)) {
            return $this->sendError('Appointment not found');
        }

        $appointment->delete();

        return $this->sendSuccess('Appointment deleted successfully');
    }
    public function getAppointments(Request $request)
    {
        $input = $request->all();
        $slotDetailId = $input['slotDetailId'];
        $companyId = $input['companyId'];
        $documentSystemID = 106;
        $empID = \Helper::getEmployeeSystemID();
        $data = Appointment::with(['documentApproved' => function ($q) use ($companyId, $documentSystemID, $empID) {
            $q->where('erp_documentapproved.rejectedYN', 0)
                ->where('erp_documentapproved.documentSystemID', $documentSystemID)
                ->where('erp_documentapproved.companySystemID', $companyId);;

            $q->with(['employeeDepartments' => function ($q2) use ($companyId, $documentSystemID, $empID) {
                $q2->where('employeesdepartments.documentSystemID', $documentSystemID)
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            }]);
            $q->with(['employeeRole' => function ($q3) use ($companyId, $documentSystemID, $empID) {
                $q3->where('appointment.company_id', $companyId)
                    ->where('appointment.approved_yn', 0)
                    ->where('appointment.confirmed_yn', 1);
            }]);
        }, 'created_by', 'detail'])
            ->where('slot_detail_id', $slotDetailId)
            ->where('confirmed_yn', 1)
            ->get();

        return $data;
    }

    public function getAppointmentList(Request $request)
    {
        $input = $request->all();
        //$slotDetailId = $input['slotDetailId'];
        $companyId = $input['companyId'];
        $documentSystemID = 106;
        $empID = \Helper::getEmployeeSystemID();
        $data = Appointment::with(['documentApproved' => function ($q) use ($companyId, $documentSystemID, $empID) {
            $q->where('erp_documentapproved.rejectedYN', 0)
                ->where('erp_documentapproved.documentSystemID', $documentSystemID)
                ->where('erp_documentapproved.companySystemID', $companyId);;

            $q->with(['employeeDepartments' => function ($q2) use ($companyId, $documentSystemID, $empID) {
                $q2->where('employeesdepartments.documentSystemID', $documentSystemID)
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            }]);
            $q->with(['employeeRole' => function ($q3) use ($companyId, $documentSystemID, $empID) {
                $q3->where('appointment.company_id', $companyId)
                    ->where('appointment.approved_yn', 0)
                    ->where('appointment.confirmed_yn', 1);
            }]);
        }, 'created_by', 'detail'])
            ->where('confirmed_yn', 1)
            ->get();

        return $data;
    }
    public function getAppointmentListSummaryView(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];
        $documentSystemID = 106;
        $empID = \Helper::getEmployeeSystemID();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $appointmentDetail = Appointment::with(['documentApproved' => function ($q) use ($companyId, $documentSystemID, $empID) {
            $q->where('erp_documentapproved.rejectedYN', 0)
                ->where('erp_documentapproved.documentSystemID', $documentSystemID)
                ->where('erp_documentapproved.companySystemID', $companyId);

            $q->with(['employeeDepartments' => function ($q2) use ($companyId, $documentSystemID, $empID) {
                $q2->where('employeesdepartments.documentSystemID', $documentSystemID)
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            }]);
        }, 'created_by', 'detail', 'slot_detail.slot_master.ware_house', 'supplier', 'attachment'])
            ->whereHas('documentApproved', function ($q) use ($companyId, $documentSystemID, $empID) {
                $q->where('erp_documentapproved.rejectedYN', 0)->where('erp_documentapproved.approvedYN', 0)
                    ->where('erp_documentapproved.documentSystemID', $documentSystemID)
                    ->where('erp_documentapproved.companySystemID', $companyId);

                $q->whereHas('employeeDepartments', function ($q2) use ($companyId, $documentSystemID, $empID) {
                    $q2->where('employeesdepartments.documentSystemID', $documentSystemID)
                        ->where('employeesdepartments.companySystemID', $companyId)
                        ->where('employeesdepartments.isActive', 1)
                        ->where('employeesdepartments.employeeSystemID', $empID);
                });
            })->where('confirmed_yn', 1)
            ->where('approved_yn', 0)
            ->where('refferedBackYN', 0);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $appointmentDetail = $appointmentDetail->where(function ($query) use ($search) {
                $query->where('primary_code', 'LIKE', "%{$search}%");
                $query->orWhereHas('slot_detail.slot_master.ware_house', function ($query1) use ($search) {
                    $query1->where('wareHouseDescription', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('created_by', function ($query1) use ($search) {
                    $query1->where('supplierName', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('slot_detail.slot_master', function ($query1) use ($search) {
                    $query1->whereDate('from_date', "%{$search}%");
                });
            });
        }

        return \DataTables::of($appointmentDetail)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function approveCalanderDelAppointment(Request $request)
    {
        $input = $request->all();
        $params = array(
            'documentApprovedID' => $input['document_approved']['documentApprovedID'],
            'documentSystemCode' => $input['id'],
                'documentSystemID' => $input['document_system_id'],
            'approvalLevelID' => $input['document_approved']['approvalLevelID'],
            'rollLevelOrder' => $input['document_approved']['rollLevelOrder'],
            'approvedComments' => $input['approvedComments']
        );


        $approve = \Helper::approveDocument($params);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }
    }

    public function rejectCalanderDelAppointment(Request $request)
    {
        $input = $request->all();
    
        $params = array(
            'documentApprovedID' => $input['document_approved']['documentApprovedID'],
            'documentSystemCode' => $input['id'],
            'documentSystemID' => $input['document_system_id'],
            'approvalLevelID' => $input['document_approved']['approvalLevelID'],
            'rollLevelOrder' => $input['document_approved']['rollLevelOrder'],
            'rejectedComments' => $input['rejectedComments']
        );

        $approve = \Helper::rejectDocument($params);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }
    }
    public function getAppointmentById(Request $request)
    {
        $input = $request->all();
        $appointmentId = $input['appointmentId'];

        $data = Appointment::with(['detail' => function ($q) {
            $q->with(['getPoDetails' => function ($q1) {
                $q1->with(['productmentOrder', 'unit']);
            }]);
        }, 'detail.getPoMaster.transactioncurrency','detail.getPoMaster.segment'])
            ->where('id', $appointmentId)->first();

        return $data;
    }

    public function getAppointmentAttachmentList(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];
        $documentSystemCode = $input['documentSystemCode'];
        $documentSystemID = 106;
        $sort = 'asc';

        $appointmentDetail = DocumentAttachments::where('documentSystemID', $documentSystemID)
            ->where('companySystemID', $companyId)
            ->where('documentSystemCode', $documentSystemCode);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $appointmentDetail = $appointmentDetail->where(function ($query) use ($search) {
                $query->where('attachmentDescription', 'LIKE', "%{$search}%");
                $query->orWhere('originalFileName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($appointmentDetail)
            ->order(function ($query) use ($input) {
               if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('attachmentID', 'asc');
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function checkDeliveryAppoinrmentApproval(Request $request)
    {
        $input = $request->all();

        $appointments = Appointment::where('appointment.id',$input['id'])->selectRaw('erp_purchaseordermaster.purchaseOrderCode,appointment.id,appointment_details.id,appointment_details.qty as planned_qty,erp_purchaseorderdetails.noQty as total_qty,erp_purchaseorderdetails.receivedQty as receivedQty,(erp_purchaseorderdetails.noQty - erp_purchaseorderdetails.receivedQty) as balance_qty,erp_purchaseorderdetails.itemPrimaryCode')
        ->join('appointment_details', 'appointment_details.appointment_id', '=', 'appointment.id')
        ->join('erp_purchaseordermaster', 'erp_purchaseordermaster.purchaseOrderID', '=', 'appointment_details.po_master_id')
        ->join('erp_purchaseorderdetails', 'erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'appointment_details.po_detail_id')
        ->get();

        $is_valid = true;
        $msg = 'Approval failed,please check the below details.'. "<br>";;
        foreach($appointments as $detail)
        {
          

            if($detail->balance_qty < $detail->planned_qty)
            {
                $info =" The item ".$detail->itemPrimaryCode. " from  purchase order ".$detail->purchaseOrderCode." has planned quantity(".$detail->planned_qty.") is greater than balance quantity(".$detail->balance_qty.").";
                $msg .= $info . "<br>";
                $is_valid = false;
            }
        }

        if(!$is_valid)
        {
            return $this->sendError($msg, 500);
        }
        else
        {
            return $this->sendResponse(true, 'succesfully checked');

        }

    }

    public function getSegmentOfAppointment(DeliveryAppointmentRequest $request)
    {
        try
        {
            $serviceLineSystemID = $this->appointmentRepository->getServiceLineSystemIDs($request);
            return $this->sendResponse($serviceLineSystemID , trans('srm_supplier_management.data_retrieved_successfully'));
        }
        catch (\Exception $e)
        {
            return $this->sendError(trans('srm_supplier_management.something_went_wrong').$e->getMessage());
        }
    }

    public function createAppointmentGrv(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $acc_d = DeliveryAppoinmentGRV::dispatch($input);

        return $this->sendResponse($acc_d, trans('srm_supplier_management.successfully_created'));
    }
}
