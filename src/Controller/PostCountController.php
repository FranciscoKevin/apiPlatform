<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PostCountController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function __invoke(Request $request): int
    {
        $onlineQuery = $request->get("isOnline");
        $conditions = [];

        if ($onlineQuery != null) {
            $conditions = ["isOnline" => $onlineQuery == "1" ? true : false];
        }
        return $this->postRepository->count($conditions);
    }
}