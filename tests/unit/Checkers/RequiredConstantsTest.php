<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Test\Checkers;

use Codeception\Test\Unit;
use Itineris\Preflight\Checkers\AbstractChecker;
use Itineris\Preflight\Config;
use Itineris\Preflight\Extra\Checkers\RequiredConstants;
use Itineris\Preflight\ResultFactory;
use Itineris\Preflight\ResultInterface;
use Itineris\Preflight\Test\Checkers\AbstractCheckerTestTrail;
use Itineris\Preflight\Test\Checkers\Trails\CompiledIncludesAwareTestTrait;
use Itineris\Preflight\Validators\AbstractValidator;
use Mockery;

class RequiredConstantsTest extends Unit
{
    use AbstractCheckerTestTrail;
    use CompiledIncludesAwareTestTrait;

    /**
     * @var \Itineris\Preflight\Extra\Test\UnitTester
     */
    protected $tester;

    public function testUsingValidator()
    {
        $constantNames = ['AAA', 'BBB'];
        $config = Mockery::mock(Config::class);
        $config->expects('isEnabled')
               ->withNoArgs()
               ->andReturnTrue()
               ->once();
        $config->expects('compileIncludes')
               ->with([])
               ->andReturn($constantNames)
               ->twice();

        $expected = Mockery::mock(ResultInterface::class);
        $validator = Mockery::mock(AbstractValidator::class);
        $validator->expects('validate')
                  ->withArgs($constantNames)
                  ->andReturn($expected);

        $checker = new RequiredConstants($validator);

        $actual = $checker->check($config);

        $this->assertSame($expected, $actual);
    }

    /**
     * TODO: Move into trait.
     */
    public function testCheckEmptyIncludesError()
    {
        $config = new Config([]);
        $checker = new RequiredConstants();

        $actual = $checker->check($config);

        $expected = ResultFactory::makeError($checker, 'Includes is empty.');
        $this->assertEquals($expected, $actual);
    }

    protected function getSubject(): AbstractChecker
    {
        $validator = Mockery::mock(AbstractValidator::class);

        return new RequiredConstants($validator);
    }
}
