<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinalReturnIncomeTemplateDetailsAPIRequest;
use App\Http\Requests\API\UpdateFinalReturnIncomeTemplateDetailsAPIRequest;
use App\Models\FinalReturnIncomeTemplateDetails;
use App\Repositories\FinalReturnIncomeTemplateDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\FinalReturnIncomeTemplateColumns;
use App\Models\FinalReturnIncomeTemplateDefaults;
use App\Models\FinalReturnIncomeReports;
use App\Models\FinalReturnIncomeTemplateLinks;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class FinalReturnIncomeTemplateDetailsController
 * @package App\Http\Controllers\API
 */

class FinalReturnIncomeTemplateDetailsAPIController extends AppBaseController
{
    /** @var  FinalReturnIncomeTemplateDetailsRepository */
    private $finalReturnIncomeTemplateDetailsRepository;

    public function __construct(FinalReturnIncomeTemplateDetailsRepository $finalReturnIncomeTemplateDetailsRepo)
    {
        $this->finalReturnIncomeTemplateDetailsRepository = $finalReturnIncomeTemplateDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/finalReturnIncomeTemplateDetails",
     *      summary="getFinalReturnIncomeTemplateDetailsList",
     *      tags={"FinalReturnIncomeTemplateDetails"},
     *      description="Get all FinalReturnIncomeTemplateDetails",
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
     *                  @OA\Items(ref="#/definitions/FinalReturnIncomeTemplateDetails")
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
        $this->finalReturnIncomeTemplateDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->finalReturnIncomeTemplateDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $finalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepository->all();

        return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), trans('custom.final_return_income_template_details_retrieved_suc'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/finalReturnIncomeTemplateDetails",
     *      summary="createFinalReturnIncomeTemplateDetails",
     *      tags={"FinalReturnIncomeTemplateDetails"},
     *      description="Create FinalReturnIncomeTemplateDetails",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinalReturnIncomeTemplateDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $isTemplateUsed = FinalReturnIncomeReports::isTemplateUsed($input['templateMasterID']);

        if($isTemplateUsed) {
            return $this->sendError(trans('custom.template_already_used_in_a_report_and_cannot_be_mo'), 500);
        }
       
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'description' => 'required',
                'sectionType' => 'required',
                'itemType' => 'required',
                'sortOrder' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $input['companySystemID'] = $input['companySystemID'];
            $input['fontColor'] = '#000000';
            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $finalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepository->create($input);
            DB::commit();
            return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), trans('custom.final_return_income_template_details_saved_success'));
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
     *      path="/finalReturnIncomeTemplateDetails/{id}",
     *      summary="getFinalReturnIncomeTemplateDetailsItem",
     *      tags={"FinalReturnIncomeTemplateDetails"},
     *      description="Get FinalReturnIncomeTemplateDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateDetails",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateDetails"
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
        /** @var FinalReturnIncomeTemplateDetails $finalReturnIncomeTemplateDetails */
        $finalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepository->findWithoutFail($id);

        if (empty($finalReturnIncomeTemplateDetails)) {
            return $this->sendError(trans('custom.final_return_income_template_details_not_found'));
        }

        return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), trans('custom.final_return_income_template_details_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/finalReturnIncomeTemplateDetails/{id}",
     *      summary="updateFinalReturnIncomeTemplateDetails",
     *      tags={"FinalReturnIncomeTemplateDetails"},
     *      description="Update FinalReturnIncomeTemplateDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateDetails",
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
     *                  ref="#/definitions/FinalReturnIncomeTemplateDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinalReturnIncomeTemplateDetailsAPIRequest $request)
    {
        $input = $request->only([
                'templateMasterID',
                'description',
                'itemType',
                'sectionType',
                'sortOrder',
                'masterID',
                'isFinalLevel',
                'bgColor',
                'fontColor',
                'companySystemID',
            ]);
            
        $input = $this->convertArrayToValue($input);

        /** @var FinalReturnIncomeTemplateDetails $finalReturnIncomeTemplateDetails */
        $finalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepository->findWithoutFail($id);
        $isTemplateUsed = FinalReturnIncomeReports::isTemplateUsed($finalReturnIncomeTemplateDetails->templateMasterID);

        if (empty($finalReturnIncomeTemplateDetails)) {
            return $this->sendError(trans('custom.final_return_income_template_details_not_found'));
        }

        if($isTemplateUsed) {
            return $this->sendError(trans('custom.template_already_used_in_a_report_and_cannot_be_up'), 500);
        }

        $finalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), trans('custom.finalreturnincometemplatedetails_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/finalReturnIncomeTemplateDetails/{id}",
     *      summary="deleteFinalReturnIncomeTemplateDetails",
     *      tags={"FinalReturnIncomeTemplateDetails"},
     *      description="Delete FinalReturnIncomeTemplateDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinalReturnIncomeTemplateDetails",
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
        /** @var FinalReturnIncomeTemplateDetails $finalReturnIncomeTemplateDetails */
        $finalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepository->findWithoutFail($id);

        $templateMasterID = $finalReturnIncomeTemplateDetails->templateMasterID;
        $masterID         = $finalReturnIncomeTemplateDetails->masterID;
        $companySystemID  = $finalReturnIncomeTemplateDetails->companySystemID;
        $isTemplateUsed = FinalReturnIncomeReports::isTemplateUsed($templateMasterID);

        if (empty($finalReturnIncomeTemplateDetails)) {
<<<<<<< HEAD
            return $this->sendError('Final Return Income Template Details not found');
        }

        if($isTemplateUsed) {
            return $this->sendError('Template already used in a report and cannot be deleted', 500);
=======
            return $this->sendError(trans('custom.final_return_income_template_details_not_found'));
        }

        if($isTemplateUsed) {
            return $this->sendError(trans('custom.template_already_used_in_a_report_and_cannot_be_de'), 500);
>>>>>>> erp-sprint-058
        }
       

        $finalReturnIncomeTemplateDetails->delete();

        $records = FinalReturnIncomeTemplateDetails::where('templateMasterID', $templateMasterID)
            ->where('masterID', $masterID)
            ->where('companySystemID', $companySystemID)
            ->orderBy('sortOrder')
            ->get();

        foreach ($records as $index => $record) {
            $record->update(['sortOrder' => $index + 1]);
        }

<<<<<<< HEAD
        return $this->sendResponse($finalReturnIncomeTemplateDetails, 'Final Return Income Template Details deleted successfully');
=======
        return $this->sendResponse($finalReturnIncomeTemplateDetails, trans('custom.final_return_income_template_details_deleted_succe'));
>>>>>>> erp-sprint-058
    }

    public function getReportTemplateDetail($templateId, Request $request) {
        $templateDetails = FinalReturnIncomeTemplateDetails::selectRaw('*,0 as expanded')
            ->with([
                'raws' => function ($q) {
                    $q->with([
                        'gl_link' => function ($gl) {
                            $gl->with(['defaultRaw' => function ($dr) {
                                $dr->whereNotNull('id');
                            }]);
                        },
                        'raws' => function ($q) {
                            $q->with([
                                'gl_link' => function ($gl) {
                                    $gl->with(['defaultRaw' => function ($dr) {
                                        $dr->whereNotNull('id');
                                    }]);
                                },
                                'raws' => function ($q) {
                                    $q->with([
                                        'gl_link' => function ($gl) {
                                            $gl->with(['defaultRaw' => function ($dr) {
                                                $dr->whereNotNull('id');
                                            }]);
                                        },
                                        'raws' => function ($q) {
                                            $q->with([
                                                'gl_link' => function ($gl) {
                                                    $gl->with(['defaultRaw' => function ($dr) {
                                                        $dr->whereNotNull('id');
                                                    }]);
                                                }
                                            ])->orderBy('sortOrder', 'asc');
                                        }
                                    ])->orderBy('sortOrder', 'asc');
                                }
                            ])->orderBy('sortOrder', 'asc');
                        },
                        'raw_defaults'
                    ])
                    ->where(function ($query) {
                        $query->where('itemType', '!=', 3)
                            ->orWhere('isFinalLevel', '!=', 1);
                    })
                    ->orderBy('sortOrder', 'asc');
                },
                'gl_link' => function ($gl) {
                    $gl->with(['defaultRaw' => function ($dr) {
                        $dr->whereNotNull('id');
                    }]);
                },
                'raw_defaults'
            ])
            ->ofMaster($templateId)
            ->whereNull('masterID')
            ->orderBy('sortOrder', 'asc')
            ->get();

            $templateColumns = FinalReturnIncomeTemplateColumns::ofTemplate($templateId)
                ->orderBy('sortOrder')
                ->get();

            $companySystemID = $request->query('companySystemID');
            $localCurrency = \Helper::companyCurrency($companySystemID);
            $isTemplateUsed = FinalReturnIncomeReports::isTemplateUsed($templateId);

            $output = [
                'templateDetails' => $templateDetails->toArray(), 
                'columns' => $templateColumns->toArray(),
                'localCurrency' => $localCurrency->localcurrency->CurrencyCode,
                'isTemplateUsed' => $isTemplateUsed
            ];

<<<<<<< HEAD
        return $this->sendResponse($output, 'Final Return Income Template Details retrieved successfully');
=======
        return $this->sendResponse($output, trans('custom.final_return_income_template_details_retrieved_suc'));
>>>>>>> erp-sprint-058
    }

    public function templateDetailRaw(Request $request) {
        $input = $request->all();

        $isTemplateUsed = FinalReturnIncomeReports::isTemplateUsed($input['templateMasterID']);

        if($isTemplateUsed) {
<<<<<<< HEAD
            return $this->sendError('Template already used in a report and cannot be modified', 500);
=======
            return $this->sendError(trans('custom.template_already_used_in_a_report_and_cannot_be_mo'), 500);
>>>>>>> erp-sprint-058
        }
       

        $maxSortOrder = FinalReturnIncomeTemplateDetails::where('templateMasterID', $input['templateMasterID'])
            ->where('masterID', $input['masterID'])
            ->where('companySystemID', $input['companySystemID'])
            ->max('sortOrder');

        $maxSortOrder = $maxSortOrder ? $maxSortOrder : 0;

        DB::beginTransaction();
        try {
           if ($input['itemType'] == 2 || 
                $input['itemType'] == 3 && $input['sectionType'] == 3 ||
                $input['itemType'] == 3 && $input['rawIdType'] == 2) {
                foreach ($input['rawID'] as $key => $rawTemplate) {
                    $linkData = [
                        'templateMasterID'      => $input['templateMasterID'],
                        'templateDetailID'      => $input['masterID'],
                        'sortOrder'             => $key + 1,
                        'rawId'                 => $rawTemplate['id'],
                        'companySystemID'       => $input['companySystemID'],
                        'createdPCID'           => gethostname(),
                        'createdUserID'         => \Helper::getEmployeeID(),
                        'createdUserSystemID'   => \Helper::getEmployeeSystemID(),
                        'createdDateTime'       => now(),
                    ];

                    $record = FinalReturnIncomeTemplateLinks::create($linkData);
                }
            } else {
                foreach ($input['rawID'] as $key => $rawTemplate) {
                    $maxSortOrder++;
                    $detailData = [
                        'templateMasterID'      => $input['templateMasterID'],
                        'masterID'              => $input['masterID'],
                        'itemType'              => 3,
                        'sectionType'           => $input['sectionType'],
                        'description'           => $rawTemplate['description'],
                        'sortOrder'             => $maxSortOrder,
                        'rawId'                 => $rawTemplate['id'],
                        'rawIdType'             => $rawTemplate['sectionType'],
                        'companySystemID'       => $input['companySystemID'],
                        'fontColor'             => '#000000',
                        'bgColor'               => null,
                        'createdPCID'           => gethostname(),
                        'createdUserID'         => \Helper::getEmployeeID(),
                        'createdUserSystemID'   => \Helper::getEmployeeSystemID(),
                        'createdDateTime'       => now(),
                    ];

                    $record = $this->finalReturnIncomeTemplateDetailsRepository->create($detailData);
                }
            }
             DB::commit();
            return $this->sendResponse(
                $record->toArray(),
<<<<<<< HEAD
                'Final Return Income Template record(s) saved successfully'
=======
                trans('custom.final_return_income_template_records_saved_success')
>>>>>>> erp-sprint-058
            );
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
