<?php

namespace Application\Concrete\Helpers;
use Application\Concrete\Models\Vacancy\Vacancy;

class ConstantHelper
{
    const NOT_RESULTS_FOUND_TEMPLATE = "utilities/no_results_found";

    const SEARCH_TYPE_PRODUCTS = "products";
    const SEARCH_TYPE_APPLICATIONS = "applications";
    const SEARCH_TYPE_CREATIONS = "creations";
    const SEARCH_TYPE_TECHNOLOGY = "technology";
    const SEARCH_TYPE_BLOG = "blog";
    const SEARCH_TYPE_CAREERS = "careers";

    const MEGA_MENU_TYPE_ONE = "Type One";
    const MEGA_MENU_TYPE_TWO = "Type Two";

    const ATTR_MEGA_MENU_TYPE = "mega_menu_type";
    const ATTR_MEGA_MENU = "is_mega_menu_enabled";

    const NAV_LEFT_ITEM = "blocks/auto_nav/header_nav/left_item";
    const NAV_RIGHT_ITEM = "blocks/auto_nav/header_nav/right_item";

    const SEARCH_TYPE_OPTIONS = [
        "" => "All",
        self::SEARCH_TYPE_PRODUCTS => "Products",
        self::SEARCH_TYPE_APPLICATIONS => "Applications",
        self::SEARCH_TYPE_CREATIONS => "Creations",
        self::SEARCH_TYPE_TECHNOLOGY => "Technology",
        self::SEARCH_TYPE_BLOG => "Blog",
        self::SEARCH_TYPE_CAREERS => "Careers"
    ];

    const SEARCH_TYPE_FILTER = [
        self::SEARCH_TYPE_PRODUCTS => 'product_detail',
        self::SEARCH_TYPE_APPLICATIONS => 'application_detail',
        self::SEARCH_TYPE_CREATIONS => 'case_study_detail',
        self::SEARCH_TYPE_TECHNOLOGY => Vacancy::PAGE_HANDLE,
        self::SEARCH_TYPE_BLOG => 'blog_entry',
        self::SEARCH_TYPE_CAREERS => 'vacancy_detail',
    ];

    const FOUND_TEXT = 'found';

    const MEDIUM_DIAMETER_RANGE = 2;

    const DIAMETER_QUERIES = [
        'More than 2 meters' => 'csi.ak_cs_diameter > '.self::MEDIUM_DIAMETER_RANGE,
        'Less than 2 meters' => 'csi.ak_cs_diameter < '.self::MEDIUM_DIAMETER_RANGE,
    ];
}