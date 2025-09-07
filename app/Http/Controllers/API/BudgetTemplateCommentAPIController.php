<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\BudgetTemplateComment;
use App\Http\Requests\API\CreateBudgetTemplateCommentAPIRequest;
use App\Http\Requests\API\UpdateBudgetTemplateCommentAPIRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetTemplateCommentAPIController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = BudgetTemplateComment::with(['user', 'budgetDetail'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse($comments->toArray(), trans('custom.comments_retrieved_successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBudgetTemplateCommentAPIRequest $request)
    {
        try {
            DB::beginTransaction();
            $comment = BudgetTemplateComment::create([
                'budget_detail_id' => $request->budget_detail_id,
                'user_id' => Auth::id(),
                'comment_text' => $request->comment_text,
                'parent_comment_id' => $request->parent_comment_id
            ]);

            // Load relationships for response
            $comment->load(['user', 'budgetDetail']);

            DB::commit();

            return $this->sendResponse($comment->toArray(), trans('custom.comment_saved_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_saving_comment') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = BudgetTemplateComment::with(['user', 'budgetDetail'])
            ->find($id);

        if (!$comment) {
            return $this->sendError(trans('custom.comment_not_found'));
        }

        return $this->sendResponse($comment->toArray(), trans('custom.comment_retrieved_successfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBudgetTemplateCommentAPIRequest $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function deleteBudgetTemplateComment(Request $request) {
        $input = $request->all();

        $comment = BudgetTemplateComment::find($input['id']);

        if (!$comment) {
            return $this->sendError(trans('custom.comment_not_found'));
        }

        // Check if user can delete this comment
        if ($comment->user_id !== Auth::id()) {
            return $this->sendError('You can only delete your own comments');
        }

        try {
            $comment->delete();
            BudgetTemplateComment::where('parent_comment_id', $input['id'])->delete();
            return $this->sendResponse([], trans('custom.comment_deleted_successfully'));

        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_deleting_comment') . $e->getMessage());
        }
    }

    /**
     * Get comments for a specific budget detail
     *
     * @param  int  $budgetDetailId
     * @return \Illuminate\Http\Response
     */
    public function getByBudgetDetail($budgetDetailId)
    {
        $comments = BudgetTemplateComment::with(['user', 'budgetDetail'])
            ->where('budget_detail_id', $budgetDetailId)
            ->whereNull('parent_comment_id') // Only top-level comments
            ->orderBy('created_at', 'desc')
            ->get();

        // Load replies for each comment
        foreach ($comments as $comment) {
            $comment->replies = BudgetTemplateComment::with(['user'])
                ->where('parent_comment_id', $comment->commentID)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $detail['comments'] = $comments;
        $detail['currentUserId'] = Auth::id();

        return $this->sendResponse($detail, trans('custom.budgettemplatecomments_retrieved_successfully'));
    }

    /**
     * Get replies for a specific comment
     *
     * @param  int  $commentId
     * @return \Illuminate\Http\Response
     */
    public function getReplies($commentId)
    {
        $replies = BudgetTemplateComment::with(['user'])
            ->where('parent_comment_id', $commentId)
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->sendResponse($replies, "Replies get success");
    }

    public function updateBudgetTemplateComment(Request $request) {
        $input = $request->all();

        $comment = BudgetTemplateComment::find($input['id']);

        if (!$comment) {
            return $this->sendError(trans('custom.comment_not_found'));
        }

        // Check if user can edit this comment
        if ($comment->user_id !== Auth::id()) {
            return $this->sendError("You can only edit your own comments");
        }

        try {
            $comment->update([
                'comment_text' => $input['comment_text']
            ]);

            $comment->load(['user', 'budgetDetail']);

            return $this->sendResponse($comment, "Comment updated successfully");

        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_updating_comment') . $e->getMessage());
        }
    }
}
