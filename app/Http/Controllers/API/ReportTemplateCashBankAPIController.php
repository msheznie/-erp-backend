<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateCashBankAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateCashBankAPIRequest;
use App\Models\ReportTemplateCashBank;
use App\Repositories\ReportTemplateCashBankRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReportTemplateCashBankController
 * @package App\Http\Controllers\API
 */

class ReportTemplateCashBankAPIController extends AppBaseController
{
    /** @var  ReportTemplateCashBankRepository */
    private $reportTemplateCashBankRepository;

    public function __construct(ReportTemplateCashBankRepository $reportTemplateCashBankRepo)
    {
        $this->reportTemplateCashBankRepository = $reportTemplateCashBankRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateCashBanks",
     *      summary="Get a listing of the ReportTemplateCashBanks.",
     *      tags={"ReportTemplateCashBank"},
     *      description="Get all ReportTemplateCashBanks",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateCashBank")
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
        $this->reportTemplateCashBankRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateCashBankRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateCashBanks = $this->reportTemplateCashBankRepository->all();

        return $this->sendResponse($reportTemplateCashBanks->toArray(), trans('custom.report_template_cash_banks_retrieved_successfully'));
    }

    /**
     * @param CreateReportTemplateCashBankAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateCashBanks",
     *      summary="Store a newly created ReportTemplateCashBank in storage",
     *      tags={"ReportTemplateCashBank"},
     *      description="Store ReportTemplateCashBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateCashBank that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateCashBank")
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
     *                  ref="#/definitions/ReportTemplateCashBank"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateCashBankAPIRequest $request)
    {
        $input = $request->all();

        $reportTemplateCashBanks = $this->reportTemplateCashBankRepository->create($input);

        return $this->sendResponse($reportTemplateCashBanks->toArray(), trans('custom.report_template_cash_bank_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateCashBanks/{id}",
     *      summary="Display the specified ReportTemplateCashBank",
     *      tags={"ReportTemplateCashBank"},
     *      description="Get ReportTemplateCashBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateCashBank",
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
     *                  ref="#/definitions/ReportTemplateCashBank"
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
        /** @var ReportTemplateCashBank $reportTemplateCashBank */
        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->findWithoutFail($id);

        if (empty($reportTemplateCashBank)) {
            return $this->sendError(trans('custom.report_template_cash_bank_not_found'));
        }

        return $this->sendResponse($reportTemplateCashBank->toArray(), trans('custom.report_template_cash_bank_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateCashBankAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateCashBanks/{id}",
     *      summary="Update the specified ReportTemplateCashBank in storage",
     *      tags={"ReportTemplateCashBank"},
     *      description="Update ReportTemplateCashBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateCashBank",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateCashBank that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateCashBank")
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
     *                  ref="#/definitions/ReportTemplateCashBank"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateCashBankAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReportTemplateCashBank $reportTemplateCashBank */
        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->findWithoutFail($id);

        if (empty($reportTemplateCashBank)) {
            return $this->sendError(trans('custom.report_template_cash_bank_not_found'));
        }

        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->update($input, $id);

        return $this->sendResponse($reportTemplateCashBank->toArray(), trans('custom.reporttemplatecashbank_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateCashBanks/{id}",
     *      summary="Remove the specified ReportTemplateCashBank from storage",
     *      tags={"ReportTemplateCashBank"},
     *      description="Delete ReportTemplateCashBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateCashBank",
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
        /** @var ReportTemplateCashBank $reportTemplateCashBank */
        $reportTemplateCashBank = $this->reportTemplateCashBankRepository->findWithoutFail($id);

        if (empty($reportTemplateCashBank)) {
            return $this->sendError(trans('custom.report_template_cash_bank_not_found'));
        }

        $reportTemplateCashBank->delete();

        return $this->sendResponse($id, trans('custom.report_template_cash_bank_deleted_successfully'));
    }
}
