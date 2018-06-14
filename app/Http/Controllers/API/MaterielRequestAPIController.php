<?php
/**
 * =============================================
 * -- File Name : MaterielRequestAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Materiel Request
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Materiel Request
 * -- REVISION HISTORY
 * -- Date: 12-June 2018 By: Fayas Description: Added new functions named as getAllRequestByCompany(),getRequestFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMaterielRequestAPIRequest;
use App\Http\Requests\API\UpdateMaterielRequestAPIRequest;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\Location;
use App\Models\MaterielRequest;
use App\Models\Priority;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\MaterielRequestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MaterielRequestController
 * @package App\Http\Controllers\API
 */

class MaterielRequestAPIController extends AppBaseController
{
    /** @var  MaterielRequestRepository */
    private $materielRequestRepository;

    public function __construct(MaterielRequestRepository $materielRequestRepo)
    {
        $this->materielRequestRepository = $materielRequestRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequests",
     *      summary="Get a listing of the MaterielRequests.",
     *      tags={"MaterielRequest"},
     *      description="Get all MaterielRequests",
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
     *                  @SWG\Items(ref="#/definitions/MaterielRequest")
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
        $this->materielRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->materielRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $materielRequests = $this->materielRequestRepository->all();

        return $this->sendResponse($materielRequests->toArray(), 'Materiel Requests retrieved successfully');
    }

    /**
     * get Request By Company.
     * POST /getAllRequestByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllRequestByCompany(Request $request)
    {

         $input = $request->all();
         $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $materielRequests = MaterielRequest::whereIn('companySystemID', $subCompanies)
                                    ->with(['created_by', 'priority_by', 'location_by','segment_by']);



        if (array_key_exists('ConfirmedYN', $input)) {

            if(($input['ConfirmedYN'] == 0 || $input['ConfirmedYN'] == 1)  && !is_null($input['ConfirmedYN'])) {
                $materielRequests->where('ConfirmedYN', $input['ConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if(($input['approved'] == 0 || $input['approved'] == -1 ) && !is_null($input['approved'])) {
                $materielRequests->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $materielRequests->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }


        $materielRequests = $materielRequests->select(
            ['erp_request.RequestID',
                'erp_request.RequestCode',
                'erp_request.comments',
                'erp_request.location',
                'erp_request.RequestedDate',
                'erp_request.priority',
                'erp_request.ConfirmedYN',
                'erp_request.approved',
                'erp_request.serviceLineSystemID',
                'erp_request.documentSystemID'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('RequestCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($materielRequests)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('RequestID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * @param CreateMaterielRequestAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/materielRequests",
     *      summary="Store a newly created MaterielRequest in storage",
     *      tags={"MaterielRequest"},
     *      description="Store MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequest that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequest")
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
     *                  ref="#/definitions/MaterielRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMaterielRequestAPIRequest $request)
    {
        $input = $this->convertArrayToValue($request->all());

        $employee = \Helper::getEmployeeInfo();

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $input['RequestedDate'] = now();

        $input['departmentID'] = 'IM';
        $input['departmentSystemID'] = 10;
        $input['documentSystemID'] =  9;
        $input['ConfirmedYN'] =  0;

        $lastSerial = MaterielRequest::where('companySystemID', $input['companySystemID'])
                                        ->where('documentSystemID', $input['documentSystemID'])
                                        ->orderBy('RequestID', 'desc')
                                        ->first();

        $lastSerialNumber = 0;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $input['serialNumber'] = $lastSerialNumber;


        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $document = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($document) {
            $input['documentID'] = $document->documentID;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
        $input['RequestCode'] = $input['companyID'] . '\\' . $input['departmentID'] . '\\' . $input['serviceLineCode'] . '\\' . $input['documentID'] . $code;

        $materielRequests = $this->materielRequestRepository->create($input);

        return $this->sendResponse($materielRequests->toArray(), 'Materiel Request saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequests/{id}",
     *      summary="Display the specified MaterielRequest",
     *      tags={"MaterielRequest"},
     *      description="Get MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequest",
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
     *                  ref="#/definitions/MaterielRequest"
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
        /** @var MaterielRequest $materielRequest */
        $materielRequest = $this->materielRequestRepository->with(['segment_by','created_by','confirmed_by'])->findWithoutFail($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        return $this->sendResponse($materielRequest->toArray(), 'Materiel Request retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMaterielRequestAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/materielRequests/{id}",
     *      summary="Update the specified MaterielRequest in storage",
     *      tags={"MaterielRequest"},
     *      description="Update MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequest",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequest that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequest")
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
     *                  ref="#/definitions/MaterielRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMaterielRequestAPIRequest $request)
    {
        $input = $request->all();

        /** @var MaterielRequest $materielRequest */
        $materielRequest = $this->materielRequestRepository->findWithoutFail($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        $materielRequest = $this->materielRequestRepository->update($input, $id);

        return $this->sendResponse($materielRequest->toArray(), 'MaterielRequest updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/materielRequests/{id}",
     *      summary="Remove the specified MaterielRequest from storage",
     *      tags={"MaterielRequest"},
     *      description="Delete MaterielRequest",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequest",
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
        /** @var MaterielRequest $materielRequest */
        $materielRequest = $this->materielRequestRepository->findWithoutFail($id);

        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request not found');
        }

        $materielRequest->delete();

        return $this->sendResponse($id, 'Materiel Request deleted successfully');
    }

    /**
     * get Request Form Data
     * get /getRequestFormData
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getRequestFormData(Request $request)
    {

        $input = $request->all();
        $companyId = $input['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $subCompanies = [$companyId];
        }

        $segments = SegmentMaster::whereIn("companySystemID", $subCompanies);

        if (array_key_exists('isFilter', $input)) {
            if ($input['isFilter'] != 1) {
                $segments = $segments->where('isActive', 1);
            }
        } else {
            $segments = $segments->where('isActive', 1);
        }

        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $priorities = Priority::all();

        $locations = Location::all();

        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'priorities' => $priorities,
            'locations' => $locations,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
