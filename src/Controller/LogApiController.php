<?php declare(strict_types=1);

namespace App\Controller;

use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class LogApiController extends AbstractController
{
    #[Route('/api/count')]
    public function getCount(
        #[MapQueryParameter] ?array $serviceNames = null,
        #[MapQueryParameter] ?string $startDate = null,
        #[MapQueryParameter] ?string $endDate = null,
    ): Response {
        return $this->json(['count' => 1239]);
    }
}
