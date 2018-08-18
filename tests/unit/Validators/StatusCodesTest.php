<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Test\Validators;

use Codeception\Test\Unit;
use Itineris\Preflight\CheckerInterface;
use Itineris\Preflight\Extra\Validators\StatusCodes;
use Itineris\Preflight\ResultFactory;
use Itineris\Preflight\Results\Success;
use Itineris\Preflight\Test\Validators\AbstractValidatorTestTrait;
use Itineris\Preflight\Validators\AbstractValidator;
use Mockery;
use WP_Mock;

class StatusCodesTest extends Unit
{
    use AbstractValidatorTestTrait;

    /**
     * @var \Itineris\Preflight\Extra\Test\UnitTester
     */
    protected $tester;

    public function testDefaultToTwoHundredOk()
    {
        $checker = Mockery::mock(CheckerInterface::class);

        $validator = new StatusCodes($checker);

        $this->assertAttributeSame(200, 'expectedStatusCode', $validator);
    }

    public function testSetExpectedStatusCode()
    {
        $checker = Mockery::mock(CheckerInterface::class);

        $validator = new StatusCodes($checker);

        $validator->setExpectedStatusCode(999);

        $this->assertAttributeSame(999, 'expectedStatusCode', $validator);
    }

    public function testSuccess()
    {
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_get')
               ->with('https://example.test/blog')
               ->andReturn(['fake-status' => 200])
               ->once();
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_get')
               ->with('https://example.test/about')
               ->andReturn(['fake-status' => 200])
               ->once();

        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_retrieve_response_code')
               ->with(['fake-status' => 200])
               ->andReturn(200)
               ->twice();

        $checker = Mockery::mock(CheckerInterface::class);

        $validator = new StatusCodes($checker);

        $actual = $validator->validate(
            'https://example.test/blog',
            'https://example.test/about'
        );

        $this->assertInstanceOf(Success::class, $actual);
    }

    public function testFailure()
    {
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_get')
               ->with('https://example.test/blog')
               ->andReturn(['fake-status' => 200])
               ->once();
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_get')
               ->with('https://example.test/about')
               ->andReturn(['fake-status' => 201])
               ->once();
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_get')
               ->with('https://example.test/404')
               ->andReturn(['fake-status' => 404])
               ->once();
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_get')
               ->with('https://example.test/epic')
               ->andReturn(['fake-status' => 'epic'])
               ->once();

        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_retrieve_response_code')
               ->with(['fake-status' => 200])
               ->andReturn(200)
               ->once();
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_retrieve_response_code')
               ->with(['fake-status' => 201])
               ->andReturn(201)
               ->once();
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_retrieve_response_code')
               ->with(['fake-status' => 404])
               ->andReturn(404)
               ->once();
        WP_Mock::userFunction('Itineris\Preflight\Extra\Validators\wp_remote_retrieve_response_code')
               ->with(['fake-status' => 'epic'])
               ->andReturn('')
               ->once();

        $checker = Mockery::mock(CheckerInterface::class);

        $validator = new StatusCodes($checker);

        $validator->setExpectedStatusCode(201);
        $actual = $validator->validate(
            'https://example.test/blog',
            'https://example.test/about',
            'https://example.test/404',
            'https://example.test/epic'
        );

        $expected = ResultFactory::makeFailure(
            $checker,
            [
                'Something went wrong:',
                'https://example.test/blog returns 200',
                'https://example.test/404 returns 404',
                'Unable to reach https://example.test/epic',
            ]
        );
        $this->assertEquals($expected, $actual);
    }

    protected function getSubject(CheckerInterface $checker, string $message): AbstractValidator
    {
        return new StatusCodes($checker, $message);
    }
}
