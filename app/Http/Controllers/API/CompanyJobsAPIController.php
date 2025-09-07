<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyJobsAPIRequest;
use App\Http\Requests\API\UpdateCompanyJobsAPIRequest;
use App\Models\CompanyJobs;
use App\Repositories\CompanyJobsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyJobsController
 * @package App\Http\Controllers\API
 */

class CompanyJobsAPIController extends AppBaseController
{
    /** @var  CompanyJobsRepository */
    private $companyJobsRepository;

    public function __construct(CompanyJobsRepository $companyJobsRepo)
    {
        $this->companyJobsRepository = $companyJobsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyJobs",
     *      summary="Get a listing of the CompanyJobs.",
     *      tags={"CompanyJobs"},
     *      description="Get all CompanyJobs",
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
     *                  @SWG\Items(ref="#/definitions/CompanyJobs")
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
        $this->companyJobsRepository->pushCriteria(new RequestCriteria($request));
        $this->companyJobsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyJobs = $this->companyJobsRepository->all();

        return $this->sendResponse($companyJobs->toArray(), trans('custom.company_jobs_retrieved_successfully'));
    }

    /**
     * @param CreateCompanyJobsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/companyJobs",
     *      summary="Store a newly created CompanyJobs in storage",
     *      tags={"CompanyJobs"},
     *      description="Store CompanyJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyJobs that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyJobs")
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
     *                  ref="#/definitions/CompanyJobs"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyJobsAPIRequest $request)
    {
        $input = $request->all();

        $companyJobs = $this->companyJobsRepository->create($input);

        return $this->sendResponse($companyJobs->toArray(), trans('custom.company_jobs_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyJobs/{id}",
     *      summary="Display the specified CompanyJobs",
     *      tags={"CompanyJobs"},
     *      description="Get CompanyJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyJobs",
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
     *                  ref="#/definitions/CompanyJobs"
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
        /** @var CompanyJobs $companyJobs */
        $companyJobs = $this->companyJobsRepository->findWithoutFail($id);

        if (empty($companyJobs)) {
            return $this->sendError(trans('custom.company_jobs_not_found'));
        }

        return $this->sendResponse($companyJobs->toArray(), trans('custom.company_jobs_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCompanyJobsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/companyJobs/{id}",
     *      summary="Update the specified CompanyJobs in storage",
     *      tags={"CompanyJobs"},
     *      description="Update CompanyJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyJobs",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyJobs that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyJobs")
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
     *                  ref="#/definitions/CompanyJobs"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyJobsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyJobs $companyJobs */
        $companyJobs = $this->companyJobsRepository->findWithoutFail($id);

        if (empty($companyJobs)) {
            return $this->sendError(trans('custom.company_jobs_not_found'));
        }

        $companyJobs = $this->companyJobsRepository->update($input, $id);

        return $this->sendResponse($companyJobs->toArray(), trans('custom.companyjobs_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/companyJobs/{id}",
     *      summary="Remove the specified CompanyJobs from storage",
     *      tags={"CompanyJobs"},
     *      description="Delete CompanyJobs",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyJobs",
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
        /** @var CompanyJobs $companyJobs */
        $companyJobs = $this->companyJobsRepository->findWithoutFail($id);

        if (empty($companyJobs)) {
            return $this->sendError(trans('custom.company_jobs_not_found'));
        }

        $companyJobs->delete();

        return $this->sendSuccess('Company Jobs deleted successfully');
    }
}
