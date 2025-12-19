<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateEmployeeMobileBillMasterAPIRequest;
use App\Http\Requests\API\UpdateEmployeeMobileBillMasterAPIRequest;
use App\Models\EmployeeMobileBillMaster;
use App\Models\MobileBillMaster;
use App\Repositories\EmployeeMobileBillMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeeMobileBillMasterController
 * @package App\Http\Controllers\API
 */

class EmployeeMobileBillMasterAPIController extends AppBaseController
{
    /** @var  EmployeeMobileBillMasterRepository */
    private $employeeMobileBillMasterRepository;

    public function __construct(EmployeeMobileBillMasterRepository $employeeMobileBillMasterRepo)
    {
        $this->employeeMobileBillMasterRepository = $employeeMobileBillMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeMobileBillMasters",
     *      summary="Get a listing of the EmployeeMobileBillMasters.",
     *      tags={"EmployeeMobileBillMaster"},
     *      description="Get all EmployeeMobileBillMasters",
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
     *                  @SWG\Items(ref="#/definitions/EmployeeMobileBillMaster")
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
        $this->employeeMobileBillMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeMobileBillMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeMobileBillMasters = $this->employeeMobileBillMasterRepository->all();

        return $this->sendResponse($employeeMobileBillMasters->toArray(), trans('custom.employee_mobile_bill_masters_retrieved_successfull'));
    }

    /**
     * @param CreateEmployeeMobileBillMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employeeMobileBillMasters",
     *      summary="Store a newly created EmployeeMobileBillMaster in storage",
     *      tags={"EmployeeMobileBillMaster"},
     *      description="Store EmployeeMobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeMobileBillMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeMobileBillMaster")
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
     *                  ref="#/definitions/EmployeeMobileBillMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEmployeeMobileBillMasterAPIRequest $request)
    {
        $input = $request->all();

        $employeeMobileBillMaster = $this->employeeMobileBillMasterRepository->create($input);

        return $this->sendResponse($employeeMobileBillMaster->toArray(), trans('custom.employee_mobile_bill_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeMobileBillMasters/{id}",
     *      summary="Display the specified EmployeeMobileBillMaster",
     *      tags={"EmployeeMobileBillMaster"},
     *      description="Get EmployeeMobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeMobileBillMaster",
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
     *                  ref="#/definitions/EmployeeMobileBillMaster"
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
        /** @var EmployeeMobileBillMaster $employeeMobileBillMaster */
        $employeeMobileBillMaster = $this->employeeMobileBillMasterRepository->findWithoutFail($id);

        if (empty($employeeMobileBillMaster)) {
            return $this->sendError(trans('custom.employee_mobile_bill_master_not_found'));
        }

