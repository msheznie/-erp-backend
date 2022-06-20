<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePortMasterAPIRequest;
use App\Http\Requests\API\UpdatePortMasterAPIRequest;
use App\Models\PortMaster;
use App\Repositories\PortMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CustomerInvoiceLogistic;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PortMasterController
 * @package App\Http\Controllers\API
 */

class PortMasterAPIController extends AppBaseController
{
    /** @var  PortMasterRepository */
    private $portMasterRepository;

    public function __construct(PortMasterRepository $portMasterRepo)
    {
        $this->portMasterRepository = $portMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/portMasters",
     *      summary="Get a listing of the PortMasters.",
     *      tags={"PortMaster"},
     *      description="Get all PortMasters",
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
     *                  @SWG\Items(ref="#/definitions/PortMaster")
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
        $this->portMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->portMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $portMasters = $this->portMasterRepository->all();

        return $this->sendResponse($portMasters->toArray(), 'Port Masters retrieved successfully');
    }

    /**
     * @param CreatePortMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/portMasters",
     *      summary="Store a newly created PortMaster in storage",
     *      tags={"PortMaster"},
     *      description="Store PortMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PortMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PortMaster")
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
     *                  ref="#/definitions/PortMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePortMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('country_id'));

        $masterData = ['port_name'=>$input['port_name'],
                        'country_id'=>$input['country_id'] ];

        if(isset($input['id'])){
            $portMaster = PortMaster::where('id', $input['id'])->update($masterData);
            return $this->sendResponse($portMaster, 'Port Master updated successfully');
        }

        $portMaster = $this->portMasterRepository->create($input);

        return $this->sendResponse($portMaster->toArray(), 'Port Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/portMasters/{id}",
     *      summary="Display the specified PortMaster",
     *      tags={"PortMaster"},
     *      description="Get PortMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PortMaster",
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
     *                  ref="#/definitions/PortMaster"
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
        /** @var PortMaster $portMaster */
        $portMaster = $this->portMasterRepository->findWithoutFail($id);

        if (empty($portMaster)) {
            return $this->sendError('Port Master not found');
        }

        return $this->sendResponse($portMaster->toArray(), 'Port Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePortMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/portMasters/{id}",
     *      summary="Update the specified PortMaster in storage",
     *      tags={"PortMaster"},
     *      description="Update PortMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PortMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PortMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PortMaster")
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
     *                  ref="#/definitions/PortMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePortMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var PortMaster $portMaster */
        $portMaster = $this->portMasterRepository->findWithoutFail($id);

        if (empty($portMaster)) {
            return $this->sendError('Port Master not found');
        }

        $portMaster = $this->portMasterRepository->update($input, $id);

        return $this->sendResponse($portMaster->toArray(), 'PortMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/portMasters/{id}",
     *      summary="Remove the specified PortMaster from storage",
     *      tags={"PortMaster"},
     *      description="Delete PortMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PortMaster",
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
        /** @var PortMaster $portMaster */
        $portMaster = $this->portMasterRepository->findWithoutFail($id);

        if (empty($portMaster)) {
            return $this->sendError('Port Master not found');
        }

        $portMaster->delete();

        return $this->sendSuccess('Port Master deleted successfully');
    }

    public function getAllPort(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $portMasters = PortMaster::where('is_deleted', 0)->with('country')->get();
        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $portMasters = $portMasters->where(function ($query) use ($search) {
                $query->where('port_name', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($portMasters)
        ->addIndexColumn()
        ->with('orderCondition', $sort)
        ->addColumn('Actions', 'Actions', "Actions")
        //->addColumn('Index', 'Index', "Index")
        ->make(true);
    }

    public function deletePort(Request $request){
        $input = $request->all();
        $portID = $input['id'];

        $customerInvoiceLogistic= CustomerInvoiceLogistic::where(function ($query) use ($portID) {
            $query->where('port_of_loading', $portID)
                  ->orWhere('port_of_discharge', $portID);
                })->first();

        if($customerInvoiceLogistic){
            return $this->sendError('This port is already used in customer invoice');
        }
        $deleteData = ['is_deleted'=>1];
        $PortMaster = PortMaster::where('id',$portID )->update($deleteData);
        return $this->sendResponse($PortMaster, 'Port Master deleted successfully');
    }


}
