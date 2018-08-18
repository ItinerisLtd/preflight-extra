<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Test\Checkers;

use Itineris\Preflight\Checkers\AbstractChecker;
use Itineris\Preflight\Config;
use Itineris\Preflight\Extra\Checkers\RequiredPackages;
use Itineris\Preflight\ResultFactory;
use Itineris\Preflight\Results\Success;
use Itineris\Preflight\Test\Checkers\AbstractCheckerTestTrail;
use Itineris\Preflight\Test\Checkers\Trails\CompiledIncludesAwareTestTrait;
use Mockery;

class RequiredPackagesTest extends \Codeception\Test\Unit
{
    use AbstractCheckerTestTrail;
    use CompiledIncludesAwareTestTrait;

    /**
     * @var \Itineris\Preflight\Extra\Test\UnitTester
     */
    protected $tester;

    public function testSuccess()
    {
        Mockery::mock('alias:WP_CLI')
               ->expects('runcommand')
               ->with(Mockery::type('string'), Mockery::type('array'))
               ->andReturn([
                   [
                       'name' => 'my/command',
                   ],
               ])
               ->once();

        $checker = new RequiredPackages();

        $actual = $checker->check(
            new Config([
                'includes' => 'my/command',
            ])
        );

        $this->assertInstanceOf(Success::class, $actual);
    }

    public function testFailure()
    {
        Mockery::mock('alias:WP_CLI')
               ->expects('runcommand')
               ->with(Mockery::type('string'), Mockery::type('array'))
               ->andReturn([])
               ->once();

        $checker = new RequiredPackages();

        $actual = $checker->check(
            new Config([
                'includes' => 'my/command',
            ])
        );

        $expected = ResultFactory::makeFailure(
            $checker,
            [
                'Required WP CLI packages not found:',
                'my/command',
            ]
        );
        $this->assertEquals($expected, $actual);
    }

    public function testMultipleFailure()
    {
        Mockery::mock('alias:WP_CLI')
               ->expects('runcommand')
               ->with(Mockery::type('string'), Mockery::type('array'))
               ->andReturn([
                   [
                       'name' => 'my/command',
                   ],
               ])
               ->once();

        $checker = new RequiredPackages();

        $actual = $checker->check(
            new Config([
                'includes' => [
                    'my/command',
                    'your/command',
                    'their/command',
                ],
            ])
        );

        $expected = ResultFactory::makeFailure(
            $checker,
            [
                'Required WP CLI packages not found:',
                'your/command',
                'their/command',
            ]
        );
        $this->assertEquals($expected, $actual);
    }

    public function testFailureExclude()
    {
        Mockery::mock('alias:WP_CLI')
               ->expects('runcommand')
               ->with(Mockery::type('string'), Mockery::type('array'))
               ->andReturn([
                   [
                       'name' => 'my/command',
                   ],
               ])
               ->once();

        $checker = new RequiredPackages();

        $actual = $checker->check(
            new Config([
                'includes' => [
                    'my/command',
                    'your/command',
                    'their/command',
                ],
                'excludes' => [
                    'your/command',
                ],
            ])
        );

        $expected = ResultFactory::makeFailure(
            $checker,
            [
                'Required WP CLI packages not found:',
                'their/command',
            ]
        );
        $this->assertEquals($expected, $actual);
    }

    protected function getSubject(): AbstractChecker
    {
        return new RequiredPackages();
    }
}
