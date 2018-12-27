<?php
/**
 * =============================================
 * -- File Name : ReportTemplateDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report Template
 * -- Author : Mubashir
 * -- Create date : 20 - December 2018
 * -- Description :  This file contains the all CRUD for Report template detail
 * -- REVISION HISTORY
 * -- Date: 20 - December 2018 By: Mubashir Description: Added new functions named as getReportTemplateDetail()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateDetailsAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateDetailsAPIRequest;
use App\Models\Company;
use App\Models\ReportTemplateColumnLink;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateLinks;
use App\Repositories\ReportTemplateDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateDetailsController
 * @package App\Http\Controllers\API
 */
class ReportTemplateDetailsAPIController extends AppBaseController
{
    /** @var  ReportTemplateDetailsRepository */
    private $reportTemplateDetailsRepository;

    public function __construct(ReportTemplateDetailsRepository $reportTemplateDetailsRepo)
    {
        $this->reportTemplateDetailsRepository = $reportTemplateDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateDetails",
     *      summary="Get a listing of the ReportTemplateDetails.",
     *      tags={"ReportTemplateDetails"},
     *      description="Get all ReportTemplateDetails",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateDetails")
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
        $this->reportTemplateDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->all();

        return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details retrieved successfully');
    }

    /**
     * @param CreateReportTemplateDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateDetails",
     *      summary="Store a newly created ReportTemplateDetails in storage",
     *      tags={"ReportTemplateDetails"},
     *      description="Store ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateDetails")
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
     *                  ref="#/definitions/ReportTemplateDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'description' => 'required',
                'itemType' => 'required',
                'sortOrder' => 'required',
            ]);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails = $this->reportTemplateDetailsRepository->create($input);
            DB::commit();
            return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateDetails/{id}",
     *      summary="Display the specified ReportTemplateDetails",
     *      tags={"ReportTemplateDetails"},
     *      description="Get ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDetails",
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
     *                  ref="#/definitions/ReportTemplateDetails"
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
        /** @var ReportTemplateDetails $reportTemplateDetails */
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            return $this->sendError('Report Template Details not found');
        }

        return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateDetails/{id}",
     *      summary="Update the specified ReportTemplateDetails in storage",
     *      tags={"ReportTemplateDetails"},
     *      description="Update ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateDetails")
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
     *                  ref="#/definitions/ReportTemplateDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['subcategory', 'gllink', 'Actions', 'DT_Row_Index']);
        $input = $this->convertArrayToValue($input);

        /** @var ReportTemplateDetails $reportTemplateDetails */
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            return $this->sendError('Report Template Details not found');
        }

        $reportTemplateDetails = $this->reportTemplateDetailsRepository->update($input, $id);

        return $this->sendResponse($reportTemplateDetails->toArray(), 'ReportTemplateDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateDetails/{id}",
     *      summary="Remove the specified ReportTemplateDetails from storage",
     *      tags={"ReportTemplateDetails"},
     *      description="Delete ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDetails",
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
        DB::beginTransaction();
        try {
            /** @var ReportTemplateDetails $reportTemplateDetails */
            $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);
            if (empty($reportTemplateDetails)) {
                return $this->sendError('Report Template Details not found');
            }

            $detID = $reportTemplateDetails->subcategory()->pluck('detID');
            $reportTemplateDetails->subcategory()->delete();
            $reportTemplateDetails->gllink()->delete();
            if($detID){
                $glLink = ReportTemplateLinks::whereIN('templateDetailID',$detID)->delete();
            }
            $reportTemplateDetails->delete();
            DB::commit();
            return $this->sendResponse($id, 'Report Template Details deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getReportTemplateDetail($id, Request $request)
    {
        $reportTemplateDetails = ReportTemplateDetails::with(['subcategory' => function ($q) {
            $q->with(['gllink' => function ($q) {
                $q->with('subcategory');
                $q->orderBy('sortOrder', 'asc');
            }]);
            $q->orderBy('sortOrder', 'asc');
        }])->OfMaster($id)->whereNull('masterID')->orderBy('sortOrder')->get();

        $reportTemplateColLink = ReportTemplateColumnLink::ofTemplate($id)->get();

        $output = ['template'=> $reportTemplateDetails->toArray(),'columns' => $reportTemplateColLink->toArray()];

        return $this->sendResponse($output, 'Report Template Details retrieved successfully');
    }


    public function getReportTemplateSubCat(Request $request)
    {
        $reportTemplateDetails = ReportTemplateDetails::where('masterID',$request->masterID)->where('detID','<',$request->detID)->orderBy('sortOrder')->get();

        return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details retrieved successfully');
    }

}
