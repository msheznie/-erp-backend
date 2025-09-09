<?php
/**
 * =============================================
 * -- File Name : ItemMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Master Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - December 2018
 * -- Description : This file contains the all CRUD for Item Master Reffered Back
 * -- REVISION HISTORY
 * -- Date: 14-December 2018 By: Fayas Description: Added new functions named as referBackHistoryByItemsMaster()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateItemMasterRefferedBackAPIRequest;
use App\Models\ItemMasterRefferedBack;
use App\Repositories\ItemMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class ItemMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  ItemMasterRefferedBackRepository */
    private $itemMasterRefferedBackRepository;

    public function __construct(ItemMasterRefferedBackRepository $itemMasterRefferedBackRepo)
    {
        $this->itemMasterRefferedBackRepository = $itemMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemMasterRefferedBacks",
     *      summary="Get a listing of the ItemMasterRefferedBacks.",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Get all ItemMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ItemMasterRefferedBack")
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
        $this->itemMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->itemMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemMasterRefferedBacks = $this->itemMasterRefferedBackRepository->all();

        return $this->sendResponse($itemMasterRefferedBacks->toArray(), trans('custom.item_master_reffered_backs_retrieved_successfully'));
    }

    /**
     * @param CreateItemMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemMasterRefferedBacks",
     *      summary="Store a newly created ItemMasterRefferedBack in storage",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Store ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemMasterRefferedBack")
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
     *                  ref="#/definitions/ItemMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $itemMasterRefferedBacks = $this->itemMasterRefferedBackRepository->create($input);

        return $this->sendResponse($itemMasterRefferedBacks->toArray(), trans('custom.item_master_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemMasterRefferedBacks/{id}",
     *      summary="Display the specified ItemMasterRefferedBack",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Get ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemMasterRefferedBack",
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
     *                  ref="#/definitions/ItemMasterRefferedBack"
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
        /** @var ItemMasterRefferedBack $itemMasterRefferedBack */
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_master_reffered_back_not_found'));
        }

        return $this->sendResponse($itemMasterRefferedBack->toArray(), trans('custom.item_master_reffered_back_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateItemMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemMasterRefferedBacks/{id}",
     *      summary="Update the specified ItemMasterRefferedBack in storage",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Update ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemMasterRefferedBack")
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
     *                  ref="#/definitions/ItemMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemMasterRefferedBack $itemMasterRefferedBack */
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_master_reffered_back_not_found'));
        }

        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($itemMasterRefferedBack->toArray(), trans('custom.itemmasterrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemMasterRefferedBacks/{id}",
     *      summary="Remove the specified ItemMasterRefferedBack from storage",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Delete ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemMasterRefferedBack",
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
        /** @var ItemMasterRefferedBack $itemMasterRefferedBack */
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_master_reffered_back_not_found'));
        }

        $itemMasterRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.item_master_reffered_back_deleted_successfully'));
    }


    public function referBackHistoryByItemsMaster(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');

        $itemMasters = ItemMasterRefferedBack::with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory'])
                                              ->where('itemCodeSystem',$input['id']);
        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('primaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemCodeSystemRefferedback', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

}
