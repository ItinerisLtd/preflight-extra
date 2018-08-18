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

    public const ID = 'required-constants';
    public const DESCRIPTION = 'Ensure required constants are defined.';

    /**
     * {@inheritdoc}
     *
     * @param Config $config The config instance.
     *
     * @return ResultInterface
     */
    protected function run(Config $config): ResultInterface
    {
        $includes = $config->compileIncludes([]);

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
        return $this->errorIfCompiledIncludesIsEmpty($config, []);
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
