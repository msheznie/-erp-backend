<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeTemplateAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeTemplateAPIRequest;
use App\Models\FinalReturnIncomeTemplate;
use App\Repositories\FinalReturnIncomeTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\FinalReturnIncomeTemplateColumns;
use App\Models\FinalReturnIncomeTemplateDetails;
use App\Models\FinalReturnIncomeTemplateLinks;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Response;

/**
 * Class FinalReturnIncomeTemplateController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeTemplateAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeTemplateRepository */
    private $finalReturnIncomeTemplateRepository;

    public function __construct(FinalReturnIncomeTemplateRepository $finalReturnIncomeTemplateRepo)
    {
        $this->finalReturnIncomeTemplateRepository = $finalReturnIncomeTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplates",
     *      summary="getFinalReturnIncomeTemplateList",
     *      tags={"FinalReturnIncomeTemplate"},
     *      description="Get all FinalReturnIncomeTemplates",
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
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeTemplate")
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
        $this->finalReturnIncomeTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalReturnIncomeTemplates = $this->finalReturnIncomeTemplateRepository->all();

        return $this->sendResponse($finalReturnIncomeTemplates->toArray(), 'Final Return Income Templates retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeTemplates",
     *      summary="createFinalReturnIncomeTemplate",
     *      tags={"FinalReturnIncomeTemplate"},
     *      description="Create FinalReturnIncomeTemplate",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplate"
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
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('final_return_income_templates', 'name')
                        ->where(function ($query) use ($request) {
                            return $query->where('companySystemID', $request->companySystemID);
                        }),
                ],
               'description' => 'required|string|max:100',
            ],
            [
                'name.unique' => 'The template name already existing',
            ]
            );

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            } 
            $input['isActive'] = 1;
            $input['isDefault'] = 0;
            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

            $finalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepository->create($input);
            $this->updateDetailAndLinksOnCreate($finalReturnIncomeTemplate);
            DB::commit();
            return $this->sendResponse($finalReturnIncomeTemplate->toArray(), 'Final Return Income Template saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplates/{id}",
     *      summary="getFinalReturnIncomeTemplateItem",
     *      tags={"FinalReturnIncomeTemplate"},
     *      description="Get FinalReturnIncomeTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplate",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplate"
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
        /** @var FinalReturnIncomeTemplate $finalReturnIncomeTemplate */
        $finalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplate)) {
            return $this->sendError('Final Return Income Template not found');
        }

        return $this->sendResponse($finalReturnIncomeTemplate->toArray(), 'Final Return Income Template retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeTemplates/{id}",
     *      summary="updateFinalReturnIncomeTemplate",
     *      tags={"FinalReturnIncomeTemplate"},
     *      description="Update FinalReturnIncomeTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplate",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplate"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinalReturnIncomeTemplate $finalReturnIncomeTemplate */
        $finalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplate)) {
            return $this->sendError('Final Return Income Template not found');
        }

        $rules = [];
        $messages = [
            'name.unique' => 'The template name already existing',
        ];

        if (isset($input['name']) && $input['name'] !== $finalReturnIncomeTemplate->name) {
            $rules['name'] = 'required|string|max:50|unique:final_return_income_templates,name,' . $id;
        }

        if (isset($input['description']) && $input['description'] !== $finalReturnIncomeTemplate->description) {
            $rules['description'] = 'required|string|max:100';
        }

        if (!empty($rules)) {
            $validator = \Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }
        }


        DB::beginTransaction();
        try {
            // Handle default flag
            if (isset($input['isDefault']) && $input['isDefault']) {
                FinalReturnIncomeTemplate::where('companySystemID', $input['companySystemID'])
                    ->where('id', '!=', $id)
                    ->update(['isDefault' => false]);
            }

            $finalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepository->update($input, $id);

            DB::commit();
            return $this->sendResponse($finalReturnIncomeTemplate->toArray(), 'Final Return Income Template updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeTemplates/{id}",
     *      summary="deleteFinalReturnIncomeTemplate",
     *      tags={"FinalReturnIncomeTemplate"},
     *      description="Delete FinalReturnIncomeTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplate",
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
        /** @var FinalReturnIncomeTemplate $finalReturnIncomeTemplate */
        $finalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplate)) {
            return $this->sendError('Final Return Income Template not found');
        }

        FinalReturnIncomeTemplateDetails::where(
        ['templateMasterID' => $finalReturnIncomeTemplate->id,
        'companySystemID' => $finalReturnIncomeTemplate->companySystemID])
        ->delete();

        FinalReturnIncomeTemplateLinks::where(
        ['templateMasterID' => $finalReturnIncomeTemplate->id,
        'companySystemID' => $finalReturnIncomeTemplate->companySystemID])
        ->delete();

        FinalReturnIncomeTemplateColumns::where(
        ['templateMasterID' => $finalReturnIncomeTemplate->id,
        'companySystemID' => $finalReturnIncomeTemplate->companySystemID])
        ->delete();

        $finalReturnIncomeTemplate->delete();

        return $this->sendResponse($finalReturnIncomeTemplate,'Final Return Income Template deleted successfully');
    }

    public function getTemplateList(Request $request) {
         $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyID = $input['companyID'];

        $finalReturnIncomeTemplates = FinalReturnIncomeTemplate::ofCompany($companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $finalReturnIncomeTemplates = $finalReturnIncomeTemplates->where(function ($query) use ($search) {
                 $query->where('description', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($finalReturnIncomeTemplates)
            ->addColumn('Actions', 'Actions', "Actions")
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

    private function updateDetailAndLinksOnCreate($masterData) {
        DB::beginTransaction();
        try {
            $detailData = [
                'templateMasterID'     => $masterData->id,
                'description'          => 'Net profit or loss (as per Profit/Loss account)',
                'itemType'             => 3,
                'sectionType'          => 1,
                'sortOrder'            => 1,
                'masterID'             => null,
                'rawId'                => 1,
                'isFinalLevel'         => 1,
                'bgColor'              => null,
                'fontColor'            => '#000000',
                'companySystemID'      => $masterData->companySystemID,
                'createdPCID'          => gethostname(),
                'createdUserSystemID'  => \Helper::getEmployeeSystemID(),
                'createdUserID'        => \Helper::getEmployeeID(),
                'createdDateTime'      => now(),
            ];

            
            $details = FinalReturnIncomeTemplateDetails::create($detailData);
            
            $columns = [
                ['description' => 'Appendix'],
                ['description' => 'Row No.'],
                ['description' => 'Taxable Income/ Deduction/ Tax Due'],
                ['description' => 'Amount'],
            ];

            $columnRows = [];
            $timestamp  = now();
            $sortOrder  = 1;

            foreach ($columns as $col) {
                $columnRows[] = [
                    'templateMasterID'     => $masterData->id,
                    'description'          => $col['description'],
                    'sortOrder'            => $sortOrder,
                    'isHide'               => 0,
                    'isDefault'            => true,
                    'width'                => null,
                    'bgColor'              => null,
                    'companySystemID'      => $masterData->companySystemID,
                    'createdPCID'          => gethostname(),
                    'createdUserSystemID'  => \Helper::getEmployeeSystemID(),
                    'createdUserID'        => \Helper::getEmployeeID(),
                    'createdDateTime'      => $timestamp,
                ];
                $sortOrder++;
            }

            if (!empty($columnRows)) {
                FinalReturnIncomeTemplateColumns::insert($columnRows);
            }

            $glAccounts = ChartOfAccount::PLaccounts($masterData->companySystemID)
                ->get(['chartOfAccountSystemID','AccountCode', 'AccountDescription']);

            $linkRows   = [];
            $timestamp  = now();
            $sortOrder  = 1; 

            foreach ($glAccounts as $gl) {
                $linkRows[] = [
                    'templateMasterID'     => $masterData->id,
                    'templateDetailID'     => $details->id,
                    'sortOrder'            => $sortOrder,
                    'glAutoID'             => $gl->chartOfAccountSystemID,
                    'glCode'               => $gl->AccountCode,
                    'glDescription'        => $gl->AccountDescription,
                    'companySystemID'      => $masterData->companySystemID,
                    'createdPCID'          => gethostname(),
                    'createdUserSystemID'  => \Helper::getEmployeeSystemID(),
                    'createdUserID'        => \Helper::getEmployeeID(),
                    'createdDateTime'      => $timestamp,
                ];
                $sortOrder++;
            }

            if (!empty($linkRows)) {
               FinalReturnIncomeTemplateLinks::insert($linkRows);
            }

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function chartOfAccountsTemplate(Request $request)
    {
        $input = $request->all();

        $items = ChartOfAccountsAssigned::with(['controlAccount', 'accountType', 'allocation'])
                ->where('CompanySystemID', $input['companyID'])
                ->where('isAssigned', -1)
                ->where('isActive', 1);

        $templateDetails = FinalReturnIncomeTemplateLinks::ofTemplate($input['masterId'])
                ->where('templateDetailID', $input['detailId'])->pluck('glAutoID')->toArray();

        $items = $items->whereNotIn('chartOfAccountSystemID', array_filter($templateDetails));
        $items = $items->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }
}
