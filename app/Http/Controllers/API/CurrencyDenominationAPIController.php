<?php
/**
 * =============================================
 * -- File Name : CurrencyDenominationAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Currency Denomination
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - January 2019
 * -- Description : This file contains the all CRUD for Currency Denomination
 * -- REVISION HISTORY
 * -- Date: 14-January 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCurrencyDenominationAPIRequest;
use App\Http\Requests\API\UpdateCurrencyDenominationAPIRequest;
use App\Models\CurrencyDenomination;
use App\Repositories\CurrencyDenominationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CurrencyDenominationController
 * @package App\Http\Controllers\API
 */

class CurrencyDenominationAPIController extends AppBaseController
{
    /** @var  CurrencyDenominationRepository */
    private $currencyDenominationRepository;

    public function __construct(CurrencyDenominationRepository $currencyDenominationRepo)
    {
        $this->currencyDenominationRepository = $currencyDenominationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyDenominations",
     *      summary="Get a listing of the CurrencyDenominations.",
     *      tags={"CurrencyDenomination"},
     *      description="Get all CurrencyDenominations",
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
     *                  @SWG\Items(ref="#/definitions/CurrencyDenomination")
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
        $this->currencyDenominationRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyDenominationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyDenominations = $this->currencyDenominationRepository->all();

        return $this->sendResponse($currencyDenominations->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.currency_denominations')]));
    }

    /**
     * @param CreateCurrencyDenominationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/currencyDenominations",
     *      summary="Store a newly created CurrencyDenomination in storage",
     *      tags={"CurrencyDenomination"},
     *      description="Store CurrencyDenomination",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyDenomination that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyDenomination")
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
     *                  ref="#/definitions/CurrencyDenomination"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCurrencyDenominationAPIRequest $request)
    {
        $input = $request->all();

        $currencyDenominations = $this->currencyDenominationRepository->create($input);

        return $this->sendResponse($currencyDenominations->toArray(), trans('custom.save', ['attribute' => trans('custom.currency_denominations')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyDenominations/{id}",
     *      summary="Display the specified CurrencyDenomination",
     *      tags={"CurrencyDenomination"},
     *      description="Get CurrencyDenomination",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyDenomination",
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
     *                  ref="#/definitions/CurrencyDenomination"
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
        /** @var CurrencyDenomination $currencyDenomination */
        $currencyDenomination = $this->currencyDenominationRepository->findWithoutFail($id);

        if (empty($currencyDenomination)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_denominations')]));
        }

        return $this->sendResponse($currencyDenomination->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.currency_denominations')]));
    }

    /**
     * @param int $id
     * @param UpdateCurrencyDenominationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/currencyDenominations/{id}",
     *      summary="Update the specified CurrencyDenomination in storage",
     *      tags={"CurrencyDenomination"},
     *      description="Update CurrencyDenomination",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyDenomination",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyDenomination that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyDenomination")
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
     *                  ref="#/definitions/CurrencyDenomination"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCurrencyDenominationAPIRequest $request)
    {
        $input = $request->all();

        /** @var CurrencyDenomination $currencyDenomination */
        $currencyDenomination = $this->currencyDenominationRepository->findWithoutFail($id);

        if (empty($currencyDenomination)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_denominations')]));
        }

        $currencyDenomination = $this->currencyDenominationRepository->update($input, $id);

        return $this->sendResponse($currencyDenomination->toArray(), trans('custom.update', ['attribute' => trans('custom.currency_denominations')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/currencyDenominations/{id}",
     *      summary="Remove the specified CurrencyDenomination from storage",
     *      tags={"CurrencyDenomination"},
     *      description="Delete CurrencyDenomination",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyDenomination",
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
        /** @var CurrencyDenomination $currencyDenomination */
        $currencyDenomination = $this->currencyDenominationRepository->findWithoutFail($id);

        if (empty($currencyDenomination)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_denominations')]));
        }

        $currencyDenomination->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.currency_denominations')]));
    }
}
