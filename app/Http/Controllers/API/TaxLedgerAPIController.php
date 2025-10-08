<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxLedgerAPIRequest;
use App\Http\Requests\API\UpdateTaxLedgerAPIRequest;
use App\Models\BookInvSuppMaster;
use App\Models\TaxLedger;
use App\helper\CommonJobService;
use App\Models\TaxLedgerDetail;
use App\Repositories\TaxLedgerRepository;
use App\Services\GeneralLedger\SupplierInvoiceGlService;
use App\Services\TaxLedgerService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxLedgerController
 * @package App\Http\Controllers\API
 */

class TaxLedgerAPIController extends AppBaseController
{
    /** @var  TaxLedgerRepository */
    private $taxLedgerRepository;

    public function __construct(TaxLedgerRepository $taxLedgerRepo)
    {
        $this->taxLedgerRepository = $taxLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxLedgers",
     *      summary="Get a listing of the TaxLedgers.",
     *      tags={"TaxLedger"},
     *      description="Get all TaxLedgers",
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
     *                  @SWG\Items(ref="#/definitions/TaxLedger")
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
        $this->taxLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->taxLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxLedgers = $this->taxLedgerRepository->all();

        return $this->sendResponse($taxLedgers->toArray(), trans('custom.tax_ledgers_retrieved_successfully'));
    }

    /**
     * @param CreateTaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxLedgers",
     *      summary="Store a newly created TaxLedger in storage",
     *      tags={"TaxLedger"},
     *      description="Store TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxLedger")
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
     *                  ref="#/definitions/TaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        $taxLedger = $this->taxLedgerRepository->create($input);

        return $this->sendResponse($taxLedger->toArray(), trans('custom.tax_ledger_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxLedgers/{id}",
     *      summary="Display the specified TaxLedger",
     *      tags={"TaxLedger"},
     *      description="Get TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedger",
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
     *                  ref="#/definitions/TaxLedger"
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
        /** @var TaxLedger $taxLedger */
        $taxLedger = $this->taxLedgerRepository->findWithoutFail($id);

        if (empty($taxLedger)) {
            return $this->sendError(trans('custom.tax_ledger_not_found'));
        }

        return $this->sendResponse($taxLedger->toArray(), trans('custom.tax_ledger_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTaxLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxLedgers/{id}",
     *      summary="Update the specified TaxLedger in storage",
     *      tags={"TaxLedger"},
     *      description="Update TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxLedger")
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
     *                  ref="#/definitions/TaxLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxLedger $taxLedger */
        $taxLedger = $this->taxLedgerRepository->findWithoutFail($id);

        if (empty($taxLedger)) {
            return $this->sendError(trans('custom.tax_ledger_not_found'));
        }

        $taxLedger = $this->taxLedgerRepository->update($input, $id);

        return $this->sendResponse($taxLedger->toArray(), trans('custom.taxledger_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxLedgers/{id}",
     *      summary="Remove the specified TaxLedger from storage",
     *      tags={"TaxLedger"},
     *      description="Delete TaxLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxLedger",
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
        /** @var TaxLedger $taxLedger */
        $taxLedger = $this->taxLedgerRepository->findWithoutFail($id);

        if (empty($taxLedger)) {
            return $this->sendError(trans('custom.tax_ledger_not_found'));
        }

        $taxLedger->delete();

        return $this->sendSuccess('Tax Ledger deleted successfully');
    }


    public function updateTaxLedgerForSupplierInvoice()
    {

        $tenants = CommonJobService::tenant_list();

        if (count($tenants) == 0) {
            return "tenant list is empty";
        }

        foreach ($tenants as $tenant) {
            $tenantDb = $tenant->database;

            CommonJobService::db_switch($tenantDb);

            $documents = BookInvSuppMaster::whereIn('documentType', [3, 4])->where('approved', -1)->get();

            foreach ($documents as $document) {

                $missingInTaxLedger = TaxLedger::where('documentMasterAutoID', $document->bookingSuppMasInvAutoID)->where('documentSystemID', 11)->first();
                $missingInTaxLedgerDetail = TaxLedgerDetail::where('documentMasterAutoID', $document->bookingSuppMasInvAutoID)->where('documentSystemID', 11)->first();

                if(!($missingInTaxLedger && $missingInTaxLedgerDetail)){
                    if (!is_null($document->bookingSuppMasInvAutoID) && !is_null($document->companySystemID) && !is_null($document->approvedByUserSystemID)) {
                        $masterModel = ['documentSystemID' => 11, 'autoID' => $document->bookingSuppMasInvAutoID, 'companySystemID' => $document->companySystemID, 'employeeSystemID' => $document->approvedByUserSystemID];

                        $result = SupplierInvoiceGlService::processEntry($masterModel);

                        if ($result['status'] && isset($result['data']['taxLedgerData'])) {
                            TaxLedgerService::postLedgerEntry($result['data']['taxLedgerData'], $masterModel);
                        }
                    }
                }
            }
        }
        return $this->sendResponse([], trans('custom.tax_ledger_updated_successfully'));
    }
}
