<?php
/**
 * =============================================
 * -- File Name : TemplatesDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Mohamed Fayas
 * -- Create date : 21 - October 2018
 * -- Description : This file contains the all CRUD for Templates Details
 * -- REVISION HISTORY
 * -- Date: 21-October 2018 By: Fayas Description: Added new function getTemplatesDetailsByMaster(),getAllGLCodesByTemplate
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTemplatesDetailsAPIRequest;
use App\Http\Requests\API\UpdateTemplatesDetailsAPIRequest;
use App\Models\BudgetTransferForm;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ReportTemplateLinks;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateDetails;
use App\Models\ErpBudgetAddition;
use App\Models\TemplatesDetails;
use App\Models\TemplatesGLCode;
use App\Repositories\TemplatesDetailsRepository;
use App\Repositories\TemplatesMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TemplatesDetailsController
 * @package App\Http\Controllers\API
 */
class TemplatesDetailsAPIController extends AppBaseController
{
    /** @var  TemplatesDetailsRepository */
    private $templatesDetailsRepository;
    private $templatesMasterRepository;

    public function __construct(TemplatesDetailsRepository $templatesDetailsRepo, TemplatesMasterRepository $templatesMasterRepo)
    {
        $this->templatesDetailsRepository = $templatesDetailsRepo;
        $this->templatesMasterRepository = $templatesMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesDetails",
     *      summary="Get a listing of the TemplatesDetails.",
     *      tags={"TemplatesDetails"},
     *      description="Get all TemplatesDetails",
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
     *                  @SWG\Items(ref="#/definitions/TemplatesDetails")
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
        $this->templatesDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->templatesDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $templatesDetails = $this->templatesDetailsRepository->all();

        return $this->sendResponse($templatesDetails->toArray(), trans('custom.templates_details_retrieved_successfully'));
    }

