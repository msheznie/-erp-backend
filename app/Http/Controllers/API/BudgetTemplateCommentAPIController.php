<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BudgetTemplateComment;
use App\Http\Requests\API\CreateBudgetTemplateCommentAPIRequest;
use App\Http\Requests\API\UpdateBudgetTemplateCommentAPIRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetTemplateCommentAPIController extends Controller
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

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
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

            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully',
                'data' => $comment
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating comment: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
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
        $comment = BudgetTemplateComment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Check if user can edit this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own comments'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $comment->update([
                'comment_text' => $request->comment_text
            ]);

            $comment->load(['user', 'budgetDetail']);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating comment: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = BudgetTemplateComment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Check if user can delete this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own comments'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting comment: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
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

        return response()->json([
            'success' => true,
            'data' => $replies
        ]);
    }
}
