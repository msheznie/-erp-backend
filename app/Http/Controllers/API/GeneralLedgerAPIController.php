<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGeneralLedgerAPIRequest;
use App\Http\Requests\API\UpdateGeneralLedgerAPIRequest;
use App\Models\GeneralLedger;
use App\Repositories\GeneralLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GeneralLedgerController
 * @package App\Http\Controllers\API
 */

class GeneralLedgerAPIController extends AppBaseController
{
    /** @var  GeneralLedgerRepository */
    private $generalLedgerRepository;

    public function __construct(GeneralLedgerRepository $generalLedgerRepo)
    {
        $this->generalLedgerRepository = $generalLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/generalLedgers",
     *      summary="Get a listing of the GeneralLedgers.",
     *      tags={"GeneralLedger"},
     *      description="Get all GeneralLedgers",
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
     *                  @SWG\Items(ref="#/definitions/GeneralLedger")
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
        $this->generalLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->generalLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $generalLedgers = $this->generalLedgerRepository->all();

        return $this->sendResponse($generalLedgers->toArray(), 'General Ledgers retrieved successfully');
    }

    /**
     * @param CreateGeneralLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/generalLedgers",
     *      summary="Store a newly created GeneralLedger in storage",
     *      tags={"GeneralLedger"},
     *      description="Store GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GeneralLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GeneralLedger")
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
     *                  ref="#/definitions/GeneralLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGeneralLedgerAPIRequest $request)
    {
        $input = $request->all();

        $generalLedgers = $this->generalLedgerRepository->create($input);

        return $this->sendResponse($generalLedgers->toArray(), 'General Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/generalLedgers/{id}",
     *      summary="Display the specified GeneralLedger",
     *      tags={"GeneralLedger"},
     *      description="Get GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GeneralLedger",
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
     *                  ref="#/definitions/GeneralLedger"
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
        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            return $this->sendError('General Ledger not found');
        }

        return $this->sendResponse($generalLedger->toArray(), 'General Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateGeneralLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/generalLedgers/{id}",
     *      summary="Update the specified GeneralLedger in storage",
     *      tags={"GeneralLedger"},
     *      description="Update GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GeneralLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GeneralLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GeneralLedger")
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
     *                  ref="#/definitions/GeneralLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGeneralLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            return $this->sendError('General Ledger not found');
        }

        $generalLedger = $this->generalLedgerRepository->update($input, $id);

        return $this->sendResponse($generalLedger->toArray(), 'GeneralLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/generalLedgers/{id}",
     *      summary="Remove the specified GeneralLedger from storage",
     *      tags={"GeneralLedger"},
     *      description="Delete GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GeneralLedger",
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
        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            return $this->sendError('General Ledger not found');
        }

        $generalLedger->delete();

        return $this->sendResponse($id, 'General Ledger deleted successfully');
    }
}
