<?php

namespace App\RealWorld\UserAction;

trait HasAction
{
    /**
     * Set new action after commenting.
     *
     * @return mixed
     */
    public function newCommentAction()
    {
        $this->action()->firstOrCreate([])->increment('comment_count');
    }

    /**
     * Get user comment count.
     *
     * @return integer
     */
    public function commentCount()
    {
        if (! $this->action)
            return 0;

        return $this->action->comment_count;
    }

    /**
     * Check if user can send comment as free.
     *
     * @return bool
     */
    public function isCommentFree()
    {
        return $this->commentCount() <= 5;
    }
}