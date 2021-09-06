<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePdcLogAPIRequest;
use App\Http\Requests\API\UpdatePdcLogAPIRequest;
use App\Models\PdcLog;
use App\Repositories\PdcLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;

/**
 * Class PdcLogController
 * @package App\Http\Controllers\API
 */

class PdcLogAPIController extends AppBaseController
{
    /** @var  PdcLogRepository */
    private $pdcLogRepository;

    public function __construct(PdcLogRepository $pdcLogRepo)
    {
        $this->pdcLogRepository = $pdcLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pdcLogs",
     *      summary="Get a listing of the PdcLogs.",
     *      tags={"PdcLog"},
     *      description="Get all PdcLogs",
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
     *                  @SWG\Items(ref="#/definitions/PdcLog")
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
        $this->pdcLogRepository->pushCriteria(new RequestCriteria($request));
        $this->pdcLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pdcLogs = $this->pdcLogRepository->with('currency')->all();

        return $this->sendResponse($pdcLogs->toArray(), 'Pdc Logs retrieved successfully');
    }

    /**
     * @param CreatePdcLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pdcLogs",
     *      summary="Store a newly created PdcLog in storage",
     *      tags={"PdcLog"},
     *      description="Store PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PdcLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PdcLog")
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
     *                  ref="#/definitions/PdcLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePdcLogAPIRequest $request)
    {
        $input = $request->all();

        $pdcLog = $this->pdcLogRepository->create($input);

        return $this->sendResponse($pdcLog->toArray(), 'Pdc Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pdcLogs/{id}",
     *      summary="Display the specified PdcLog",
     *      tags={"PdcLog"},
     *      description="Get PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PdcLog",
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
     *                  ref="#/definitions/PdcLog"
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
        /** @var PdcLog $pdcLog */
        $pdcLog = $this->pdcLogRepository->findWithoutFail($id);

        if (empty($pdcLog)) {
            return $this->sendError('Pdc Log not found');
        }

        return $this->sendResponse($pdcLog->toArray(), 'Pdc Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePdcLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pdcLogs/{id}",
     *      summary="Update the specified PdcLog in storage",
     *      tags={"PdcLog"},
     *      description="Update PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PdcLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PdcLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PdcLog")
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
     *                  ref="#/definitions/PdcLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePdcLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var PdcLog $pdcLog */
        $pdcLog = $this->pdcLogRepository->findWithoutFail($id);

        if (empty($pdcLog)) {
            return $this->sendError('Pdc Log not found');
        }

        $pdcLog = $this->pdcLogRepository->update($input, $id);

        return $this->sendResponse($pdcLog->toArray(), 'PdcLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pdcLogs/{id}",
     *      summary="Remove the specified PdcLog from storage",
     *      tags={"PdcLog"},
     *      description="Delete PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PdcLog",
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
        /** @var PdcLog $pdcLog */
        $pdcLog = $this->pdcLogRepository->findWithoutFail($id);

        if (empty($pdcLog)) {
            return $this->sendError('Pdc Log not found');
        }

        $pdcLog->delete();

        return $this->sendSuccess('Pdc Log deleted successfully');
    }

    public function getIssuedAndReceivedCheques(Request $request) {


        $input = $request;
        $fromDate = Carbon::parse(trim($input['fromDate'],'"'));
        $toDate = Carbon::parse(trim($input['toDate'],'"'));
        $bank = $input['bank'];


        $receivedCheques = PdcLog::whereBetween('chequeDate',[$fromDate,$toDate])->where('paymentBankID',$bank)->where('documentSystemID',21)->with('currency')->get();

        $issuedCheques = PdcLog::whereBetween('chequeDate',[$fromDate,$toDate])->where('paymentBankID',$bank)->where('documentSystemID',4)->with('currency')->get();

        $data = [
            "receivedCheques" => $receivedCheques,
            "issuedCheques"   => $issuedCheques
        ];
        
        return $this->sendResponse($data, 'Data received successfully');

    }

    public function getAllBanks(Request $request) {
        $pdcLogs =  PdcLog::all()->pluck('bank')->unique();

        return $pdcLogs;
    }
}
