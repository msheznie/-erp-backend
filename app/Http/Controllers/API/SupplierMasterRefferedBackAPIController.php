<?php
/**
 * =============================================
 * -- File Name : SupplierMasterRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Supplier Master Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 17 - December 2018
 * -- Description : This file contains the all CRUD for Supplier Master Referred Back
 * -- REVISION HISTORY
 * -- Date: 17-December 2018 By: Fayas Description: Added new functions named as referBackHistoryBySupplierMaster()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateSupplierMasterRefferedBackAPIRequest;
use App\Models\SupplierMasterRefferedBack;
use App\Repositories\SupplierMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class SupplierMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  SupplierMasterRefferedBackRepository */
    private $supplierMasterRefferedBackRepository;

    public function __construct(SupplierMasterRefferedBackRepository $supplierMasterRefferedBackRepo)
    {
        $this->supplierMasterRefferedBackRepository = $supplierMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierMasterRefferedBacks",
     *      summary="Get a listing of the SupplierMasterRefferedBacks.",
     *      tags={"SupplierMasterRefferedBack"},
     *      description="Get all SupplierMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/SupplierMasterRefferedBack")
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
        $this->supplierMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierMasterRefferedBacks = $this->supplierMasterRefferedBackRepository->all();

        return $this->sendResponse($supplierMasterRefferedBacks->toArray(), trans('custom.supplier_master_reffered_backs_retrieved_successfu'));
    }

    /**
     * @param CreateSupplierMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierMasterRefferedBacks",
     *      summary="Store a newly created SupplierMasterRefferedBack in storage",
     *      tags={"SupplierMasterRefferedBack"},
     *      description="Store SupplierMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierMasterRefferedBack")
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
     *                  ref="#/definitions/SupplierMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $supplierMasterRefferedBacks = $this->supplierMasterRefferedBackRepository->create($input);

        return $this->sendResponse($supplierMasterRefferedBacks->toArray(), trans('custom.supplier_master_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierMasterRefferedBacks/{id}",
     *      summary="Display the specified SupplierMasterRefferedBack",
     *      tags={"SupplierMasterRefferedBack"},
     *      description="Get SupplierMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierMasterRefferedBack",
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
     *                  ref="#/definitions/SupplierMasterRefferedBack"
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
        /** @var SupplierMasterRefferedBack $supplierMasterRefferedBack */
        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->with(['finalApprovedBy'])->findWithoutFail($id);

        if (empty($supplierMasterRefferedBack)) {
            return $this->sendError(trans('custom.supplier_master_reffered_back_not_found'));
        }

        return $this->sendResponse($supplierMasterRefferedBack->toArray(), trans('custom.supplier_master_reffered_back_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateSupplierMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierMasterRefferedBacks/{id}",
     *      summary="Update the specified SupplierMasterRefferedBack in storage",
     *      tags={"SupplierMasterRefferedBack"},
     *      description="Update SupplierMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierMasterRefferedBack")
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
     *                  ref="#/definitions/SupplierMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierMasterRefferedBack $supplierMasterRefferedBack */
        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($supplierMasterRefferedBack)) {
            return $this->sendError(trans('custom.supplier_master_reffered_back_not_found'));
        }

        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($supplierMasterRefferedBack->toArray(), trans('custom.suppliermasterrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierMasterRefferedBacks/{id}",
     *      summary="Remove the specified SupplierMasterRefferedBack from storage",
     *      tags={"SupplierMasterRefferedBack"},
     *      description="Delete SupplierMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierMasterRefferedBack",
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
        /** @var SupplierMasterRefferedBack $supplierMasterRefferedBack */
        $supplierMasterRefferedBack = $this->supplierMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($supplierMasterRefferedBack)) {
            return $this->sendError(trans('custom.supplier_master_reffered_back_not_found'));
        }

        $supplierMasterRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.supplier_master_reffered_back_deleted_successfully'));
    }


    public function referBackHistoryBySupplierMaster(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('supplierCountryID', 'isCriticalYN', 'isActive','supplierConfirmedYN','approvedYN'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $supplierMasters = SupplierMasterRefferedBack::where('supplierCodeSystem',$input['id'])
        ->with(['categoryMaster','critical','country','supplierCurrency' => function ($query) {
            $query->where('isDefault', -1)
                ->with(['currencyMaster']);
        }]);


        if ($search) {
            $supplierMasters = $supplierMasters->where(function ($query) use ($search) {
                $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($supplierMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('supplierCodeSystemRefferedBack', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }
}
