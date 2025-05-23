<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\PostComment;
use App\Repositories\CommentsRepository;
use App\Services\CommentsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostCommentController extends Controller
{
    protected $commentsService;
    public function __construct()
    {
        $model = new PostComment();

        $repository = new CommentsRepository($model);
        $this->commentsService = new CommentsService($repository);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $postId = request()->query('post_id');
            $modelType = 'post';
            if(!$postId){
                return response()->error('Post ID is required', 422);
            }
            $comments = $this->commentsService->getAllComments($postId, $modelType);
            return response()->success($comments, 'Comments retrieved successfully');
        }catch (\Exception $e){
            return response()->error('Error occurred while retrieving comments', 500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'post_id' => 'required|integer|exists:posts,id',
                'comment' => 'required|string|max:500',
                'parent_id' => 'nullable|integer|exists:post_comments,id',

            ]);

            if($validator->fails()){
                return response()->error($validator->errors()->first(), 422, $validator->errors());
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();

            $comment = $this->commentsService->createComment($data);

            return response()->success($comment, 'Comment added successfully', 201);
        }catch (\Exception $e){
            return response()->error('Error occurred while adding comment', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $comment = $this->commentsService->deleteComment($id);
            if(!$comment){
                return response()->error('Comment not found', 404);
            }
            return response()->success(null, 'Comment deleted successfully');
        }catch (\Exception $e){
            return response()->error('Error occurred while deleting comment', 500, $e->getMessage());
        }
    }
}
