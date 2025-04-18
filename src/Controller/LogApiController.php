<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LogEntry\CountSearchCriteria;
use App\Repository\LogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class LogApiController extends AbstractController
{
    public function __construct(
        private readonly LogEntryRepository $repository
    ) {
    }

    #[Route('/api/count', methods: ['GET'])]
    public function getCount(
        #[MapQueryString(validationFailedStatusCode: 400)] CountSearchCriteria $criteria
            = new CountSearchCriteria()
    ): Response {
        try {
            return $this->json(
                ['counter' => $this->repository->getCount($criteria)]
            );
        } catch (\Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                500
            );
        }
    }
}
