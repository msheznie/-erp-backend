<?php
/**
 * =============================================
 * -- File Name : ItemReturnDetailsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Item Return Details Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 06 - December 2018
 * -- Description : This file contains the all CRUD for Item Return Details Referred Back
 * -- REVISION HISTORY
 * -- Date: 06-December 2018 By: Fayas Description: Added new functions named as getItemReturnDetailsReferBack()
 *
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemReturnDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateItemReturnDetailsRefferedBackAPIRequest;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetailsRefferedBack;
use App\Models\ItemReturnMaster;
use App\Models\ItemReturnMasterRefferedBack;
use App\Models\Unit;
use App\Repositories\ItemReturnDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemReturnDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class ItemReturnDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  ItemReturnDetailsRefferedBackRepository */
    private $itemReturnDetailsRefferedBackRepository;

    public function __construct(ItemReturnDetailsRefferedBackRepository $itemReturnDetailsRefferedBackRepo)
    {
        $this->itemReturnDetailsRefferedBackRepository = $itemReturnDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnDetailsRefferedBacks",
     *      summary="Get a listing of the ItemReturnDetailsRefferedBacks.",
     *      tags={"ItemReturnDetailsRefferedBack"},
     *      description="Get all ItemReturnDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ItemReturnDetailsRefferedBack")
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
        $this->itemReturnDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->itemReturnDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemReturnDetailsRefferedBacks = $this->itemReturnDetailsRefferedBackRepository->all();

        return $this->sendResponse($itemReturnDetailsRefferedBacks->toArray(), trans('custom.item_return_details_reffered_backs_retrieved_succe'));
    }

    /**
     * @param CreateItemReturnDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemReturnDetailsRefferedBacks",
     *      summary="Store a newly created ItemReturnDetailsRefferedBack in storage",
     *      tags={"ItemReturnDetailsRefferedBack"},
     *      description="Store ItemReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnDetailsRefferedBack")
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
     *                  ref="#/definitions/ItemReturnDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemReturnDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $itemReturnDetailsRefferedBacks = $this->itemReturnDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($itemReturnDetailsRefferedBacks->toArray(), trans('custom.item_return_details_reffered_back_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnDetailsRefferedBacks/{id}",
     *      summary="Display the specified ItemReturnDetailsRefferedBack",
     *      tags={"ItemReturnDetailsRefferedBack"},
     *      description="Get ItemReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetailsRefferedBack",
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
     *                  ref="#/definitions/ItemReturnDetailsRefferedBack"
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
        /** @var ItemReturnDetailsRefferedBack $itemReturnDetailsRefferedBack */
        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnDetailsRefferedBack)) {
            return $this->sendError(trans('custom.item_return_details_reffered_back_not_found'));
        }

        return $this->sendResponse($itemReturnDetailsRefferedBack->toArray(), trans('custom.item_return_details_reffered_back_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdateItemReturnDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemReturnDetailsRefferedBacks/{id}",
     *      summary="Update the specified ItemReturnDetailsRefferedBack in storage",
     *      tags={"ItemReturnDetailsRefferedBack"},
     *      description="Update ItemReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnDetailsRefferedBack")
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
     *                  ref="#/definitions/ItemReturnDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemReturnDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemReturnDetailsRefferedBack $itemReturnDetailsRefferedBack */
        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnDetailsRefferedBack)) {
            return $this->sendError(trans('custom.item_return_details_reffered_back_not_found'));
        }

        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($itemReturnDetailsRefferedBack->toArray(), trans('custom.itemreturndetailsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemReturnDetailsRefferedBacks/{id}",
     *      summary="Remove the specified ItemReturnDetailsRefferedBack from storage",
     *      tags={"ItemReturnDetailsRefferedBack"},
     *      description="Delete ItemReturnDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetailsRefferedBack",
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
        /** @var ItemReturnDetailsRefferedBack $itemReturnDetailsRefferedBack */
        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnDetailsRefferedBack)) {
            return $this->sendError(trans('custom.item_return_details_reffered_back_not_found'));
        }

        $itemReturnDetailsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.item_return_details_reffered_back_deleted_successf'));
    }


    public function getItemReturnDetailsReferBack(Request $request)
    {
        $input = $request->all();
        $rId = $input['itemReturnAutoID'];
        $timesReferred = $input['timesReferred'];
        $itemReturnMaster = ItemReturnMaster::find($rId);

        if (empty($itemReturnMaster)) {
            return $this->sendError(trans('custom.item_return_not_found'));
        }

        $items = ItemReturnDetailsRefferedBack::where('itemReturnAutoID', $rId)
            ->where('timesReferred', $timesReferred)
            ->with(['uom_issued', 'uom_receiving', 'issue'])
            ->get();

        foreach ($items as $item) {

            $issueUnit = Unit::where('UnitID', $item['itemUnitOfMeasure'])->with(['unitConversion.sub_unit'])->first();

            $issueUnits = array();
            foreach ($issueUnit->unitConversion as $unit) {
                $temArray = array('value' => $unit->sub_unit->UnitID, 'label' => $unit->sub_unit->UnitShortCode);
                array_push($issueUnits, $temArray);
            }

            $item->issueUnits = $issueUnits;

            if ($item['itemCodeSystem']) {
                $itemIssues = ItemIssueMaster::whereHas('details', function ($q) use ($item) {
                    $q->where('itemCodeSystem', $item['itemCodeSystem']);
                    })
                    ->where('companySystemID', $itemReturnMaster->companySystemID)
                    ->where('serviceLineSystemID', $itemReturnMaster->serviceLineSystemID)
                    ->where('wareHouseFrom', $itemReturnMaster->wareHouseLocation)
                    ->where('approved', -1)
                    ->select('itemIssueAutoID AS value', 'itemIssueCode AS label')
                    ->get();

                $item->issues = $itemIssues;
            } else {
                $item->issues = [];
            }
        }

        return $this->sendResponse($items->toArray(), trans('custom.material_return_details_retrieved_successfully'));
    }
}
