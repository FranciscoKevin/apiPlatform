<?php

namespace App\Controller;

use App\Entity\Post;

class PostPublishController
{
    public function __invoke(Post $data): Post
    {
        $data->setIsOnline(true);
        return $data;
    }
}