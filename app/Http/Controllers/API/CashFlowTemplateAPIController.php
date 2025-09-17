<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCashFlowTemplateAPIRequest;
use App\Http\Requests\API\UpdateCashFlowTemplateAPIRequest;
use App\Models\CashFlowTemplate;
use App\Models\CashFlowTemplateDetail;
use App\Repositories\CashFlowTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

/**
 * Class CashFlowTemplateController
 * @package App\Http\Controllers\API
 */

class CashFlowTemplateAPIController extends AppBaseController
{
    /** @var  CashFlowTemplateRepository */
    private $cashFlowTemplateRepository;

    public function __construct(CashFlowTemplateRepository $cashFlowTemplateRepo)
    {
        $this->cashFlowTemplateRepository = $cashFlowTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowTemplates",
     *      summary="Get a listing of the CashFlowTemplates.",
     *      tags={"CashFlowTemplate"},
     *      description="Get all CashFlowTemplates",
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
     *                  @SWG\Items(ref="#/definitions/CashFlowTemplate")
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
        $this->cashFlowTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->cashFlowTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cashFlowTemplates = $this->cashFlowTemplateRepository->all();

        return $this->sendResponse($cashFlowTemplates->toArray(), trans('custom.cash_flow_templates_retrieved_successfully'));
    }

