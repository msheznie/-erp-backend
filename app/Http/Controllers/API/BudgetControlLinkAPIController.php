<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetControlLinkAPIRequest;
use App\Http\Requests\API\UpdateBudgetControlLinkAPIRequest;
use App\Models\BudgetControlLink;
use App\Repositories\BudgetControlLinkRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetControlLinkController
 * @package App\Http\Controllers\API
 */

class BudgetControlLinkAPIController extends AppBaseController
{
    /** @var  BudgetControlLinkRepository */
    private $budgetControlLinkRepository;

    public function __construct(BudgetControlLinkRepository $budgetControlLinkRepo)
    {
        $this->budgetControlLinkRepository = $budgetControlLinkRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetControlLinks",
     *      summary="getBudgetControlLinkList",
     *      tags={"BudgetControlLink"},
     *      description="Get all BudgetControlLinks",
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
     *                  @OA\Items(ref="#/definitions/BudgetControlLink")
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
        $this->budgetControlLinkRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetControlLinkRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetControlLinks = $this->budgetControlLinkRepository->all();

        return $this->sendResponse($budgetControlLinks->toArray(), 'Budget Control Links retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/budgetControlLinks",
     *      summary="createBudgetControlLink",
     *      tags={"BudgetControlLink"},
     *      description="Create BudgetControlLink",
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
     *                  ref="#/definitions/BudgetControlLink"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetControlLinkAPIRequest $request)
    {
        $input = $request->all();
        $validator = \Validator::make($request->all(), [
            'glAutoID' => 'required',
            'companySystemID' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $tempDetail = BudgetControlLink::where('controlId',$input['id'])->where('companySystemID',$input['companySystemID'])->pluck('glAutoID')->toArray();


        $finalError = array(
            'already_gl_linked' => array(),
        );
        $error_count = 0;

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
            }else{
                foreach ($input['glAutoID'] as $key => $val) {
                    if (!in_array($val['chartOfAccountSystemID'], $tempDetail)) {
                        $data['controlId'] = $input['id'];
                        $data['glAutoID'] = $val['chartOfAccountSystemID'];
                        $data['glCode'] = $val['AccountCode'];
                        $data['glDescription'] = $val['AccountDescription'];
                        $data['companySystemID'] = $input['companySystemID'];
                        $data['createdPCID'] = gethostname();
                        $data['createdUserID'] = \Helper::getEmployeeID();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $budgetControlLink = $this->budgetControlLinkRepository->create($data);
                    }
                }
            }
        }


        return $this->sendResponse($budgetControlLink->toArray(), 'Budget Control Link saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/budgetControlLinks/{id}",
     *      summary="getBudgetControlLinkItem",
     *      tags={"BudgetControlLink"},
     *      description="Get BudgetControlLink",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetControlLink",
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
     *                  ref="#/definitions/BudgetControlLink"
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
        /** @var BudgetControlLink $budgetControlLink */
        $budgetControlLink = $this->budgetControlLinkRepository->findWithoutFail($id);

        if (empty($budgetControlLink)) {
            return $this->sendError('Budget Control Link not found');
        }

        return $this->sendResponse($budgetControlLink->toArray(), 'Budget Control Link retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/budgetControlLinks/{id}",
     *      summary="updateBudgetControlLink",
     *      tags={"BudgetControlLink"},
     *      description="Update BudgetControlLink",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetControlLink",
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
     *                  ref="#/definitions/BudgetControlLink"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetControlLinkAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetControlLink $budgetControlLink */
        $budgetControlLink = $this->budgetControlLinkRepository->findWithoutFail($id);

        if (empty($budgetControlLink)) {
            return $this->sendError('Budget Control Link not found');
        }

        $budgetControlLink = $this->budgetControlLinkRepository->update($input, $id);

        return $this->sendResponse($budgetControlLink->toArray(), 'BudgetControlLink updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/budgetControlLinks/{id}",
     *      summary="deleteBudgetControlLink",
     *      tags={"BudgetControlLink"},
     *      description="Delete BudgetControlLink",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BudgetControlLink",
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
        /** @var BudgetControlLink $budgetControlLink */
        $budgetControlLink = $this->budgetControlLinkRepository->findWithoutFail($id);

        if (empty($budgetControlLink)) {
            return $this->sendError('Budget Control Link not found');
        }

        $budgetControlLink->delete();
        
        return $this->sendResponse(true,'Budget Control Link deleted successfully');
    }
}
