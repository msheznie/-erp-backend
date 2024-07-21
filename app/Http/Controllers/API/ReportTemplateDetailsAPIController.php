<?php
/**
 * =============================================
 * -- File Name : ReportTemplateDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report Template
 * -- Author : Mubashir
 * -- Create date : 20 - December 2018
 * -- Description :  This file contains the all CRUD for Report template detail
 * -- REVISION HISTORY
 * -- Date: 20 - December 2018 By: Mubashir Description: Added new functions named as getReportTemplateDetail()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReportTemplateDetailsAPIRequest;
use App\Http\Requests\API\UpdateReportTemplateDetailsAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\ReportTemplate;
use App\Models\Budjetdetails;
use App\Models\ReportTemplateColumnLink;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateLinks;
use App\Repositories\ReportTemplateDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\DocumentCodeGenerate;

/**
 * Class ReportTemplateDetailsController
 * @package App\Http\Controllers\API
 */
class ReportTemplateDetailsAPIController extends AppBaseController
{
    /** @var  ReportTemplateDetailsRepository */
    private $reportTemplateDetailsRepository;
    private $finalLevelSubCategories;

    public function __construct(ReportTemplateDetailsRepository $reportTemplateDetailsRepo)
    {
        $this->reportTemplateDetailsRepository = $reportTemplateDetailsRepo;
        $this->finalLevelSubCategories = [];
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateDetails",
     *      summary="Get a listing of the ReportTemplateDetails.",
     *      tags={"ReportTemplateDetails"},
     *      description="Get all ReportTemplateDetails",
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
     *                  @SWG\Items(ref="#/definitions/ReportTemplateDetails")
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
        $this->reportTemplateDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->reportTemplateDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->all();

        return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details retrieved successfully');
    }

