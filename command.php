<?php
declare(strict_types=1);

use Itineris\Preflight\Extra\PreflightExtra;

if (! class_exists('WP_CLI')) {
    return;
}

PreflightExtra::run();
