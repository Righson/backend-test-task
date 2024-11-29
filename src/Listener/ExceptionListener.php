<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $data = [
            'error' => $exception->getMessage(),
        ];

        $response = new JsonResponse($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }
}

