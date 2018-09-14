<?php
/**
 * =============================================
 * -- File Name : MatchDocumentMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  MatchDocumentMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 13 - September 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 13-September 2018 By: Nazir Description: Added new functions named as getMatchDocumentMasterFormData() For load Master View
 * -- Date: 13-September 2018 By: Nazir Description: Added new functions named as getMatchDocumentMasterView()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMatchDocumentMasterAPIRequest;
use App\Http\Requests\API\UpdateMatchDocumentMasterAPIRequest;
use App\Models\CurrencyMaster;
use App\Models\MatchDocumentMaster;
use App\Models\Months;
use App\Models\SupplierAssigned;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\MatchDocumentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class MatchDocumentMasterController
 * @package App\Http\Controllers\API
 */
class MatchDocumentMasterAPIController extends AppBaseController
{
    /** @var  MatchDocumentMasterRepository */
    private $matchDocumentMasterRepository;

    public function __construct(MatchDocumentMasterRepository $matchDocumentMasterRepo)
    {
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/matchDocumentMasters",
     *      summary="Get a listing of the MatchDocumentMasters.",
     *      tags={"MatchDocumentMaster"},
     *      description="Get all MatchDocumentMasters",
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
     *                  @SWG\Items(ref="#/definitions/MatchDocumentMaster")
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
        $this->matchDocumentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->matchDocumentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $matchDocumentMasters = $this->matchDocumentMasterRepository->all();

        return $this->sendResponse($matchDocumentMasters->toArray(), 'Match Document Masters retrieved successfully');
    }

    /**
     * @param CreateMatchDocumentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/matchDocumentMasters",
     *      summary="Store a newly created MatchDocumentMaster in storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Store MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MatchDocumentMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MatchDocumentMaster")
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
     *                  ref="#/definitions/MatchDocumentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMatchDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        $matchDocumentMasters = $this->matchDocumentMasterRepository->create($input);

        return $this->sendResponse($matchDocumentMasters->toArray(), 'Match Document Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Display the specified MatchDocumentMaster",
     *      tags={"MatchDocumentMaster"},
     *      description="Get MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
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
     *                  ref="#/definitions/MatchDocumentMaster"
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
        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        return $this->sendResponse($matchDocumentMaster->toArray(), 'Match Document Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMatchDocumentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Update the specified MatchDocumentMaster in storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Update MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MatchDocumentMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MatchDocumentMaster")
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
     *                  ref="#/definitions/MatchDocumentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMatchDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        $matchDocumentMaster = $this->matchDocumentMasterRepository->update($input, $id);

        return $this->sendResponse($matchDocumentMaster->toArray(), 'MatchDocumentMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Remove the specified MatchDocumentMaster from storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Delete MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
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
        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        $matchDocumentMaster->delete();

        return $this->sendResponse($id, 'Match Document Master deleted successfully');
    }

    public function getMatchDocumentMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = MatchDocumentMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'suppliers' => $supplier
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getMatchDocumentMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $invMaster = MatchDocumentMaster::where('companySystemID', $input['companySystemID']);
        $invMaster->where('documentSystemID', $input['documentId']);
        $invMaster->with(['created_by' => function ($query) {
        }, 'supplier' => function ($query) {
        }, 'transactioncurrency' => function ($query) {
        }]);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('bookingDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('bookingDate', '=', $input['year']);
            }
        }

        if (array_key_exists('BPVsupplierID', $input)) {
            if ($input['BPVsupplierID'] && !is_null($input['BPVsupplierID'])) {
                $invMaster->where('BPVsupplierID', $input['BPVsupplierID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('matchingDocCode', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('supplierName', 'like', "%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($invMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('matchDocumentMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
