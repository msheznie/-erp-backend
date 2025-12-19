<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMobileDetailAPIRequest;
use App\Http\Requests\API\UpdateMobileDetailAPIRequest;
use App\Models\MobileDetail;
use App\Repositories\MobileDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;

/**
 * Class MobileDetailController
 * @package App\Http\Controllers\API
 */

class MobileDetailAPIController extends AppBaseController
{
    /** @var  MobileDetailRepository */
    private $mobileDetailRepository;

    public function __construct(MobileDetailRepository $mobileDetailRepo)
    {
        $this->mobileDetailRepository = $mobileDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileDetails",
     *      summary="Get a listing of the MobileDetails.",
     *      tags={"MobileDetail"},
     *      description="Get all MobileDetails",
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
     *                  @SWG\Items(ref="#/definitions/MobileDetail")
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
        $this->mobileDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->mobileDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mobileDetails = $this->mobileDetailRepository->all();

        return $this->sendResponse($mobileDetails->toArray(), trans('custom.mobile_details_retrieved_successfully'));
    }

    /**
     * @param CreateMobileDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mobileDetails",
     *      summary="Store a newly created MobileDetail in storage",
     *      tags={"MobileDetail"},
     *      description="Store MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileDetail")
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
     *                  ref="#/definitions/MobileDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMobileDetailAPIRequest $request)
    {
        $input = $request->all();

        $mobileDetail = $this->mobileDetailRepository->create($input);

        return $this->sendResponse($mobileDetail->toArray(), trans('custom.mobile_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileDetails/{id}",
     *      summary="Display the specified MobileDetail",
     *      tags={"MobileDetail"},
     *      description="Get MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileDetail",
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
     *                  ref="#/definitions/MobileDetail"
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
        /** @var MobileDetail $mobileDetail */
        $mobileDetail = $this->mobileDetailRepository->findWithoutFail($id);

        if (empty($mobileDetail)) {
            return $this->sendError(trans('custom.mobile_detail_not_found'));
        }

        return $this->sendResponse($mobileDetail->toArray(), trans('custom.mobile_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMobileDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mobileDetails/{id}",
     *      summary="Update the specified MobileDetail in storage",
     *      tags={"MobileDetail"},
     *      description="Update MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileDetail")
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
     *                  ref="#/definitions/MobileDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMobileDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var MobileDetail $mobileDetail */
        $mobileDetail = $this->mobileDetailRepository->findWithoutFail($id);

        if (empty($mobileDetail)) {
            return $this->sendError(trans('custom.mobile_detail_not_found'));
        }

        $mobileDetail = $this->mobileDetailRepository->update($input, $id);

        return $this->sendResponse($mobileDetail->toArray(), trans('custom.mobiledetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mobileDetails/{id}",
     *      summary="Remove the specified MobileDetail from storage",
     *      tags={"MobileDetail"},
     *      description="Delete MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileDetail",
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
        /** @var MobileDetail $mobileDetail */
        $mobileDetail = $this->mobileDetailRepository->findWithoutFail($id);

        if (empty($mobileDetail)) {
            return $this->sendError(trans('custom.mobile_detail_not_found'));
        }

        $mobileDetail->delete();

        return $this->sendSuccess('Mobile Detail deleted successfully');
    }

    public function getAllMobileBillDetail(Request $request){
        $input = $request->all();
        $id = isset($input['id'])?$input['id']:0;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $mobileMaster = MobileDetail::where('mobilebillMasterID',$id)->with(['mobile_pool.mobile_master.employee']);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $mobileMaster = $mobileMaster->where(function ($query) use ($search) {
                $query->where('myNumber', 'LIKE', "%{$search}%")
                    ->orWhere('DestCountry', 'LIKE', "%{$search}%")
                    ->orWhere('DestNumber', 'LIKE', "%{$search}%")
                    ->orWhere('Narration', 'LIKE', "%{$search}%")
                    ->orWhereHas('mobile_pool', function ($query) use ($search){
                        $query->whereHas('mobile_master', function ($q) use ($search){
                            $q->whereHas('employee', function ($q1) use ($search){
                                $q1->where('empID', 'LIKE', "%{$search}%")
                                    ->orWhere('empName', 'LIKE', "%{$search}%");
                            });
                        });
                    });
            });
        }

        return \DataTables::eloquent($mobileMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('mobileDetailID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function downloadDetailTemplate(Request $request)
    {
        $input = $request->all();
        $disk = isset($input['companySystemID']) ? Helper::policyWiseDisk($input['companySystemID'], 'local_public') : 'local_public';
        if (Storage::disk($disk)->exists('mobile_bill_templates/detail_template.xlsx')) {
            return Storage::disk($disk)->download('mobile_bill_templates/detail_template.xlsx', 'detail_template.xlsx');
        } else {
            return $this->sendError(trans('custom.summary_template_not_found'), 500);
        }
    }
}
