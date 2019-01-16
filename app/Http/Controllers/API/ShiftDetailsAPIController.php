<?php
/**
 * =============================================
 * -- File Name : ShiftDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Shift Details
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - January 2019
 * -- Description : This file contains the all CRUD for Shift Details
 * -- REVISION HISTORY
 * -- Date: 14-January 2018 By: Fayas Description: Added new functions named as getPosShiftDetails()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateShiftDetailsAPIRequest;
use App\Http\Requests\API\UpdateShiftDetailsAPIRequest;
use App\Models\Company;
use App\Models\Counter;
use App\Models\CurrencyDenomination;
use App\Models\OutletUsers;
use App\Models\ShiftDetails;
use App\Models\WarehouseMaster;
use App\Repositories\ShiftDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ShiftDetailsController
 * @package App\Http\Controllers\API
 */

class ShiftDetailsAPIController extends AppBaseController
{
    /** @var  ShiftDetailsRepository */
    private $shiftDetailsRepository;

    public function __construct(ShiftDetailsRepository $shiftDetailsRepo)
    {
        $this->shiftDetailsRepository = $shiftDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/shiftDetails",
     *      summary="Get a listing of the ShiftDetails.",
     *      tags={"ShiftDetails"},
     *      description="Get all ShiftDetails",
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
     *                  @SWG\Items(ref="#/definitions/ShiftDetails")
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
        $this->shiftDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->shiftDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $shiftDetails = $this->shiftDetailsRepository->all();

        return $this->sendResponse($shiftDetails->toArray(), 'Shift Details retrieved successfully');
    }

    /**
     * @param CreateShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/shiftDetails",
     *      summary="Store a newly created ShiftDetails in storage",
     *      tags={"ShiftDetails"},
     *      description="Store ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ShiftDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ShiftDetails")
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
     *                  ref="#/definitions/ShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateShiftDetailsAPIRequest $request)
    {
        $input = $request->all();

        $shiftDetails = $this->shiftDetailsRepository->create($input);

        return $this->sendResponse($shiftDetails->toArray(), 'Shift Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/shiftDetails/{id}",
     *      summary="Display the specified ShiftDetails",
     *      tags={"ShiftDetails"},
     *      description="Get ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ShiftDetails",
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
     *                  ref="#/definitions/ShiftDetails"
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
        /** @var ShiftDetails $shiftDetails */
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            return $this->sendError('Shift Details not found');
        }

        return $this->sendResponse($shiftDetails->toArray(), 'Shift Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateShiftDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/shiftDetails/{id}",
     *      summary="Update the specified ShiftDetails in storage",
     *      tags={"ShiftDetails"},
     *      description="Update ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ShiftDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ShiftDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ShiftDetails")
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
     *                  ref="#/definitions/ShiftDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateShiftDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ShiftDetails $shiftDetails */
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            return $this->sendError('Shift Details not found');
        }

        $shiftDetails = $this->shiftDetailsRepository->update($input, $id);

        return $this->sendResponse($shiftDetails->toArray(), 'ShiftDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/shiftDetails/{id}",
     *      summary="Remove the specified ShiftDetails from storage",
     *      tags={"ShiftDetails"},
     *      description="Delete ShiftDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ShiftDetails",
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
        /** @var ShiftDetails $shiftDetails */
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            return $this->sendError('Shift Details not found');
        }

        $shiftDetails->delete();

        return $this->sendResponse($id, 'Shift Details deleted successfully');
    }

    public function getPosShiftDetails(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $validator = \Validator::make($input, [
            'companyId' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companyId']);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $currencyDenomination = CurrencyDenomination::where('currencyID',$company->localCurrencyID)
                                                      ->orderBy('amount','desc')
                                                      ->get();

        $assignedOutlet = OutletUsers::where('userID',$employee->employeeSystemID)
                                       ->where('companySystemID',$input['companyId'])
                                       ->where('isActive',1)
                                       ->first();

        if(empty($assignedOutlet)){
            return $this->sendError('This user is not assigned for any outlet.');
        }

        $counters = Counter::where('companySystemID',$input['companyId'])
                             ->where('wareHouseID',$assignedOutlet->wareHouseID)
                             ->get();

        $output = array(
            'company' => $company,
            'currencyDenomination' => $currencyDenomination,
            'outlet' => $assignedOutlet,
            'counters' => $counters
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}
