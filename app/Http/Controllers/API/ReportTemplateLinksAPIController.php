<?php
/**
 * =============================================
 * -- File Name : ReportTemplateLinksAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report Template
 * -- Author : Mubashir
 * -- Create date : 20 - December 2018
 * -- Description :  This file contains the all CRUD for Report template gl link
 * -- REVISION HISTORY
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateLinksAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateLinksAPIRequest;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\Budjetdetails;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateLinks;
use App\Repositories\ReportTemplateLinksRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\ChartOfAccountDependency;
use Illuminate\Support\Facades\DB;
use App\Models\BudgetMaster;

/**
 * Class ReportTemplateLinksController
 * @package App\Http\Controllers\API
 */
class ReportTemplateLinksAPIController extends AppBaseController
{
    /** @var  ReportTemplateLinksRepository */
    private $reportTemplateLinksRepository;

    public function __construct(ReportTemplateLinksRepository $reportTemplateLinksRepo)
    {
        $this->reportTemplateLinksRepository = $reportTemplateLinksRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateLinks",
     *      summary="Get a listing of the ReportTemplateLinks.",
     *      tags={"ReportTemplateLinks"},
     *      description="Get all ReportTemplateLinks",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateLinks")
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
        $this->reportTemplateLinksRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateLinksRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateLinks = $this->reportTemplateLinksRepository->all();

        return $this->sendResponse($reportTemplateLinks->toArray(), trans('custom.report_template_links_retrieved_successfully'));
    }

