<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSecondaryCompanyAPIRequest;
use App\Http\Requests\API\UpdateSecondaryCompanyAPIRequest;
use App\Models\SecondaryCompany;
use App\Repositories\SecondaryCompanyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SecondaryCompanyController
 * @package App\Http\Controllers\API
 */

class SecondaryCompanyAPIController extends AppBaseController
{
    /** @var  SecondaryCompanyRepository */
    private $secondaryCompanyRepository;

    public function __construct(SecondaryCompanyRepository $secondaryCompanyRepo)
    {
        $this->secondaryCompanyRepository = $secondaryCompanyRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/secondaryCompanies",
     *      summary="Get a listing of the SecondaryCompanies.",
     *      tags={"SecondaryCompany"},
     *      description="Get all SecondaryCompanies",
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
     *                  @SWG\Items(ref="#/definitions/SecondaryCompany")
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
        $this->secondaryCompanyRepository->pushCriteria(new RequestCriteria($request));
        $this->secondaryCompanyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $secondaryCompanies = $this->secondaryCompanyRepository->all();

        return $this->sendResponse($secondaryCompanies->toArray(), trans('custom.secondary_companies_retrieved_successfully'));
    }

    /**
     * @param CreateSecondaryCompanyAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/secondaryCompanies",
     *      summary="Store a newly created SecondaryCompany in storage",
     *      tags={"SecondaryCompany"},
     *      description="Store SecondaryCompany",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SecondaryCompany that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SecondaryCompany")
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
     *                  ref="#/definitions/SecondaryCompany"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSecondaryCompanyAPIRequest $request)
    {
        $input = $request->all();

        $secondaryCompany = $this->secondaryCompanyRepository->create($input);

        return $this->sendResponse($secondaryCompany->toArray(), trans('custom.secondary_company_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/secondaryCompanies/{id}",
     *      summary="Display the specified SecondaryCompany",
     *      tags={"SecondaryCompany"},
     *      description="Get SecondaryCompany",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SecondaryCompany",
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
     *                  ref="#/definitions/SecondaryCompany"
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
        /** @var SecondaryCompany $secondaryCompany */
        $secondaryCompany = $this->secondaryCompanyRepository->findWithoutFail($id);

        if (empty($secondaryCompany)) {
            return $this->sendError(trans('custom.secondary_company_not_found'));
        }

        return $this->sendResponse($secondaryCompany->toArray(), trans('custom.secondary_company_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSecondaryCompanyAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/secondaryCompanies/{id}",
     *      summary="Update the specified SecondaryCompany in storage",
     *      tags={"SecondaryCompany"},
     *      description="Update SecondaryCompany",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SecondaryCompany",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SecondaryCompany that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SecondaryCompany")
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
     *                  ref="#/definitions/SecondaryCompany"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSecondaryCompanyAPIRequest $request)
    {
        $input = $request->all();

        /** @var SecondaryCompany $secondaryCompany */
        $secondaryCompany = $this->secondaryCompanyRepository->findWithoutFail($id);

        if (empty($secondaryCompany)) {
            return $this->sendError(trans('custom.secondary_company_not_found'));
        }

        $secondaryCompany = $this->secondaryCompanyRepository->update($input, $id);

        return $this->sendResponse($secondaryCompany->toArray(), trans('custom.secondarycompany_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/secondaryCompanies/{id}",
     *      summary="Remove the specified SecondaryCompany from storage",
     *      tags={"SecondaryCompany"},
     *      description="Delete SecondaryCompany",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SecondaryCompany",
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
        /** @var SecondaryCompany $secondaryCompany */
        $secondaryCompany = $this->secondaryCompanyRepository->findWithoutFail($id);

        if (empty($secondaryCompany)) {
            return $this->sendError(trans('custom.secondary_company_not_found'));
        }

        $secondaryCompany->delete();

        return $this->sendResponse($id, trans('custom.secondary_company_deleted_successfully'));
    }
}
