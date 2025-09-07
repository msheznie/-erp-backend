<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCashFlowTemplateLinkAPIRequest;
use App\Http\Requests\API\UpdateCashFlowTemplateLinkAPIRequest;
use App\Models\CashFlowTemplateLink;
use App\Models\CashFlowTemplateDetail;
use App\Repositories\CashFlowTemplateLinkRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CashFlowTemplateLinkController
 * @package App\Http\Controllers\API
 */

class CashFlowTemplateLinkAPIController extends AppBaseController
{
    /** @var  CashFlowTemplateLinkRepository */
    private $cashFlowTemplateLinkRepository;

    public function __construct(CashFlowTemplateLinkRepository $cashFlowTemplateLinkRepo)
    {
        $this->cashFlowTemplateLinkRepository = $cashFlowTemplateLinkRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowTemplateLinks",
     *      summary="Get a listing of the CashFlowTemplateLinks.",
     *      tags={"CashFlowTemplateLink"},
     *      description="Get all CashFlowTemplateLinks",
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
     *                  @SWG\Items(ref="#/definitions/CashFlowTemplateLink")
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
        $this->cashFlowTemplateLinkRepository->pushCriteria(new RequestCriteria($request));
        $this->cashFlowTemplateLinkRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cashFlowTemplateLinks = $this->cashFlowTemplateLinkRepository->all();

        return $this->sendResponse($cashFlowTemplateLinks->toArray(), trans('custom.cash_flow_template_links_retrieved_successfully'));
    }

    /**
     * @param CreateCashFlowTemplateLinkAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cashFlowTemplateLinks",
     *      summary="Store a newly created CashFlowTemplateLink in storage",
     *      tags={"CashFlowTemplateLink"},
     *      description="Store CashFlowTemplateLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplateLink that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplateLink")
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
     *                  ref="#/definitions/CashFlowTemplateLink"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCashFlowTemplateLinkAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'glAutoID' => 'required'
        ],[ 'glAutoID.required' => 'Please select a GL code' ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $tempDetail = CashFlowTemplateLink::ofTemplate($input['templateMasterID'])->where('templateDetailID',$input['templateDetailID'])->pluck('glAutoID')->toArray();

        $finalError = array(
            'already_gl_linked' => array(),
        );
        $error_count = 0;

        if ($input['glAutoID']) {
            foreach ($input['glAutoID'] as $key => $val) {
                if (in_array($val['chartOfAccountSystemID'], $tempDetail)) {
                    array_push($finalError['already_gl_linked'], $val['AccountCode'] . ' | ' . $val['AccountDescription']);
                    $error_count++;
                }
            }
            $confirm_error = array('type' => 'already_gl_linked', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot add gl codes as it is already assigned", 500, $confirm_error);
            } else {
                foreach ($input['glAutoID'] as $key => $val) {
                    if (!in_array($val['chartOfAccountSystemID'], $tempDetail)) {
                        $data['templateMasterID'] = $input['templateMasterID'];
                        $data['templateDetailID'] = $input['templateDetailID'];
                        $data['sortOrder'] = $key + 1;
                        $data['glAutoID'] = $val['chartOfAccountSystemID'];
                        $data['glCode'] = $val['AccountCode'];
                        $data['glDescription'] = $val['AccountDescription'];
                        $data['companySystemID'] = $input['companySystemID'];
                        if ($val["controlAccounts"] == 'BSA') {
                            $data['categoryType'] = 1;
                        } else {
                            $data['categoryType'] = 2;
                        }
                        $data['createdPCID'] = gethostname();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $reportTemplateLinks = $this->cashFlowTemplateLinkRepository->create($data);
                    }
                }
            }
        }

        

        $updateTemplateDetailAsFinal = CashFlowTemplateDetail::where('id', $input['templateDetailID'])->update(['isFinalLevel' => 1]);

        $lastSortOrder = CashFlowTemplateLink::ofTemplate($input['templateMasterID'])->where('templateDetailID',$input['templateDetailID'])->orderBy('id','asc')->get();
        if(count($lastSortOrder) > 0){
            foreach ($lastSortOrder as $key => $val) {
                $data2['sortOrder'] = $key + 1;
                $reportTemplateLinks = $this->cashFlowTemplateLinkRepository->update($data2, $val->id);
            }
        }

        return $this->sendResponse([], trans('custom.cash_flow_template_link_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowTemplateLinks/{id}",
     *      summary="Display the specified CashFlowTemplateLink",
     *      tags={"CashFlowTemplateLink"},
     *      description="Get CashFlowTemplateLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateLink",
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
     *                  ref="#/definitions/CashFlowTemplateLink"
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
        /** @var CashFlowTemplateLink $cashFlowTemplateLink */
        $cashFlowTemplateLink = $this->cashFlowTemplateLinkRepository->findWithoutFail($id);

