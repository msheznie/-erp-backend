<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCashFlowTemplateDetailAPIRequest;
use App\Http\Requests\API\UpdateCashFlowTemplateDetailAPIRequest;
use App\Models\CashFlowTemplateDetail;
use App\Models\CashFlowTemplateLink;
use App\Models\CashFlowTemplate;
use App\Models\Company;
use App\Repositories\CashFlowTemplateDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

/**
 * Class CashFlowTemplateDetailController
 * @package App\Http\Controllers\API
 */

class CashFlowTemplateDetailAPIController extends AppBaseController
{
    /** @var  CashFlowTemplateDetailRepository */
    private $cashFlowTemplateDetailRepository;

    public function __construct(CashFlowTemplateDetailRepository $cashFlowTemplateDetailRepo)
    {
        $this->cashFlowTemplateDetailRepository = $cashFlowTemplateDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowTemplateDetails",
     *      summary="Get a listing of the CashFlowTemplateDetails.",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Get all CashFlowTemplateDetails",
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
     *                  @SWG\Items(ref="#/definitions/CashFlowTemplateDetail")
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
        $this->cashFlowTemplateDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->cashFlowTemplateDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cashFlowTemplateDetails = $this->cashFlowTemplateDetailRepository->all();

        return $this->sendResponse($cashFlowTemplateDetails->toArray(), trans('custom.cash_flow_template_details_retrieved_successfully'));
    }

    /**
     * @param CreateCashFlowTemplateDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cashFlowTemplateDetails",
     *      summary="Store a newly created CashFlowTemplateDetail in storage",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Store CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplateDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplateDetail")
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
     *                  ref="#/definitions/CashFlowTemplateDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCashFlowTemplateDetailAPIRequest $request)
    {
        $input = $request->all();

        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->create($input);

        return $this->sendResponse($cashFlowTemplateDetail->toArray(), trans('custom.cash_flow_template_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowTemplateDetails/{id}",
     *      summary="Display the specified CashFlowTemplateDetail",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Get CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateDetail",
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
     *                  ref="#/definitions/CashFlowTemplateDetail"
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
        /** @var CashFlowTemplateDetail $cashFlowTemplateDetail */
        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->findWithoutFail($id);

        if (empty($cashFlowTemplateDetail)) {
            return $this->sendError(trans('custom.cash_flow_template_detail_not_found'));
        }

