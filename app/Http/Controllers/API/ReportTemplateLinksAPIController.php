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
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateLinks;
use App\Repositories\ReportTemplateLinksRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

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

        return $this->sendResponse($reportTemplateLinks->toArray(), 'Report Template Links retrieved successfully');
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

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $tempDetail = ReportTemplateLinks::ofTemplate($input['templateMasterID'])->pluck('glAutoID')->toArray();

        if ($input['glAutoID']) {
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

        return $this->sendResponse([], 'Report Template Links saved successfully');
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
            return $this->sendError('Report Template Links not found');
        }

        return $this->sendResponse($reportTemplateLinks->toArray(), 'Report Template Links retrieved successfully');
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
            return $this->sendError('Report Template Links not found');
        }

        $reportTemplateLinks = $this->reportTemplateLinksRepository->update($input, $id);

        return $this->sendResponse($reportTemplateLinks->toArray(), 'ReportTemplateLinks updated successfully');
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
            return $this->sendError('Report Template Links not found');
        }

        $reportTemplateLinks->delete();

        return $this->sendResponse($id, 'Report Template Links deleted successfully');
    }

    public function reportTemplateDetailSubCatLink(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'subCategory' => 'required'
        ]);

        if ($validator->fails()) {//echo 'in';exit;
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
        return $this->sendResponse([], 'Report Template Links saved successfully');
    }


}
