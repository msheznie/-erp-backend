<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateSrmDepartmentMasterAPIRequest;
use App\Http\Requests\API\UpdateSrmDepartmentMasterAPIRequest;
use App\Models\SrmDepartmentMaster;
use App\Models\SrmTenderDepartment;
use App\Repositories\SrmDepartmentMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrmDepartmentMasterController
 * @package App\Http\Controllers\API
 */

class SrmDepartmentMasterAPIController extends AppBaseController
{
    /** @var  SrmDepartmentMasterRepository */
    private $srmDepartmentMasterRepository;

    public function __construct(SrmDepartmentMasterRepository $srmDepartmentMasterRepo)
    {
        $this->srmDepartmentMasterRepository = $srmDepartmentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/srmDepartmentMasters",
     *      summary="getSrmDepartmentMasterList",
     *      tags={"SrmDepartmentMaster"},
     *      description="Get all SrmDepartmentMasters",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/SrmDepartmentMaster")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->srmDepartmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->srmDepartmentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srmDepartmentMasters = $this->srmDepartmentMasterRepository->all();

        return $this->sendResponse($srmDepartmentMasters->toArray(), 'Department retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/srmDepartmentMasters",
     *      summary="createSrmDepartmentMaster",
     *      tags={"SrmDepartmentMaster"},
     *      description="Create SrmDepartmentMaster",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SrmDepartmentMaster"
     *              ),
     *              @OA\Property(
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
        $input = $this->convertArrayToValue($input);
        $companySystemID = $request->input('companySystemID');
        $validator = \Validator::make($input, [
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $departmentExist = SrmDepartmentMaster::select('id', 'description')
            ->where('description', '=', $input['description'])->first();

        if (!empty($departmentExist)) {
            return $this->sendError(trans('srm_masters.department_description_already_exists', [
                'code' => $input['description'],
            ]));
        }

        $input['created_at'] = Carbon::now();
        $input['created_by'] = Helper::getEmployeeSystemID();
        $input['company_id'] = $companySystemID;

        $srmDepartmentMaster = $this->srmDepartmentMasterRepository->create($input);

        return $this->sendResponse($srmDepartmentMaster->toArray(), trans('srm_masters.department_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/srmDepartmentMasters/{id}",
     *      summary="getSrmDepartmentMasterItem",
     *      tags={"SrmDepartmentMaster"},
     *      description="Get SrmDepartmentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmDepartmentMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SrmDepartmentMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var SrmDepartmentMaster $srmDepartmentMaster */
        $srmDepartmentMaster = SrmDepartmentMaster::find($id);

        if (empty($srmDepartmentMaster)) {
            return $this->sendError('Department not found');
        }

        return $this->sendResponse($srmDepartmentMaster->toArray(), 'Department retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/srmDepartmentMasters/{id}",
     *      summary="updateSrmDepartmentMaster",
     *      tags={"SrmDepartmentMaster"},
     *      description="Update SrmDepartmentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmDepartmentMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SrmDepartmentMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        /** @var SrmDepartmentMaster $srmDepartmentMaster */
        $srmDepartmentMaster = SrmDepartmentMaster::find($id);

        if (empty($srmDepartmentMaster)) {
            return $this->sendError(trans('srm_masters.department_not_found'));
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $departmentExist = SrmDepartmentMaster::select('id', 'description')
            ->where('description', '=', $input['description'])
            ->where('id', '!=', $id)
            ->first();

        if (!empty($departmentExist)) {
            return $this->sendError(trans('srm_masters.department_description_already_exists', [
                'code' => $input['description'],
            ]));
        }

        $tenderDepartmentExist = SrmTenderDepartment::where('department_id', $id)->first();

        if(empty($tenderDepartmentExist)){
            $input['updated_by'] = Helper::getEmployeeSystemID();
            $input['updated_at'] = Carbon::now();

            $srmDepartmentMaster = SrmDepartmentMaster::where('id', $id)->update($input);

            return $this->sendResponse($srmDepartmentMaster, trans('srm_masters.department_updated_successfully'));
        }else{
            return $this->sendError(trans('srm_masters.department_is_already_pulled_to_tender_or_rfx'));
        }


    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/srmDepartmentMasters/{id}",
     *      summary="deleteSrmDepartmentMaster",
     *      tags={"SrmDepartmentMaster"},
     *      description="Delete SrmDepartmentMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmDepartmentMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var SrmDepartmentMaster $srmDepartmentMaster */
        $srmDepartmentMaster = $this->srmDepartmentMasterRepository->findWithoutFail($id);

        if (empty($srmDepartmentMaster)) {
            return $this->sendError('Department not found');
        }

        $srmDepartmentMaster->delete();

        return $this->sendSuccess('Department deleted successfully');
    }

    public function getAllDepartments(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'desc';
        } else {
            $sort = 'asc';
        }
        $departments = SrmDepartmentMaster::select('*')->orderBy('id', $sort);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $departments = $departments->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($departments)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }

    public function updateDepartmentStatus(Request $request){

        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $srmDepartmentMaster = SrmDepartmentMaster::find($input['id']);

        if (empty($srmDepartmentMaster)) {
            return $this->sendError(trans('srm_masters.department_not_found'));
        }

        $input['updated_by'] = Helper::getEmployeeSystemID();
        $input['updated_at'] = Carbon::now();

        $srmDepartmentMaster = SrmDepartmentMaster::where('id', $input['id'])->update($input);

        if($srmDepartmentMaster){
                return ['success' => true, 'message' => trans('srm_masters.department_updated_successfully')];
            } else {
            return ['success' => false, 'message' => trans('srm_masters.unexpected_error')];
        }

    }
}
