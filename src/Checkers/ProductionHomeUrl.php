<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Checkers;

use Itineris\Preflight\Checkers\AbstractChecker;
use Itineris\Preflight\Config;
use Itineris\Preflight\ResultFactory;
use Itineris\Preflight\ResultInterface;
use Itineris\Preflight\Results\Error;

class ProductionHomeUrl extends AbstractChecker
{
    public const ID = 'extra-production-home-url';
    public const DESCRIPTION = 'Ensure home URL is on production.';

    /**
     * {@inheritdoc}
     *
     * @param Config $config The config instance.
     *
     * @return ResultInterface
     */
    protected function run(Config $config): ResultInterface
    {
        $expected = (string) $config->get('url');
        $actual = home_url('/');

        return ($actual === $expected || $actual === $expected . '/')
            ? ResultFactory::makeSuccess($this)
            : ResultFactory::makeFailure($this, [
                'Unexpected home URL:',
                $actual,
            ]);
    }

    /**
     * Before actually run the check, check the config is valid.
     *
     * @param Config $config The config instance.
     *
     * @return Error|null
     */
    protected function maybeInvalidConfig(Config $config): ?Error
    {
        $expected = (string) $config->get('url');

        return (empty($expected))
            ? ResultFactory::makeError($this, 'Expected URL not defined in preflight.toml.')
            : null;
    }
}