    /**
     * @param CreateTemplatesDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/templatesDetails",
     *      summary="Store a newly created TemplatesDetails in storage",
     *      tags={"TemplatesDetails"},
     *      description="Store TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesDetails")
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
     *                  ref="#/definitions/TemplatesDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTemplatesDetailsAPIRequest $request)
    {
        $input = $request->all();

        $templatesDetails = $this->templatesDetailsRepository->create($input);

        return $this->sendResponse($templatesDetails->toArray(), trans('custom.templates_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/templatesDetails/{id}",
     *      summary="Display the specified TemplatesDetails",
     *      tags={"TemplatesDetails"},
     *      description="Get TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesDetails",
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
     *                  ref="#/definitions/TemplatesDetails"
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
        /** @var TemplatesDetails $templatesDetails */
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            return $this->sendError(trans('custom.templates_details_not_found'));
        }

        return $this->sendResponse($templatesDetails->toArray(), trans('custom.templates_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTemplatesDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/templatesDetails/{id}",
     *      summary="Update the specified TemplatesDetails in storage",
     *      tags={"TemplatesDetails"},
     *      description="Update TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TemplatesDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TemplatesDetails")
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
     *                  ref="#/definitions/TemplatesDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTemplatesDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TemplatesDetails $templatesDetails */
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            return $this->sendError(trans('custom.templates_details_not_found'));
        }

        $templatesDetails = $this->templatesDetailsRepository->update($input, $id);

        return $this->sendResponse($templatesDetails->toArray(), trans('custom.templatesdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/templatesDetails/{id}",
     *      summary="Remove the specified TemplatesDetails from storage",
     *      tags={"TemplatesDetails"},
     *      description="Delete TemplatesDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TemplatesDetails",
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
        /** @var TemplatesDetails $templatesDetails */
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            return $this->sendError(trans('custom.templates_details_not_found'));
        }

        $templatesDetails->delete();

        return $this->sendResponse($id, trans('custom.templates_details_deleted_successfully'));
    }

    public function getTemplatesDetailsByMaster(Request $request)
    {

        $id = $request->get('id');

        $budgetTransferMaster = BudgetTransferForm::find($id);

        if (empty($budgetTransferMaster)) {
            return $this->sendError(trans('custom.budget_transfer_not_found'));
        }

        $templateMaster = ReportTemplate::find($budgetTransferMaster->templatesMasterAutoID);

        if (empty($templateMaster)) {
            return $this->sendError(trans('custom.templates_master_not_found'));
        }

        $details = ReportTemplateDetails::where('companyReportTemplateID', $budgetTransferMaster->templatesMasterAutoID)
                                        ->where('isFinalLevel', 1)
                                        ->get();

        return $this->sendResponse($details, trans('custom.templates_details_retrieved_successfully'));
    }

    public function getTemplateByGLCode(Request $request){
        $id = $request->get('id');
        $glCodeID = $request->get('glCodeID');

         $companySystemID = $request->get('companySystemID');

        $budgetTransferMaster = BudgetTransferForm::find($id);

        if (empty($budgetTransferMaster)) {
            return $this->sendError(trans('custom.budget_transfer_not_found'));
        }

        $templateMaster = ReportTemplate::find($budgetTransferMaster->templatesMasterAutoID);

        if (empty($templateMaster)) {
            return $this->sendError(trans('custom.templates_master_not_found'));
        }

        $details = ReportTemplateDetails::where('companyReportTemplateID', $budgetTransferMaster->templatesMasterAutoID)
            ->where('isFinalLevel', 1)
            ->get();

        $detIDs = collect($details)->pluck('detID')->toArray();
        $templateMasterID = collect($details)->pluck('companyReportTemplateID')->toArray();

        $glData = ReportTemplateLinks::where('glAutoID',$glCodeID)->whereIn('templateMasterID', $templateMasterID)->whereIn('templateDetailID',$detIDs)->get();

        $templateDetail = ReportTemplateDetails::where('detID', $glData[0]->templateDetailID)->where('companyReportTemplateID',$glData[0]->templateMasterID)->get();

        return $this->sendResponse($templateDetail, trans('custom.template_description_retrieved_successfully'));
    }

    public function getAllGLCodes(Request $request){

        $id = $request->get('id');

        $budgetTransferMaster = BudgetTransferForm::find($id);

        if (empty($budgetTransferMaster)) {
            return $this->sendError(trans('custom.budget_transfer_not_found'));
        }

        $templateMaster = ReportTemplate::find($budgetTransferMaster->templatesMasterAutoID);

        if (empty($templateMaster)) {
            return $this->sendError(trans('custom.templates_master_not_found'));
        }

        $details = ReportTemplateDetails::where('companyReportTemplateID', $budgetTransferMaster->templatesMasterAutoID)
            ->where('isFinalLevel', 1)
            ->get();
        $detIDs = collect($details)->pluck('detID')->toArray();
        $templateMasterID = collect($details)->pluck('companyReportTemplateID')->toArray();

        $glData = ReportTemplateLinks::whereNotNull('glAutoID')->whereIn('templateMasterID', $templateMasterID)->whereIn('templateDetailID',$detIDs)->get();

        $glIds = collect($glData)->pluck('glAutoID')->toArray();

        $glCodes = ChartOfAccountsAssigned::where('companySystemID', $request->get('companySystemID'))->whereIn('chartOfAccountSystemID', $glIds)
            ->get(['chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'controlAccounts']);

       return $this->sendResponse($glCodes, trans('custom.gl_codes_retrieved_successfully'));
    }

    public function getAllGLCodesByTemplate(Request $request)
    {

        $id = $request->get('id');
        $templateDetail = ReportTemplateDetails::find($id);

        if (empty($templateDetail)) {
            return $this->sendError(trans('custom.templates_detail_not_found'));
        }

        $glData = ReportTemplateLinks::where('templateMasterID', $templateDetail->companyReportTemplateID)
                                    ->where('templateDetailID', $id)
                                    ->whereNotNull('glAutoID')
                                    ->get();

        $glIds = collect($glData)->pluck('glAutoID')->toArray();

        $glCodes = ChartOfAccountsAssigned::where('companySystemID', $request->get('companySystemID'))
            ->whereIn('chartOfAccountSystemID', $glIds)
            ->get(['chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'controlAccounts']);

        return $this->sendResponse($glCodes, trans('custom.gl_codes_retrieved_successfully'));
    }

    public function getTemplatesDetailsById(Request $request)
    {

        $id = $request->get('id');

        $templateMaster = ReportTemplate::find($id);

        if (empty($templateMaster)) {
            return $this->sendError(trans('custom.templates_master_not_found'));
        }

        $details = ReportTemplateDetails::where('companyReportTemplateID', $id)
                                        ->get();

        return $this->sendResponse($details, trans('custom.templates_details_retrieved_successfully'));
    }
}