    /**
     * @param CreateReportTemplateLinksAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateLinks",
     *      summary="Store a newly created ReportTemplateLinks in storage",
     *      tags={"ReportTemplateLinks"},
     *      description="Store ReportTemplateLinks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateLinks that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateLinks")
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
     *                  ref="#/definitions/ReportTemplateLinks"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateLinksAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'glAutoID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $tempDetail = ReportTemplateLinks::ofTemplate($input['templateMasterID'])->pluck('glAutoID')->toArray();

        $finalError = array(
            'already_gl_linked' => array(),
        );
        $error_count = 0;

        if ($input['glAutoID']) {
            foreach ($input['glAutoID'] as $key => $val) {
                if (in_array($val['chartOfAccountSystemID'], $tempDetail)) {
                    array_push($finalError['already_gl_linked'], $val['AccountCode'] . ' | ' . $val['AccountDescription']);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'already_gl_linked', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot add gl codes as it is already assigned", 500, $confirm_error);
            }else{
                foreach ($input['glAutoID'] as $key => $val) {
                    if (!in_array($val['chartOfAccountSystemID'], $tempDetail)) {
                        $data['templateMasterID'] = $input['templateMasterID'];
                        $data['templateDetailID'] = $input['templateDetailID'];
                        $data['sortOrder'] = $key + 1;
                        $data['glAutoID'] = $val['chartOfAccountSystemID'];
                        $data['glCode'] = $val['AccountCode'];
                        $data['glDescription'] = $val['AccountDescription'];
                        $data['companySystemID'] = $input['companySystemID'];
                        $data['companyID'] = $input['companyID'];
                        if($input['reportID'] == 1) {
                            if ($val["controlAccounts"] == 'BSA') {
                                $data['categoryType'] = 1;
                            } else {
                                $data['categoryType'] = 2;
                            }
                        }
                        $data['createdPCID'] = gethostname();
                        $data['createdUserID'] = \Helper::getEmployeeID();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $reportTemplateLinks = $this->reportTemplateLinksRepository->create($data);
                    }
                }
            }
        }

        $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $input['templateDetailID'])->update(['isFinalLevel' => 1]);

        $lastSortOrder = ReportTemplateLinks::ofTemplate($input['templateMasterID'])->where('templateDetailID',$input['templateDetailID'])->orderBy('linkID','asc')->get();
        if(count($lastSortOrder) > 0){
            foreach ($lastSortOrder as $key => $val) {
                $data2['sortOrder'] = $key + 1;
                $reportTemplateLinks = $this->reportTemplateLinksRepository->update($data2, $val->linkID);
            }
        }

        return $this->sendResponse([], trans('custom.report_template_links_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateLinks/{id}",
     *      summary="Display the specified ReportTemplateLinks",
     *      tags={"ReportTemplateLinks"},
     *      description="Get ReportTemplateLinks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateLinks",
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
     *                  ref="#/definitions/ReportTemplateLinks"
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
        /** @var ReportTemplateLinks $reportTemplateLinks */
        $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            return $this->sendError(trans('custom.report_template_links_not_found'));
        }

        return $this->sendResponse($reportTemplateLinks->toArray(), trans('custom.report_template_links_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateLinksAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateLinks/{id}",
     *      summary="Update the specified ReportTemplateLinks in storage",
     *      tags={"ReportTemplateLinks"},
     *      description="Update ReportTemplateLinks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateLinks",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateLinks that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateLinks")
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
     *                  ref="#/definitions/ReportTemplateLinks"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateLinksAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['subcategory', 'Actions', 'DT_Row_Index']);
        $input = $this->convertArrayToValue($input);
        /** @var ReportTemplateLinks $reportTemplateLinks */
        $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            return $this->sendError(trans('custom.report_template_links_not_found'));
        }

        $reportTemplateLinks = $this->reportTemplateLinksRepository->update($input, $id);

        return $this->sendResponse($reportTemplateLinks->toArray(), trans('custom.reporttemplatelinks_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateLinks/{id}",
     *      summary="Remove the specified ReportTemplateLinks from storage",
     *      tags={"ReportTemplateLinks"},
     *      description="Delete ReportTemplateLinks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateLinks",
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
        /** @var ReportTemplateLinks $reportTemplateLinks */
        $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            return $this->sendError(trans('custom.report_template_links_not_found'));
        }

        // $checkLinkInBudget = Budjetdetails::where('chartOfAccountID', $reportTemplateLinks->glAutoID)
        //                                   ->where('templateDetailID', $reportTemplateLinks->templateDetailID)
        //                                   ->first();

        $templateMasterID = $reportTemplateLinks->templateMasterID;
        $checkLinkInBudget = BudgetMaster::where('templateMasterID',$templateMasterID)->first();

        if ($checkLinkInBudget) {
             return $this->sendError(trans('custom.this_chart_of_account_cannot_be_deleted_since_the_'));
        }


        $reportTemplateLinks->delete();

        $checkTemplateLinksExists = ReportTemplateLinks::where('templateDetailID', $reportTemplateLinks->templateDetailID)->first();

        if (!$checkTemplateLinksExists) {
            $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $reportTemplateLinks->templateDetailID)->update(['isFinalLevel' => 0]);
        }

        return $this->sendResponse($id, trans('custom.report_template_links_deleted_successfully'));
    }

    public function reportTemplateDetailSubCatLink(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'subCategory' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $tempDetail = ReportTemplateLinks::where('templateDetailID', $input['templateDetailID'])->pluck('subCategory')->toArray();

        if ($input['subCategory']) {
            foreach ($input['subCategory'] as $key => $val) {
                if (!in_array($val['detID'], $tempDetail)) {
                    $data['templateMasterID'] = $input['templateMasterID'];
                    $data['templateDetailID'] = $input['templateDetailID'];
                    $data['sortOrder'] = $key + 1;
                    $data['subCategory'] = $val['detID'];
                    $data['companySystemID'] = $input['companySystemID'];
                    $data['companyID'] = $input['companyID'];
                    $data['createdPCID'] = gethostname();
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $reportTemplateLinks = $this->reportTemplateLinksRepository->create($data);
                }
            }
        }

        $lastSortOrder = ReportTemplateLinks::ofTemplate($input['templateMasterID'])->where('templateDetailID',$input['templateDetailID'])->orderBy('linkID','asc')->get();
        if(count($lastSortOrder) > 0){
            foreach ($lastSortOrder as $key => $val) {
                $data2['sortOrder'] = $key + 1;
                $reportTemplateLinks = $this->reportTemplateLinksRepository->update($data2, $val->linkID);
            }
        }
        return $this->sendResponse([], trans('custom.report_template_links_saved_successfully'));
    }

    public function deleteAllLinkedGLCodes(Request $request)
    {
        $input = $request->all();
        $glCodes = ReportTemplateLinks::where('templateDetailID',$request->templateDetailID)
                                      ->first();
                                    //   ->get()
                                    //   ->pluck('glAutoID')
                                    //   ->toArray();

        // $checkLinkInBudget = Budjetdetails::whereIn('chartOfAccountID', $glCodes)
        //                                   ->where('templateDetailID', $request->templateDetailID)
        //                                   ->first();
        $templateMasterID = $glCodes->templateMasterID;
        $checkLinkInBudget = BudgetMaster::where('templateMasterID',$templateMasterID)->first();

        if ($checkLinkInBudget) {
             return $this->sendError(trans('custom.chart_of_accounts_of_this_category_cannot_be_delet'));
        }

        $reportTemplateLinks = ReportTemplateLinks::where('templateDetailID',$request->templateDetailID)->delete();
        $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $request->templateDetailID)->update(['isFinalLevel' => 0]);
        return $this->sendResponse([], trans('custom.report_template_links_deleted_successfully'));
    }

    public function assignReportTemplateToGl(Request $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $reportTemplateMaster = ReportTemplate::find($input['selectedReportTemplate']);

        $tempDetail = ReportTemplateLinks::ofTemplate($input['selectedReportTemplate'])->pluck('glAutoID')->toArray();

        $finalError = array(
            'already_gl_linked' => array(),
        );
        $error_count = 0;
         $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])->first();
        if ($input['chartOfAccountSystemID']) {
            if (in_array($input['chartOfAccountSystemID'], $tempDetail)) {
                array_push($finalError['already_gl_linked'], $chartOfAccount->AccountCode . ' | ' . $chartOfAccount->AccountDescription);
                $error_count++;
            }

            $confirm_error = array('type' => 'already_gl_linked', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot add gl codes as it is already assigned", 500, $confirm_error);
            }else{
                if (!in_array($input['chartOfAccountSystemID'], $tempDetail)) {
                    $data['templateMasterID'] = $input['selectedReportTemplate'];
                    $data['templateDetailID'] = $input['selectedReportCategory'];
                    $data['sortOrder'] = 1;
                    $data['glAutoID'] = $input['chartOfAccountSystemID'];
                    $data['glCode'] = $chartOfAccount->AccountCode;
                    $data['glDescription'] = $chartOfAccount->AccountDescription;
                    $data['companySystemID'] = $input['companySystemID'];
                    $data['companyID'] = $input['companyID'];
                    if($reportTemplateMaster->reportID == 1) {
                        if ($chartOfAccount->controlAccounts == 'BSA') {
                            $data['categoryType'] = 1;
                        } else {
                            $data['categoryType'] = 2;
                        }
                    }
                    $data['createdPCID'] = gethostname();
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $reportTemplateLinks = $this->reportTemplateLinksRepository->create($data);
                }
            }
        }

        $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $input['selectedReportCategory'])->update(['isFinalLevel' => 1]);

        $lastSortOrder = ReportTemplateLinks::ofTemplate($input['selectedReportTemplate'])->where('templateDetailID',$input['selectedReportCategory'])->orderBy('linkID','asc')->get();
        if(count($lastSortOrder) > 0){
            foreach ($lastSortOrder as $key => $val) {
                $data2['sortOrder'] = $key + 1;
                $reportTemplateLinks = $this->reportTemplateLinksRepository->update($data2, $val->linkID);
            }
        }

        return $this->sendResponse([], trans('custom.report_template_links_saved_successfully'));
    }

    public function reAssignAndDeleteGlLink(Request $request)
    {
        $input = $request->all();

        if (!isset($input['reportTemplateCategory']) || (isset($input['reportTemplateCategory']) && $input['reportTemplateCategory'] == null)) {
            return $this->sendError("Please select a template category");
        }

        DB::beginTransaction();
        try {
            $id = isset($input['deleteGlLinkID']) ? $input['deleteGlLinkID'] : 0;
            $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

            if (empty($reportTemplateLinks)) {
                return $this->sendError(trans('custom.report_template_links_not_found'));
            }

            // $checkLinkInBudget = Budjetdetails::where('chartOfAccountID', $reportTemplateLinks->glAutoID)
            //                                   ->where('templateDetailID', $reportTemplateLinks->templateDetailID)
            //                                   ->first();
            $templateMasterID = $reportTemplateLinks->templateMasterID;
            $checkLinkInBudget = BudgetMaster::where('templateMasterID',$templateMasterID)->first();
            if ($checkLinkInBudget && $input['isBudgetCreated']) {
                 return $this->sendError('The Chart of Accounts has been pulled into the budget. If you change the Template Category for the GL code, it may cause mismatches in the budget values during preview ,Are you sure you want to change the Template Category?', 500,['type' => 'budgetExist']);
            }

            $reportTemplateLinks->delete();

            $checkTemplateLinksExists = ReportTemplateLinks::where('templateDetailID', $reportTemplateLinks->templateDetailID)->first();

            if (!$checkTemplateLinksExists) {
                $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $reportTemplateLinks->templateDetailID)->update(['isFinalLevel' => 0]);
            }

            $result = ChartOfAccountDependency::addChartOfAccountToTemplate($reportTemplateLinks->glAutoID, $reportTemplateLinks->companySystemID, $reportTemplateLinks->templateMasterID, $input['reportTemplateCategory']);

            if (!$result['status']) {
                DB::rollBack();
                return $this->sendError("Error occured while assigned to report template category");
            }

            DB::commit();
            return $this->sendResponse($id, trans('custom.report_template_links_deleted_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage() . " Line" . $exception->getLine(), 500);
        }

    }
}
