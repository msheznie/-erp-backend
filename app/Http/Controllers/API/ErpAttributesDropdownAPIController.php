<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpAttributesDropdownAPIRequest;
use App\Http\Requests\API\UpdateErpAttributesDropdownAPIRequest;
use App\Models\ErpAttributesDropdown;
use App\Repositories\ErpAttributesDropdownRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class ErpAttributesDropdownController
 * @package App\Http\Controllers\API
 */

class ErpAttributesDropdownAPIController extends AppBaseController
{
    /** @var  ErpAttributesDropdownRepository */
    private $erpAttributesDropdownRepository;

    public function __construct(ErpAttributesDropdownRepository $erpAttributesDropdownRepo)
    {
        $this->erpAttributesDropdownRepository = $erpAttributesDropdownRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpAttributesDropdowns",
     *      summary="Get a listing of the ErpAttributesDropdowns.",
     *      tags={"ErpAttributesDropdown"},
     *      description="Get all ErpAttributesDropdowns",
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
     *                  @SWG\Items(ref="#/definitions/ErpAttributesDropdown")
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
        $this->erpAttributesDropdownRepository->pushCriteria(new RequestCriteria($request));
        $this->erpAttributesDropdownRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpAttributesDropdowns = $this->erpAttributesDropdownRepository->all();

        return $this->sendResponse($erpAttributesDropdowns->toArray(), 'Erp Attributes Dropdowns retrieved successfully');
    }


    public function addDropdownData(Request $request){
        DB::beginTransaction();
        try {
            $input= $request->all();

            $descriptionValidate = ErpAttributesDropdown::where('description', $input['description'])
                                                            ->where('attributes_id', $input['attributes_id'])->get();
            if (count($descriptionValidate) > 0){
                return $this->sendError('Description Already Exists');
            }

           
            $attributes = ErpAttributesDropdown::create($input);

            
        DB::commit();
        return $this->sendResponse([], 'New Record Added Successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getDropdownData(Request $request){
        $input = $request->all();
        $attributes_id = $input[0];
        return$dropdownData = ErpAttributesDropdown::where('attributes_id',$attributes_id)->get();

        return $this->sendResponse($dropdownData, 'Record retrieved successfully');
    }

    public function getAttributesDropdownData(Request $request){
        $input = $request->all();
        $attributes_id = $input['attributes_id'];
        $attributesDropdown = ErpAttributesDropdown::where('attributes_id',$attributes_id);

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $attributesDropdown = $attributesDropdown->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($attributesDropdown)
                ->order(function ($query) use ($input) {
                    if (request()->has('order')) {
                        if ($input['order'][0]['column'] == 0) {
                            $query->orderBy('id', $input['order'][0]['dir']);
                        }
                    }
                })
                ->addIndexColumn()
                ->with('orderCondition', $sort)
                ->addColumn('Actions', 'Actions', "Actions")
                ->make(true);
    }

    /**
     * @param CreateErpAttributesDropdownAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpAttributesDropdowns",
     *      summary="Store a newly created ErpAttributesDropdown in storage",
     *      tags={"ErpAttributesDropdown"},
     *      description="Store ErpAttributesDropdown",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpAttributesDropdown that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpAttributesDropdown")
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
     *                  ref="#/definitions/ErpAttributesDropdown"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpAttributesDropdownAPIRequest $request)
    {
        $input = $request->all();

        $erpAttributesDropdown = $this->erpAttributesDropdownRepository->create($input);

        return $this->sendResponse($erpAttributesDropdown->toArray(), 'Erp Attributes Dropdown saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpAttributesDropdowns/{id}",
     *      summary="Display the specified ErpAttributesDropdown",
     *      tags={"ErpAttributesDropdown"},
     *      description="Get ErpAttributesDropdown",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributesDropdown",
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
     *                  ref="#/definitions/ErpAttributesDropdown"
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
        /** @var ErpAttributesDropdown $erpAttributesDropdown */
        $erpAttributesDropdown = $this->erpAttributesDropdownRepository->findWithoutFail($id);

        if (empty($erpAttributesDropdown)) {
            return $this->sendError('Erp Attributes Dropdown not found');
        }

        return $this->sendResponse($erpAttributesDropdown->toArray(), 'Erp Attributes Dropdown retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateErpAttributesDropdownAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpAttributesDropdowns/{id}",
     *      summary="Update the specified ErpAttributesDropdown in storage",
     *      tags={"ErpAttributesDropdown"},
     *      description="Update ErpAttributesDropdown",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributesDropdown",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpAttributesDropdown that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpAttributesDropdown")
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
     *                  ref="#/definitions/ErpAttributesDropdown"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpAttributesDropdownAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpAttributesDropdown $erpAttributesDropdown */
        $erpAttributesDropdown = $this->erpAttributesDropdownRepository->findWithoutFail($id);

        if (empty($erpAttributesDropdown)) {
            return $this->sendError('Erp Attributes Dropdown not found');
        }

        $erpAttributesDropdown = $this->erpAttributesDropdownRepository->update($input, $id);

        return $this->sendResponse($erpAttributesDropdown->toArray(), 'ErpAttributesDropdown updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpAttributesDropdowns/{id}",
     *      summary="Remove the specified ErpAttributesDropdown from storage",
     *      tags={"ErpAttributesDropdown"},
     *      description="Delete ErpAttributesDropdown",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpAttributesDropdown",
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
        /** @var ErpAttributesDropdown $erpAttributesDropdown */
        $erpAttributesDropdown = $this->erpAttributesDropdownRepository->findWithoutFail($id);

        if (empty($erpAttributesDropdown)) {
            return $this->sendError('Erp Attributes Dropdown not found');
        }

        $erpAttributesDropdown->delete();

        return $this->sendResponse([],'Erp Attributes Dropdown deleted successfully');
    }
}
