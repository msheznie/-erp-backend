<?php
/**
 * =============================================
 * -- File Name : DocumentEmailNotificationDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DocumentEmailNotificationDetail
 * -- Author : Mohamed Nazir
 * -- Create date : 10 - January 2019
 * -- Description : This file contains the all CRUD for Document Email Notification Detail
 * -- REVISION HISTORY
 * -- Date: 10-January 2019 By: Nazir Description: Added new function getAllCompanyEmailSendingPolicy(),
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentEmailNotificationDetailAPIRequest;
use App\Http\Requests\API\UpdateDocumentEmailNotificationDetailAPIRequest;
use App\Models\Company;
use App\Models\DocumentEmailNotificationDetail;
use App\Models\Employee;
use App\Repositories\DocumentEmailNotificationDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentEmailNotificationDetailController
 * @package App\Http\Controllers\API
 */
class DocumentEmailNotificationDetailAPIController extends AppBaseController
{
    /** @var  DocumentEmailNotificationDetailRepository */
    private $documentEmailNotificationDetailRepository;

    public function __construct(DocumentEmailNotificationDetailRepository $documentEmailNotificationDetailRepo)
    {
        $this->documentEmailNotificationDetailRepository = $documentEmailNotificationDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentEmailNotificationDetails",
     *      summary="Get a listing of the DocumentEmailNotificationDetails.",
     *      tags={"DocumentEmailNotificationDetail"},
     *      description="Get all DocumentEmailNotificationDetails",
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
     *                  @SWG\Items(ref="#/definitions/DocumentEmailNotificationDetail")
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
        $this->documentEmailNotificationDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->documentEmailNotificationDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentEmailNotificationDetails = $this->documentEmailNotificationDetailRepository->all();

        return $this->sendResponse($documentEmailNotificationDetails->toArray(), trans('custom.document_email_notification_details_retrieved_succ'));
    }

    /**
     * @param CreateDocumentEmailNotificationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/documentEmailNotificationDetails",
     *      summary="Store a newly created DocumentEmailNotificationDetail in storage",
     *      tags={"DocumentEmailNotificationDetail"},
     *      description="Store DocumentEmailNotificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentEmailNotificationDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentEmailNotificationDetail")
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
     *                  ref="#/definitions/DocumentEmailNotificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentEmailNotificationDetailAPIRequest $request)
    {
        $input = $request->all();
        $employees = collect($input["employeeSystemID"])->pluck("employeeSystemID")->toArray();

        $validate = DocumentEmailNotificationDetail::where('companySystemID', $request->companySystemID)->where('emailNotificationID', $request->emailNotificationID)->whereIN('employeeSystemID', $employees)->exists();

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        if ($validate) {
            return $this->sendError(trans('custom.selected_employee_already_exists_in_the_selected_p'));
        } else {
            if ($employees) {
                foreach ($employees as $val) {
                    $employeeMas = Employee::where('employeeSystemID', $input['employeeSystemID'])->first();
                    $inputArr = [
                        "companyID" => $company->CompanyID,
                        "companySystemID" => $input["companySystemID"],
                        "emailNotificationID" => $input["emailNotificationID"],
                        "employeeSystemID" => $val,
                        "empID" => $employeeMas->empID,
                        "sendYN" => 1
                    ];
                    $employeeEmailPolicy = $this->documentEmailNotificationDetailRepository->create($inputArr);
                }
            }
        }

        return $this->sendResponse($employeeEmailPolicy->toArray(), trans('custom.document_email_notification_detail_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentEmailNotificationDetails/{id}",
     *      summary="Display the specified DocumentEmailNotificationDetail",
     *      tags={"DocumentEmailNotificationDetail"},
     *      description="Get DocumentEmailNotificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationDetail",
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
     *                  ref="#/definitions/DocumentEmailNotificationDetail"
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
        /** @var DocumentEmailNotificationDetail $documentEmailNotificationDetail */
        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationDetail)) {
            return $this->sendError(trans('custom.document_email_notification_detail_not_found'));
        }

        return $this->sendResponse($documentEmailNotificationDetail->toArray(), trans('custom.document_email_notification_detail_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param UpdateDocumentEmailNotificationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/documentEmailNotificationDetails/{id}",
     *      summary="Update the specified DocumentEmailNotificationDetail in storage",
     *      tags={"DocumentEmailNotificationDetail"},
     *      description="Update DocumentEmailNotificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentEmailNotificationDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentEmailNotificationDetail")
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
     *                  ref="#/definitions/DocumentEmailNotificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentEmailNotificationDetailAPIRequest $request)
    {
        $input = $request->all();

        $input = $request->all();
        $input = array_except($input, ['company',
            'policy_category',
            'companyID',
            'companySystemID',
            'companyID',
            'empID',
            'employee_by',
            'employeeSystemID']);

        $input = $this->convertArrayToValue($input);

        /** @var DocumentEmailNotificationDetail $documentEmailNotificationDetail */
        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationDetail)) {
            return $this->sendError(trans('custom.document_email_notification_detail_not_found'));
        }

        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->update($input, $id);

        return $this->sendResponse($documentEmailNotificationDetail->toArray(), trans('custom.documentemailnotificationdetail_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/documentEmailNotificationDetails/{id}",
     *      summary="Remove the specified DocumentEmailNotificationDetail from storage",
     *      tags={"DocumentEmailNotificationDetail"},
     *      description="Delete DocumentEmailNotificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentEmailNotificationDetail",
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
        /** @var DocumentEmailNotificationDetail $documentEmailNotificationDetail */
        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationDetail)) {
            return $this->sendError(trans('custom.document_email_notification_detail_not_found'));
        }

        $documentEmailNotificationDetail->delete();

        return $this->sendResponse($id, trans('custom.document_email_notification_detail_deleted_success'));
    }

    public function getAllCompanyEmailSendingPolicy(Request $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('companySystemID', 'emailNotificationID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companySystemID'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $search = $request->input('search.value');

        $companyPolicyMasters = DocumentEmailNotificationDetail::whereIn('companySystemID', $childCompanies)
            ->with(['company', 'policyCategory', 'employee_by']);

        if (array_key_exists('emailNotificationID', $input)) {
            if ($input['emailNotificationID'] != 0 && !is_null($input['emailNotificationID'])) {
                $companyPolicyMasters = $companyPolicyMasters->where('emailNotificationID', $input['emailNotificationID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $companyPolicyMasters = $companyPolicyMasters->where(function ($query) use ($search) {
                $query->where('policyCategory', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($companyPolicyMasters)
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
            ->make(true);

    }
}
