<?php
namespace Application\Concrete\View;

class View extends \Concrete\Core\View\View
{
    public static function elementRender($_file, $args = null, $_pkgHandle = null)
    {
        $html = '';
        ob_start();
        static::element($_file, $args, $_pkgHandle);
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
