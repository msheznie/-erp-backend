<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrpErpPayShiftEmployeesAPIRequest;
use App\Http\Requests\API\UpdateSrpErpPayShiftEmployeesAPIRequest;
use App\Models\SrpErpPayShiftEmployees;
use App\Repositories\SrpErpPayShiftEmployeesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrpErpPayShiftEmployeesController
 * @package App\Http\Controllers\API
 */

class SrpErpPayShiftEmployeesAPIController extends AppBaseController
{
    /** @var  SrpErpPayShiftEmployeesRepository */
    private $srpErpPayShiftEmployeesRepository;

    public function __construct(SrpErpPayShiftEmployeesRepository $srpErpPayShiftEmployeesRepo)
    {
        $this->srpErpPayShiftEmployeesRepository = $srpErpPayShiftEmployeesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpPayShiftEmployees",
     *      summary="Get a listing of the SrpErpPayShiftEmployees.",
     *      tags={"SrpErpPayShiftEmployees"},
     *      description="Get all SrpErpPayShiftEmployees",
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
     *                  @SWG\Items(ref="#/definitions/SrpErpPayShiftEmployees")
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
        $this->srpErpPayShiftEmployeesRepository->pushCriteria(new RequestCriteria($request));
        $this->srpErpPayShiftEmployeesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepository->all();

        return $this->sendResponse($srpErpPayShiftEmployees->toArray(), trans('custom.srp_erp_pay_shift_employees_retrieved_successfully'));
    }

    /**
     * @param CreateSrpErpPayShiftEmployeesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/srpErpPayShiftEmployees",
     *      summary="Store a newly created SrpErpPayShiftEmployees in storage",
     *      tags={"SrpErpPayShiftEmployees"},
     *      description="Store SrpErpPayShiftEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpPayShiftEmployees that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpPayShiftEmployees")
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
     *                  ref="#/definitions/SrpErpPayShiftEmployees"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrpErpPayShiftEmployeesAPIRequest $request)
    {
        $input = $request->all();

        $srpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepository->create($input);

        return $this->sendResponse($srpErpPayShiftEmployees->toArray(), trans('custom.srp_erp_pay_shift_employees_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpPayShiftEmployees/{id}",
     *      summary="Display the specified SrpErpPayShiftEmployees",
     *      tags={"SrpErpPayShiftEmployees"},
     *      description="Get SrpErpPayShiftEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpPayShiftEmployees",
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
     *                  ref="#/definitions/SrpErpPayShiftEmployees"
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
        /** @var SrpErpPayShiftEmployees $srpErpPayShiftEmployees */
        $srpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepository->findWithoutFail($id);

        if (empty($srpErpPayShiftEmployees)) {
            return $this->sendError(trans('custom.srp_erp_pay_shift_employees_not_found'));
        }

        return $this->sendResponse($srpErpPayShiftEmployees->toArray(), trans('custom.srp_erp_pay_shift_employees_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSrpErpPayShiftEmployeesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/srpErpPayShiftEmployees/{id}",
     *      summary="Update the specified SrpErpPayShiftEmployees in storage",
     *      tags={"SrpErpPayShiftEmployees"},
     *      description="Update SrpErpPayShiftEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpPayShiftEmployees",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpPayShiftEmployees that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpPayShiftEmployees")
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
     *                  ref="#/definitions/SrpErpPayShiftEmployees"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrpErpPayShiftEmployeesAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrpErpPayShiftEmployees $srpErpPayShiftEmployees */
        $srpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepository->findWithoutFail($id);

        if (empty($srpErpPayShiftEmployees)) {
            return $this->sendError(trans('custom.srp_erp_pay_shift_employees_not_found'));
        }

        $srpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepository->update($input, $id);

        return $this->sendResponse($srpErpPayShiftEmployees->toArray(), trans('custom.srperppayshiftemployees_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/srpErpPayShiftEmployees/{id}",
     *      summary="Remove the specified SrpErpPayShiftEmployees from storage",
     *      tags={"SrpErpPayShiftEmployees"},
     *      description="Delete SrpErpPayShiftEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpPayShiftEmployees",
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
        /** @var SrpErpPayShiftEmployees $srpErpPayShiftEmployees */
        $srpErpPayShiftEmployees = $this->srpErpPayShiftEmployeesRepository->findWithoutFail($id);

        if (empty($srpErpPayShiftEmployees)) {
            return $this->sendError(trans('custom.srp_erp_pay_shift_employees_not_found'));
        }

        $srpErpPayShiftEmployees->delete();

        return $this->sendSuccess('Srp Erp Pay Shift Employees deleted successfully');
    }
}
