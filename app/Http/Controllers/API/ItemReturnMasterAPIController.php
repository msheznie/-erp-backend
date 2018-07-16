<?php
/**
 * =============================================
 * -- File Name : ItemReturnMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 16 - July 2018
 * -- Description : This file contains the all CRUD for Document Attachments
 * -- REVISION HISTORY
 * -- Date: 16 - July 2018 By: Fayas Description: Added new functions named as getAllMaterielReturnByCompany(),getMaterielReturnFormData()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemReturnMasterAPIRequest;
use App\Http\Requests\API\UpdateItemReturnMasterAPIRequest;
use App\Models\ItemReturnMaster;
use App\Repositories\ItemReturnMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemReturnMasterController
 * @package App\Http\Controllers\API
 */

class ItemReturnMasterAPIController extends AppBaseController
{
    /** @var  ItemReturnMasterRepository */
    private $itemReturnMasterRepository;

    public function __construct(ItemReturnMasterRepository $itemReturnMasterRepo)
    {
        $this->itemReturnMasterRepository = $itemReturnMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnMasters",
     *      summary="Get a listing of the ItemReturnMasters.",
     *      tags={"ItemReturnMaster"},
     *      description="Get all ItemReturnMasters",
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
     *                  @SWG\Items(ref="#/definitions/ItemReturnMaster")
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
        $this->itemReturnMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemReturnMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemReturnMasters = $this->itemReturnMasterRepository->all();

        return $this->sendResponse($itemReturnMasters->toArray(), 'Item Return Masters retrieved successfully');
    }


    /**
     * @param CreateItemReturnMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemReturnMasters",
     *      summary="Store a newly created ItemReturnMaster in storage",
     *      tags={"ItemReturnMaster"},
     *      description="Store ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnMaster")
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
     *                  ref="#/definitions/ItemReturnMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemReturnMasterAPIRequest $request)
    {
        $input = $request->all();

        $itemReturnMasters = $this->itemReturnMasterRepository->create($input);

        return $this->sendResponse($itemReturnMasters->toArray(), 'Item Return Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnMasters/{id}",
     *      summary="Display the specified ItemReturnMaster",
     *      tags={"ItemReturnMaster"},
     *      description="Get ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMaster",
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
     *                  ref="#/definitions/ItemReturnMaster"
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
        /** @var ItemReturnMaster $itemReturnMaster */
        $itemReturnMaster = $this->itemReturnMasterRepository->findWithoutFail($id);

        if (empty($itemReturnMaster)) {
            return $this->sendError('Item Return Master not found');
        }

        return $this->sendResponse($itemReturnMaster->toArray(), 'Item Return Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemReturnMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemReturnMasters/{id}",
     *      summary="Update the specified ItemReturnMaster in storage",
     *      tags={"ItemReturnMaster"},
     *      description="Update ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnMaster")
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
     *                  ref="#/definitions/ItemReturnMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemReturnMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemReturnMaster $itemReturnMaster */
        $itemReturnMaster = $this->itemReturnMasterRepository->findWithoutFail($id);

        if (empty($itemReturnMaster)) {
            return $this->sendError('Item Return Master not found');
        }

        $itemReturnMaster = $this->itemReturnMasterRepository->update($input, $id);

        return $this->sendResponse($itemReturnMaster->toArray(), 'ItemReturnMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemReturnMasters/{id}",
     *      summary="Remove the specified ItemReturnMaster from storage",
     *      tags={"ItemReturnMaster"},
     *      description="Delete ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMaster",
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
        /** @var ItemReturnMaster $itemReturnMaster */
        $itemReturnMaster = $this->itemReturnMasterRepository->findWithoutFail($id);

        if (empty($itemReturnMaster)) {
            return $this->sendError('Item Return Master not found');
        }

        $itemReturnMaster->delete();

        return $this->sendResponse($id, 'Item Return Master deleted successfully');
    }

    /**
     * get All Materiel Issues By Company
     * POST /getAllMaterielIssuesByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllMaterielReturnByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $itemIssueMaster = ItemIssueMaster::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'warehouse_by', 'segment_by', 'customer_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $itemIssueMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $itemIssueMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseFrom', $input)) {
            if ($input['wareHouseFrom'] && !is_null($input['wareHouseFrom'])) {
                $itemIssueMaster->where('wareHouseFrom', $input['wareHouseFrom']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('issueDate', '=', $input['year']);
            }
        }


        $itemIssueMaster = $itemIssueMaster->select(
               ['erp_itemissuemaster.itemIssueAutoID',
                'erp_itemissuemaster.itemIssueCode',
                'erp_itemissuemaster.comment',
                'erp_itemissuemaster.issueDate',
                'erp_itemissuemaster.customerSystemID',
                'erp_itemissuemaster.confirmedYN',
                'erp_itemissuemaster.approved',
                'erp_itemissuemaster.serviceLineSystemID',
                'erp_itemissuemaster.documentSystemID',
                'erp_itemissuemaster.confirmedByEmpSystemID',
                'erp_itemissuemaster.createdUserSystemID',
                'erp_itemissuemaster.confirmedDate',
                'erp_itemissuemaster.approvedDate',
                'erp_itemissuemaster.createdDateTime',
                'erp_itemissuemaster.issueRefNo',
                'erp_itemissuemaster.wareHouseFrom'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemIssueAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
