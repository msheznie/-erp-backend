<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCashFlowReportAPIRequest;
use App\Http\Requests\API\UpdateCashFlowReportAPIRequest;
use App\Models\CashFlowReport;
use App\Models\CashFlowReportDetail;
use App\Models\CashFlowTemplateDetail;
use App\Models\CompanyFinanceYear;
use App\Models\CashFlowTemplate;
use App\Repositories\CashFlowReportRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class CashFlowReportController
 * @package App\Http\Controllers\API
 */

class CashFlowReportAPIController extends AppBaseController
{
    /** @var  CashFlowReportRepository */
    private $cashFlowReportRepository;

    public function __construct(CashFlowReportRepository $cashFlowReportRepo)
    {
        $this->cashFlowReportRepository = $cashFlowReportRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowReports",
     *      summary="Get a listing of the CashFlowReports.",
     *      tags={"CashFlowReport"},
     *      description="Get all CashFlowReports",
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
     *                  @SWG\Items(ref="#/definitions/CashFlowReport")
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
        $this->cashFlowReportRepository->pushCriteria(new RequestCriteria($request));
        $this->cashFlowReportRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cashFlowReports = $this->cashFlowReportRepository->all();

        return $this->sendResponse($cashFlowReports->toArray(), 'Cash Flow Reports retrieved successfully');
    }

    /**
     * @param CreateCashFlowReportAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cashFlowReports",
     *      summary="Store a newly created CashFlowReport in storage",
     *      tags={"CashFlowReport"},
     *      description="Store CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowReport that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowReport")
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
     *                  ref="#/definitions/CashFlowReport"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCashFlowReportAPIRequest $request)
    {
        $input = $request->all();
        $input['createdPCID'] = gethostname();
        $input['date'] = Carbon::parse($input['date']);
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $cashFlowReport = $this->cashFlowReportRepository->create($input);

        $cashFlowReportID = $cashFlowReport->id;

        $cashFlowTemplateData = CashFlowTemplateDetail::where('cashFlowTemplateID', $input['cashFlowTemplateID'])->get();

        foreach ($cashFlowTemplateData as $key => $value) {
            $data['cashFlowTemplateDetailID'] = $value->id;
            $data['cashFlowReportID'] = $cashFlowReportID;

            //logic need to caluate using seperate function with switch case
            $data['amount'] = 0;

            CashFlowReportDetail::create($data);
        }        

        return $this->sendResponse($cashFlowReport->toArray(), 'Cash Flow Report saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowReports/{id}",
     *      summary="Display the specified CashFlowReport",
     *      tags={"CashFlowReport"},
     *      description="Get CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReport",
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
     *                  ref="#/definitions/CashFlowReport"
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
        /** @var CashFlowReport $cashFlowReport */
        $cashFlowReport = $this->cashFlowReportRepository->findWithoutFail($id);

        if (empty($cashFlowReport)) {
            return $this->sendError('Cash Flow Report not found');
        }

        return $this->sendResponse($cashFlowReport->toArray(), 'Cash Flow Report retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCashFlowReportAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cashFlowReports/{id}",
     *      summary="Update the specified CashFlowReport in storage",
     *      tags={"CashFlowReport"},
     *      description="Update CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReport",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowReport that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowReport")
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
     *                  ref="#/definitions/CashFlowReport"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCashFlowReportAPIRequest $request)
    {
        $input = $request->all();

        /** @var CashFlowReport $cashFlowReport */
        $cashFlowReport = $this->cashFlowReportRepository->findWithoutFail($id);

        if (empty($cashFlowReport)) {
            return $this->sendError('Cash Flow Report not found');
        }

        $cashFlowReport = $this->cashFlowReportRepository->update($input, $id);

        return $this->sendResponse($cashFlowReport->toArray(), 'CashFlowReport updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cashFlowReports/{id}",
     *      summary="Remove the specified CashFlowReport from storage",
     *      tags={"CashFlowReport"},
     *      description="Delete CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReport",
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
        /** @var CashFlowReport $cashFlowReport */
        $cashFlowReport = $this->cashFlowReportRepository->findWithoutFail($id);

        if (empty($cashFlowReport)) {
            return $this->sendError('Cash Flow Report not found');
        }

        $cashFlowReport->delete();

        return $this->sendSuccess('Cash Flow Report deleted successfully');
    }

    public function getCashFlowFormData(Request $request)
    {
        $input = $request->all();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"))
                                                ->where('companySystemID', $input['companySystemID'])
                                                ->get();

        $templates = CashFlowTemplate::OfCompany($input['companySystemID'])
                                     ->get();

        $data = [
            'companyFinanceYear' => $companyFinanceYear,
            'templates' => $templates
        ];

        return $this->sendResponse($data, "Form data retrived successfully");
    }


    public function getCashFlowReports(Request $request)
    {

        $input = $request->all();

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

        $results = CashFlowReport::with(['finance_year_by'])
                                 ->whereIn('companySystemID', $subCompanies);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $results = $results->where(function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($results)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
