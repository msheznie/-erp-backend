<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderMasterAPIRequest;
use App\Models\CurrencyMaster;
use App\Models\EnvelopType;
use App\Models\TenderMaster;
use App\Models\TenderType;
use App\Repositories\TenderMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderMasterController
 * @package App\Http\Controllers\API
 */

class TenderMasterAPIController extends AppBaseController
{
    /** @var  TenderMasterRepository */
    private $tenderMasterRepository;

    public function __construct(TenderMasterRepository $tenderMasterRepo)
    {
        $this->tenderMasterRepository = $tenderMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasters",
     *      summary="Get a listing of the TenderMasters.",
     *      tags={"TenderMaster"},
     *      description="Get all TenderMasters",
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
     *                  @SWG\Items(ref="#/definitions/TenderMaster")
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
        $this->tenderMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderMasters = $this->tenderMasterRepository->all();

        return $this->sendResponse($tenderMasters->toArray(), 'Tender Masters retrieved successfully');
    }

    /**
     * @param CreateTenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderMasters",
     *      summary="Store a newly created TenderMaster in storage",
     *      tags={"TenderMaster"},
     *      description="Store TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMaster")
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
     *                  ref="#/definitions/TenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderMasterAPIRequest $request)
    {
        $input = $request->all();

        $tenderMaster = $this->tenderMasterRepository->create($input);

        return $this->sendResponse($tenderMaster->toArray(), 'Tender Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasters/{id}",
     *      summary="Display the specified TenderMaster",
     *      tags={"TenderMaster"},
     *      description="Get TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
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
     *                  ref="#/definitions/TenderMaster"
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
        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        return $this->sendResponse($tenderMaster->toArray(), 'Tender Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderMasters/{id}",
     *      summary="Update the specified TenderMaster in storage",
     *      tags={"TenderMaster"},
     *      description="Update TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMaster")
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
     *                  ref="#/definitions/TenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        $tenderMaster = $this->tenderMasterRepository->update($input, $id);

        return $this->sendResponse($tenderMaster->toArray(), 'TenderMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderMasters/{id}",
     *      summary="Remove the specified TenderMaster from storage",
     *      tags={"TenderMaster"},
     *      description="Delete TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
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
        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        $tenderMaster->delete();

        return $this->sendSuccess('Tender Master deleted successfully');
    }

    public function getTenderMasterList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];



        $tenderMaster = TenderMaster::with(['tender_type','envelop_type','currency'])->where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->orWhereHas('tender_type', function ($query1) use ($search) {
                    $query1->where('name', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('envelop_type', function ($query1) use ($search) {
                    $query1->where('name', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('currency', function ($query1) use ($search) {
                    $query1->where('CurrencyName', 'LIKE', "%{$search}%");
                    $query1->orWhere('CurrencyCode', 'LIKE', "%{$search}%");
                });
                $query->orWhere('description', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($tenderMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getTenderDropDowns(Request $request)
    {
        $input = $request->all();

        $data['tenderType'] = TenderType::get();
        $data['envelopType'] = EnvelopType::get();
        $data['currency'] = CurrencyMaster::get();

        return $data;
    }
}
