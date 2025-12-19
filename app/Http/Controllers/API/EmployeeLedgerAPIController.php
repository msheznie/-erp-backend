<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeLedgerAPIRequest;
use App\Http\Requests\API\UpdateEmployeeLedgerAPIRequest;
use App\Models\EmployeeLedger;
use App\Repositories\EmployeeLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeeLedgerController
 * @package App\Http\Controllers\API
 */

class EmployeeLedgerAPIController extends AppBaseController
{
    /** @var  EmployeeLedgerRepository */
    private $employeeLedgerRepository;

    public function __construct(EmployeeLedgerRepository $employeeLedgerRepo)
    {
        $this->employeeLedgerRepository = $employeeLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeLedgers",
     *      summary="Get a listing of the EmployeeLedgers.",
     *      tags={"EmployeeLedger"},
     *      description="Get all EmployeeLedgers",
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
     *                  @SWG\Items(ref="#/definitions/EmployeeLedger")
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
        $this->employeeLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeLedgers = $this->employeeLedgerRepository->all();

        return $this->sendResponse($employeeLedgers->toArray(), trans('custom.employee_ledgers_retrieved_successfully'));
    }

    /**
     * @param CreateEmployeeLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employeeLedgers",
     *      summary="Store a newly created EmployeeLedger in storage",
     *      tags={"EmployeeLedger"},
     *      description="Store EmployeeLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeLedger")
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
     *                  ref="#/definitions/EmployeeLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEmployeeLedgerAPIRequest $request)
    {
        $input = $request->all();

        $employeeLedger = $this->employeeLedgerRepository->create($input);

        return $this->sendResponse($employeeLedger->toArray(), trans('custom.employee_ledger_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeLedgers/{id}",
     *      summary="Display the specified EmployeeLedger",
     *      tags={"EmployeeLedger"},
     *      description="Get EmployeeLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeLedger",
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
     *                  ref="#/definitions/EmployeeLedger"
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
        /** @var EmployeeLedger $employeeLedger */
        $employeeLedger = $this->employeeLedgerRepository->findWithoutFail($id);

        if (empty($employeeLedger)) {
            return $this->sendError(trans('custom.employee_ledger_not_found'));
        }

        return $this->sendResponse($employeeLedger->toArray(), trans('custom.employee_ledger_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEmployeeLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employeeLedgers/{id}",
     *      summary="Update the specified EmployeeLedger in storage",
     *      tags={"EmployeeLedger"},
     *      description="Update EmployeeLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeLedger")
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
     *                  ref="#/definitions/EmployeeLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEmployeeLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeeLedger $employeeLedger */
        $employeeLedger = $this->employeeLedgerRepository->findWithoutFail($id);

        if (empty($employeeLedger)) {
            return $this->sendError(trans('custom.employee_ledger_not_found'));
        }

        $employeeLedger = $this->employeeLedgerRepository->update($input, $id);

        return $this->sendResponse($employeeLedger->toArray(), trans('custom.employeeledger_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employeeLedgers/{id}",
     *      summary="Remove the specified EmployeeLedger from storage",
     *      tags={"EmployeeLedger"},
     *      description="Delete EmployeeLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeLedger",
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
        /** @var EmployeeLedger $employeeLedger */
        $employeeLedger = $this->employeeLedgerRepository->findWithoutFail($id);

        if (empty($employeeLedger)) {
            return $this->sendError(trans('custom.employee_ledger_not_found'));
        }

        $employeeLedger->delete();

        return $this->sendSuccess('Employee Ledger deleted successfully');
    }
}
