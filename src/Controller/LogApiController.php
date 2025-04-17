<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\LogEntry;
use DateTimeImmutable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class LogApiController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/api/count')]
    public function getCount(
        #[MapQueryParameter] ?array $serviceNames = null,
        #[MapQueryParameter] ?string $startDate = null,
        #[MapQueryParameter] ?string $endDate = null,
    ): Response {
        $count = 0;

        $logEntryRepository = $this->entityManager->getRepository(LogEntry::class);

        try {
            $criteria = Criteria::create();
            if ($serviceNames !== null) {
                $criteria->andWhere(
                    Criteria::expr()->in('service_name', $serviceNames)
                );
            }
            if ($startDate !== null) {
                $criteria->andWhere(
                    Criteria::expr()->gte('timestamp', DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $startDate))
                );
            }
            if ($endDate !== null) {
                $criteria->andWhere(
                    Criteria::expr()->lte('timestamp', DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $endDate))
                );
            }

            $count = $logEntryRepository->matching($criteria)
                ->count();
        } catch (\Exception $exception) {
            $this->json(['error' => $exception->getMessage()], 500);
        }

        return $this->json(['count' => $count]);
    }
}
