<?php

/**
 * WooCommerce moves internal classes between releases, and this finder is also
 * used to regenerate stubs for older versions, so every appended directory is
 * optional: a missing one is skipped instead of aborting the whole run.
 *
 * @param string $dir Directory relative to the repository root.
 * @param callable(\StubsGenerator\Finder): \StubsGenerator\Finder $configure
 */
$optional = static function (string $dir, callable $configure): ?\StubsGenerator\Finder {
    if (!\is_dir($dir)) {
        return null;
    }

    return $configure(\StubsGenerator\Finder::create()->in([$dir]));
};

$stubs = \StubsGenerator\Finder::create()
    ->in('source/woocommerce/includes');

$appends = [
    $optional(
        'source/woocommerce',
        static fn($f) => $f->files()->depth('< 1')->path('woocommerce.php')
    ),
    $optional(
        'source/woocommerce/src',
        static fn($f) => $f
            ->notPath('Api')
            ->notPath('Internal')
            // Uses woocommerce/blueprint
            ->notPath('Admin/Features/Blueprint')
            ->sortByName(true)
    ),
    // WC_Query uses this internal trait
    $optional(
        'source/woocommerce/src/Internal/Traits',
        static fn($f) => $f->files()->depth('< 1')->path('AccessiblePrivateMethods.php')
    ),
    // WC_Abstract_Order uses these internal traits
    $optional(
        'source/woocommerce/src/Internal/CostOfGoodsSold',
        static fn($f) => $f->files()->depth('< 1')
    ),
    $optional(
        'source/woocommerce/src/Internal',
        static fn($f) => $f->files()->depth('< 1')->path('RegisterHooksInterface.php')
    ),
    // Removed in WooCommerce 11.0; the surviving classes moved to
    // src/Admin/BlockTemplates, which the src/ append above already covers.
    $optional(
        'source/woocommerce/src/Internal/Admin/BlockTemplates',
        static fn($f) => $f->files()->depth('< 1')
    ),
    $optional(
        'source/woocommerce/src/Internal/Traits',
        static fn($f) => $f->files()->depth('< 1')->path('OrderAttributionMeta.php')
    ),
    $optional(
        'source/woocommerce/lib/packages/Detection',
        static fn($f) => $f->files()->depth('< 1')->path('MobileDetect.php')
    ),
    // ProductQuery uses this internal interface
    $optional(
        'source/woocommerce/src/Internal/ProductFilters/Interfaces',
        static fn($f) => $f->files()->depth('< 1')->path('QueryClausesGenerator.php')
    ),
    // WC_REST_Products_V2_Controller uses this internal trait
    $optional(
        'source/woocommerce/src/Internal/Traits',
        static fn($f) => $f->files()->depth('< 1')->path('RestApiCache.php')
    ),
    // FulfillmentException extends this internal class
    $optional(
        'source/woocommerce/src/Internal/Admin/Settings/Exceptions',
        static fn($f) => $f->files()->depth('< 1')->path('ApiException.php')
    ),
    $optional(
        'source/woocommerce/src/Internal',
        static fn($f) => $f->files()->depth('< 1')->path('RestApiControllerBase.php')
    ),
/*
    $optional(
        'source/woocommerce/src/Internal/Admin',
        static fn($f) => $f->files()->depth('< 1')->path('CouponsMovedTrait.php')
    ),
*/
/*
    // Comment out existing interface exclusion
    // $ editor vendor/php-stubs/generator/src/NodeVisitor.php:352
    $optional(
        'source/woocommerce/vendor/psr/container/src',
        static fn($f) => $f->sortByName(true)
    ),
*/
];

foreach (\array_filter($appends) as $append) {
    $stubs->append($append);
}

return $stubs
    // Exclude woocommerce.com API as is uses the woocommerce-rest-api package.
    ->notPath('wccom-site/rest-api/endpoints')
    // Exclude WP-CLI command as is extends Plugin_Command.
    ->notPath('cli/class-wc-cli-com-extension-command.php')
    // Templates.
    ->notPath('admin/views')
    ->notPath('admin/helper/views')
    ->notPath('admin/importers/views')
    ->notPath('admin/marketplace-suggestions/templates')
    ->notPath('admin/marketplace-suggestions/views')
    ->notPath('admin/meta-boxes/views')
    ->notPath('admin/plugin-updates/views')
    ->notPath('admin/settings/views')
    // $ ls includes/shipping/*/includes/*.php
    ->notPath('shipping/flat-rate/includes/settings-flat-rate.php')
    ->notPath('shipping/legacy-flat-rate/includes/settings-flat-rate.php')
    // Legacy WooCommerce API.
    ->notPath('api/legacy')
    ->notPath('legacy/api')
    // Update functions.
    ->notPath('wc-update-functions.php')
    ->sortByName(true)
;
