<?php

namespace App\Interfaces;

interface CommentsInterface
{
    public function getCommentsByPostId(int $postId, string $modelType): array;

    public function createComment(array $data);

    public function deleteComment(int $commentId): bool;
}
