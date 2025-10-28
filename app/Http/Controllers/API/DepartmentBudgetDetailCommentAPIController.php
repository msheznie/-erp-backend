<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\BudgetDetailComment;
use App\Models\Employee;
use App\Http\Requests\API\CreateBudgetDetailCommentAPIRequest;
use App\Http\Requests\API\UpdateBudgetDetailCommentAPIRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentBudgetDetailCommentAPIController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = BudgetDetailComment::with(['created_by_emp'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse($comments->toArray(), 'Department budget detail comments retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\API\CreateBudgetDetailCommentAPIRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBudgetDetailCommentAPIRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $comment = BudgetDetailComment::create([
                'budgetDetailID' => $request->budgetDetailId,
                'comment' => $request->comment,
                'created_by' => Auth::user()->employee_id,
                'parentId' => $request->parentId ?? null
            ]);

            // Load relationships for response
            $comment->load(['created_by_emp']);

            DB::commit();

            return $this->sendResponse($comment->toArray(), 'Department budget detail comment saved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error saving department budget detail comment: ' . $e->getMessage());
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
        $comment = BudgetDetailComment::with(['created_by_emp'])
            ->find($id);

        if (!$comment) {
            return $this->sendError('Department budget detail comment not found');
        }

        return $this->sendResponse($comment->toArray(), 'Department budget detail comment retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\API\UpdateBudgetDetailCommentAPIRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBudgetDetailCommentAPIRequest $request, $id)
    {
        try {
            $comment = BudgetDetailComment::find($id);

            if (!$comment) {
                return $this->sendError('Department budget detail comment not found');
            }

            // Check if user can edit this comment
            if ($comment->created_by !== Auth::user()->employee_id) {
                return $this->sendError('You can only edit your own comments');
            }

            $comment->update([
                'comment' => $request->comment
            ]);

            $comment->load(['created_by_emp']);

            return $this->sendResponse($comment->toArray(), 'Department budget detail comment updated successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error updating department budget detail comment: ' . $e->getMessage());
        }
    }

    /**
     * Resolve a comment conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resolve(Request $request)
    {
        try {
            $comment = BudgetDetailComment::find($request->commentId);

            if (!$comment) {
                return $this->sendError('Department budget detail comment not found');
            }

            // Check if user has permission to resolve comments
            // You can add specific permission checks here based on your business logic
            // For example, only finance users or managers can resolve comments
            
            $comment->update([
                'is_resolved' => true,
                'resolved_by' => Auth::user()->employee_id,
                'resolved_at' => now()
            ]);

            // If this is a parent comment, also resolve all child comments (replies)
            if (!$comment->parentId) {
                BudgetDetailComment::where('parentId', $comment->id)
                    ->update([
                        'is_resolved' => true,
                        'resolved_by' => Auth::user()->employee_id,
                        'resolved_at' => now()
                    ]);
            }

            $comment->load(['created_by_emp', 'resolved_by_emp']);

            return $this->sendResponse($comment->toArray(), 'Comment conversation resolved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error resolving comment conversation: ' . $e->getMessage());
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
        try {
            $comment = BudgetDetailComment::find($id);

            if (!$comment) {
                return $this->sendError('Department budget detail comment not found');
            }

            // Check if user can delete this comment
            if ($comment->created_by !== Auth::user()->employee_id) {
                return $this->sendError('You can only delete your own comments');
            }

            // If this is a parent comment, delete all its replies first
            if (is_null($comment->parentId)) {
                BudgetDetailComment::where('parentId', $comment->id)->delete();
            }

            $comment->delete();

            return $this->sendResponse([], 'Department budget detail comment deleted successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error deleting department budget detail comment: ' . $e->getMessage());
        }
    }

    /**
     * Get comments for a specific budget detail
     *
     * @param  int  $budgetDetailId
     * @return \Illuminate\Http\Response
     */
    public function getByBudgetDetail($budgetDetailId, Request $request)
    {
        // Get parent comments (where parentId is null)
        $parentComments = BudgetDetailComment::with(['created_by_emp'])
            ->where('budgetDetailID', $budgetDetailId)
            ->whereNull('parentId')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all replies for these parent comments
        $parentCommentIds = $parentComments->pluck('id')->toArray();
        $replies = BudgetDetailComment::with(['created_by_emp'])
            ->where('budgetDetailID', $budgetDetailId)
            ->whereIn('parentId', $parentCommentIds)
            ->orderBy('created_at', 'asc')
            ->get();

        // Group replies by parent comment ID
        $repliesByParent = $replies->groupBy('parentId');

        // Attach replies to their parent comments
        $commentsWithReplies = $parentComments->map(function ($comment) use ($repliesByParent) {
            $comment->replies = $repliesByParent->get($comment->id, collect())->toArray();
            return $comment;
        });

        return $this->sendResponse($commentsWithReplies->toArray(), 'Department budget detail comments retrieved successfully');
    }

    /**
     * Get comments count for a specific budget detail
     *
     * @param  int  $budgetDetailId
     * @return \Illuminate\Http\Response
     */
    public function getCommentsCount($budgetDetailId, Request $request)
    {
        $count = BudgetDetailComment::where('budgetDetailID', $budgetDetailId)->count();

        return $this->sendResponse(['count' => $count], 'Department budget detail comments count retrieved successfully');
    }

    /**
     * Get comments for multiple budget details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCommentsByBudgetDetailIds(Request $request)
    {
        $budgetDetailIds = $request->budgetDetailIds;
        
        if (empty($budgetDetailIds) || !is_array($budgetDetailIds)) {
            return $this->sendError('Invalid budget detail IDs provided');
        }

        $comments = BudgetDetailComment::with(['created_by_emp'])
            ->whereIn('budgetDetailID', $budgetDetailIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('budgetDetailID');

        return $this->sendResponse($comments->toArray(), 'Department budget detail comments retrieved successfully');
    }

    /**
     * Save a new comment (alternative endpoint)
     *
     * @param  \App\Http\Requests\API\CreateBudgetDetailCommentAPIRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function save(CreateBudgetDetailCommentAPIRequest $request)
    {
        return $this->store($request);
    }

    /**
     * Get all comments with pagination
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCommentsPaginated(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $budgetDetailId = $request->get('budget_detail_id');

        $query = BudgetDetailComment::with(['created_by_emp']);

        if ($budgetDetailId) {
            $query->where('budgetDetailID', $budgetDetailId);
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->sendResponse($comments->toArray(), 'Department budget detail comments retrieved successfully');
    }

    /**
     * Get recent comments for dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRecentComments(Request $request)
    {
        $limit = $request->get('limit', 10);
        $budgetDetailId = $request->get('budget_detail_id');

        $query = BudgetDetailComment::with(['created_by_emp']);

        if ($budgetDetailId) {
            $query->where('budgetDetailID', $budgetDetailId);
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->sendResponse($comments->toArray(), 'Recent department budget detail comments retrieved successfully');
    }
}
