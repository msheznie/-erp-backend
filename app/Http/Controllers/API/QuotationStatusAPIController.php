<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationStatusAPIRequest;
use App\Http\Requests\API\UpdateQuotationStatusAPIRequest;
use App\Models\QuotationStatus;
use App\Models\QuotationMaster;
use App\Repositories\QuotationStatusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;

/**
 * Class QuotationStatusController
 * @package App\Http\Controllers\API
 */

class QuotationStatusAPIController extends AppBaseController
{
    /** @var  QuotationStatusRepository */
    private $quotationStatusRepository;

    public function __construct(QuotationStatusRepository $quotationStatusRepo)
    {
        $this->quotationStatusRepository = $quotationStatusRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationStatuses",
     *      summary="Get a listing of the QuotationStatuses.",
     *      tags={"QuotationStatus"},
     *      description="Get all QuotationStatuses",
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
     *                  @SWG\Items(ref="#/definitions/QuotationStatus")
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
        $this->quotationStatusRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationStatuses = $this->quotationStatusRepository->all();

        return $this->sendResponse($quotationStatuses->toArray(), 'Quotation Statuses retrieved successfully');
    }

    /**
     * @param CreateQuotationStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationStatuses",
     *      summary="Store a newly created QuotationStatus in storage",
     *      tags={"QuotationStatus"},
     *      description="Store QuotationStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationStatus that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationStatus")
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
     *                  ref="#/definitions/QuotationStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationStatusAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $quotationID = $input['quotationID'];

        $quotationMasterData = QuotationMaster::find($quotationID);
        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation not found');
        }

        if (isset($input['quotationStatusDate']) && $input['quotationStatusDate']) {
            $input['quotationStatusDate'] = new Carbon($input['quotationStatusDate']);
        }

        $input['companySystemID'] = $quotationMasterData->companySystemID;
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

        $quotationStatus = $this->quotationStatusRepository->create($input);

        return $this->sendResponse($quotationStatus->toArray(), 'Quotation Status saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationStatuses/{id}",
     *      summary="Display the specified QuotationStatus",
     *      tags={"QuotationStatus"},
     *      description="Get QuotationStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationStatus",
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
     *                  ref="#/definitions/QuotationStatus"
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
        /** @var QuotationStatus $quotationStatus */
        $quotationStatus = $this->quotationStatusRepository->findWithoutFail($id);

        if (empty($quotationStatus)) {
            return $this->sendError('Quotation Status not found');
        }

        return $this->sendResponse($quotationStatus->toArray(), 'Quotation Status retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationStatuses/{id}",
     *      summary="Update the specified QuotationStatus in storage",
     *      tags={"QuotationStatus"},
     *      description="Update QuotationStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationStatus",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationStatus that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationStatus")
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
     *                  ref="#/definitions/QuotationStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationStatusAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['modified_by']);
        $input = $this->convertArrayToValue($input);
        
        /** @var QuotationStatus $quotationStatus */
        $quotationStatus = $this->quotationStatusRepository->findWithoutFail($id);

        if (empty($quotationStatus)) {
            return $this->sendError('Quotation Status not found');
        }

        $quotationMasterData = QuotationMaster::find($input['quotationID']);
        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation not found');
        }

        if (isset($input['quotationStatusDate']) && $input['quotationStatusDate']) {
            $input['quotationStatusDate'] = new Carbon($input['quotationStatusDate']);
        }

        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

        $quotationStatus = $this->quotationStatusRepository->update($input, $id);

        return $this->sendResponse($quotationStatus->toArray(), 'Quotation Status updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationStatuses/{id}",
     *      summary="Remove the specified QuotationStatus from storage",
     *      tags={"QuotationStatus"},
     *      description="Delete QuotationStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationStatus",
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
        /** @var QuotationStatus $quotationStatus */
        $quotationStatus = $this->quotationStatusRepository->findWithoutFail($id);

        if (empty($quotationStatus)) {
            return $this->sendError('Quotation Status not found');
        }

        $quotationStatus->delete();

        return $this->sendResponse($id, 'Quotation Status deleted successfully');
    }

    public function getQuotationStatus(Request $request)
    {
        $input = $request->all();
        $quotationID = $input['quotationID'];

        $items = QuotationStatus::where('quotationID', $quotationID)
                                ->with(['modified_by'])
                                ->get();

        return $this->sendResponse($items->toArray(), 'Status details retrieved successfully');
    }
}
