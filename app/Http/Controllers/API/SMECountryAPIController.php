<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMECountryAPIRequest;
use App\Http\Requests\API\UpdateSMECountryAPIRequest;
use App\Models\SMECountry;
use App\Repositories\SMECountryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMECountryController
 * @package App\Http\Controllers\API
 */

class SMECountryAPIController extends AppBaseController
{
    /** @var  SMECountryRepository */
    private $sMECountryRepository;

    public function __construct(SMECountryRepository $sMECountryRepo)
    {
        $this->sMECountryRepository = $sMECountryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECountries",
     *      summary="Get a listing of the SMECountries.",
     *      tags={"SMECountry"},
     *      description="Get all SMECountries",
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
     *                  @SWG\Items(ref="#/definitions/SMECountry")
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
        $this->sMECountryRepository->pushCriteria(new RequestCriteria($request));
        $this->sMECountryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMECountries = $this->sMECountryRepository->all();

        return $this->sendResponse($sMECountries->toArray(), trans('custom.s_m_e_countries_retrieved_successfully'));
    }

    /**
     * @param CreateSMECountryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMECountries",
     *      summary="Store a newly created SMECountry in storage",
     *      tags={"SMECountry"},
     *      description="Store SMECountry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECountry that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECountry")
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
     *                  ref="#/definitions/SMECountry"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMECountryAPIRequest $request)
    {
        $input = $request->all();

        $sMECountry = $this->sMECountryRepository->create($input);

        return $this->sendResponse($sMECountry->toArray(), trans('custom.s_m_e_country_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMECountries/{id}",
     *      summary="Display the specified SMECountry",
     *      tags={"SMECountry"},
     *      description="Get SMECountry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECountry",
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
     *                  ref="#/definitions/SMECountry"
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
        /** @var SMECountry $sMECountry */
        $sMECountry = $this->sMECountryRepository->findWithoutFail($id);

        if (empty($sMECountry)) {
            return $this->sendError(trans('custom.s_m_e_country_not_found'));
        }

        return $this->sendResponse($sMECountry->toArray(), trans('custom.s_m_e_country_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMECountryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMECountries/{id}",
     *      summary="Update the specified SMECountry in storage",
     *      tags={"SMECountry"},
     *      description="Update SMECountry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECountry",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMECountry that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMECountry")
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
     *                  ref="#/definitions/SMECountry"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMECountryAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMECountry $sMECountry */
        $sMECountry = $this->sMECountryRepository->findWithoutFail($id);

        if (empty($sMECountry)) {
            return $this->sendError(trans('custom.s_m_e_country_not_found'));
        }

        $sMECountry = $this->sMECountryRepository->update($input, $id);

        return $this->sendResponse($sMECountry->toArray(), trans('custom.smecountry_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMECountries/{id}",
     *      summary="Remove the specified SMECountry from storage",
     *      tags={"SMECountry"},
     *      description="Delete SMECountry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMECountry",
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
        /** @var SMECountry $sMECountry */
        $sMECountry = $this->sMECountryRepository->findWithoutFail($id);

        if (empty($sMECountry)) {
            return $this->sendError(trans('custom.s_m_e_country_not_found'));
        }

        $sMECountry->delete();

        return $this->sendSuccess('S M E Country deleted successfully');
    }
}