        if (empty($cashFlowTemplateLink)) {
            return $this->sendError(trans('custom.cash_flow_template_link_not_found'));
        }

        return $this->sendResponse($cashFlowTemplateLink->toArray(), trans('custom.cash_flow_template_link_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCashFlowTemplateLinkAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cashFlowTemplateLinks/{id}",
     *      summary="Update the specified CashFlowTemplateLink in storage",
     *      tags={"CashFlowTemplateLink"},
     *      description="Update CashFlowTemplateLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateLink",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplateLink that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplateLink")
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
     *                  ref="#/definitions/CashFlowTemplateLink"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCashFlowTemplateLinkAPIRequest $request)
    {
        $input = $request->all();

        /** @var CashFlowTemplateLink $cashFlowTemplateLink */
        $cashFlowTemplateLink = $this->cashFlowTemplateLinkRepository->findWithoutFail($id);

        if (empty($cashFlowTemplateLink)) {
            return $this->sendError(trans('custom.cash_flow_template_link_not_found'));
        }

        $cashFlowTemplateLink = $this->cashFlowTemplateLinkRepository->update($input, $id);

        return $this->sendResponse($cashFlowTemplateLink->toArray(), trans('custom.cashflowtemplatelink_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cashFlowTemplateLinks/{id}",
     *      summary="Remove the specified CashFlowTemplateLink from storage",
     *      tags={"CashFlowTemplateLink"},
     *      description="Delete CashFlowTemplateLink",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateLink",
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
        /** @var ReportTemplateLinks $reportTemplateLinks */
        $reportTemplateLinks = $this->cashFlowTemplateLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            return $this->sendError(trans('custom.template_links_not_found'));
        }

        $reportTemplateLinks->delete();

        $checkTemplateLinksExists = CashFlowTemplateLink::where('templateDetailID', $reportTemplateLinks->templateDetailID)->first();

        if (!$checkTemplateLinksExists) {
            $updateTemplateDetailAsFinal = CashFlowTemplateDetail::where('id', $reportTemplateLinks->templateDetailID)->update(['isFinalLevel' => 0]);
        }

        return $this->sendResponse($id, trans('custom.report_template_links_deleted_successfully'));
    }

    public function deleteAllLinkedGLCodesCashFlow(Request $request)
    {
        $input = $request->all();

        $reportTemplateLinks = CashFlowTemplateLink::where('templateDetailID',$request->templateDetailID)->delete();
        $updateTemplateDetailAsFinal = CashFlowTemplateDetail::where('id', $request->templateDetailID)->update(['isFinalLevel' => 0]);
        return $this->sendResponse([], trans('custom.report_template_links_deleted_successfully'));
    }


    public function cashFlowTemplateDetailSubCatLink(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'subCategory' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $tempDetail = CashFlowTemplateLink::where('templateDetailID', $input['templateDetailID'])->pluck('subCategory')->toArray();

        if ($input['subCategory']) {
            foreach ($input['subCategory'] as $key => $val) {
                if (!in_array($val['id'], $tempDetail)) {
                    $data['templateMasterID'] = $input['templateMasterID'];
                    $data['templateDetailID'] = $input['templateDetailID'];
                    $data['sortOrder'] = $key + 1;
                    $data['subCategory'] = $val['id'];
                    $data['companySystemID'] = $input['companySystemID'];
                    $data['createdPCID'] = gethostname();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                    $reportTemplateLinks = $this->cashFlowTemplateLinkRepository->create($data);
                }
            }
        }

        $lastSortOrder = CashFlowTemplateLink::ofTemplate($input['templateMasterID'])->where('templateDetailID',$input['templateDetailID'])->orderBy('id','asc')->get();
        if(count($lastSortOrder) > 0){
            foreach ($lastSortOrder as $key => $val) {
                $data2['sortOrder'] = $key + 1;
                $reportTemplateLinks = $this->cashFlowTemplateLinkRepository->update($data2, $val->id);
            }
        }
        return $this->sendResponse([], trans('custom.report_template_links_saved_successfully'));
    }
}