    /**
     * @param CreateCashFlowTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cashFlowTemplates",
     *      summary="Store a newly created CashFlowTemplate in storage",
     *      tags={"CashFlowTemplate"},
     *      description="Store CashFlowTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplate")
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
     *                  ref="#/definitions/CashFlowTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCashFlowTemplateAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $input['isActive'] = 1;
            $input['type'] = 1;
            $input['createdPCID'] = gethostname();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplates = $this->cashFlowTemplateRepository->create($input);

            $data['cashFlowTemplateID'] = $reportTemplates->id;
            $data['description'] = trans('custom.operating_activities');
            $data['type'] = 1;
            $data['masterID'] = null;
            $data['sortOrder'] = 1;
            $data['subExits'] = 1;
            $data['isDefault'] = 1;
            $data['logicType'] = null;
            $data['controlAccountType'] = null;
            $data['createdPCID'] = gethostname();
            $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails = CashFlowTemplateDetail::create($data);

            $data2['cashFlowTemplateID'] = $reportTemplates->id;
            $data2['description'] = trans('custom.loss_before_income_tax');
            $data2['type'] = 2;
            $data2['masterID'] = $reportTemplateDetails->id;
            $data2['sortOrder'] = 1;
            $data2['subExits'] = 0;
            $data2['logicType'] = 1;
            $data2['isDefault'] = 1;
            $data2['controlAccountType'] = 1;
            $data2['createdPCID'] = gethostname();
            $data2['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails2 = CashFlowTemplateDetail::create($data2);

            $data3['cashFlowTemplateID'] = $reportTemplates->id;
            $data3['description'] = trans('custom.adjustments_for');
            $data3['type'] = 2;
            $data3['masterID'] = $reportTemplateDetails->id;
            $data3['sortOrder'] = 2;
            $data3['subExits'] = 1;
            $data3['logicType'] = 1;
            $data3['isDefault'] = 1;
            $data3['controlAccountType'] = 1;
            $data3['createdPCID'] = gethostname();
            $data3['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails3 = CashFlowTemplateDetail::create($data3);

            $data4['cashFlowTemplateID'] = $reportTemplates->id;
            $data4['description'] = trans('custom.operating_cash_flows_before_working_capital_changes');
            $data4['type'] = 2;
            $data4['masterID'] = $reportTemplateDetails->id;
            $data4['sortOrder'] = 3;
            $data4['subExits'] = 1;
            $data4['logicType'] = 1;
            $data4['isDefault'] = 1;
            $data4['controlAccountType'] = 2;
            $data4['createdPCID'] = gethostname();
            $data4['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails4 = CashFlowTemplateDetail::create($data4);

            $data5['cashFlowTemplateID'] = $reportTemplates->id;
            $data5['description'] = trans('custom.cash_used_in_generated_from_operations');
            $data5['type'] = 3;
            $data5['isFinalLevel'] = 1;
            $data5['masterID'] = $reportTemplateDetails->id;
            $data5['sortOrder'] = 4;
            $data5['subExits'] = 0;
            $data5['logicType'] = 2;
            $data5['isDefault'] = 1;
            $data5['controlAccountType'] = null;
            $data5['createdPCID'] = gethostname();
            $data5['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails5 = CashFlowTemplateDetail::create($data5);

            $data6['cashFlowTemplateID'] = $reportTemplates->id;
            $data6['description'] = trans('custom.income_tax_paid');
            $data6['type'] = 2;
            $data6['masterID'] = $reportTemplateDetails->id;
            $data6['sortOrder'] = 5;
            $data6['subExits'] = 0;
            $data6['logicType'] = 3;
            $data6['controlAccountType'] = 2;
            $data6['isDefault'] = 1;
            $data6['manualGlMapping'] = 1;
            $data6['createdPCID'] = gethostname();
            $data6['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails6 = CashFlowTemplateDetail::create($data6);

            $data7['cashFlowTemplateID'] = $reportTemplates->id;
            $data7['description'] = trans('custom.end_of_service_benefits_paid');
            $data7['type'] = 2;
            $data7['masterID'] = $reportTemplateDetails->id;
            $data7['sortOrder'] = 6;
            $data7['subExits'] = 0;
            $data7['logicType'] = 3;
            $data7['controlAccountType'] = 2;
            $data7['isDefault'] = 1;
            $data7['manualGlMapping'] = 1;
            $data7['createdPCID'] = gethostname();
            $data7['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails7 = CashFlowTemplateDetail::create($data7);

            $data8['cashFlowTemplateID'] = $reportTemplates->id;
            $data8['description'] = trans('custom.net_cash_used_in_generated_from_operating_activities');
            $data8['type'] = 3;
            $data8['isFinalLevel'] = 1;
            $data8['masterID'] = $reportTemplateDetails->id;
            $data8['sortOrder'] = 7;
            $data8['subExits'] = 0;
            $data8['logicType'] = 2;
            $data8['isDefault'] = 1;
            $data8['controlAccountType'] = null;
            $data8['createdPCID'] = gethostname();
            $data8['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails8 = CashFlowTemplateDetail::create($data8);



            $data9['cashFlowTemplateID'] = $reportTemplates->id;
            $data9['description'] = trans('custom.investing_activities');
            $data9['type'] = 1;
            $data9['masterID'] = null;
            $data9['sortOrder'] = 2;
            $data9['subExits'] = 1;
            $data9['isDefault'] = 1;
            $data9['logicType'] = null;
            $data9['controlAccountType'] = null;
            $data9['proceedPaymentSelection'] = 1;
            $data9['manualGlMapping'] = 1;
            $data9['createdPCID'] = gethostname();
            $data9['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails9 = CashFlowTemplateDetail::create($data9);

            $data10['cashFlowTemplateID'] = $reportTemplates->id;
            $data10['description'] = trans('custom.net_cash_used_in_investing_activities');
            $data10['type'] = 3;
            $data10['isFinalLevel'] = 1;
            $data10['masterID'] = $reportTemplateDetails9->id;
            $data10['sortOrder'] = 1;
            $data10['isDefault'] = 1;
            $data10['subExits'] = 0;
            $data10['logicType'] = 2;
            $data10['controlAccountType'] = null;
            $data10['createdPCID'] = gethostname();
            $data10['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails10 = CashFlowTemplateDetail::create($data10);

            $data11['cashFlowTemplateID'] = $reportTemplates->id;
            $data11['description'] = trans('custom.financing_activities');
            $data11['type'] = 1;
            $data11['masterID'] = null;
            $data11['sortOrder'] = 3;
            $data11['subExits'] = 1;
            $data11['isDefault'] = 1;
            $data11['manualGlMapping'] = 1;
            $data11['logicType'] = null;
            $data11['controlAccountType'] = null;
            $data11['proceedPaymentSelection'] = 1;
            $data11['createdPCID'] = gethostname();
            $data11['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails11 = CashFlowTemplateDetail::create($data11);

            $data12['cashFlowTemplateID'] = $reportTemplates->id;
            $data12['description'] = trans('custom.net_cash_generated_from_used_in_financing_activities');
            $data12['type'] = 3;
            $data12['isFinalLevel'] = 1;
            $data12['masterID'] = $reportTemplateDetails11->id;
            $data12['sortOrder'] = 1;
            $data12['isDefault'] = 1;
            $data12['subExits'] = 0;
            $data12['logicType'] = 2;
            $data12['controlAccountType'] = null;
            $data12['createdPCID'] = gethostname();
            $data12['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails12 = CashFlowTemplateDetail::create($data12);

            $data13['cashFlowTemplateID'] = $reportTemplates->id;
            $data13['description'] = trans('custom.net_change_in_cash_and_cash_equivalents');
            $data13['type'] = 3;
            $data13['isFinalLevel'] = 1;
            $data13['masterID'] = null;
            $data13['sortOrder'] = 4;
            $data13['subExits'] = 0;
            $data13['logicType'] = 2;
            $data13['isDefault'] = 1;
            $data13['controlAccountType'] = null;
            $data13['createdPCID'] = gethostname();
            $data13['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails13 = CashFlowTemplateDetail::create($data13);

            $data14['cashFlowTemplateID'] = $reportTemplates->id;
            $data14['description'] = trans('custom.cash_and_cash_equivalents_at_beginning_of_the_year');
            $data14['type'] = 2;
            $data14['masterID'] = $reportTemplateDetails13->id;
            $data14['sortOrder'] = 1;
            $data14['isDefault'] = 1;
            $data14['subExits'] = 0;
            $data14['logicType'] = 4;
            $data14['controlAccountType'] = 2;
            $data14['createdPCID'] = gethostname();
            $data14['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails14 = CashFlowTemplateDetail::create($data14);

            $data15['cashFlowTemplateID'] = $reportTemplates->id;
            $data15['description'] = trans('custom.provision_for_expected_credit_losses_on_bank_balances');
            $data15['type'] = 2;
            $data15['masterID'] = $reportTemplateDetails13->id;
            $data15['sortOrder'] = 2;
            $data15['subExits'] = 0;
            $data15['logicType'] = 5;
            $data15['controlAccountType'] = 2;
            $data15['isDefault'] = 1;
            $data15['createdPCID'] = gethostname();
            $data15['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails15 = CashFlowTemplateDetail::create($data15);

            $data16['cashFlowTemplateID'] = $reportTemplates->id;
            $data16['description'] = trans('custom.cash_and_cash_equivalents_at_end_of_the_year');
            $data16['type'] = 3;
            $data16['isFinalLevel'] = 1;
            $data16['masterID'] = null;
            $data16['sortOrder'] = 5;
            $data16['subExits'] = 0;
            $data16['logicType'] = 2;
            $data16['controlAccountType'] = null;
            $data16['isDefault'] = 1;
            $data16['createdPCID'] = gethostname();
            $data16['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $reportTemplateDetails16 = CashFlowTemplateDetail::create($data16);

            DB::commit();
            return $this->sendResponse($reportTemplates->toArray(), trans('custom.report_template_saved_successfully'));
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
     *      path="/cashFlowTemplates/{id}",
     *      summary="Display the specified CashFlowTemplate",
     *      tags={"CashFlowTemplate"},
     *      description="Get CashFlowTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplate",
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
     *                  ref="#/definitions/CashFlowTemplate"
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
        /** @var CashFlowTemplate $cashFlowTemplate */
        $cashFlowTemplate = $this->cashFlowTemplateRepository->findWithoutFail($id);

