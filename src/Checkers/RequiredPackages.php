<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Checkers;

use Itineris\Preflight\Checkers\AbstractChecker;
use Itineris\Preflight\Checkers\Traits\CompiledIncludesAwareTrait;
use Itineris\Preflight\Config;
use Itineris\Preflight\ResultFactory;
use Itineris\Preflight\ResultInterface;
use Itineris\Preflight\Results\Error;
use WP_CLI;

class RequiredPackages extends AbstractChecker
{
    use CompiledIncludesAwareTrait;

    public const ID = 'extra-required-packages';
    public const DESCRIPTION = 'Ensure required WP CLI packages are installed.';
    public const FAILURE_MESSAGE = 'Required WP CLI packages not found:';

    // This is for subclasses.
    public const DEFAULT_INCLUDES = [];

    /**
     * Run the check and return a result.
     *
     * Assume the checker is enabled and its config make sense.
     *
     * @param Config $config The config instance.
     *
     * @return ResultInterface
     */
    protected function run(Config $config): ResultInterface
    {
        $installed = WP_CLI::runcommand(
            'package list --fields=name --format=json',
            [
                'return' => true,
                'parse' => 'json',
                'launch' => true,
                'exit_error' => false,
            ]
        );

        $installed = array_map(function (array $plugin): string {
            return $plugin['name'];
        }, $installed);

        $missing = array_diff(
            $config->compileIncludes(static::DEFAULT_INCLUDES),
            $installed
        );

        return (empty($missing))
            ? ResultFactory::makeSuccess($this)
            : ResultFactory::makeFailure(
                $this,
                array_merge([self::FAILURE_MESSAGE], $missing)
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param Config $config The config instance.
     *
     * @return Error|null
     */
    protected function maybeInvalidConfig(Config $config): ?Error
    {
        return $this->errorIfCompiledIncludesIsEmpty($config, static::DEFAULT_INCLUDES);
    }
}
