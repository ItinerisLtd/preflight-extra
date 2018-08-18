<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra\Validators;

use Itineris\Preflight\ResultInterface;
use Itineris\Preflight\Validators\AbstractValidator;

class StatusCodes extends AbstractValidator
{
    /**
     * The expected status code.
     *
     * @var int
     */
    protected $expectedStatusCode = 200;

    /**
     * Expected status code setter.
     *
     * @param int $expectedStatusCode The expected status code.
     */
    public function setExpectedStatusCode(int $expectedStatusCode): void
    {
        $this->expectedStatusCode = $expectedStatusCode;
    }

    /**
     * Validates URLs return expected status code.
     *
     * @param string ...$urls Urls to be checked.
     *
     * @return ResultInterface
     */
    public function validate(string ...$urls): ResultInterface
    {
        $messages = array_filter(
            array_map(function (string $url): ?string {
                return $this->getMessage($url);
            }, $urls)
        );

        return $this->report(...$messages);
    }

    /**
     * Check the URL's status code.
     *
     * @param string $url The URL to be checked.
     *
     * @return null|string Returns null if status codes is expected.
     */
    protected function getMessage(string $url): ?string
    {
        $response = wp_remote_get($url);
        $responseCode = wp_remote_retrieve_response_code($response);

        if (! is_int($responseCode)) {
            return 'Unable to reach ' . $url;
        }

        return ($this->expectedStatusCode === $responseCode)
            ? null
            : $url . ' returns ' . $responseCode;
    }
}
