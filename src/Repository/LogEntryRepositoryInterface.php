<?php

declare(strict_types=1);

namespace App\Repository;

use App\Repository\LogEntry\CountSearchCriteria;

interface LogEntryRepositoryInterface
{
    public function getCount(CountSearchCriteria $searchCriteria): int;
}
