<?php
/**
 * =============================================
 * -- File Name : LogisticAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic
 * -- REVISION HISTORY
 * -- Date: 12-September 2018 By: Fayas Description: Added new functions named as getAllLogisticByCompany(),getLogisticFormData(),
 *                                exportLogisticsByCompanyReport()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticAPIRequest;
use App\Http\Requests\API\UpdateLogisticAPIRequest;
use App\Models\Logistic;
use App\Repositories\LogisticRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticController
 * @package App\Http\Controllers\API
 */

class LogisticAPIController extends AppBaseController
{
    /** @var  LogisticRepository */
    private $logisticRepository;

    public function __construct(LogisticRepository $logisticRepo)
    {
        $this->logisticRepository = $logisticRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logistics",
     *      summary="Get a listing of the Logistics.",
     *      tags={"Logistic"},
     *      description="Get all Logistics",
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
     *                  @SWG\Items(ref="#/definitions/Logistic")
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
        $this->logisticRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logistics = $this->logisticRepository->all();

        return $this->sendResponse($logistics->toArray(), 'Logistics retrieved successfully');
    }

    /**
     * @param CreateLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logistics",
     *      summary="Store a newly created Logistic in storage",
     *      tags={"Logistic"},
     *      description="Store Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Logistic that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Logistic")
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
     *                  ref="#/definitions/Logistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticAPIRequest $request)
    {
        $input = $request->all();

        $logistics = $this->logisticRepository->create($input);

        return $this->sendResponse($logistics->toArray(), 'Logistic saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logistics/{id}",
     *      summary="Display the specified Logistic",
     *      tags={"Logistic"},
     *      description="Get Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Logistic",
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
     *                  ref="#/definitions/Logistic"
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
        /** @var Logistic $logistic */
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            return $this->sendError('Logistic not found');
        }

        return $this->sendResponse($logistic->toArray(), 'Logistic retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logistics/{id}",
     *      summary="Update the specified Logistic in storage",
     *      tags={"Logistic"},
     *      description="Update Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Logistic",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Logistic that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Logistic")
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
     *                  ref="#/definitions/Logistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticAPIRequest $request)
    {
        $input = $request->all();

        /** @var Logistic $logistic */
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            return $this->sendError('Logistic not found');
        }

        $logistic = $this->logisticRepository->update($input, $id);

        return $this->sendResponse($logistic->toArray(), 'Logistic updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logistics/{id}",
     *      summary="Remove the specified Logistic from storage",
     *      tags={"Logistic"},
     *      description="Delete Logistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Logistic",
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
        /** @var Logistic $logistic */
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            return $this->sendError('Logistic not found');
        }

        $logistic->delete();

        return $this->sendResponse($id, 'Logistic deleted successfully');
    }

    /**
     * get All Logistic By Company
     * POST /getAllLogisticByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllLogisticByCompany(Request $request)
    {

        $input = $request->all();
        $logistics = ($this->getAllLogisticByCompanyQry($request));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        return \DataTables::eloquent($logistics)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('logisticMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Logistic Form Data
     * Get /getLogisticFormData
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getLogisticFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $units = Unit::all();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'units' => $units
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function exportLogisticsByCompanyReport(Request $request)
    {
        $data = array();
        $output = ($this->getAllLogisticByCompanyQry($request))->orderBy('logisticMasterID', 'DES')->get();
        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
               $data[$x]['Logistic Code'] = $value->logisticDocCode;
                $data[$x]['Invoice No'] = $value->customInvoiceNo;
                $data[$x]['Invoice Amount'] = $value->customInvoiceAmount;
                $data[$x]['Invoice Date'] =  \Helper::dateFormat($value->customInvoiceDate);
                if ($value->shipping_mode) {
                    $data[$x]['Mode'] = $value->shipping_mode->modeShippingDescription;
                } else {
                    $data[$x]['Mode'] = '';
                }
                if ($value->supplier_by) {
                    $data[$x]['Supplier'] = $value->supplier_by->supplierName;
                } else {
                    $data[$x]['Supplier'] = '';
                }
                $data[$x]['Comments '] = $value->comments;

                $data[$x]['Renewal Date'] =  \Helper::dateFormat($value->nextCustomDocRenewalDate);
                $data[$x]['Arrival Date'] =  \Helper::dateFormat($value->customeArrivalDate);
                if($value->ftaOrDF){
                    $data[$x]['FTA/DF'] =  $value->ftaOrDF;
                }else{
                    $data[$x]['FTA/DF'] =  'NA';
                }
                if ($value->created_by) {
                    $data[$x]['Created By'] =  $value->created_by->empName;
                } else {
                    $data[$x]['Created By'] =  '';
                }
                $data[$x]['Created at'] =  \Helper::dateFormat($value->createdDateTime);
                $x++;
            }
        }

        $csv = \Excel::create('logistic_by_company', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    public function getAllLogisticByCompanyQry($request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));
        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $logistics = Logistic::whereIn('companySystemID', $subCompanies)
            ->with(['created_by','supplier_by','shipping_mode']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $logistics = $logistics->where(function ($query) use ($search) {
                $query->where('logisticDocCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

        return $logistics;
    }

}
