<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTaxVatCategoriesAPIRequest;
use App\Http\Requests\API\UpdateTaxVatCategoriesAPIRequest;
use App\Models\FinanceItemCategoryMaster;
use App\Models\TaxVatCategories;
use App\Models\TaxVatMainCategories;
use App\Models\YesNoSelection;
use App\Repositories\TaxVatCategoriesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxVatCategoriesController
 * @package App\Http\Controllers\API
 */

class TaxVatCategoriesAPIController extends AppBaseController
{
    /** @var  TaxVatCategoriesRepository */
    private $taxVatCategoriesRepository;

    public function __construct(TaxVatCategoriesRepository $taxVatCategoriesRepo)
    {
        $this->taxVatCategoriesRepository = $taxVatCategoriesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxVatCategories",
     *      summary="Get a listing of the TaxVatCategories.",
     *      tags={"TaxVatCategories"},
     *      description="Get all TaxVatCategories",
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
     *                  @SWG\Items(ref="#/definitions/TaxVatCategories")
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
        $this->taxVatCategoriesRepository->pushCriteria(new RequestCriteria($request));
        $this->taxVatCategoriesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxVatCategories = $this->taxVatCategoriesRepository->all();

        return $this->sendResponse($taxVatCategories->toArray(), 'Tax Vat Categories retrieved successfully');
    }

    /**
     * @param CreateTaxVatCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxVatCategories",
     *      summary="Store a newly created TaxVatCategories in storage",
     *      tags={"TaxVatCategories"},
     *      description="Store TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxVatCategories that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxVatCategories")
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
     *                  ref="#/definitions/TaxVatCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxVatCategoriesAPIRequest $request)
    {
        $input = $request->all();
//
        if(!(isset($input['taxMasterAutoID']) && $input['taxMasterAutoID'])){
            return $this->sendError('Tax Master Auto ID is not found',500);
        }
        $messages = [
            'mainCategory.required' => 'Main Category is required.',
            'subCategoryDescription.required' => 'Sub Category is required.',
            'percentage.required' => 'Percentage is required.',
            'applicableOn.required' => 'Applicable On is required.',

        ];
        $validator = \Validator::make($input, [
            'mainCategory' => 'required',
            'subCategoryDescription' => 'required',
            'percentage' => 'required',
            'applicableOn' => 'required',

        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        // check duplicated subcategory
        $isDuplicated = TaxVatCategories::where('subCategoryDescription',$input['subCategoryDescription'])->where('taxMasterAutoID',$input['taxMasterAutoID'])->exists();
        if($isDuplicated){
           return $this->sendError('Subcategory is already taken',500);
        }

        $employee = Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $taxVatCategories = $this->taxVatCategoriesRepository->create($input);

        return $this->sendResponse($taxVatCategories->toArray(), 'Tax Vat Categories saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxVatCategories/{id}",
     *      summary="Display the specified TaxVatCategories",
     *      tags={"TaxVatCategories"},
     *      description="Get TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatCategories",
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
     *                  ref="#/definitions/TaxVatCategories"
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
        /** @var TaxVatCategories $taxVatCategories */
        $taxVatCategories = $this->taxVatCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatCategories)) {
            return $this->sendError('Tax Vat Categories not found');
        }

        return $this->sendResponse($taxVatCategories->toArray(), 'Tax Vat Categories retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTaxVatCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxVatCategories/{id}",
     *      summary="Update the specified TaxVatCategories in storage",
     *      tags={"TaxVatCategories"},
     *      description="Update TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatCategories",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxVatCategories that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxVatCategories")
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
     *                  ref="#/definitions/TaxVatCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxVatCategoriesAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,['main','tax','created_by']);
        $input = $this->convertArrayToSelectedValue($input, array('applicableOn', 'mainCategory'));

        /** @var TaxVatCategories $taxVatCategories */
        $taxVatCategories = $this->taxVatCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatCategories)) {
            return $this->sendError('Tax Vat Categories not found');
        }

        if(!(isset($input['taxMasterAutoID']) && $input['taxMasterAutoID'])){
            return $this->sendError('Tax Master Auto ID is not found',500);
        }
        $messages = [
            'mainCategory.required' => 'Main Category is required.',
            'subCategoryDescription.required' => 'Sub Category is required.',
            'percentage.required' => 'Percentage is required.',
            'applicableOn.required' => 'Applicable On is required.',

        ];
        $validator = \Validator::make($input, [
            'mainCategory' => 'required',
            'subCategoryDescription' => 'required',
            'percentage' => 'required',
            'applicableOn' => 'required',

        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $isDuplicated = TaxVatCategories::where('subCategoryDescription',$input['subCategoryDescription'])->where('taxMasterAutoID',$input['taxMasterAutoID'])->where('taxVatSubCategoriesAutoID','!=',$id)->exists();
        if($isDuplicated){
            return $this->sendError('Subcategory is already taken',500);
        }

        $employee = Helper::getEmployeeInfo();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $taxVatCategories = $this->taxVatCategoriesRepository->update($input, $id);

        return $this->sendResponse($taxVatCategories->toArray(), 'TaxVatCategories updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxVatCategories/{id}",
     *      summary="Remove the specified TaxVatCategories from storage",
     *      tags={"TaxVatCategories"},
     *      description="Delete TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatCategories",
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
        /** @var TaxVatCategories $taxVatCategories */
        $taxVatCategories = $this->taxVatCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatCategories)) {
            return $this->sendError('Tax Vat Categories not found');
        }

        $taxVatCategories->delete();

        return $this->sendSuccess('Tax Vat Categories deleted successfully');
    }

    public function getAllVatCategories(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $taxMasterAutoID = $request['taxMasterAutoID'];

        $vatCategories = TaxVatCategories::where('taxMasterAutoID', $taxMasterAutoID)
            ->with(['tax', 'created_by','main']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $vatCategories = $vatCategories->where(function ($query) use ($search) {
                $query->whereHas('tax', function($q)use ($search){
                    $q->where('taxShortCode','LIKE', "%{$search}%")
                    ->orWhere('taxDescription','LIKE', "%{$search}%");
                })
                ->orWhereHas('main',function($q)use ($search){
                    $q->where('mainCategoryDescription','LIKE', "%{$search}%");
                });
            });
        }

        return \DataTables::eloquent($vatCategories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('taxVatSubCategoriesAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getVatCategoriesFormData(Request $request){

        $input = $request->all();
        $main = TaxVatMainCategories::where('taxMasterAutoID',$input['taxMasterAutoID'])->get();
        $applicable = array(array('value' => 1, 'label' => 'Gross Amount'), array('value' => 2, 'label' => 'Net Amount'));
        $output = array(
            'mainCategories' => $main,
            'applicableOns' => $applicable,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
