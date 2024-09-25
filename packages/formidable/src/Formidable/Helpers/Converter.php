<?php
namespace Concrete\Package\Formidable\Src\Formidable\Helpers;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\UserKey;

class Converter {

	protected $form;
	protected $elements;

	protected $result;
	protected $data;

	protected $page;
	protected $user;

	protected $skipEmpty = false;
	protected $skipLayout = false;

	public function setForm($form)
	{
		$this->form = $form;
		$this->elements = $form->getElements();
	}
	public function getForm()
	{
		return $this->form;
	}
	public function getFormElements()
	{
		return $this->elements;
	}

	public function setResult($result)
	{
		$this->result = $result;
		$this->data = $result->getElementData();

		$this->page = $result->getPageObject();
		$this->user = $result->getUserObject();
	}
	public function getResult()
	{
		return $this->result;
	}
	public function getResultData()
	{
		return $this->data;
	}

	public function setPage($page)
	{
		$this->page = $page;
	}
	public function getPage()
	{
		return $this->page;
	}

	public function setUser($user)
	{
		$this->user = $user;
	}
	public function getUser()
	{
		return $this->user;
	}

	public function setSkipEmpty($skipEmpty = false)
	{
		$this->skipEmpty = $skipEmpty;
	}
	public function getSkipEmpty()
	{
		return $this->skipEmpty;
	}

	public function setSkipLayout($skipLayout = false)
	{
		$this->skipLayout = $skipLayout;
	}
	public function getSkipLayout()
	{
		return $this->skipLayout;
	}

	public function convertTags($content, $format = '%s: %s <br />')
    {
		$labels = $values = [];

		// change all paths to full urls
		$content = $this->convertURL($content);

		// convert all form data in message
		if (preg_match('/{%form_/', $content)) {
			$form = $this->getForm();
			if (is_object($form)) {
				$all = '';
				foreach (self::getFormTags() as $tag) {
					$labels[] = '/{%form_label\|'.$tag['handle'].'%}/';
					$label = preg_quote($tag['label']);
					$values[] = $label;

					$value = '';
					$labels[] = '/{%form_value\|'.$tag['handle'].'%}/';
					$value = preg_quote((string)$form->{$tag['callback']}());
					$values[] = $value;

					$all .= sprintf($format, $label, $value);
				}
				$labels[] = '/{%form_data%}/';
				$values[] = $all;
			}
		}

        // convert all result in message
		if (preg_match('/{%result_/', $content)) {
			$result = $this->getResult();
			if (is_object($result)) {
				$all = '';
				foreach (self::getResultTags() as $tag) {
					$labels[] = '/{%result_label\|'.$tag['handle'].'%}/';
					$label = preg_quote($tag['label']);
					$values[] = $label;

					$value = '';
					$labels[] = '/{%result_value\|'.$tag['handle'].'%}/';
					$arg = '';
					$callback = $tag['callback'];
					if (is_array($callback)) {
						$arg = $callback[1];
						$callback = $callback[0];
					}
					$value = preg_quote((string)$result->{$callback}($arg));
					$values[] = $value;

					$all .= sprintf($format, $label, $value);
				}
				$labels[] = '/{%result_data%}/';
				$values[] = $all;
			}
		}

		// convert all form elements in message
		if (preg_match('/{%element_/', $content)) {
			$resultData = $this->getResultData();
			if (is_object($resultData)) {
				$all = '';
				$elements = $this->getFormElements();
				foreach ($elements as $el) {

					$type = $el->getTypeObject();

					if ($this->getSkipLayout() && $type->getGroupHandle() == 'layout') {
						continue;
					}

					// it true, there is an active dependecy rule.
					// if active, skip validation
					if ($type->skipByDependency($resultData)) {
					 	continue;
					}

					$value = '';
					$value = $resultData->get($el->getItemID());
					if (!empty($value)) {
						$value = $resultData->get($el->getItemID())->getDisplayValue('html');
					}
					if ($type->getGroupHandle() == 'layout') {
						$value = $el->field();
					}

					if ($this->getSkipEmpty() && empty($value)) {
						continue;
					}

					$labels[] = '/{%element_label\|'.$el->getHandle().'%}/';
					$label = preg_quote($el->getName());
					$values[] = $label;

					$labels[] = '/{%element_value\|'.$el->getHandle().'%}/';
					$value = preg_quote($value);
					$values[] = $value;

					$all .= sprintf($format, $label, $value);

				}
				$labels[] = '/{%element_data%}/';
				$values[] = $all;
			}
		}

		// convert all page data in message
		if (preg_match('/{%page_/', $content)) {
			$page = $this->getPage();
			if (is_object($page)) {
				$all = '';
				foreach (self::getPageTags() as $tag) {
					$labels[] = '/{%page_label\|'.$tag['handle'].'%}/';
					$label = preg_quote($tag['label']);
					$values[] = $label;

					$value = '';
					$labels[] = '/{%page_value\|'.$tag['handle'].'%}/';
					if (is_object($page)) {
						$arg = '';
						$callback = $tag['callback'];
						if (is_array($callback)) {
							$arg = $callback[1];
							$callback = $callback[0];
						}
						$value = preg_quote((string)$page->{$callback}($arg));
					}
					$values[] = $value;

					$all .= sprintf($format, $label, $value);
				}
				$labels[] = '/{%page_data%}/';
				$values[] = $all;
			}
		}


		// convert all page data in message
		if (preg_match('/{%user_/', $content)) {
			$user = $this->getUser();
			if (is_object($user)) {
				$all = '';
				foreach (self::getUserTags() as $tag) {
					$labels[] = '/{%user_label\|'.$tag['handle'].'%}/';
					$label = preg_quote($tag['label']);
					$values[] = $label;

					$value = '';
					$labels[] = '/{%user_value\|'.$tag['handle'].'%}/';
					if (is_object($user)) {
						$arg = '';
						$callback = $tag['callback'];
						if (is_array($callback)) {
							$arg = $callback[1];
							$callback = $callback[0];
						}
						if (method_exists($user, $callback)) {
							$value = (string)$user->{$callback}($arg);
						}
						else {
							$value = (string)$user->getUserInfoObject()->{$callback}($arg);
						}

						$value = preg_quote($value);
					}
					$values[] = $value;

					$all .= sprintf($format, $label, $value);
				}
				$labels[] = '/{%user_data%}/';
				$values[] = $all;
			}
		}

        // now clean up!
		// remove empty labels / values
		$labels[] = '/{%(.*)%}(|:)/';
		$values[] = '';

		$labels[] = "/<[^\/>]*>([\s]?)*<\/[^>]*>/";
		$values[] = '';

		// do your magic!
		$content = preg_replace($labels, $values, $content);
		$content = $this->inversePregQuote($content);

		// remove empty tags
		//$pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/";
		//$content = preg_replace($pattern, '', $content);

		return $content;
	}

