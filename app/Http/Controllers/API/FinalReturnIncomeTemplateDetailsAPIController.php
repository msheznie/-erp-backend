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

        return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), 'Final Return Income Template Details retrieved successfully');
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
            return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), 'Final Return Income Template Details saved successfully');
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
            return $this->sendError('Final Return Income Template Details not found');
        }

        return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), 'Final Return Income Template Details retrieved successfully');
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

        if (empty($finalReturnIncomeTemplateDetails)) {
            return $this->sendError('Final Return Income Template Details not found');
        }

        $finalReturnIncomeTemplateDetails = $this->finalReturnIncomeTemplateDetailsRepository->update($input, $id);

        return $this->sendResponse($finalReturnIncomeTemplateDetails->toArray(), 'FinalReturnIncomeTemplateDetails updated successfully');
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

        if (empty($finalReturnIncomeTemplateDetails)) {
            return $this->sendError('Final Return Income Template Details not found');
        }

        $templateMasterID = $finalReturnIncomeTemplateDetails->templateMasterID;
        $masterID         = $finalReturnIncomeTemplateDetails->masterID;
        $companySystemID  = $finalReturnIncomeTemplateDetails->companySystemID;

        $finalReturnIncomeTemplateDetails->delete();

        $records = FinalReturnIncomeTemplateDetails::where('templateMasterID', $templateMasterID)
            ->where('masterID', $masterID)
            ->where('companySystemID', $companySystemID)
            ->orderBy('sortOrder')
            ->get();

        foreach ($records as $index => $record) {
            $record->update(['sortOrder' => $index + 1]);
        }

        return $this->sendResponse($finalReturnIncomeTemplateDetails, 'Final Return Income Template Details deleted successfully');
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
            $isTemplateUsed = FinalReturnIncomeReports::where('template_id', $templateId)->exists();

            $output = [
                'templateDetails' => $templateDetails->toArray(), 
                'columns' => $templateColumns->toArray(),
                'localCurrency' => $localCurrency->localcurrency->CurrencyCode,
                'isTemplateUsed' => $isTemplateUsed
            ];

        return $this->sendResponse($output, 'Final Return Income Template Details retrieved successfully');
    }

    public function templateDetailRaw(Request $request) {
        $input = $request->all();

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
                'Final Return Income Template record(s) saved successfully'
            );
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
