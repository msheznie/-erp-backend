<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetDetailCommentAPIRequest;
use App\Http\Requests\API\UpdateBudgetDetailCommentAPIRequest;
use App\Models\BudgetDetailComment;
use App\Repositories\BudgetDetailCommentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetDetailCommentController
 * @package App\Http\Controllers\API
 */

class BudgetDetailCommentAPIController extends AppBaseController
{
    /** @var  BudgetDetailCommentRepository */
    private $budgetDetailCommentRepository;

    public function __construct(BudgetDetailCommentRepository $budgetDetailCommentRepo)
    {
        $this->budgetDetailCommentRepository = $budgetDetailCommentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetDetailComments",
     *      summary="Get a listing of the BudgetDetailComments.",
     *      tags={"BudgetDetailComment"},
     *      description="Get all BudgetDetailComments",
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
     *                  @SWG\Items(ref="#/definitions/BudgetDetailComment")
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
        $this->budgetDetailCommentRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetDetailCommentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetDetailComments = $this->budgetDetailCommentRepository->all();

        return $this->sendResponse($budgetDetailComments->toArray(), trans('custom.budget_detail_comments_retrieved_successfully'));
    }

    /**
     * @param CreateBudgetDetailCommentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetDetailComments",
     *      summary="Store a newly created BudgetDetailComment in storage",
     *      tags={"BudgetDetailComment"},
     *      description="Store BudgetDetailComment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetDetailComment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetDetailComment")
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
     *                  ref="#/definitions/BudgetDetailComment"
     *              ),
     *              @SWG\Property(
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

        if (isset($input['comment'])) {
            $commentData = $input['comment'];

            if (isset($commentData['id']) && $commentData['id'] > 0) {
                $updateData = [
                    'comment' => $input['comment']['comment'],
                    'created_by' => \Helper::getEmployeeSystemID()
                ];

                $budgetDetailComment = $this->budgetDetailCommentRepository->update($updateData, $commentData['id']);   
            } else {
                 if (isset($input['budgetDetails']) && count($input['budgetDetails']) > 0) {
                    $saveData = [
                        'budgetDetailID' => $input['budgetDetails'][0]['budjetDetailsID'],
                        'comment' => $input['comment']['comment'],
                        'created_by' => \Helper::getEmployeeSystemID()
                    ];
                    
                    $budgetDetailComment = $this->budgetDetailCommentRepository->create($saveData);
                }            
            }
        }

        return $this->sendResponse([], trans('custom.budget_detail_comment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetDetailComments/{id}",
     *      summary="Display the specified BudgetDetailComment",
     *      tags={"BudgetDetailComment"},
     *      description="Get BudgetDetailComment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailComment",
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
     *                  ref="#/definitions/BudgetDetailComment"
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
        /** @var BudgetDetailComment $budgetDetailComment */
        $budgetDetailComment = $this->budgetDetailCommentRepository->findWithoutFail($id);

        if (empty($budgetDetailComment)) {
            return $this->sendError(trans('custom.budget_detail_comment_not_found'));
        }

        return $this->sendResponse($budgetDetailComment->toArray(), trans('custom.budget_detail_comment_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetDetailCommentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetDetailComments/{id}",
     *      summary="Update the specified BudgetDetailComment in storage",
     *      tags={"BudgetDetailComment"},
     *      description="Update BudgetDetailComment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailComment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetDetailComment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetDetailComment")
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
     *                  ref="#/definitions/BudgetDetailComment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetDetailCommentAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetDetailComment $budgetDetailComment */
        $budgetDetailComment = $this->budgetDetailCommentRepository->findWithoutFail($id);

        if (empty($budgetDetailComment)) {
            return $this->sendError(trans('custom.budget_detail_comment_not_found'));
        }

        $budgetDetailComment = $this->budgetDetailCommentRepository->update($input, $id);

        return $this->sendResponse($budgetDetailComment->toArray(), trans('custom.budgetdetailcomment_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetDetailComments/{id}",
     *      summary="Remove the specified BudgetDetailComment from storage",
     *      tags={"BudgetDetailComment"},
     *      description="Delete BudgetDetailComment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailComment",
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
        /** @var BudgetDetailComment $budgetDetailComment */
        $budgetDetailComment = $this->budgetDetailCommentRepository->findWithoutFail($id);

        if (empty($budgetDetailComment)) {
            return $this->sendError(trans('custom.budget_detail_comment_not_found'));
        }

        $budgetDetailComment->delete();

        return $this->sendSuccess('Budget Detail Comment deleted successfully');
    }

    public function getBudgetDetailComment(Request $request)
    {
        $input = $request->all();

        $budgetDetailID = (isset($input['budgetDetails']) && count($input['budgetDetails']) > 0) ? $input['budgetDetails'][0]['budjetDetailsID']: null; 

        $budgetComments = [];
        if (!is_null($budgetDetailID)) {
            $budgetComments = BudgetDetailComment::where('budgetDetailID', $budgetDetailID)
                                                 ->with(['created_by_emp'])
                                                 ->get();
        }

         return $this->sendResponse($budgetComments, trans('custom.budget_detail_comments_retrived_successfully'));

    }
}