	public static function getFormTags()
	{
		$data = [
			[
				'handle' => 'form_id',
				'label' => t('Form ID'),
				'comment' => t('(unique ID)'),
				'type' => t('Integer'),
				'callback' => 'getItemID'
			],
			[
				'handle' => 'form_name',
				'label' => t('Name'),
				'type' => t('Text'),
				'callback' => 'getName'
			],
			[
				'handle' => 'form_handle',
				'label' => t('Handle'),
				'type' => t('Text'),
				'callback' => 'getHandle'
			],
		];
		return $data;
	}

	public static function getResultTags()
	{
		$data= [
			[
				'handle' => 'result_id',
				'label' => t('Result ID'),
				'comment' => t('(unique ID)'),
				'type' => t('Integer'),
				'callback' => 'getItemID'
			],
			[
				'handle' => 'result_ip',
				'label' => t('IP'),
				'type' => t('Text'),
				'callback' => 'getIP'
			],
			[
				'handle' => 'result_browser',
				'label' => t('Browser'),
				'type' => t('Text'),
				'callback' => 'getBrowser'
			],
			[
				'handle' => 'result_device',
				'label' => t('Device'),
				'type' => t('Text'),
				'callback' => 'getDevice'
			],
			[
				'handle' => 'result_resolution',
				'label' => t('Resolution'),
				'type' => t('Text'),
				'callback' => 'getResolution'
			],
			[
				'handle' => 'result_operating_system',
				'label' => t('Operating System'),
				'type' => t('Text'),
				'callback' => 'getOperatingSystem'
			],
			[
				'handle' => 'result_locale',
				'label' => t('Locale'),
				'type' => t('Text'),
				'callback' => ['getLocale', 'true']
			],
			[
				'handle' => 'result_date_added',
				'label' => t('Date Added'),
				'type' => t('Text'),
				'callback' => ['getDateAdded', 'true']
			],
		];
		return $data;
	}

