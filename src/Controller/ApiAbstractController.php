<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiAbstractController extends AbstractController
{
    protected function jsonMessage(int $code, string $message): JsonResponse
    {
        return $this->json([
            'code' => $code,
            'message' => $message,
        ]);
    }
}