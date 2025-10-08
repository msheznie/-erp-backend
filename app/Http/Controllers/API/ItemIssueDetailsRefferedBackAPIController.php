<?php
/**
 * =============================================
 * -- File Name : ItemIssueDetailsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  IItem Issue Details Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - December 2018
 * -- Description : This file contains the all CRUD for IItem Issue Details Reffered Back
 * -- REVISION HISTORY
 * -- Date: 03-December 2018 By: Fayas Description: Added new functions named as getItemIssueDetailsReferBack()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemIssueDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateItemIssueDetailsRefferedBackAPIRequest;
use App\Models\ItemIssueDetailsRefferedBack;
use App\Models\Unit;
use App\Repositories\ItemIssueDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemIssueDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class ItemIssueDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  ItemIssueDetailsRefferedBackRepository */
    private $itemIssueDetailsRefferedBackRepository;

    public function __construct(ItemIssueDetailsRefferedBackRepository $itemIssueDetailsRefferedBackRepo)
    {
        $this->itemIssueDetailsRefferedBackRepository = $itemIssueDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueDetailsRefferedBacks",
     *      summary="Get a listing of the ItemIssueDetailsRefferedBacks.",
     *      tags={"ItemIssueDetailsRefferedBack"},
     *      description="Get all ItemIssueDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ItemIssueDetailsRefferedBack")
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
        $this->itemIssueDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->itemIssueDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemIssueDetailsRefferedBacks = $this->itemIssueDetailsRefferedBackRepository->all();

        return $this->sendResponse($itemIssueDetailsRefferedBacks->toArray(), trans('custom.item_issue_details_reffered_backs_retrieved_succes'));
    }

    /**
     * @param CreateItemIssueDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemIssueDetailsRefferedBacks",
     *      summary="Store a newly created ItemIssueDetailsRefferedBack in storage",
     *      tags={"ItemIssueDetailsRefferedBack"},
     *      description="Store ItemIssueDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueDetailsRefferedBack")
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
     *                  ref="#/definitions/ItemIssueDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemIssueDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $itemIssueDetailsRefferedBacks = $this->itemIssueDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($itemIssueDetailsRefferedBacks->toArray(), trans('custom.item_issue_details_reffered_back_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueDetailsRefferedBacks/{id}",
     *      summary="Display the specified ItemIssueDetailsRefferedBack",
     *      tags={"ItemIssueDetailsRefferedBack"},
     *      description="Get ItemIssueDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetailsRefferedBack",
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
     *                  ref="#/definitions/ItemIssueDetailsRefferedBack"
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
        /** @var ItemIssueDetailsRefferedBack $itemIssueDetailsRefferedBack */
        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueDetailsRefferedBack)) {
            return $this->sendError(trans('custom.item_issue_details_reffered_back_not_found'));
        }

        return $this->sendResponse($itemIssueDetailsRefferedBack->toArray(), trans('custom.item_issue_details_reffered_back_retrieved_success'));
    }

    /**
     * @param int $id
     * @param UpdateItemIssueDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemIssueDetailsRefferedBacks/{id}",
     *      summary="Update the specified ItemIssueDetailsRefferedBack in storage",
     *      tags={"ItemIssueDetailsRefferedBack"},
     *      description="Update ItemIssueDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueDetailsRefferedBack")
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
     *                  ref="#/definitions/ItemIssueDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemIssueDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemIssueDetailsRefferedBack $itemIssueDetailsRefferedBack */
        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueDetailsRefferedBack)) {
            return $this->sendError(trans('custom.item_issue_details_reffered_back_not_found'));
        }

        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($itemIssueDetailsRefferedBack->toArray(), trans('custom.itemissuedetailsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemIssueDetailsRefferedBacks/{id}",
     *      summary="Remove the specified ItemIssueDetailsRefferedBack from storage",
     *      tags={"ItemIssueDetailsRefferedBack"},
     *      description="Delete ItemIssueDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetailsRefferedBack",
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
        /** @var ItemIssueDetailsRefferedBack $itemIssueDetailsRefferedBack */
        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueDetailsRefferedBack)) {
            return $this->sendError(trans('custom.item_issue_details_reffered_back_not_found'));
        }

        $itemIssueDetailsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.item_issue_details_reffered_back_deleted_successfu'));
    }

    public function getItemIssueDetailsReferBack(Request $request)
    {
        $input = $request->all();
        $itemIssueAutoID = $input['itemIssueAutoID'];
        $timesReferred = $input['timesReferred'];
        $items = ItemIssueDetailsRefferedBack::where('itemIssueAutoID', $itemIssueAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['uom_default', 'uom_issuing','item_by'])
            ->get();


        foreach ($items as $item) {

            $issueUnit = Unit::where('UnitID', $item['itemUnitOfMeasure'])->with(['unitConversion.sub_unit'])->first();

            $issueUnits = array();
            foreach ($issueUnit->unitConversion as $unit) {
                $temArray = array('value' => $unit->sub_unit->UnitID, 'label' => $unit->sub_unit->UnitShortCode);
                array_push($issueUnits, $temArray);
            }

            $item->issueUnits = $issueUnits;
        }

        return $this->sendResponse($items->toArray(), trans('custom.stock_receive_details_retrieved_successfully_1'));
    }
}
