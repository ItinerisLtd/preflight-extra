<?php
declare(strict_types=1);

namespace Itineris\Preflight\Extra;

use Itineris\Preflight\CheckerCollectionFactory;
use Itineris\Preflight\Extra\Checkers\ExpectedStatusCodes;
use Itineris\Preflight\Extra\Checkers\ProductionHomeUrl;
use Itineris\Preflight\Extra\Checkers\ProductionSiteUrl;
use Itineris\Preflight\Extra\Checkers\RequiredConstants;
use Itineris\Preflight\Extra\Checkers\RequiredPackages;
use Itineris\Preflight\Extra\Checkers\RequiredPlugins;
use WP_CLI;

class PreflightExtra
{
    private const CHECKERS = [
        ExpectedStatusCodes::class,
        ProductionSiteUrl::class,
        ProductionHomeUrl::class,
        RequiredConstants::class,
        RequiredPackages::class,
        RequiredPlugins::class,
    ];

    /**
     * Begin package execution.
     */
    public static function run(): void
    {
        foreach (self::CHECKERS as $checker) {
            WP_CLI::add_wp_hook(CheckerCollectionFactory::REGISTER_HOOK, [$checker, 'register'], 200);
        }
    }
}
