<?php

namespace Application\Concrete\Models\Locale;

use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Page;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\View\View;
use Core;

class App
{
    const LOCALE_EN = 'en_US';
    const LOCALE_AR = 'ar_KW';
    const ARABIC    = 'Arabic';
    const ENGLISH   = 'English';

    const ARABIC_PAGE_SLUG = '/ar';

    const ENGLISH_SLUG      = 'en';
    const ARABIC_SLUG       = 'ar';

    public static function isArabic()
    {
        return (self::getSessionLocale() == self::LOCALE_AR);
    }

    public static function getHomeUrl()
    {
        return (self::getSessionLocale() == self::LOCALE_AR) ? View::url('/ar') : View::url('/');
    }

    public static function getSearchUrl()
    {
        return (self::getSessionLocale() == self::LOCALE_AR) ? View::url('/ar/search') : View::url('/search');
    }

    /**
     * This is a utility function that is used inside a view to setup urls w/tasks and parameters. This will return the URL according to current locale.
     *
     * @param string $action
     * @param string $task
     *
     * @return string $url
     */

    public static function getLocaleUrl($action, $task = null)
    {
        return (self::getSessionLocale() == self::LOCALE_AR) ? View::url(static::ARABIC_PAGE_SLUG  . $action, $task) : View::url($action, $task);
    }

    public static function getTempLocale()
    {
        return isset($_REQUEST['_lcl']) && static::isLocaleValid($_REQUEST['_lcl']) ? $_REQUEST['_lcl'] : '';
    }

    public static function isLocaleValid($locale)
    {
        return in_array($locale, [self::LOCALE_EN, self::LOCALE_AR, self::ENGLISH_SLUG, self::ARABIC_SLUG], true);
    }

    public static function getSessionLocale()
    {
        return Localization::activeLocale();
    }

    protected static function getLocaleFromUrl()
    {
        $currpage       = Page::getCurrentPage();
        if ($currpage){
            $is_system_page = $currpage->isSystemPage(); //single page
            if (!$is_system_page) {
                $requesr_uri = $_SERVER['REQUEST_URI'];
                //cross check with the page id now.
                $collectionPath = $currpage->getCollectionPath();
                if ($collectionPath === '/ar' || str_contains($requesr_uri, '/ar/')) {
                    return App::LOCALE_AR;
                } else {
                    return App::LOCALE_EN;
                }
            }
        }
        return App::LOCALE_EN;
    }

    public static function setSessionLocale($locale)
    {
        $_SESSION['ACTIVE_LOCALE'] = $locale;
    }

    public static function getSessionLocaleName()
    {
        if (self::getSessionLocale() == App::LOCALE_EN) {
            return App::ENGLISH;
        } else {
            return App::ARABIC;
        }
    }

    public static function getPublicDomain() {
        return Config::get('site.sites.public.domain');
    }

    public static function setSessionLocaleFromUrl()
    {
        //Set session locale as per /ar.
        //check in url first
        $currpage = Page::getCurrentPage();
        $is_system_page = $currpage->isSystemPage(); //single page


        if(!$is_system_page) {
            $requesr_uri = $_SERVER['REQUEST_URI'];

            //cross check with the page id now.
            $collectionPath = $currpage->getCollectionPath();
            if($currpage->getCollectionPath() === '/ar' || str_contains($requesr_uri,'/ar/')) {
                App::setSessionLocale(App::LOCALE_AR);
            } else {
                App::setSessionLocale(App::LOCALE_EN);
            }
        }else{
            //system page
            //dont change locale based on path
        }

        //finally set the locale in the lang package
        $lang = Section::getByLocale(App::getSessionLocale());
        Core::make('session')->set('multilingual_default_locale', $lang->getLocale());
    }

    public static function getApiLocale($language)
    {
        if($language) {
            switch ($language) {
                case self::ENGLISH_SLUG:
                    $language = self::LOCALE_EN;
                    break;
                case self::ARABIC_SLUG:
                    $language = self::LOCALE_AR;
                    break;
            }
        }

        return $language;
    }

    public static function getAlternateLocalePageId($pageId, $locale) {

        $translatedPageId = null;
        $page = null;

        if($pageId) {
            $page = Page::getByID($pageId);
        }

        if($page) {
            $locale = $locale == self::LOCALE_AR ? self::LOCALE_EN : self::LOCALE_AR;

            $lang = Section::getByLocale($locale);

            $translatedPageId = $lang->getTranslatedPageID($page);
        }

        return $translatedPageId;
    }
}