	public static function getPageTags()
	{
		$data= [
			[
				'handle' => 'page_id',
				'label' => t('Page ID'),
				'comment' => t('(unique ID)'),
				'type' => t('Integer'),
				'callback' => 'getCollectionID'
			],
			[
				'handle' => 'page_name',
				'label' => t('Page Name'),
				'type' => t('Text'),
				'callback' => 'getCollectionName'
			],
			[
				'handle' => 'page_url',
				'label' => t('Page URL'),
				'comment' => t('(link)'),
				'type' => t('URL'),
				'callback' => 'getCollectionLink'
			],
			[
				'handle' => 'page_added',
				'label' => t('Date Added'),
				'type' => t('Date'),
				'callback' => 'getCollectionDateAdded'
			],
			[
				'handle' => 'page_modified',
				'label' => t('Last Modification Date'),
				'type' => t('Date'),
				'callback' => 'getCollectionDateLastModified'
			],
			[
				'handle' => 'page_public',
				'label' => t('Publication Date'),
				'type' => t('Date'),
				'callback' => 'getCollectionDatePublic'
			],
		];

		$aks = CollectionKey::getList();
		foreach ($aks as $ak) {
			$type = $ak->getAttributeType();
			$data[] = [
				'handle' => 'ak_page_'.$ak->getAttributeKeyHandle(),
				'label' => tc('AttributeKeyName', $ak->getAttributeKeyName()),
				'type' => tc('AttributeTypeName', $type->getAttributeTypeName()),
				'callback' => ['getAttribute', $type->getAttributeTypeHandle()]
			];
		}

		return $data;
	}

	public static function getUserTags()
	{
		$data= [
			[
				'handle' => 'user_id',
				'label' => t('User ID'),
				'comment' => t('(unique ID)'),
				'type' => t('Integer'),
				'callback' => 'getUserID'
			],
			[
				'handle' => 'user_name',
				'label' => t('Username'),
				'type' => t('Text'),
				'callback' => 'getUserName'
			],
			[
				'handle' => 'user_email',
				'label' => t('Email Address'),
				'type' => t('Email Address'),
				'callback' => 'getUserEmail'
			],
			[
				'handle' => 'user_profile',
				'label' => t('Profile URL'),
				'comments' => t('(link)'),
				'type' => t('URL'),
				'callback' => 'getUserPublicProfileUrl'
			],
		];

		$aks = UserKey::getList();
		foreach ($aks as $ak) {
			$type = $ak->getAttributeType();
			$data[] = [
				'handle' => 'ak_user_'.$ak->getAttributeKeyHandle(),
				'label' => tc('AttributeKeyName', $ak->getAttributeKeyName()),
				'type' => tc('AttributeTypeName', $type->getAttributeTypeName()),
				'callback' => ['getAttribute', $type->getAttributeTypeHandle()]
			];
		}

		return $data;
	}

	private function convertURL($content)
    {
		$content = str_ireplace([' href=" http',' src=" http'], [' href="http',' src="http'], $content);

		// replace relative urls by absolute (prefix them with BASE_URL)
		$pattern = '/href=[\'|"](?!http|https|ftp|irc|feed|mailto|#)([\/]?)([^\'|"]*)[\'|"]/i';
		$replace = 'href="'.BASE_URL.'/$2"';
		$content = preg_replace($pattern, $replace, $content);

		// replace relative img urls by absolute (prefix them with BASE_URL)
		$pattern = '/src=[\'|"](?!http|https|ftp|irc|feed|mailto|#)([\/]?)([^\'|"]*)[\'|"]/i';
		$replace = 'src="'.BASE_URL.'/$2"';
		$content = preg_replace($pattern, $replace, $content);

		return $content;
	}

    private function inversePregQuote($str) {
		return strtr($str, [
			'\\.'  => '.',
			'\\\\' => '\\',
			'\\+'  => '+',
			'\\*'  => '*',
			'\\?'  => '?',
			'\\['  => '[',
			'\\^'  => '^',
			'\\]'  => ']',
			'\\$'  => '$',
			'\\('  => '(',
			'\\)'  => ')',
			'\\{'  => '{',
			'\\}'  => '}',
			'\\='  => '=',
			'\\!'  => '!',
			'\\<'  => '<',
			'\\>'  => '>',
			'\\|'  => '|',
			'\\:'  => ':',
			'\\-'  => '-'
        ]);
	}


}