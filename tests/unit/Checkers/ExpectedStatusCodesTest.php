<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Test\Checkers;

use Codeception\Test\Unit;
use Itineris\Preflight\Checkers\AbstractChecker;
use Itineris\Preflight\Extra\Checkers\ExpectedStatusCodes;
use Itineris\Preflight\Test\Checkers\AbstractCheckerTestTrail;

class ExpectedStatusCodesTest extends Unit
{
    use AbstractCheckerTestTrail;

    /**
     * @var \Itineris\Preflight\Extra\Test\UnitTester
     */
    protected $tester;

    protected function getSubject(): AbstractChecker
    {
        return new ExpectedStatusCodes();
    }
}
