<?php
/**
 * =============================================
 * -- File Name : QuotationMasterVersionAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationMasterVersion
 * -- Author : Mohamed Nazir
 * -- Create date : 29 - January 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Master Version
 * -- REVISION HISTORY
 * -- Date: 30-January 2019 By: Nazir Description: Added new function getSalesQuotationRevisionHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationMasterVersionAPIRequest;
use App\Http\Requests\API\UpdateQuotationMasterVersionAPIRequest;
use App\Models\QuotationMasterVersion;
use App\Repositories\QuotationMasterVersionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuotationMasterVersionController
 * @package App\Http\Controllers\API
 */

class QuotationMasterVersionAPIController extends AppBaseController
{
    /** @var  QuotationMasterVersionRepository */
    private $quotationMasterVersionRepository;

    public function __construct(QuotationMasterVersionRepository $quotationMasterVersionRepo)
    {
        $this->quotationMasterVersionRepository = $quotationMasterVersionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasterVersions",
     *      summary="Get a listing of the QuotationMasterVersions.",
     *      tags={"QuotationMasterVersion"},
     *      description="Get all QuotationMasterVersions",
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
     *                  @SWG\Items(ref="#/definitions/QuotationMasterVersion")
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
        $this->quotationMasterVersionRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationMasterVersionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationMasterVersions = $this->quotationMasterVersionRepository->all();

        return $this->sendResponse($quotationMasterVersions->toArray(), trans('custom.quotation_master_versions_retrieved_successfully'));
    }

    /**
     * @param CreateQuotationMasterVersionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationMasterVersions",
     *      summary="Store a newly created QuotationMasterVersion in storage",
     *      tags={"QuotationMasterVersion"},
     *      description="Store QuotationMasterVersion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMasterVersion that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMasterVersion")
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
     *                  ref="#/definitions/QuotationMasterVersion"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationMasterVersionAPIRequest $request)
    {
        $input = $request->all();

        $quotationMasterVersions = $this->quotationMasterVersionRepository->create($input);

        return $this->sendResponse($quotationMasterVersions->toArray(), trans('custom.quotation_master_version_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasterVersions/{id}",
     *      summary="Display the specified QuotationMasterVersion",
     *      tags={"QuotationMasterVersion"},
     *      description="Get QuotationMasterVersion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMasterVersion",
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
     *                  ref="#/definitions/QuotationMasterVersion"
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
        /** @var QuotationMasterVersion $quotationMasterVersion */
        $quotationMasterVersion = $this->quotationMasterVersionRepository->with(['confirmed_by', 'created_by'])->findWithoutFail($id);

        if (empty($quotationMasterVersion)) {
            return $this->sendError(trans('custom.quotation_master_version_not_found'));
        }

        return $this->sendResponse($quotationMasterVersion->toArray(), trans('custom.quotation_master_version_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateQuotationMasterVersionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationMasterVersions/{id}",
     *      summary="Update the specified QuotationMasterVersion in storage",
     *      tags={"QuotationMasterVersion"},
     *      description="Update QuotationMasterVersion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMasterVersion",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMasterVersion that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMasterVersion")
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
     *                  ref="#/definitions/QuotationMasterVersion"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationMasterVersionAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationMasterVersion $quotationMasterVersion */
        $quotationMasterVersion = $this->quotationMasterVersionRepository->findWithoutFail($id);

        if (empty($quotationMasterVersion)) {
            return $this->sendError(trans('custom.quotation_master_version_not_found'));
        }

        $quotationMasterVersion = $this->quotationMasterVersionRepository->update($input, $id);

        return $this->sendResponse($quotationMasterVersion->toArray(), trans('custom.quotationmasterversion_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationMasterVersions/{id}",
     *      summary="Remove the specified QuotationMasterVersion from storage",
     *      tags={"QuotationMasterVersion"},
     *      description="Delete QuotationMasterVersion",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMasterVersion",
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
        /** @var QuotationMasterVersion $quotationMasterVersion */
        $quotationMasterVersion = $this->quotationMasterVersionRepository->findWithoutFail($id);

        if (empty($quotationMasterVersion)) {
            return $this->sendError(trans('custom.quotation_master_version_not_found'));
        }

        $quotationMasterVersion->delete();

        return $this->sendResponse($id, trans('custom.quotation_master_version_deleted_successfully'));
    }

    public function getSalesQuotationRevisionHistory(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $quotationMasterID = $request['quotationMasterID'];


        $quotationMasterVersion = QuotationMasterVersion::where('quotationMasterID', $quotationMasterID);

        return \DataTables::eloquent($quotationMasterVersion)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('quotationVerstionMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
