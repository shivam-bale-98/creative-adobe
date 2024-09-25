<?php

use Concrete\Core\Routing\Router;

/** @var Router $router */

$router = $app->make(Router::class);

//AppApi
$router->buildGroup()
    ->setPrefix('/api/v1/blocks')
    ->setNamespace('\Application\Concrete\Api\V1')
    ->routes(function ($groupRouter) {
        $groupRouter->get('/listing_block', 'BlocksApi::getListingBlock');
        $groupRouter->get('/slider_block', 'BlocksApi::getSliderBlockAttributeOptions');

        $groupRouter->get('/download_block', 'DownloadsBlockApi::getDownloadsBlock');

    });