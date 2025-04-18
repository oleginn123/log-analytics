<?php

declare(strict_types=1);

namespace App\Repository\LogEntry;

use DateTimeImmutable;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Type;

class CountSearchCriteria
{
    /**
     * @param null|array<string> $serviceNames
     */
    public function __construct(
        #[Type('array')]
        #[All([new Type('string')])]
        private ?array $serviceNames = null,
        #[AtLeastOneOf([new Blank(), new DateTime()])]
        private ?string $startDate = null,
        #[AtLeastOneOf([new Blank(), new DateTime()])]
        private ?string $endDate = null,
        #[AtLeastOneOf([new Blank(), new Type('int')])]
        private ?int $statusCode = null,
    ) {
    }

    public function toFilterCriteria(): Criteria
    {
        $criteria = Criteria::create();

        if ($this->serviceNames !== null) {
            $criteria->andWhere(
                Criteria::expr()->in('service_name', $this->serviceNames)
            );
        }

        if ($this->startDate !== null) {
            $criteria->andWhere(
                Criteria::expr()->gte('timestamp', DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->startDate))
            );
        }

        if ($this->endDate !== null) {
            $criteria->andWhere(
                Criteria::expr()->lte('timestamp', DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->endDate))
            );
        }

        if ($this->statusCode !== null) {
            $criteria->andWhere(
                Criteria::expr()->eq('code', $this->statusCode)
            );
        }

        return $criteria;
    }
}
