<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Checkers;

use Itineris\Preflight\Checkers\AbstractChecker;
use Itineris\Preflight\Checkers\Traits\ValidatorAwareTrait;
use Itineris\Preflight\Config;
use Itineris\Preflight\Extra\Validators\StatusCodes;
use Itineris\Preflight\ResultFactory;
use Itineris\Preflight\ResultInterface;
use Itineris\Preflight\Results\Error;
use Itineris\Preflight\Validators\AbstractValidator;

class ExpectedStatusCodes extends AbstractChecker
{
    use ValidatorAwareTrait;

    public const ID = 'extra-expected-status-codes';
    public const DESCRIPTION = '**Experimental** Ensure paths return expected status codes.';

    /**
     * Run the check and return a result.
     *
     * Assume the checker is enabled and its config make sense.
     *
     * TODO: Test me!
     *
     * @param Config $config The config instance.
     *
     * @return ResultInterface
     */
    protected function run(Config $config): ResultInterface
    {
        $groups = (array) $config->get('groups');

        // TODO: Test me!
        $results = array_map(function (array $group): ResultInterface {
            $code = (int) $group['code'] ?? 200;

            $this->validator->setExpectedStatusCode($code);

            $urls = array_map(function (string $path): string {
                return home_url($path);
            }, $group['paths'] ?? []);

            return $this->validator->validate(...$urls);
        }, $groups);

        // TODO: Method to merge results? Or, allow returning multiple results?
        // Assume successful results don't have messages .
        $messages = array_map(function (ResultInterface $result): array {
            return $result->getMessages();
        }, $results);

        switch (count($messages)) {
            case 0:
                $messages = [];
                break;
            case 1:
                $messages = $messages[0];
                break;
            default:
                $messages = array_merge(...$messages);
        }

        $messages = array_filter($messages);

        if (! empty($messages)) {
            return ResultFactory::makeFailure(
                $this,
                array_merge(['***** Experimental *****', 'Something went wrong:'], $messages)
            );
        }

        return ResultFactory::makeSuccess($this);
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
        $groups = (array) $config->get('groups');

        return empty($groups)
            ? ResultFactory::makeError($this, ['***** Experimental *****', 'Groups is empty.'])
            : null;
    }

    /**
     * Returns a default validator instance.
     *
     * Used by the constructor.
     *
     * @return AbstractValidator
     */
    protected function makeDefaultValidator(): AbstractValidator
    {
        return new StatusCodes($this, '');
    }
}