        return $this->sendResponse($employeeMobileBillMaster->toArray(), trans('custom.employee_mobile_bill_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEmployeeMobileBillMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employeeMobileBillMasters/{id}",
     *      summary="Update the specified EmployeeMobileBillMaster in storage",
     *      tags={"EmployeeMobileBillMaster"},
     *      description="Update EmployeeMobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeMobileBillMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeMobileBillMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeMobileBillMaster")
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
     *                  ref="#/definitions/EmployeeMobileBillMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEmployeeMobileBillMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeeMobileBillMaster $employeeMobileBillMaster */
        $employeeMobileBillMaster = $this->employeeMobileBillMasterRepository->findWithoutFail($id);

        if (empty($employeeMobileBillMaster)) {
            return $this->sendError(trans('custom.employee_mobile_bill_master_not_found'));
        }

        $employeeMobileBillMaster = $this->employeeMobileBillMasterRepository->update($input, $id);

        return $this->sendResponse($employeeMobileBillMaster->toArray(), trans('custom.employeemobilebillmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employeeMobileBillMasters/{id}",
     *      summary="Remove the specified EmployeeMobileBillMaster from storage",
     *      tags={"EmployeeMobileBillMaster"},
     *      description="Delete EmployeeMobileBillMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeMobileBillMaster",
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
        /** @var EmployeeMobileBillMaster $employeeMobileBillMaster */
        $employeeMobileBillMaster = $this->employeeMobileBillMasterRepository->findWithoutFail($id);

        if (empty($employeeMobileBillMaster)) {
            return $this->sendError(trans('custom.employee_mobile_bill_master_not_found'));
        }

        $employeeMobileBillMaster->delete();

        return $this->sendSuccess('Employee Mobile Bill Master deleted successfully');
    }

    public function generateEmployeeBill(Request $request){

        $input = $request->all();
        $user = Helper::getEmployeeInfo();
        $employeeMobileBillMaster = null;
        if(!(isset($input['mobilebillMasterID']) && $input['mobilebillMasterID']>0)){
            return $this->sendError(trans('custom.mobile_bill_master_id_not_found'));
        }

        $mobilMaster = MobileBillMaster::with(['summary'=> function($query){
            $query->with(['mobile_pool.mobile_master.employee']);
        }])->find($input['mobilebillMasterID']);

        if(empty($mobilMaster)){
            return $this->sendError(trans('custom.mobile_bill_master_not_found'));
        }

        $input['isSubmitted'] = 0;
        $input['RollLevForApp_curr'] = 0;
        $summary = isset($mobilMaster->summary)?$mobilMaster->summary:[];


        if(!empty($summary)) {

            foreach ($summary as $row){

                $input['mobileNo'] = $row->mobileNumber;
                $input['totalAmount'] = $row->totalCurrentCharges;

                if(isset($row->mobile_pool->mobile_master->employee)){
                    $employee = $row->mobile_pool->mobile_master->employee;
                    $input['companyID'] = $employee->empCompanyID;
                    $input['companySysID'] = $employee->empCompanySystemID;
                    $input['employeeSystemID'] = $employee->employeeSystemID;
                    $input['empID'] = $employee->empID;
                }else{
                    $input['companyID'] = '';
                    $input['companySysID'] = 0;
                    $input['employeeSystemID'] = 0;
                    $input['empID'] = 0;
                }

                $input['creditLimit'] = isset($row->mobile_pool->mobile_master->creditlimit)?$row->mobile_pool->mobile_master->creditlimit:0;

                if(isset($input['creditLimit']) && ($input['creditLimit'] < $input['totalAmount'])){
                    $input['exceededAmount'] = number_format(($input['totalAmount'] - $input['creditLimit']),3);
                    $input['deductionAmount'] = number_format(($input['totalAmount'] - $input['creditLimit']),3);
                }else{
                    $input['exceededAmount'] = 0;
                    $input['deductionAmount'] = 0;
                }

                $input['createDate'] = Carbon::now()->format('d/m/Y');
                $input['createUserID'] = $user->empID;
                $input['createPCID'] = gethostname();

                $employeeMobileBillMaster = $this->employeeMobileBillMasterRepository->create($input);

            }
        }

        if($employeeMobileBillMaster != null){
            return $this->sendResponse($employeeMobileBillMaster->toArray(), trans('custom.employee_mobile_bill_master_saved_successfully'));
        }
        return $this->sendError(trans('custom.error_occur_1'),500);

    }



    public function getAllEmployeeMobileBill(Request $request){
        $input = $request->all();
        $id = isset($input['id'])?$input['id']:0;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $mobileMaster = EmployeeMobileBillMaster::where('mobilebillMasterID',$id)->with(['employee']);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $mobileMaster = $mobileMaster->where(function ($query) use ($search) {
                $query->where('empID', 'LIKE', "%{$search}%")
                    ->orWhere('companyID', 'LIKE', "%{$search}%")
                    ->orWhere('mobileNo', 'LIKE', "%{$search}%")
                ->orWhereHas('employee', function ($query){
                    $query->where('empName');
                });
            });
        }

        return \DataTables::eloquent($mobileMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('EmployeemobilebillmasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function exportEmployeeMobileBill(Request $request){

        $input = $request->all();
        $id = isset($input['id'])?$input['id']:0;
        $type = isset($input['type'])?$input['type']:'csv';
        $mobileMaster = EmployeeMobileBillMaster::where('mobilebillMasterID',$id)->with(['employee'])->orderBy('EmployeemobilebillmasterID','DESC')->get();

        if (!empty($mobileMaster) && count((array)$mobileMaster)>0) {
            $x = 0;
            $data = [];
            foreach ($mobileMaster as $val) {
                $x++;
                $empName = isset($val->employee->empName)?$val->employee->empName:'';

                $data[$x]['Company ID'] = $val->companyID;
                $data[$x]['Employee'] = $val->empID.' - '.$empName;
                $data[$x]['Mobile No'] = $val->mobileNo;
                $data[$x]['Credit Limit'] = round($val->creditLimit,3);
                $data[$x]['Exceeded Amount'] = round($val->exceededAmount,3);
                $data[$x]['Deduction Amount'] = round($val->deductionAmount,3);
                $data[$x]['Official Amount'] = round($val->officialAmount,3);
                $data[$x]['Personal Amount'] = round($val->personalAmount,3);
            }

            \Excel::create('employee_mobile_bill_report', function ($excel) use ($data) {
                $excel->sheet('sheet name', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', true);
                    $sheet->setAutoSize(true);
                    $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    
                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }
                });
                $lastrow = $excel->getActiveSheet()->getHighestRow();
                $excel->getActiveSheet()->getStyle('A1:N' . $lastrow)->getAlignment()->setWrapText(true);
            })->download($type);

            return $this->sendResponse(array(), trans('custom.success_export'));
        }
        return $this->sendError( 'No Records Found');

    }
}
