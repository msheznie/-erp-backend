<?php
/**
=============================================
-- File Name : ErpItemLedgerAPIController.php
-- Project Name : ERP
-- Module Name :  ERP Item Leger
-- Author : Desh Dilshan
-- Create date : 30 - May 2018
-- Description : This file contains the all CRUD for Item Ledger
-- REVISION HISTORY
 * * -- Date: 31-May 2018 By: Desh Description: Added new functions named as getErpLedgerByFilter(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpItemLedgerAPIRequest;
use App\Http\Requests\API\UpdateErpItemLedgerAPIRequest;
use App\Models\ErpItemLedger;
use App\Repositories\ErpItemLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpItemLedgerController
 * @package App\Http\Controllers\API
 */

class ErpItemLedgerAPIController extends AppBaseController
{
    /** @var  ErpItemLedgerRepository */
    private $erpItemLedgerRepository;

    public function __construct(ErpItemLedgerRepository $erpItemLedgerRepo)
    {
        $this->erpItemLedgerRepository = $erpItemLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpItemLedgers",
     *      summary="Get a listing of the ErpItemLedgers.",
     *      tags={"ErpItemLedger"},
     *      description="Get all ErpItemLedgers",
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
     *                  @SWG\Items(ref="#/definitions/ErpItemLedger")
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
        $this->erpItemLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->erpItemLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpItemLedgers = $this->erpItemLedgerRepository->all();

        return $this->sendResponse($erpItemLedgers->toArray(), 'Erp Item Ledgers retrieved successfully');
    }

    /**
     * @param CreateErpItemLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpItemLedgers",
     *      summary="Store a newly created ErpItemLedger in storage",
     *      tags={"ErpItemLedger"},
     *      description="Store ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpItemLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpItemLedger")
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
     *                  ref="#/definitions/ErpItemLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */

    public function store(CreateErpItemLedgerAPIRequest $request)
    {
        $input = $request->all();

        $erpItemLedgers = $this->erpItemLedgerRepository->create($input);

        return $this->sendResponse($erpItemLedgers->toArray(), 'Erp Item Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpItemLedgers/{id}",
     *      summary="Display the specified ErpItemLedger",
     *      tags={"ErpItemLedger"},
     *      description="Get ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpItemLedger",
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
     *                  ref="#/definitions/ErpItemLedger"
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
        /** @var ErpItemLedger $erpItemLedger */
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            return $this->sendError('Erp Item Ledger not found');
        }

        return $this->sendResponse($erpItemLedger->toArray(), 'Erp Item Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateErpItemLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpItemLedgers/{id}",
     *      summary="Update the specified ErpItemLedger in storage",
     *      tags={"ErpItemLedger"},
     *      description="Update ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpItemLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpItemLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpItemLedger")
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
     *                  ref="#/definitions/ErpItemLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpItemLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpItemLedger $erpItemLedger */
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            return $this->sendError('Erp Item Ledger not found');
        }

        $erpItemLedger = $this->erpItemLedgerRepository->update($input, $id);

        return $this->sendResponse($erpItemLedger->toArray(), 'ErpItemLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpItemLedgers/{id}",
     *      summary="Remove the specified ErpItemLedger from storage",
     *      tags={"ErpItemLedger"},
     *      description="Delete ErpItemLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpItemLedger",
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
        /** @var ErpItemLedger $erpItemLedger */
        $erpItemLedger = $this->erpItemLedgerRepository->findWithoutFail($id);

        if (empty($erpItemLedger)) {
            return $this->sendError('Erp Item Ledger not found');
        }

        $erpItemLedger->delete();

        return $this->sendResponse($id, 'Erp Item Ledger deleted successfully');
    }

    public function getErpLedger(Request $request){

        $item = ErpItemLedger::select('companySystemID','itemSystemCode','itemPrimaryCode','itemDescription')
            ->where('companySystemID',$request->selectedCompanyId)
            ->groupBy('companySystemID','itemSystemCode')
            ->get();

        $document = DB::table('erp_itemledger')
            ->join('erp_documentmaster', 'erp_itemledger.documentSystemID', '=', 'erp_documentmaster.documentSystemID')
            ->select('erp_itemledger.companySystemID', 'erp_itemledger.documentSystemID', 'erp_documentmaster.documentDescription')
            ->where('erp_itemledger.companySystemID',$request->selectedCompanyId)
            ->groupBy('erp_itemledger.companySystemID','erp_itemledger.documentSystemID')
            ->get();

        $warehouse = DB::table('erp_itemledger')
            ->join('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
            ->select('erp_itemledger.companySystemID', 'erp_itemledger.wareHouseSystemCode', 'warehousemaster.wareHouseDescription')
            ->where('erp_itemledger.companySystemID',$request->selectedCompanyId)
            ->groupBy('erp_itemledger.companySystemID','erp_itemledger.wareHouseSystemCode')
            ->get();

        $output = array(
            'item' => $item,
            'document' => $document,
            'warehouse' => $warehouse
        );
        return $this->sendResponse($output, 'Supplier Master retrieved successfully');
    }

    public function getErpLedgerByFilter(Request $request){

            $warehouse = DB::table('erp_itemledger')
                ->leftJoin('units', 'erp_itemledger.unitOfMeasure', '=', 'units.UnitID')
                ->leftJoin('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
                ->leftJoin('employees', 'erp_itemledger.createdUserSystemID', '=', 'employees.employeeSystemID')
                ->leftJoin('erp_documentmaster', 'erp_itemledger.documentSystemID', '=', 'erp_documentmaster.documentSystemID')
                ->join('companymaster', 'erp_itemledger.companySystemID', '=', 'companymaster.companySystemID')
                ->leftJoin('currencymaster', 'erp_itemledger.wacLocalCurrencyID', '=', 'currencymaster.currencyID')
                ->leftJoin('currencymaster AS currencymaster_1', 'erp_itemledger.wacRptCurrencyID', '=', 'currencymaster_1.currencyID')
                ->join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
                ->selectRaw('erp_itemledger.companyID,
                            companymaster.CompanyName,
                            erp_itemledger.documentID,
                            erp_documentmaster.documentDescription,
                            erp_itemledger.documentCode,
                            erp_itemledger.itemPrimaryCode,
                            itemmaster.secondaryItemCode,
                            erp_itemledger.itemDescription,
                            erp_itemledger.unitOfMeasure,
                            erp_itemledger.inOutQty,
                            erp_itemledger.comments,
                            erp_itemledger.transactionDate,
                            units.UnitShortCode,
                            warehousemaster.wareHouseDescription,
                            employees.empName,
                            currencymaster.CurrencyName AS LocalCurrency,
                            erp_itemledger.wacLocal,
                            (erp_itemledger.inOutQty*erp_itemledger.wacLocal) as TotalWacLocal,
                            currencymaster_1.CurrencyName as RepCurrency,
                            erp_itemledger.wacRpt,
                            (erp_itemledger.inOutQty*erp_itemledger.wacRpt) as TotalWacRpt')
                ->where('erp_itemledger.companySystemID',$request->selectedCompanyId)
                ->groupBy('erp_itemledger.companySystemID','erp_itemledger.wareHouseSystemCode')
                ->get();


    }

}
