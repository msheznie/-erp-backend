<?php
/**
 * =============================================
 * -- File Name : MobileNoPoolAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Mobile No Pool
 * -- Author : Mohamed Rilwan
 * -- Create date : 09 - July 2020
 * -- Description : This file contains the all CRUD for Mobile No Pool
 * -- REVISION HISTORY
 * -- Date: 09 - July 2020 By: Rilwan Description: Added new functions named as getAllMobileNo()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMobileNoPoolAPIRequest;
use App\Http\Requests\API\UpdateMobileNoPoolAPIRequest;
use App\Models\Company;
use App\Models\MobileMaster;
use App\Models\MobileNoPool;
use App\Repositories\MobileNoPoolRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MobileNoPoolController
 * @package App\Http\Controllers\API
 */

class MobileNoPoolAPIController extends AppBaseController
{
    /** @var  MobileNoPoolRepository */
    private $mobileNoPoolRepository;

    public function __construct(MobileNoPoolRepository $mobileNoPoolRepo)
    {
        $this->mobileNoPoolRepository = $mobileNoPoolRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileNoPools",
     *      summary="Get a listing of the MobileNoPools.",
     *      tags={"MobileNoPool"},
     *      description="Get all MobileNoPools",
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
     *                  @SWG\Items(ref="#/definitions/MobileNoPool")
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
        $this->mobileNoPoolRepository->pushCriteria(new RequestCriteria($request));
        $this->mobileNoPoolRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mobileNoPools = $this->mobileNoPoolRepository->all();

        return $this->sendResponse($mobileNoPools->toArray(), trans('custom.mobile_no_pools_retrieved_successfully'));
    }

    /**
     * @param CreateMobileNoPoolAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mobileNoPools",
     *      summary="Store a newly created MobileNoPool in storage",
     *      tags={"MobileNoPool"},
     *      description="Store MobileNoPool",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileNoPool that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileNoPool")
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
     *                  ref="#/definitions/MobileNoPool"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMobileNoPoolAPIRequest $request)
    {
        $input = $request->all();



        if(isset($input['companySystemID'])){
            $company = Company::find($input['companySystemID']);
            if(!empty($company)){
                $input['companyID'] = $company->CompanyID;
            }
        }

        if(isset($input['mobilenopoolID']) && $input['mobilenopoolID']){
            $messages = [
                'mobileNo.unique' => 'The mobile number is already taken.'
            ];

            $validator = \Validator::make($input, [
                'mobileNo' => ['required', Rule::unique('hrms_mobilenopool')->ignore($input['mobilenopoolID'], 'mobilenopoolID')]
            ], $messages);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $mobileNoPool = $this->mobileNoPoolRepository->findWithoutFail($input['mobilenopoolID']);
            if (empty($mobileNoPool)) {
                return $this->sendError(trans('custom.mobile_no_pool_not_found'));
            }
            $mobileNoPool = $this->mobileNoPoolRepository->update($input, $input['mobilenopoolID']);

        }else{

            $messages = [
                'mobileNo.unique' => 'The mobile number is already taken.'
            ];

            $validator = \Validator::make($input, [
                'mobileNo' => 'required|unique:hrms_mobilenopool'
            ], $messages);
            //

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $mobileNoPool = $this->mobileNoPoolRepository->create($input);
        }

        return $this->sendResponse($mobileNoPool->toArray(), trans('custom.mobile_no_pool_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileNoPools/{id}",
     *      summary="Display the specified MobileNoPool",
     *      tags={"MobileNoPool"},
     *      description="Get MobileNoPool",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileNoPool",
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
     *                  ref="#/definitions/MobileNoPool"
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
        /** @var MobileNoPool $mobileNoPool */
        $mobileNoPool = $this->mobileNoPoolRepository->findWithoutFail($id);

        if (empty($mobileNoPool)) {
            return $this->sendError(trans('custom.mobile_no_pool_not_found'));
        }

        return $this->sendResponse($mobileNoPool->toArray(), trans('custom.mobile_no_pool_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMobileNoPoolAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mobileNoPools/{id}",
     *      summary="Update the specified MobileNoPool in storage",
     *      tags={"MobileNoPool"},
     *      description="Update MobileNoPool",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileNoPool",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileNoPool that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileNoPool")
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
     *                  ref="#/definitions/MobileNoPool"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMobileNoPoolAPIRequest $request)
    {
        $input = $request->all();

        /** @var MobileNoPool $mobileNoPool */
        $mobileNoPool = $this->mobileNoPoolRepository->findWithoutFail($id);

        if (empty($mobileNoPool)) {
            return $this->sendError(trans('custom.mobile_no_not_found'));
        }

        $mobileNoPool = $this->mobileNoPoolRepository->update($input, $id);

        return $this->sendResponse($mobileNoPool->toArray(), trans('custom.mobile_no_pool_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mobileNoPools/{id}",
     *      summary="Remove the specified MobileNoPool from storage",
     *      tags={"MobileNoPool"},
     *      description="Delete MobileNoPool",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileNoPool",
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
        /** @var MobileNoPool $mobileNoPool */
        $mobileNoPool = $this->mobileNoPoolRepository->findWithoutFail($id);

        if (empty($mobileNoPool)) {
            return $this->sendError(trans('custom.mobile_no_not_found'));
        }

        $isExist = MobileMaster::where('mobileNoPoolID',$mobileNoPool->mobilenopoolID)->exists();

        if ($isExist) {
            return $this->sendError(trans('custom.you_cannot_delete_mobile_no_has_already_assigned'));
        }

        $mobileNoPool->delete();

        return $this->sendResponse([],trans('custom.mobile_no_pool_deleted_successfully'));
    }

    public function getAllMobileNo(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $mobilePool = MobileNoPool::whereNotNull('mobileNo');
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $mobilePool = $mobilePool->where(function ($query) use ($search) {
                $query->where('mobileNo', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($mobilePool)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('mobilenopoolID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