        return $this->sendResponse($cashFlowTemplateDetail->toArray(), trans('custom.cash_flow_template_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCashFlowTemplateDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cashFlowTemplateDetails/{id}",
     *      summary="Update the specified CashFlowTemplateDetail in storage",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Update CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplateDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplateDetail")
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
     *                  ref="#/definitions/CashFlowTemplateDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCashFlowTemplateDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['subcategory', 'gllink', 'Actions', 'DT_Row_Index', 'subcategorytot']);
        $input = $this->convertArrayToValue($input);

        if($input['proceedPaymentType'] == 1){
            $input['logicType'] = 6;
        }
        if($input['proceedPaymentType'] == 2){
            $input['logicType'] = 3;
        }

         /** @var ReportTemplateDetails $reportTemplateDetails */
        $reportTemplateDetails = $this->cashFlowTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            return $this->sendError(trans('custom.template_details_not_found'));
        }

        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->update($input, $id);

        return $this->sendResponse($cashFlowTemplateDetail->toArray(), trans('custom.cashflowtemplatedetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cashFlowTemplateDetails/{id}",
     *      summary="Remove the specified CashFlowTemplateDetail from storage",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Delete CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateDetail",
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
        DB::beginTransaction();
        try {
            /** @var ReportTemplateDetails $reportTemplateDetails */
            $reportTemplateDetails = $this->cashFlowTemplateDetailRepository->findWithoutFail($id);
            if (empty($reportTemplateDetails)) {
                return $this->sendError(trans('custom.template_details_not_found'));
            }

            $checkIsAddedToGroupTotal = CashFlowTemplateLink::where('subCategory', $id)
                                                           ->where('templateMasterID', $reportTemplateDetails->cashFlowTemplateID)
                                                           ->whereHas('template_category', function($query) {
                                                                $query->where('type', 3);
                                                           })
                                                           ->count();

            if ($checkIsAddedToGroupTotal > 0) {
                return $this->sendError(trans('custom.category_cannot_be_deleted_as_it_is_added_for_tota'));
            }


            $detID = $reportTemplateDetails->subcategory()->pluck('id')->toArray();

            foreach ($detID as $key => $value) {
                $res = $this->deleteSubCategories($value);
                if (!$res['status']) {
                    return $this->sendError($res['message']);
                }
            }

            $reportTemplateDetails->subcategory()->delete();
            $reportTemplateDetails->gllink()->delete();
            $reportTemplateDetails->subcatlink()->delete();
            if ($detID) {
                $glLink = CashFlowTemplateLink::whereIN('templateDetailID', $detID)->delete();
            }
            $reportTemplateDetails->delete();
            DB::commit();
            return $this->sendResponse($id, trans('custom.template_details_deleted_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function deleteSubCategories($categoryID)
    {
        $reportTemplateDetails = $this->cashFlowTemplateDetailRepository->findWithoutFail($categoryID);
        if (empty($reportTemplateDetails)) {
            return ['status'=> false, 'message' => trans('custom.template_details_not_found')];
        }

        $detID = $reportTemplateDetails->subcategory()->pluck('id')->toArray();

        foreach ($detID as $key => $value) {
            $res = $this->deleteSubCategories($value);
            if (!$res['status']) {
                return ['status'=> false, 'message' => $res['message']];
            }
        }

        $reportTemplateDetails->subcategory()->delete();
        $reportTemplateDetails->gllink()->delete();
        $reportTemplateDetails->subcatlink()->delete();
        if ($detID) {
            $glLink = CashFlowTemplateLink::whereIN('templateDetailID', $detID)->delete();
        }
        $reportTemplateDetails->delete();

        return ['status' => true];
    }

    public function getCashFlowTemplateDetail($id, Request $request)
    {
        $reportTemplateDetails = CashFlowTemplateDetail::selectRaw('*,0 as expanded')->with(['subcategory' => function ($q) {
            $q->with(['gllink' => function ($q) {
                $q->with('subcategory');
                $q->orderBy('sortOrder', 'asc');
            }, 'subcategory' => function ($q) {
                $q->with(['gllink' => function ($q) {
                    $q->with('subcategory');
                    $q->orderBy('sortOrder', 'asc');
                }, 'subcategory' => function ($q) {
                    $q->with(['gllink' => function ($q) {
                        $q->with('subcategory');
                        $q->orderBy('sortOrder', 'asc');
                    }, 'subcategory' => function ($q) {
                        $q->with(['gllink' => function ($q) {
                            $q->with('subcategory');
                            $q->orderBy('sortOrder', 'asc');
                        }]);
                        $q->orderBy('sortOrder', 'asc');
                    }]);
                    $q->orderBy('sortOrder', 'asc');
                }]);
                $q->orderBy('sortOrder', 'asc');
            }]);
            $q->orderBy('sortOrder', 'asc');
        }, 'subcategorytot' => function ($q) {
            $q->with('subcategory');
        }, 'gllink'])->OfMaster($id)->whereNull('masterID')->orderBy('sortOrder')->get();

        $output = ['template' => $reportTemplateDetails->toArray()];

        return $this->sendResponse($output, trans('custom.report_template_details_retrieved_successfully'));
    }

     public function addCashFlowTemplateSubCategory(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'subCategory.*.description' => 'required',
                'subCategory.*.type' => 'required',
                'subCategory.*.sortOrder' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }


            $input['createdPCID'] = gethostname();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

            $subCategory = $input['subCategory'];
            unset($input['subCategory']);
            if(count($subCategory) > 0){
                foreach ($subCategory as $val) {
                    $masterDetails = CashFlowTemplateDetail::find($input['masterID']);

                    if ($masterDetails) {
                        $input['controlAccountType'] = $masterDetails->controlAccountType;
                        $input['manualGlMapping'] = $masterDetails->manualGlMapping;
                    }

                    $input['description'] = $val['description'];
                    $input['type'] = $val['type'];
                    $input['sortOrder'] = $val['sortOrder'];
                    $input['proceedPaymentType'] = isset($val['proceedPaymentType']) ? $val['proceedPaymentType'] : null;
                    if($input['proceedPaymentType'] == 1){
                        $input['logicType'] = 6;
                    }
                    if($input['proceedPaymentType'] == 2){
                        $input['logicType'] = 3;
                    }
                    if($input['type'] == 3){
                        $input['isFinalLevel'] = 1;
                    } else {
                        $input['isFinalLevel'] = 0;
                    }

                    $reportTemplateDetails = CashFlowTemplateDetail::create($input);
                }
            }
            DB::commit();
            return $this->sendResponse([], trans('custom.report_template_details_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getCashFlowTemplateSubCat(Request $request)
    {
        $reportTemplateDetails = '';
        if ($request->isHeader == 1) {
            $reportTemplateDetails = CashFlowTemplateDetail::where('cashFlowTemplateID', $request->templateID)->whereIN('type', [2,4])->orderBy('sortOrder')->get();
            return $this->sendResponse($reportTemplateDetails->toArray(), trans('custom.report_template_details_retrieved_successfully'));
        } else {
            $reportTemplateDetails = CashFlowTemplateDetail::where('masterID', $request->masterID)->where('sortOrder', '<', $request->sortOrder)->whereIN('type', [2,4])->orderBy('sortOrder')->get();

            $this->finalLevelSubCategories = [];
            $reportTemplateDetailsFinalLevels = $this->getFinalCategoriesOfSubLevel($reportTemplateDetails, [2,4]);

            return $this->sendResponse($reportTemplateDetailsFinalLevels, trans('custom.report_template_details_retrieved_successfully'));
        }
    }

    public function getFinalCategoriesOfSubLevel($categories, $types)
    {
        foreach ($categories as $key => $value) {
            if ($value->isFinalLevel == 1) {
                $this->finalLevelSubCategories[] = $value;
            } else {
                $reportTemplateDetails = CashFlowTemplateDetail::where('masterID', $value->id)->whereIN('type', $types)->orderBy('sortOrder')->get();
                $this->getFinalCategoriesOfSubLevel($reportTemplateDetails, $types);
            }
        }

        return $this->finalLevelSubCategories;
    }
}
