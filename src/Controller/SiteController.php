<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    #[Route('/')]
    public function index(): JsonResponse
    {
        return $this->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(): JsonResponse
    {
        return $this->json(['error' => '', 'price' => 0], Response::HTTP_OK);
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(): JsonResponse
    {
        return $this->json(['error' => '', 'purchase_id' => 0], Response::HTTP_OK);
    }
}
