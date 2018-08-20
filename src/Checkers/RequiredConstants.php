<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Checkers;

use Itineris\Preflight\Checkers\AbstractChecker;
use Itineris\Preflight\Checkers\Traits\CompiledIncludesAwareTrait;
use Itineris\Preflight\Checkers\Traits\ValidatorAwareTrait;
use Itineris\Preflight\Config;
use Itineris\Preflight\ResultInterface;
use Itineris\Preflight\Results\Error;
use Itineris\Preflight\Validators\RequiredConstants as RequiredConstantsValidator;

class RequiredConstants extends AbstractChecker
{
    use ValidatorAwareTrait;
    use CompiledIncludesAwareTrait;

    public const ID = 'extra-required-constants';
    public const DESCRIPTION = 'Ensure required constants are defined.';

    // This is for subclasses.
    public const DEFAULT_INCLUDES = [];

    /**
     * {@inheritdoc}
     *
     * @param Config $config The config instance.
     *
     * @return ResultInterface
     */
    protected function run(Config $config): ResultInterface
    {
        $includes = $config->compileIncludes(static::DEFAULT_INCLUDES);

        return $this->validator->validate(...$includes);
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

    /**
     * Returns a default validator instance.
     *
     * Used by the constructor.
     *
     * @return RequiredConstantsValidator
     */
    protected function makeDefaultValidator(): RequiredConstantsValidator
    {
        return new RequiredConstantsValidator($this);
    }
}
