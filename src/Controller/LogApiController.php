<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogApiController extends AbstractController
{
    #[Route('/api/count')]
    public function getCount(): Response
    {
        return $this->json(['count' => 1239]);
    }
}
