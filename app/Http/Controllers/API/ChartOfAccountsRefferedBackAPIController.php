<?php
/**
 * =============================================
 * -- File Name : ChartOfAccountsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Chart Of Accounts Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - December 2018
 * -- Description : This file contains the all CRUD for Chart Of Accounts Reffered Back
 * -- REVISION HISTORY
 * -- Date: 18-December 2018 By: Fayas Description:  Added new function referBackHistoryByChartOfAccount()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChartOfAccountsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateChartOfAccountsRefferedBackAPIRequest;
use App\Models\ChartOfAccountsRefferedBack;
use App\Repositories\ChartOfAccountsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChartOfAccountsRefferedBackController
 * @package App\Http\Controllers\API
 */

class ChartOfAccountsRefferedBackAPIController extends AppBaseController
{
    /** @var  ChartOfAccountsRefferedBackRepository */
    private $chartOfAccountsRefferedBackRepository;

    public function __construct(ChartOfAccountsRefferedBackRepository $chartOfAccountsRefferedBackRepo)
    {
        $this->chartOfAccountsRefferedBackRepository = $chartOfAccountsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountsRefferedBacks",
     *      summary="Get a listing of the ChartOfAccountsRefferedBacks.",
     *      tags={"ChartOfAccountsRefferedBack"},
     *      description="Get all ChartOfAccountsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ChartOfAccountsRefferedBack")
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
        $this->chartOfAccountsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->chartOfAccountsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chartOfAccountsRefferedBacks = $this->chartOfAccountsRefferedBackRepository->all();

        return $this->sendResponse($chartOfAccountsRefferedBacks->toArray(), 'Chart Of Accounts Reffered Backs retrieved successfully');
    }

    /**
     * @param CreateChartOfAccountsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chartOfAccountsRefferedBacks",
     *      summary="Store a newly created ChartOfAccountsRefferedBack in storage",
     *      tags={"ChartOfAccountsRefferedBack"},
     *      description="Store ChartOfAccountsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountsRefferedBack")
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
     *                  ref="#/definitions/ChartOfAccountsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChartOfAccountsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $chartOfAccountsRefferedBacks = $this->chartOfAccountsRefferedBackRepository->create($input);

        return $this->sendResponse($chartOfAccountsRefferedBacks->toArray(), 'Chart Of Accounts Reffered Back saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountsRefferedBacks/{id}",
     *      summary="Display the specified ChartOfAccountsRefferedBack",
     *      tags={"ChartOfAccountsRefferedBack"},
     *      description="Get ChartOfAccountsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountsRefferedBack",
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
     *                  ref="#/definitions/ChartOfAccountsRefferedBack"
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
        /** @var ChartOfAccountsRefferedBack $chartOfAccountsRefferedBack */
        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->with(['finalApprovedBy'])->findWithoutFail($id);

        if (empty($chartOfAccountsRefferedBack)) {
            return $this->sendError('Chart Of Accounts Reffered Back not found');
        }

        return $this->sendResponse($chartOfAccountsRefferedBack->toArray(), 'Chart Of Accounts Reffered Back retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateChartOfAccountsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chartOfAccountsRefferedBacks/{id}",
     *      summary="Update the specified ChartOfAccountsRefferedBack in storage",
     *      tags={"ChartOfAccountsRefferedBack"},
     *      description="Update ChartOfAccountsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountsRefferedBack")
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
     *                  ref="#/definitions/ChartOfAccountsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChartOfAccountsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChartOfAccountsRefferedBack $chartOfAccountsRefferedBack */
        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->findWithoutFail($id);

        if (empty($chartOfAccountsRefferedBack)) {
            return $this->sendError('Chart Of Accounts Reffered Back not found');
        }

        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($chartOfAccountsRefferedBack->toArray(), 'ChartOfAccountsRefferedBack updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chartOfAccountsRefferedBacks/{id}",
     *      summary="Remove the specified ChartOfAccountsRefferedBack from storage",
     *      tags={"ChartOfAccountsRefferedBack"},
     *      description="Delete ChartOfAccountsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountsRefferedBack",
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
        /** @var ChartOfAccountsRefferedBack $chartOfAccountsRefferedBack */
        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->findWithoutFail($id);

        if (empty($chartOfAccountsRefferedBack)) {
            return $this->sendError('Chart Of Accounts Reffered Back not found');
        }

        $chartOfAccountsRefferedBack->delete();

        return $this->sendResponse($id, 'Chart Of Accounts Reffered Back deleted successfully');
    }

    public function referBackHistoryByChartOfAccount(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input,array('controlAccountsSystemID','isBank','catogaryBLorPLID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $chartOfAccount = ChartOfAccountsRefferedBack::with(['controlAccount', 'accountType'])
                                                       ->where('chartOfAccountSystemID',$input['id']);

        $search = $request->input('search.value');
        if($search){
            $chartOfAccount =   $chartOfAccount->where(function ($query) use($search) {
                $query->where('AccountCode','LIKE',"%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($chartOfAccount)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('chartOfAccountSystemIDRefferedBack', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }
}
