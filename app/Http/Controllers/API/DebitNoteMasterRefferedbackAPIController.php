<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDebitNoteMasterRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteMasterRefferedbackAPIRequest;
use App\Models\DebitNoteMasterRefferedback;
use App\Repositories\DebitNoteMasterRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DebitNoteMasterRefferedbackController
 * @package App\Http\Controllers\API
 */

class DebitNoteMasterRefferedbackAPIController extends AppBaseController
{
    /** @var  DebitNoteMasterRefferedbackRepository */
    private $debitNoteMasterRefferedbackRepository;

    public function __construct(DebitNoteMasterRefferedbackRepository $debitNoteMasterRefferedbackRepo)
    {
        $this->debitNoteMasterRefferedbackRepository = $debitNoteMasterRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteMasterRefferedbacks",
     *      summary="Get a listing of the DebitNoteMasterRefferedbacks.",
     *      tags={"DebitNoteMasterRefferedback"},
     *      description="Get all DebitNoteMasterRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/DebitNoteMasterRefferedback")
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
        $this->debitNoteMasterRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->debitNoteMasterRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $debitNoteMasterRefferedbacks = $this->debitNoteMasterRefferedbackRepository->all();

        return $this->sendResponse($debitNoteMasterRefferedbacks->toArray(), trans('custom.debit_note_master_refferedbacks_retrieved_successf'));
    }

    /**
     * @param CreateDebitNoteMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/debitNoteMasterRefferedbacks",
     *      summary="Store a newly created DebitNoteMasterRefferedback in storage",
     *      tags={"DebitNoteMasterRefferedback"},
     *      description="Store DebitNoteMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteMasterRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteMasterRefferedback")
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
     *                  ref="#/definitions/DebitNoteMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDebitNoteMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $debitNoteMasterRefferedbacks = $this->debitNoteMasterRefferedbackRepository->create($input);

        return $this->sendResponse($debitNoteMasterRefferedbacks->toArray(), trans('custom.debit_note_master_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteMasterRefferedbacks/{id}",
     *      summary="Display the specified DebitNoteMasterRefferedback",
     *      tags={"DebitNoteMasterRefferedback"},
     *      description="Get DebitNoteMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteMasterRefferedback",
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
     *                  ref="#/definitions/DebitNoteMasterRefferedback"
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
        /** @var DebitNoteMasterRefferedback $debitNoteMasterRefferedback */
        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->with(['created_by', 'confirmed_by', 'company', 'transactioncurrency','finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'supplier' => function ($query) {
            $query->selectRaw("CONCAT(primarySupplierCode,' | ',supplierName) as supplierName, supplierCodeSystem");
        }])->findWithoutFail($id);

        if (empty($debitNoteMasterRefferedback)) {
            return $this->sendError(trans('custom.debit_note_master_refferedback_not_found'));
        }

        return $this->sendResponse($debitNoteMasterRefferedback->toArray(), trans('custom.debit_note_master_refferedback_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateDebitNoteMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/debitNoteMasterRefferedbacks/{id}",
     *      summary="Update the specified DebitNoteMasterRefferedback in storage",
     *      tags={"DebitNoteMasterRefferedback"},
     *      description="Update DebitNoteMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteMasterRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteMasterRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteMasterRefferedback")
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
     *                  ref="#/definitions/DebitNoteMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDebitNoteMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DebitNoteMasterRefferedback $debitNoteMasterRefferedback */
        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteMasterRefferedback)) {
            return $this->sendError(trans('custom.debit_note_master_refferedback_not_found'));
        }

        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->update($input, $id);

        return $this->sendResponse($debitNoteMasterRefferedback->toArray(), trans('custom.debitnotemasterrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/debitNoteMasterRefferedbacks/{id}",
     *      summary="Remove the specified DebitNoteMasterRefferedback from storage",
     *      tags={"DebitNoteMasterRefferedback"},
     *      description="Delete DebitNoteMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteMasterRefferedback",
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
        /** @var DebitNoteMasterRefferedback $debitNoteMasterRefferedback */
        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteMasterRefferedback)) {
            return $this->sendError(trans('custom.debit_note_master_refferedback_not_found'));
        }

        $debitNoteMasterRefferedback->delete();

        return $this->sendResponse($id, trans('custom.debit_note_master_refferedback_deleted_successfull'));
    }

    public function getDebitNoteAmendHistory(Request $request)
    {
        $input = $request->all();

        $debitNoteAmendHistory = DebitNoteMasterRefferedback::where('debitNoteAutoID', $input['debitNoteAutoID'])
            ->with(['created_by','confirmed_by','modified_by','supplier','approved_by', 'transactioncurrency'])
            ->get();

        return $this->sendResponse($debitNoteAmendHistory, trans('custom.debit_note_detail_retrieved_successfully'));
    }
}
