<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetInsuranceDetailAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetInsuranceDetailAPIRequest;
use App\Models\Company;
use App\Models\FixedAssetInsuranceDetail;
use App\Repositories\FixedAssetInsuranceDetailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetInsuranceDetailController
 * @package App\Http\Controllers\API
 */

class FixedAssetInsuranceDetailAPIController extends AppBaseController
{
    /** @var  FixedAssetInsuranceDetailRepository */
    private $fixedAssetInsuranceDetailRepository;

    public function __construct(FixedAssetInsuranceDetailRepository $fixedAssetInsuranceDetailRepo)
    {
        $this->fixedAssetInsuranceDetailRepository = $fixedAssetInsuranceDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetInsuranceDetails",
     *      summary="Get a listing of the FixedAssetInsuranceDetails.",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Get all FixedAssetInsuranceDetails",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetInsuranceDetail")
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
        $this->fixedAssetInsuranceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetInsuranceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetInsuranceDetails = $this->fixedAssetInsuranceDetailRepository->all();

        return $this->sendResponse($fixedAssetInsuranceDetails->toArray(), trans('custom.fixed_asset_insurance_details_retrieved_successful'));
    }

    /**
     * @param CreateFixedAssetInsuranceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetInsuranceDetails",
     *      summary="Store a newly created FixedAssetInsuranceDetail in storage",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Store FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetInsuranceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetInsuranceDetail")
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
     *                  ref="#/definitions/FixedAssetInsuranceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetInsuranceDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = [
            'dateOfExpiry.after_or_equal' => trans('custom.date_of_expiry_cannot_be_less_than_date_of_insurance'),
        ];
        $validator = \Validator::make($request->all(), [
            'dateOfInsurance' => 'required|date',
            'dateOfExpiry' => 'required|date|after_or_equal:dateOfInsurance',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        if (isset($input['dateOfInsurance'])) {
            if ($input['dateOfInsurance']) {
                $input['dateOfInsurance'] = new Carbon($input['dateOfInsurance']);
            }
        }

        if (isset($input['dateOfExpiry'])) {
            if ($input['dateOfExpiry']) {
                $input['dateOfExpiry'] = new Carbon($input['dateOfExpiry']);
            }
        }

        $input['createdByUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $fixedAssetInsuranceDetails = $this->fixedAssetInsuranceDetailRepository->create($input);

        return $this->sendResponse($fixedAssetInsuranceDetails->toArray(), trans('custom.fixed_asset_insurance_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetInsuranceDetails/{id}",
     *      summary="Display the specified FixedAssetInsuranceDetail",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Get FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetInsuranceDetail",
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
     *                  ref="#/definitions/FixedAssetInsuranceDetail"
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
        /** @var FixedAssetInsuranceDetail $fixedAssetInsuranceDetail */
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            return $this->sendError(trans('custom.fixed_asset_insurance_detail_not_found'));
        }

        return $this->sendResponse($fixedAssetInsuranceDetail->toArray(), trans('custom.fixed_asset_insurance_detail_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetInsuranceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetInsuranceDetails/{id}",
     *      summary="Update the specified FixedAssetInsuranceDetail in storage",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Update FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetInsuranceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetInsuranceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetInsuranceDetail")
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
     *                  ref="#/definitions/FixedAssetInsuranceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetInsuranceDetailAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $messages = [
            'dateOfExpiry.after_or_equal' => trans('custom.date_of_expiry_cannot_be_less_than_date_of_insurance'),
        ];
        $validator = \Validator::make($request->all(), [
            'dateOfInsurance' => 'required|date',
            'dateOfExpiry' => 'required|date|after_or_equal:dateOfInsurance',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['dateOfInsurance'])) {
            if ($input['dateOfInsurance']) {
                $input['dateOfInsurance'] = new Carbon($input['dateOfInsurance']);
            }
        }

        if (isset($input['dateOfExpiry'])) {
            if ($input['dateOfExpiry']) {
                $input['dateOfExpiry'] = new Carbon($input['dateOfExpiry']);
            }
        }

        /** @var FixedAssetInsuranceDetail $fixedAssetInsuranceDetail */
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            return $this->sendError(trans('custom.fixed_asset_insurance_detail_not_found'));
        }

        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->update($input, $id);

        return $this->sendResponse($fixedAssetInsuranceDetail->toArray(), trans('custom.fixedassetinsurancedetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetInsuranceDetails/{id}",
     *      summary="Remove the specified FixedAssetInsuranceDetail from storage",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Delete FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetInsuranceDetail",
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
        /** @var FixedAssetInsuranceDetail $fixedAssetInsuranceDetail */
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            return $this->sendError(trans('custom.fixed_asset_insurance_detail_not_found'));
        }

        $fixedAssetInsuranceDetail->delete();

        return $this->sendResponse($id, trans('custom.fixed_asset_insurance_detail_deleted_successfully'));
    }
}
