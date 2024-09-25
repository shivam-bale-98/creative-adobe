<?php
namespace Concrete\Package\Formidable\Src\Formidable\Helpers;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Http\Request;
use Concrete\Core\Localization\Localization;
use Concrete\Package\Formidable\Src\Formidable\Results\ResultLog;
use Concrete\Package\Formidable\Src\BrowserDetection;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Text;
use Concrete\Package\Formidable\Src\Formidable\Forms\Columns\Column;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;
use Concrete\Package\Formidable\Src\Formidable\Forms\Rows\Row;
use Concrete\Package\Formidable\Src\Formidable\Templates\Template;
use Concrete\Package\Formidable\Src\Formidable\Mails\Mail;

class Common {

    public static function getIP()
	{
		$request = app()->make(Request::class);
		$ip = $request->server->get('REMOTE_ADDR');
		if (!empty($request->server->get('HTTP_CLIENT_IP'))) {
			$ip = $request->server->get('HTTP_CLIENT_IP');
		} elseif (!empty($request->server->get('HTTP_X_FORWARDED_FOR'))) {
			$ip = $request->server->get('HTTP_X_FORWARDED_FOR');
		}
		return $ip;
	}

	public static function getLocale()
    {
		return Localization::activeLocale();
	}

    public static function createLog($result, $action = '', $u = '')
    {
        if (empty($u)) {
            $u = new User();
        }
        $log = new ResultLog();
        $log->setResult($result);
        $log->setAction($action);
        $log->setUser($u);
		$log->setDateAdded(new \DateTime());
        $log->save();
    }

	public static function getBrowserData()
	{
		$browser = new BrowserDetection();
		$info = $browser->getAll(app()->make(Request::class)->server->get('HTTP_USER_AGENT'));
		if (count($info)) {
			return $info;
		}
		return [];
	}

	public static function generateHandle($name, $type = 'form', $form = null, $current = null, &$additional = 0)
	{
		$handle = app()->make(Text::class)->handle($name);
		if (!empty($additional)) {
			$handle = $handle.'_'.$additional;
		}

		switch ($type) {
			case 'form':
				$item = Form::getByHandle($handle);
				$current = $form;
			break;
			case 'row':
				$item = Row::getByHandle($handle, $form);
			break;
			case 'column':
				$item = Column::getByHandle($handle, $form);
			break;
			case 'element':
				$item = Element::getByHandle($handle, $form);
			break;
			case 'mail':
				$item = Mail::getByHandle($handle, $form);
			break;
			case 'template':
				$item = Template::getByHandle($handle);
				$current = $form;
			break;
		}

		// non existing found!
		if (!is_object($item)) {
			return $handle;
		}

		// is the same, so return!
		if (is_object($current) && $item->getItemID() == $current->getItemID()) {
			return $handle;
		}

		// existing, but not current. so try again!
		$additional++;
		$handle = self::generateHandle($name, $type, $form, $current, $additional);

		return $handle;
	}

	public static function createURL($string)
	{
		if (!preg_match("/^(?:ht)tps?:\/\//i", $string)) {
			$string = "https://" . $string;
		}
		return $string;
	}
}