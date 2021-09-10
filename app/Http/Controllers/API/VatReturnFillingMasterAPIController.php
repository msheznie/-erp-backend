<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFillingMasterAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFillingMasterAPIRequest;
use App\Models\VatReturnFillingMaster;
use App\Repositories\VatReturnFillingMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class VatReturnFillingMasterController
 * @package App\Http\Controllers\API
 */

class VatReturnFillingMasterAPIController extends AppBaseController
{
    /** @var  VatReturnFillingMasterRepository */
    private $vatReturnFillingMasterRepository;

    public function __construct(VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->vatReturnFillingMasterRepository = $vatReturnFillingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingMasters",
     *      summary="Get a listing of the VatReturnFillingMasters.",
     *      tags={"VatReturnFillingMaster"},
     *      description="Get all VatReturnFillingMasters",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFillingMaster")
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
        $this->vatReturnFillingMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFillingMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFillingMasters = $this->vatReturnFillingMasterRepository->all();

        return $this->sendResponse($vatReturnFillingMasters->toArray(), 'Vat Return Filling Masters retrieved successfully');
    }

    /**
     * @param CreateVatReturnFillingMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFillingMasters",
     *      summary="Store a newly created VatReturnFillingMaster in storage",
     *      tags={"VatReturnFillingMaster"},
     *      description="Store VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingMaster")
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
     *                  ref="#/definitions/VatReturnFillingMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'date' => 'required',
            'companySystemID' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $categories = VatReturnFillingCategory::where('isActive', 1)
                                              ->whereNull('masterID')
                                              ->get();

        $input['date'] = Carbon::parse($input['date']);

        DB::beginTransaction();
        try {
            $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->create($input);

            if ($vatReturnFillingMaster) {
                foreach ($categories as $key => $value) {
                    $filledCategoryData = [
                        'categoryID' => $value->id,
                        'vatReturnFillingID' => $vatReturnFillingMaster->id,
                    ];

                    $saveResCategory = VatReturnFilledCategory::create($filledCategoryData);

                    if ($saveResCategory) {
                        $subCategories = VatReturnFillingCategory::where('isActive', 1)
                                                                  ->where('masterID', $value->id)
                                                                  ->get();

                        foreach ($subCategories as $key1 => $value1) {
                            $res = $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->generateFilling($input['date'], $value1->id);
                        }

                    }
                }
            }


            DB::commit();
            return $this->sendResponse($vatReturnFillingMaster->toArray(), 'Vat Return Filling Master saved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError("Error occured", 500);
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingMasters/{id}",
     *      summary="Display the specified VatReturnFillingMaster",
     *      tags={"VatReturnFillingMaster"},
     *      description="Get VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMaster",
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
     *                  ref="#/definitions/VatReturnFillingMaster"
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
        /** @var VatReturnFillingMaster $vatReturnFillingMaster */
        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMaster)) {
            return $this->sendError('Vat Return Filling Master not found');
        }

        return $this->sendResponse($vatReturnFillingMaster->toArray(), 'Vat Return Filling Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFillingMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFillingMasters/{id}",
     *      summary="Update the specified VatReturnFillingMaster in storage",
     *      tags={"VatReturnFillingMaster"},
     *      description="Update VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingMaster")
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
     *                  ref="#/definitions/VatReturnFillingMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFillingMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFillingMaster $vatReturnFillingMaster */
        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMaster)) {
            return $this->sendError('Vat Return Filling Master not found');
        }

        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->update($input, $id);

        return $this->sendResponse($vatReturnFillingMaster->toArray(), 'VatReturnFillingMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFillingMasters/{id}",
     *      summary="Remove the specified VatReturnFillingMaster from storage",
     *      tags={"VatReturnFillingMaster"},
     *      description="Delete VatReturnFillingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingMaster",
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
        /** @var VatReturnFillingMaster $vatReturnFillingMaster */
        $vatReturnFillingMaster = $this->vatReturnFillingMasterRepository->findWithoutFail($id);

        if (empty($vatReturnFillingMaster)) {
            return $this->sendError('Vat Return Filling Master not found');
        }

        $vatReturnFillingMaster->delete();

        return $this->sendSuccess('Vat Return Filling Master deleted successfully');
    }

    public function getVatReturnFillings(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'serviceLineSystemID', 'approvedYN', 'Year', 'templateMasterID', 'Year'));

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

        $results = VatReturnFillingMaster::whereIn('companySystemID', $subCompanies);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $results = $results->where(function ($query) use ($search) {
                $query->where('comment', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($results)
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
}
