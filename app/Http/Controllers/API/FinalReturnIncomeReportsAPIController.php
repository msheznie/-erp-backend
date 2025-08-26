<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeReportsAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeReportsAPIRequest;
use App\Models\FinalReturnIncomeReports;
use App\Repositories\FinalReturnIncomeReportsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\FinalReturnIncomeReportDetails;
use App\Models\FinalReturnIncomeTemplate;
use App\Models\FinalReturnIncomeTemplateColumns;
use App\Models\FinalReturnIncomeTemplateDetails;
use App\Models\FinalReturnIncomeReportDetailValues;
use App\Models\SMECompany;
use App\Models\YesNoSelection;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinalReturnIncomeReportsController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeReportsAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeReportsRepository */
    private $finalReturnIncomeReportsRepository;

    public function __construct(FinalReturnIncomeReportsRepository $finalReturnIncomeReportsRepo)
    {
        $this->finalReturnIncomeReportsRepository = $finalReturnIncomeReportsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeReports",
     *      summary="getFinalReturnIncomeReportsList",
     *      tags={"FinalReturnIncomeReports"},
     *      description="Get all FinalReturnIncomeReports",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeReports")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->finalReturnIncomeReportsRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeReportsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalReturnIncomeReports = $this->finalReturnIncomeReportsRepository->all();

        return $this->sendResponse($finalReturnIncomeReports->toArray(), 'Final Return Income Reports retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeReports",
     *      summary="createFinalReturnIncomeReports",
     *      tags={"FinalReturnIncomeReports"},
     *      description="Create FinalReturnIncomeReports",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/FinalReturnIncomeReports"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $details = 0;

        $finalReturnIncomeReports = $this->finalReturnIncomeReportsRepository->create($input);

        $reportID = $finalReturnIncomeReports->id;
        $templateRows = FinalReturnIncomeTemplateDetails::Where('templateMasterID', $input['template_id'])
            ->where('companySystemID', $input['companySystemID'])->get();
        $templateColumns = FinalReturnIncomeTemplateColumns::where('templateMasterID', $input['template_id'])
            ->where('companySystemID', $input['companySystemID'])->get();

        foreach($templateRows as $row) {
           if (is_null($row->masterID)) {
                $data['report_id'] = $reportID;
                $data['template_detail_id'] = $row->id;
                $data['amount'] = 0;

                FinalReturnIncomeReportDetails::create($data);
            }
        }

        return $this->sendResponse($finalReturnIncomeReports->toArray(), 'Final Return Income Reports saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeReports/{id}",
     *      summary="getFinalReturnIncomeReportsItem",
     *      tags={"FinalReturnIncomeReports"},
     *      description="Get FinalReturnIncomeReports",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReports",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/FinalReturnIncomeReports"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var FinalReturnIncomeReports $finalReturnIncomeReports */
        $finalReturnIncomeReports = $this->finalReturnIncomeReportsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReports)) {
            return $this->sendError('Final Return Income Reports not found');
        }

        return $this->sendResponse($finalReturnIncomeReports->toArray(), 'Final Return Income Reports retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeReports/{id}",
     *      summary="updateFinalReturnIncomeReports",
     *      tags={"FinalReturnIncomeReports"},
     *      description="Update FinalReturnIncomeReports",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReports",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/FinalReturnIncomeReports"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeReportsAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalReturnIncomeReports $finalReturnIncomeReports */
        $finalReturnIncomeReports = $this->finalReturnIncomeReportsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReports)) {
            return $this->sendError('Final Return Income Reports not found');
        }

        $finalReturnIncomeReports = $this->finalReturnIncomeReportsRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeReports->toArray(), 'FinalReturnIncomeReports updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeReports/{id}",
     *      summary="deleteFinalReturnIncomeReports",
     *      tags={"FinalReturnIncomeReports"},
     *      description="Delete FinalReturnIncomeReports",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeReports",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var FinalReturnIncomeReports $finalReturnIncomeReports */
        $finalReturnIncomeReports = $this->finalReturnIncomeReportsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeReports)) {
            return $this->sendError('Final Return Income Reports not found');
        }

        $finalReturnIncomeReports->delete();

        return $this->sendResponse($finalReturnIncomeReports, 'Final Return Income Reports deleted successfully');
    }

    public function getReportList(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyID = $input['companyID'];

        $finalReturnIncomeReports = FinalReturnIncomeReports::ofCompany($companyID)->with('finance_year_by');

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $finalReturnIncomeReports = $finalReturnIncomeReports->where(function ($query) use ($search) {
                 $query->where('report_name', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($finalReturnIncomeReports)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->rawColumns(['name','Actions'])
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getFormData(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companySystemID'];

         $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"))
                                                ->where('companySystemID', $companyID)
                                                ->get();

        $templates = FinalReturnIncomeTemplate::OfCompany($companyID)
                                     ->get();

        $data = [
            'companyFinanceYear' => $companyFinanceYear,
            'templates' => $templates
        ];

        return $this->sendResponse($data, "Form data retrived successfully");
    }

    public function checkYearExists(Request $request) {
        $input = $request->all();
        $companyID = $input['companySystemID'];
        $financeYearID = $input['financialyear_id'];

        $exists = FinalReturnIncomeReports::where('companySystemID', $companyID)
                ->where('financialyear_id', $financeYearID)
                ->exists();

        return $this->sendResponse($exists, "");
    }

    public function getIncomeReportDetails($id, Request $request)
    {
        $incomeReportMaster = FinalReturnIncomeReports::where('id', $id)
                                            ->with('finance_year_by', 'template', 'confirmed_by')->first();

        $rowDetails = FinalReturnIncomeReportDetails::where('report_id', $id)
            ->with([
                'template_detail.raws',
                'template_detail.raw_defaults',
                'template_detail.raws.raw_defaults'
            ])
            ->whereHas('template_detail')
            ->with(['template_detail' => function ($q) {
                $q->orderBy('sortOrder', 'asc');
            }])
            ->get();

        $columnDetails = FinalReturnIncomeTemplateColumns::where('templateMasterID', $incomeReportMaster->template_id)
            ->get();

        $company = SMECompany::find($incomeReportMaster->companySystemID);
        $yesNoSelection = YesNoSelection::all();

        $data =[
            'master' => $incomeReportMaster,
            'details' => $rowDetails,
            'columns' => $columnDetails,
            'company' => $company,
            'yesNoSelection' => $yesNoSelection
        ];

        return $this->sendResponse($data, "Income report details retrieved successfully");
    }

    public function confirmReturnIncomeReport(Request $request) {
        $input = $request->master;
       
        $input = array_except($input, ['finance_year_by','template','confirmed_by']);

        if($input['confirmedYN'] == 1) {
            $input['confirmedByEmpSystemID'] = \Helper::getEmployeeSystemID();
            $input['confirmedDate'] = now();
        }

        if($input['submittedYN'] == 1) {
            $input['submittedByEmpSystemID'] = \Helper::getEmployeeSystemID();
            $input['submittedDate'] = now();
        }
        
        $input = $this->convertArrayToValue($input);
        FinalReturnIncomeReports::where('id', $input['id'])
            ->update($input);

        return $this->sendResponse([], "Income report confirmed successfully");
    }

}