    /**
     * @param CreateReportTemplateDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reportTemplateDetails",
     *      summary="Store a newly created ReportTemplateDetails in storage",
     *      tags={"ReportTemplateDetails"},
     *      description="Store ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateDetails")
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
     *                  ref="#/definitions/ReportTemplateDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTemplateDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'description' => 'required',
                'prefix' => 'required',
                'serialLength' => 'required',
                'itemType' => 'required',
                'sortOrder' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $checkPrefixDuplicate = ReportTemplateDetails::where('prefix', $input['prefix'])
                                                          ->where('companyReportTemplateID', $input['companyReportTemplateID'])
                                                          ->first();
            if ($checkPrefixDuplicate) {
                return $this->sendError("Prefix already exists.", 500);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            if($input['itemType'] == 3){
                $input['categoryType'] = null;
            }

            $input['fontColor'] = '#000000';
            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails = $this->reportTemplateDetailsRepository->create($input);
            DB::commit();
            return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reportTemplateDetails/{id}",
     *      summary="Display the specified ReportTemplateDetails",
     *      tags={"ReportTemplateDetails"},
     *      description="Get ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDetails",
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
     *                  ref="#/definitions/ReportTemplateDetails"
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
        /** @var ReportTemplateDetails $reportTemplateDetails */
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            return $this->sendError('Report Template Details not found');
        }

        return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReportTemplateDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reportTemplateDetails/{id}",
     *      summary="Update the specified ReportTemplateDetails in storage",
     *      tags={"ReportTemplateDetails"},
     *      description="Update ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportTemplateDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportTemplateDetails")
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
     *                  ref="#/definitions/ReportTemplateDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTemplateDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['subcategory', 'gllink', 'Actions', 'DT_Row_Index', 'subcategorytot']);
        $input = $this->convertArrayToValue($input);

        if (isset($input['itemType']) && ($input['itemType'] == 2 || $input['itemType'] == 1)) {
            if ($input['serialLength'] == 0) {
                return $this->sendError("Serial Number length cannot be zero.", 500);
            }

            $checkPrefixDuplicate = ReportTemplateDetails::where('prefix', $input['prefix'])
                                                          ->where('detID', '!=', $input['detID'])
                                                          ->first();
            if ($checkPrefixDuplicate) {
                return $this->sendError("Prefix already exists.", 500);
            }
        }

         /** @var ReportTemplateDetails $reportTemplateDetails */
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            return $this->sendError('Report Template Details not found');
        }

        
        if (!is_null($reportTemplateDetails->masterID) && isset($input['itemType']) && ($input['itemType'] == 2 || $input['itemType'] == 1)) {
            $masterData = ReportTemplateDetails::find($reportTemplateDetails->masterID);

            if (floatval($masterData->serialLength) >= floatval($input['serialLength'])) {
                return $this->sendError("Prefix length cannot be less than or equal to it's parent category length.", 500);
            }
        }


        $firstLevels = ReportTemplateDetails::where('masterID', $id)->get();

        foreach ($firstLevels as $key1 => $value1) {
            $secondLevels = ReportTemplateDetails::where('masterID', $value1->detID)->get();
            
            foreach ($secondLevels as $key2 => $value2) {
                $secondLevels = ReportTemplateDetails::where('masterID', $value2->detID)->get();

                foreach ($secondLevels as $key3 => $value3) {
                    $thirdLevels = ReportTemplateDetails::where('masterID', $value3->detID)->get();

                    foreach ($thirdLevels as $key4 => $value4) {
                        $fouthLevels = ReportTemplateDetails::where('masterID', $value3->detID)->get();

                        foreach ($fouthLevels as $key5 => $value5) {
                            ReportTemplateDetails::where('detID', $value5->detID)->update(['controlAccountType' => $input['controlAccountType']]);
                        }
                        
                        ReportTemplateDetails::where('detID', $value4->detID)->update(['controlAccountType' => $input['controlAccountType']]);
                    }

                    ReportTemplateDetails::where('detID', $value3->detID)->update(['controlAccountType' => $input['controlAccountType']]);
                }
                ReportTemplateDetails::where('detID', $value2->detID)->update(['controlAccountType' => $input['controlAccountType']]);
            }

            ReportTemplateDetails::where('detID', $value1->detID)->update(['controlAccountType' => $input['controlAccountType']]);
        }





        $reportTemplateDetails = $this->reportTemplateDetailsRepository->update($input, $id);

        return $this->sendResponse($reportTemplateDetails->toArray(), 'ReportTemplateDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reportTemplateDetails/{id}",
     *      summary="Remove the specified ReportTemplateDetails from storage",
     *      tags={"ReportTemplateDetails"},
     *      description="Delete ReportTemplateDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReportTemplateDetails",
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
            $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);
            if (empty($reportTemplateDetails)) {
                return $this->sendError('Report Template Details not found');
            }

            $checkIsAddedToGroupTotal = ReportTemplateLinks::where('subCategory', $id)
                                                           ->where('templateMasterID', $reportTemplateDetails->companyReportTemplateID)
                                                           ->whereHas('template_category', function($query) {
                                                                $query->where('itemType', 3);
                                                           })
                                                           ->count();

            if ($checkIsAddedToGroupTotal > 0) {
                return $this->sendError('Category cannot be deleted as it is added for total calculation');
            }


            $columnLink = ReportTemplateColumnLink::whereRaw("formulaRowID LIKE '$id,%' OR formulaRowID LIKE '%,$id,%' OR formulaRowID LIKE '%,$id' OR formulaRowID = '$id'")->first();

            if ($columnLink) {
                return $this->sendError('You cannot delete this record because already this record has been added to the formula');
            }


            $glCodes = ReportTemplateLinks::where('templateDetailID',$id)
                                      ->get()
                                      ->pluck('glAutoID')
                                      ->toArray();

            $checkLinkInBudget = Budjetdetails::whereIn('chartOfAccountID', $glCodes)
                                              ->where('templateDetailID', $id)
                                              ->first();

            if ($checkLinkInBudget) {
                return $this->sendError('You cannot delete this record because chart of accounts under this category has been pulled to budget');
            }

            $detID = $reportTemplateDetails->subcategory()->pluck('detID')->toArray();

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
                $glLink = ReportTemplateLinks::whereIN('templateDetailID', $detID)->delete();
            }
            $reportTemplateDetails->delete();
            DB::commit();
            return $this->sendResponse($id, 'Report Template Details deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function deleteSubCategories($categoryID)
    {
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($categoryID);
        if (empty($reportTemplateDetails)) {
            return ['status'=> false, 'message' => 'Report Template Details not found'];
        }

        $columnLink = ReportTemplateColumnLink::whereRaw("formulaRowID LIKE '$categoryID,%' OR formulaRowID LIKE '%,$categoryID,%' OR formulaRowID LIKE '%,$categoryID' OR formulaRowID = '$categoryID'")->first();

        if ($columnLink) {
            return ['status'=> false, 'message' => 'You cannot delete this record because already this record has been added to the formula'];
        }

        $glCodes = ReportTemplateLinks::where('templateDetailID',$categoryID)
                                      ->get()
                                      ->pluck('glAutoID')
                                      ->toArray();

        $checkLinkInBudget = Budjetdetails::whereIn('chartOfAccountID', $glCodes)
                                          ->where('templateDetailID', $categoryID)
                                          ->first();

        if ($checkLinkInBudget) {
            return ['status'=> false, 'message' => 'You cannot delete this record because chart of accounts under this category has been pulled to budget'];
        }

        $detID = $reportTemplateDetails->subcategory()->pluck('detID')->toArray();

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
            $glLink = ReportTemplateLinks::whereIN('templateDetailID', $detID)->delete();
        }
        $reportTemplateDetails->delete();

        return ['status' => true];
    }

    public function getReportTemplateDetail($id, Request $request)
    {
        $reportTemplateDetails = ReportTemplateDetails::selectRaw('*,0 as expanded')->with(['subcategory' => function ($q) {
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
        }])->OfMaster($id)->whereNull('masterID')->orderBy('sortOrder')->get();

        $reportTemplateColLink = ReportTemplateColumnLink::ofTemplate($id)->orderBy('sortOrder', 'asc')->get();
        $reportTemplateMaster = ReportTemplate::find($id);

        $assignedGL = 0;
        $linkedGL = 0;
        if($reportTemplateMaster->reportID == 3){
            $assignedGL = ChartOfAccount::where('isActive', 1)->where('isApproved', 1)->count();
        }else{
            $assignedGL = ChartOfAccount::where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL)->where('isActive', 1)->where('isApproved', 1)->count();
        }
        if($reportTemplateMaster->reportID == 1) {
            $linkedGL = ReportTemplateLinks::OfTemplate($id)->whereNotNull('glAutoID')->whereHas('chartofaccount', function ($q) {
                $q->where('catogaryBLorPL','<>', 'PL');
            })->count();
        }else{
            $linkedGL = ReportTemplateLinks::OfTemplate($id)->whereNotNull('glAutoID')->count();
        }

        
        if($reportTemplateMaster->reportID == 1) {
            $linkedGL = ReportTemplateLinks::OfTemplate($id)->whereNotNull('glAutoID')->whereHas('chartofaccount', function ($q) {
                $q->where('catogaryBLorPL','<>', 'PL');
            })->get();
        }else{
            $linkedGL = ReportTemplateLinks::OfTemplate($id)->whereNotNull('glAutoID')->get();
        }

        $unAssignedGL = ChartOfAccount::where('isActive', 1)
            ->where('isApproved', 1);

        if($reportTemplateMaster->reportID != 3){
            $unAssignedGL = $unAssignedGL->where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL);
        }
        if($linkedGL){
            $linkedGLArray = $linkedGL->pluck('glAutoID');
        }
        $unAssignedGL = $unAssignedGL->whereNotIn('chartOfAccountSystemID',$linkedGLArray);



        $remainingGLCount = $unAssignedGL->count();

        $output = ['template' => $reportTemplateDetails->toArray(), 'columns' => $reportTemplateColLink->toArray(), 'remainingGLCount' => $remainingGLCount, 'columnTemplateID' => $reportTemplateMaster->columnTemplateID];

        return $this->sendResponse($output, 'Report Template Details retrieved successfully');
    }


    public function getReportTemplateSubCat(Request $request)
    {
        $reportTemplateDetails = '';
        if ($request->isHeader == 1) {
            $reportTemplateDetails = ReportTemplateDetails::where('companyReportTemplateID', $request->companyReportTemplateID)->whereIN('itemType', [2,4])->orderBy('sortOrder')->get();
            return $this->sendResponse($reportTemplateDetails->toArray(), 'Report Template Details retrieved successfully');
        } else {
            $reportTemplateDetails = ReportTemplateDetails::where('masterID', $request->masterID)->where('sortOrder', '<', $request->sortOrder)->whereIN('itemType', [2,4])->orderBy('sortOrder')->get();

            $this->finalLevelSubCategories = [];
            $reportTemplateDetailsFinalLevels = $this->getFinalCategoriesOfSubLevel($reportTemplateDetails, [2,4]);

            return $this->sendResponse($reportTemplateDetailsFinalLevels, 'Report Template Details retrieved successfully');
        }

    }

    public function getFinalCategoriesOfSubLevel($categories, $itemTypes)
    {
        foreach ($categories as $key => $value) {
            if ($value->isFinalLevel == 1) {
                $this->finalLevelSubCategories[] = $value;
            } else {
                $reportTemplateDetails = ReportTemplateDetails::where('masterID', $value->detID)->whereIN('itemType', $itemTypes)->orderBy('sortOrder')->get();
                $this->getFinalCategoriesOfSubLevel($reportTemplateDetails, $itemTypes);
            }
        }

        return $this->finalLevelSubCategories;
    }

    public function addSubCategory(Request $request)
    {
        $input = $request->all();
        //$input = $this->convertArrayToValue($input);
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'subCategory.*.description' => 'required',
                'subCategory.*.itemType' => 'required',
                'subCategory.*.sortOrder' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $prefixValidate = true;
            foreach ($input['subCategory'] as $key => $value) {
                if (isset($value['itemType']) && $value['itemType'] == 2) {
                    if ((!isset($value['prefix']) || (isset($value['prefix']) && $value['prefix'] == "")) || !isset($value['serialLength']) || (isset($value['serialLength']) && $value['serialLength'] == "")) {
                        $prefixValidate = false;
                    }
                }
            }

            if (!$prefixValidate) {
                return $this->sendError("Prefix and serial number length is required to sub category", 500);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $input['fontColor'] = '#000000';
            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

            $subCategory = $input['subCategory'];
            unset($input['subCategory']);
            if(count($subCategory) > 0){
                foreach ($subCategory as $val) {
                    $masterDetails = ReportTemplateDetails::find($input['masterID']);

                    if ($masterDetails) {
                        $input['controlAccountType'] = $masterDetails->controlAccountType;
                    }

                    $input['description'] = $val['description'];
                    $input['itemType'] = $val['itemType'];
                    $input['sortOrder'] = $val['sortOrder'];
                    if($input['itemType'] == 3){
                        $input['categoryType'] = null;
                        $input['isFinalLevel'] = 1;
                        $input['prefix'] = null;
                        $input['serialLength'] = 0;
                    } else {
                        $input['isFinalLevel'] = 0;
                        $input['prefix'] = $val['prefix'];
                        $input['serialLength'] = $val['serialLength'];

                        if ($val['serialLength'] == 0) {
                            return $this->sendError("Serial Number length cannot be zero.", 500);
                        }

                        $checkPrefixDuplicate = ReportTemplateDetails::where('prefix', $val['prefix'])
                                                                       ->where('companyReportTemplateID', $input['companyReportTemplateID'])
                                                                      ->first();
                        if ($checkPrefixDuplicate) {
                            return $this->sendError("Prefix ".$val['prefix']. " cannot be duplicated.", 500);
                        }
                    }

                    $reportTemplateDetails = $this->reportTemplateDetailsRepository->create($input);
                }
            }
            DB::commit();
            return $this->sendResponse([], 'Report Template Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getUnassignedGLForReportTemplate(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $id = $input['companyReportTemplateID'];



        $reportTemplateMaster = ReportTemplate::find($id);

        if($reportTemplateMaster->reportID == 1) {
            $linkedGL = ReportTemplateLinks::OfTemplate($id)->whereNotNull('glAutoID')->whereHas('chartofaccount', function ($q) {
                $q->where('catogaryBLorPL','<>', 'PL');
            })->get();
        }else{
            $linkedGL = ReportTemplateLinks::OfTemplate($id)->whereNotNull('glAutoID')->get();
        }

        $unAssignedGL = ChartOfAccount::where('isActive', 1)
            ->where('isApproved', 1);

        if($reportTemplateMaster->reportID != 3){
            $unAssignedGL = $unAssignedGL->where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL);
        }
        if($linkedGL){
            $linkedGLArray = $linkedGL->pluck('glAutoID');
        }
        $unAssignedGL = $unAssignedGL->whereNotIn('chartOfAccountSystemID',$linkedGLArray);
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $unAssignedGL = $unAssignedGL->where(function ($query) use ($search) {
                $query->where('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($unAssignedGL)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('chartOfAccountSystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function mirrorReportTemplateRowConfiguration(Request $request)
    {
        $input = $request->all();

        $templateID = $input['templateID'];
        $mirrorTemplateID = $input['mirrorTemplateID'];

        $reportTemplateDetails = ReportTemplateDetails::with(['subcategory' => function ($q) {
            $q->with(['gllink' => function ($q) {
                $q->with('subcategory_detail');
            }, 'subcategory'  => function ($q) {
                $q->with(['gllink' => function ($q) {
                    $q->with('subcategory_detail');
                }, 'subcategory'  => function ($q) {
                    $q->with(['gllink' => function ($q) {
                        $q->with('subcategory_detail');
                    }, 'subcategory'  => function ($q) {
                        $q->with(['gllink' => function ($q) {
                            $q->with('subcategory_detail');
                        }]);
                    }]);
                }]);
            }]);
        }, 'subcategorytot' => function ($q) {
            $q->with('subcategory_detail');
        }])->OfMaster($mirrorTemplateID)->whereNull('masterID')->get()->toArray();

        $oldReportIds = [];
        DB::beginTransaction();
        try {
            foreach ($reportTemplateDetails as $key => $value) {
                $detID = $value['detID'];
                $value['detID'] = null;
                $value['companyReportTemplateID'] = $templateID;
                $subcategory = $value['subcategory'];
                $subcategorytot = $value['subcategorytot'];
                unset($value['subcategory']);
                unset($value['subcategorytot']);

                $saveTemplateDetails = ReportTemplateDetails::create($value);
                $oldReportIds[$detID] = $saveTemplateDetails['detID'];

                foreach ($subcategory as $subKey => $subCateogryValue) {
                    $subCategoryDetID = $subCateogryValue['detID'];
                    $subCateogryValue['detID'] = null;
                    $subCateogryValue['companyReportTemplateID'] = $templateID;
                    $subCateogryValue['masterID'] = $saveTemplateDetails['detID'];
                    $gllink = $subCateogryValue['gllink'];
                    unset($subCateogryValue['gllink']);
                    $subCateogryValueArray = $subCateogryValue['subcategory'];
                    unset($subCateogryValue['subcategory']);
                    $saveSubCategory = ReportTemplateDetails::create($subCateogryValue);
                    $oldReportIds[$subCategoryDetID] = $saveSubCategory['detID'];

                    foreach ($gllink as $glKey => $glValue) {
                        $glValue['linkID'] = null;
                        $glValue['templateMasterID'] = $templateID;
                        $glValue['templateDetailID'] = $saveSubCategory['detID'];

                        $glSubCategory = $glValue['subCategory'];
                        $glSubCategoryDetail = $glValue['subcategory_detail'];
                        unset($glValue['subcategory_detail']);

                        if ($glValue['subCategory'] != null && isset($oldReportIds[$glValue['subCategory']]) && $oldReportIds[$glValue['subCategory']] != null) {
                            $glValue['subCategory'] = $oldReportIds[$glValue['subCategory']];
                        } else {
                            $glValue['subCategory'] = null;
                        }
                        $saveTemplateDetailLinks = ReportTemplateLinks::create($glValue);
                    }

                    foreach ($subCateogryValueArray as $subKey1 => $subCateogryValue1) {
                        $subCategoryDetID1 = $subCateogryValue1['detID'];
                        $subCateogryValue1['detID'] = null;
                        $subCateogryValue1['companyReportTemplateID'] = $templateID;
                        $subCateogryValue1['masterID'] = $saveSubCategory['detID'];
                        $subCateogryValue1Array = $subCateogryValue1['subcategory'];
                        unset($subCateogryValue1['subcategory']);
                        $gllink1 = $subCateogryValue1['gllink'];
                        unset($subCateogryValue1['gllink']);
                        $saveSubCategory1 = ReportTemplateDetails::create($subCateogryValue1);
                        $oldReportIds[$subCategoryDetID1] = $saveSubCategory1['detID'];

                        foreach ($gllink1 as $glKey1 => $glValue1) {
                            $glValue1['linkID'] = null;
                            $glValue1['templateMasterID'] = $templateID;
                            $glValue1['templateDetailID'] = $saveSubCategory1['detID'];

                            $glSubCategory = $glValue1['subCategory'];
                            $glSubCategoryDetail = $glValue1['subcategory_detail'];
                            unset($glValue1['subcategory_detail']);

                            if ($glValue1['subCategory'] != null && isset($oldReportIds[$glValue1['subCategory']]) && $oldReportIds[$glValue1['subCategory']] != null) {
                                $glValue1['subCategory'] = $oldReportIds[$glValue1['subCategory']];
                            } else {
                                $glValue1['subCategory'] = null;
                            }
                            $saveTemplateDetailLinks = ReportTemplateLinks::create($glValue1);
                        }

                        foreach ($subCateogryValue1Array as $subKey2 => $subCateogryValue2) {
                            $subCategoryDetID2 = $subCateogryValue2['detID'];
                            $subCateogryValue2['detID'] = null;
                            $subCateogryValue2['companyReportTemplateID'] = $templateID;
                            $subCateogryValue2['masterID'] = $saveSubCategory1['detID'];
                            $gllink2 = $subCateogryValue2['gllink'];
                            unset($subCateogryValue2['gllink']);
                            $subCateogryValue2Array = $subCateogryValue2['subcategory'];
                            unset($subCateogryValue2['subcategory']);
                            $saveSubCategory2 = ReportTemplateDetails::create($subCateogryValue2);
                            $oldReportIds[$subCategoryDetID2] = $saveSubCategory2['detID'];

                            foreach ($gllink2 as $glKey2 => $glValue2) {
                                $glValue2['linkID'] = null;
                                $glValue2['templateMasterID'] = $templateID;
                                $glValue2['templateDetailID'] = $saveSubCategory2['detID'];

                                $glSubCategory = $glValue2['subCategory'];
                                $glSubCategoryDetail = $glValue2['subcategory_detail'];
                                unset($glValue2['subcategory_detail']);

                                if ($glValue2['subCategory'] != null && isset($oldReportIds[$glValue2['subCategory']]) && $oldReportIds[$glValue2['subCategory']] != null) {
                                    $glValue2['subCategory'] = $oldReportIds[$glValue2['subCategory']];
                                } else {
                                    $glValue2['subCategory'] = null;
                                }
                                $saveTemplateDetailLinks = ReportTemplateLinks::create($glValue2);
                            }

                            foreach ($subCateogryValue2Array as $subKey2 => $subCateogryValue3) {
                                $subCategoryDetID3 = $subCateogryValue3['detID'];
                                $subCateogryValue3['detID'] = null;
                                $subCateogryValue3['companyReportTemplateID'] = $templateID;
                                $subCateogryValue3['masterID'] = $saveSubCategory2['detID'];
                                $gllink3 = $subCateogryValue3['gllink'];
                                unset($subCateogryValue3['gllink']);
                                $saveSubCategory3 = ReportTemplateDetails::create($subCateogryValue3);
                                $oldReportIds[$subCategoryDetID3] = $saveSubCategory3['detID'];

                                foreach ($gllink3 as $glKey3 => $glValue3) {
                                    $glValue3['linkID'] = null;
                                    $glValue3['templateMasterID'] = $templateID;
                                    $glValue3['templateDetailID'] = $saveSubCategory3['detID'];

                                    $glSubCategory = $glValue3['subCategory'];
                                    $glSubCategoryDetail = $glValue3['subcategory_detail'];
                                    unset($glValue3['subcategory_detail']);

                                    if ($glValue3['subCategory'] != null && isset($oldReportIds[$glValue3['subCategory']]) && $oldReportIds[$glValue3['subCategory']] != null) {
                                        $glValue3['subCategory'] = $oldReportIds[$glValue3['subCategory']];
                                    } else {
                                        $glValue3['subCategory'] = null;
                                    }
                                    $saveTemplateDetailLinks = ReportTemplateLinks::create($glValue3);
                                }
                            }
                        }
                    }
                }

                foreach ($subcategorytot as $subTotKey => $subCateogryTotValue) {
                    $subCateogryTotValue['linkID'] = null;
                    $subCateogryTotValue['templateMasterID'] = $templateID;
                    $subCateogryTotValue['templateDetailID'] = $saveTemplateDetails['detID'];

                    $glSubCategory = $subCateogryTotValue['subCategory'];
                    $glSubCategoryDetail = $subCateogryTotValue['subcategory_detail'];
                    unset($subCateogryTotValue['subcategory_detail']);

                    if ($subCateogryTotValue['subCategory'] != null && isset($oldReportIds[$subCateogryTotValue['subCategory']]) && $oldReportIds[$subCateogryTotValue['subCategory']] != null) {
                        $subCateogryTotValue['subCategory'] = $oldReportIds[$subCateogryTotValue['subCategory']];
                    } else {
                        $subCateogryTotValue['subCategory'] = null;
                    }

                    $saveTemplateDetailLinks = ReportTemplateLinks::create($subCateogryTotValue);
                }

            }
            
            DB::commit();
            return $this->sendResponse([], 'Report Template Details mirrored successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage()." Line". $exception->getLine(), 500);
        }
    }


    public function getReportTemplatesCategoryByTemplate(Request $request)
    {
        $input = $request->all();
        $reportTemplate = ReportTemplateDetails::where('companyReportTemplateID', $input['selectedReportTemplate'])
                                               ->whereDoesntHave('gllink', function($query) use ($input){
                                                    $query->where('glAutoID', $input['chartOfAccountSystemID']);
                                               })
                                               ->where('itemType', 2)
                                               ->where('isFinalLevel', 1)
                                               ->whereNotNull('masterID')
                                               ->get();
        return $this->sendResponse($reportTemplate, 'Report Template retrieved successfully');
    }

    public function getDefaultTemplateCategories(Request $request)
    {
        $input = $request->all();

        if (isset($input['controlAccountsSystemID'])) {
            $reportTemplate = ReportTemplateDetails::where('itemType', 2)
                                                   ->where('controlAccountType', $input['controlAccountsSystemID'])
                                                   ->where(function($query) {
                                                        $query->where('isFinalLevel', 1)
                                                             ->orWhereDoesntHave('subcategory');
                                                   })
                                                   ->whereHas('master', function($query) use ($input){
                                                        $query->where('reportID', $input['catogaryBLorPLID'])
                                                              ->when(!isset($input['currentTemplateDetailID']), function($query) {
                                                                    $query->where('isDefault', 1);
                                                              });
                                                   })
                                                   ->when(isset($input['currentTemplateDetailID']) && $input['currentTemplateDetailID'] > 0 , function($query) use ($input) {
                                                        $query->where('detID', '!=', $input['currentTemplateDetailID']);
                                                   })
                                                   ->when(isset($input['companyReportTemplateID']) && $input['companyReportTemplateID'] > 0 , function($query) use ($input) {
                                                        $query->where('companyReportTemplateID', $input['companyReportTemplateID']);
                                                   })
                                                   ->whereNotNull('masterID')
                                                   ->get();
            
        } else {
            $reportTemplate = [];
        }
        return $this->sendResponse($reportTemplate, 'Report Template retrieved successfully');
    }

    public function linkPandLGLCodeValidation(Request $request)
    {
        $input = $request->all();
        $glIds = [];
        $getExistinglinks = ReportTemplateLinks::where('templateDetailID', $input['detID'])
                                               ->groupBy('glAutoID')
                                               ->get();

        if (sizeof($getExistinglinks) > 0) {
            $glIds = $getExistinglinks->pluck('glAutoID');
        }

        $chartofaccount = ChartOfAccount::whereNotIn('chartOfAccountSystemID', $glIds)
                                        ->where('isApproved', 1)
                                        // ->where('isActive', 1)
                                        ->where('catogaryBLorPL', 'PL')
                                        ->count();
      
        return $this->sendResponse($chartofaccount, 'gl validated successfully');
    }

    public function linkPandLGLCode(Request $request)
    {
        $input = $request->all();
        $glIds = [];
        $getExistinglinks = ReportTemplateLinks::where('templateDetailID', $input['detID'])
                                               ->groupBy('glAutoID')
                                               ->get();

        $maxSortOrder = ReportTemplateLinks::where('templateDetailID', $input['detID'])
                                               ->max('sortOrder');

        if (sizeof($getExistinglinks) > 0) {
            $glIds = $getExistinglinks->pluck('glAutoID');
        }

        $chartofaccount = ChartOfAccount::whereNotIn('chartOfAccountSystemID', $glIds)
                                        ->where('isApproved', 1)
                                        // ->where('isActive', 1)
                                        ->where('catogaryBLorPL', 'PL')
                                        ->get();

        if (count($chartofaccount) > 0) {
            foreach ($chartofaccount as $key => $val) {
                $data3['templateMasterID'] = $input['companyReportTemplateID'];
                $data3['templateDetailID'] = $input['detID'];
                $data3['sortOrder'] = ((isset($maxSortOrder) && $maxSortOrder != null) ? $maxSortOrder : 0) + $key + 1;
                $data3['glAutoID'] = $val['chartOfAccountSystemID'];
                $data3['glCode'] = $val['AccountCode'];
                $data3['glDescription'] = $val['AccountDescription'];
                $data3['companySystemID'] = $input['companySystemID'];
                $data3['companyID'] = $input['companyID'];
                $data3['createdPCID'] = gethostname();
                $data3['createdUserID'] = \Helper::getEmployeeID();
                $data3['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                ReportTemplateLinks::create($data3);
            }

            $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $input['detID'])->update(['isFinalLevel' => 1]);
        }
      
        return $this->sendResponse([], 'gl synced successfully');
    }

    public function getChartOfAccountCode(Request $request)
    {
        $input = $request->all();

        $reportCategoryDetail = DocumentCodeGenerate::generateAccountCode($input['reportTemplateCategory']);

        if (!$reportCategoryDetail['status']) {
            return $this->sendError($reportCategoryDetail['message'], 500);
        }

        return $this->sendResponse($reportCategoryDetail['data'], 'gl code retrieved successfully');
    }
}
