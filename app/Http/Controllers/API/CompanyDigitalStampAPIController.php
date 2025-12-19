<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\API\CreateCompanyDigitalStampAPIRequest;
use App\Http\Requests\API\UpdateCompanyDigitalStampAPIRequest;
use App\Models\CompanyDigitalStamp;
use App\Repositories\CompanyDigitalStampRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyDigitalStampController
 * @package App\Http\Controllers\API
 */

class CompanyDigitalStampAPIController extends AppBaseController
{
    /** @var  CompanyDigitalStampRepository */
    private $companyDigitalStampRepository;

    public function __construct(CompanyDigitalStampRepository $companyDigitalStampRepo)
    {
        $this->companyDigitalStampRepository = $companyDigitalStampRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyDigitalStamps",
     *      summary="Get a listing of the CompanyDigitalStamps.",
     *      tags={"CompanyDigitalStamp"},
     *      description="Get all CompanyDigitalStamps",
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
     *                  @SWG\Items(ref="#/definitions/CompanyDigitalStamp")
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
        $this->companyDigitalStampRepository->pushCriteria(new RequestCriteria($request));
        $this->companyDigitalStampRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyDigitalStamps = $this->companyDigitalStampRepository->all();

        return $this->sendResponse($companyDigitalStamps->toArray(), trans('custom.company_digital_stamps_retrieved_successfully'));
    }

    /**
     * @param CreateCompanyDigitalStampAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/companyDigitalStamps",
     *      summary="Store a newly created CompanyDigitalStamp in storage",
     *      tags={"CompanyDigitalStamp"},
     *      description="Store CompanyDigitalStamp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyDigitalStamp that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyDigitalStamp")
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
     *                  ref="#/definitions/CompanyDigitalStamp"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyDigitalStampAPIRequest $request)
    {
        $input = $request->all();

        $companyDigitalStamp = $this->companyDigitalStampRepository->create($input);

        return $this->sendResponse($companyDigitalStamp->toArray(), trans('custom.company_digital_stamp_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyDigitalStamps/{id}",
     *      summary="Display the specified CompanyDigitalStamp",
     *      tags={"CompanyDigitalStamp"},
     *      description="Get CompanyDigitalStamp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyDigitalStamp",
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
     *                  ref="#/definitions/CompanyDigitalStamp"
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
        /** @var CompanyDigitalStamp $companyDigitalStamp */
        $companyDigitalStamp = $this->companyDigitalStampRepository->findWithoutFail($id);

        if (empty($companyDigitalStamp)) {
            return $this->sendError(trans('custom.company_digital_stamp_not_found'));
        }

        return $this->sendResponse($companyDigitalStamp->toArray(), trans('custom.company_digital_stamp_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCompanyDigitalStampAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/companyDigitalStamps/{id}",
     *      summary="Update the specified CompanyDigitalStamp in storage",
     *      tags={"CompanyDigitalStamp"},
     *      description="Update CompanyDigitalStamp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyDigitalStamp",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyDigitalStamp that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyDigitalStamp")
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
     *                  ref="#/definitions/CompanyDigitalStamp"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyDigitalStampAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyDigitalStamp $companyDigitalStamp */
        $companyDigitalStamp = $this->companyDigitalStampRepository->findWithoutFail($id);

        if (empty($companyDigitalStamp)) {
            return $this->sendError(trans('custom.company_digital_stamp_not_found'));
        }

        $companyDigitalStamp = $this->companyDigitalStampRepository->update($input, $id);

        return $this->sendResponse($companyDigitalStamp->toArray(), trans('custom.companydigitalstamp_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/companyDigitalStamps/{id}",
     *      summary="Remove the specified CompanyDigitalStamp from storage",
     *      tags={"CompanyDigitalStamp"},
     *      description="Delete CompanyDigitalStamp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyDigitalStamp",
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
        /** @var CompanyDigitalStamp $companyDigitalStamp */
        $companyDigitalStamp = $this->companyDigitalStampRepository->findWithoutFail($id);

        $disk = Helper::policyWiseDisk($companyDigitalStamp->company_system_id, 'public');
        $re = Storage::disk($disk)->delete($companyDigitalStamp->path);

        if (empty($companyDigitalStamp)) {
            return $this->sendError(trans('custom.company_digital_stamp_not_found'));
        }

        $companyDigitalStamp->delete();

        return $this->sendResponse($companyDigitalStamp,trans('custom.company_digital_stamp_deleted_successfully'));
    }
}