        if (empty($cashFlowTemplate)) {
            return $this->sendError(trans('custom.cash_flow_template_not_found'));
        }

        return $this->sendResponse($cashFlowTemplate->toArray(), trans('custom.cash_flow_template_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCashFlowTemplateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cashFlowTemplates/{id}",
     *      summary="Update the specified CashFlowTemplate in storage",
     *      tags={"CashFlowTemplate"},
     *      description="Update CashFlowTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplate")
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
     *                  ref="#/definitions/CashFlowTemplate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCashFlowTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var CashFlowTemplate $cashFlowTemplate */
        $cashFlowTemplate = $this->cashFlowTemplateRepository->findWithoutFail($id);

        if (empty($cashFlowTemplate)) {
            return $this->sendError(trans('custom.cash_flow_template_not_found'));
        }

        $cashFlowTemplate = $this->cashFlowTemplateRepository->update($input, $id);

        return $this->sendResponse($cashFlowTemplate->toArray(), trans('custom.cashflowtemplate_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cashFlowTemplates/{id}",
     *      summary="Remove the specified CashFlowTemplate from storage",
     *      tags={"CashFlowTemplate"},
     *      description="Delete CashFlowTemplate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplate",
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
        /** @var CashFlowTemplate $cashFlowTemplate */
        $cashFlowTemplate = $this->cashFlowTemplateRepository->findWithoutFail($id);

        if (empty($cashFlowTemplate)) {
            return $this->sendError(trans('custom.cash_flow_template_not_found'));
        }

        CashFlowTemplateDetail::where('cashFlowTemplateID', $id)->delete();

        $cashFlowTemplate->delete();

        return $this->sendResponse([], trans('custom.cash_flow_template_deleted_successfully'));
    }


    public function getAllCashFlowTemplate(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyID = $input['companyID'];


        $reportTemplate = CashFlowTemplate::OfCompany($companyID);



        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $reportTemplate = $reportTemplate->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($reportTemplate)
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


    public function getCashFlowReportHeaderData(Request $request)
    {
        $input = $request->all();

        $templateData = CashFlowTemplateDetail::with(['subcategory' => function ($query) {
                                                $query->where('type', 2)
                                                      ->orderBy('sortOrder');
                                            }])->find($input['templateDetailID']);

        return $this->sendResponse($templateData, trans('custom.report_template_retrieved_successfully'));
    }
}
