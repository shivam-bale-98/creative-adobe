<?php
namespace Concrete\Package\Formidable\Block\FormidableForm;

use Concrete\Core\Block\BlockController;

use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Http\Service\Json;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Antispam\Service as Antispam;
use Concrete\Core\Block\Block;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Converter;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Common as CommonHelper;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;
use Concrete\Package\Formidable\Src\Formidable\Forms\FormList;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Results\ResultElement;
use Concrete\Package\Formidable\Src\Formidable\Results\Result;
use Exception;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\View\View;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController {

	public $helpers = ['form'];

    /**
     * @var int|null
     */
    public $formID;

    /**
     * @var string|null
     */
    public $options;

	/**
     * @var object|null
     */
    public $ff;

	/**
     * @var object|null
     */
    public $c;

	/**
     * @var string|null
     */
    public $ccm_token;


	protected $pkgHandle = 'formidable';
	protected $btDefaultSet = 'form';
	protected $btTable = 'btFormidableForm';
	protected $btInterfaceWidth = 700;
    protected $btInterfaceHeight = 525;
	protected $btCacheBlockRecord = false;
	protected $btCacheBlockOutput = false;
	protected $btCacheBlockOutputOnPost = false;
	protected $btCacheBlockOutputForRegisteredUsers = false;
	protected $btCacheBlockOutputLifetime = 300;


	public function getBlockTypeName()
	{
		return t("Formidable Form");
	}

	public function getBlockTypeDescription()
	{
		return t("Add a Formidable Form on your page");
	}

	public function on_start()
	{
		if ($this->formID) {
			$ff = Form::getByID($this->formID);
			if (is_object($ff)) {

				if ($ff->getSite() == $this->app->make('site')->getActiveSiteForEditing()) {

					$this->ff = $ff;

					$elements = $this->ff->getElements();
					foreach ($elements as $el) {
						$el->getTypeObject()->requireAssets();
					}

					$this->set('ff', $ff);
				}
			}
		}

		// set current page
		$c = Page::getCurrentPage();
		$this->c = $c;
		$this->set('c', $c);

		$this->ccm_token = $this->app->make(Token::class)->generate($this->pkgHandle);
		$this->set('ccm_token', $this->ccm_token);

		$locale = CommonHelper::getLocale();
		$this->set('locale', $locale);

	}

	public function registerViewAssets($outputContent = '')
	{
		if (!$this->ff) {
			return;
		}

		// always use jquery
		$this->requireAsset('javascript', 'jquery');

		// now for the form specific
		$items = $this->ff->getAssets();
		foreach ((array)$items as $key => $item) {
			if ($key == 'groups') {
				foreach ($item as $i) {
					$this->requireAsset($i);
				}
				continue;
			}
			$this->requireAsset($item[0], $item[1]);
		}
	}

	public function view()
	{
		$this->loadAssets();

		// load form
		if (!$this->ff) {
			$this->set('not_found', true);
			return;
		}

		// has elements?
		if (!$this->ff->getTotalElements()) {
			$this->set('no_elements', true);
			return;
		}

		// enabled
		if (!$this->ff->getEnabled()) {
			$this->set('disabled', true);
			return;
		}

		// limits
		if ($this->ff->isLimited()) {
			if ($this->ff->getLimitMessage() == Form::LIMIT_MESSAGE_REDIRECT) {
				$this->redirectToPage($this->ff->getLimitMessagePageObject());
			}
			else {
				$message = $this->convertMessage($this->ff->getLimitMessageContent());
				$this->set('limited', $message);
				return;
			}
		}

		// schedule
		if ($this->ff->isScheduled()) {
			if ($this->ff->getScheduleMessage() == Form::SCHEDULE_MESSAGE_REDIRECT) {
				$this->redirectToPage($this->ff->getScheduleMessagePageObject());
			}
			else {
				$message = $this->convertMessage($this->ff->getScheduleMessageContent());
				$this->set('scheduled', $message);
				return;
			}
		}

        $this->requireAsset('javascript-inline', 'formidable/captcha_inline');
        $this->requireAsset('javascript', 'formidable/captcha_render');
	}

	public function add()
	{
		$this->edit();
	}

	public function composer()
    {
        $this->edit();
    }

    public function edit()
    {
		$this->loadAssets();

		$forms = [];

		$list = new FormList();
		$list->filterBySite($this->app->make('site')->getActiveSiteForEditing());
		$list->filterByEnabled();
        $items = $list->getResults();
		foreach ($items as $ff) {
			$forms[$ff->getItemID()] = $ff->getName();
		}
		if (count($forms)) {
			$this->set('forms', $forms);
		}

		$this->requireAsset('ace');
	}

    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');

		if (!$args['formID']) {
            $e->add(t('Please select a form'));
        }
		else {
			$ff = Form::getByID($args['formID']);
			if (!$ff) {
				$e->add(t('Form isn\'t valid or removed.'));
			}
		}
		$options = isset($args['options'])?$args['options']:[];
		if (isset($options['success'])) {
			if ($options['success'] == 'content') {
				if (!isset($options['successContent']) || empty($options['successContent'])) {
					$e->add(t('Message on success is invalid or empty'));
				}
			}
			if ($options['success'] == 'redirect') {
				if (!isset($options['successPage']) || empty($options['successPage'])) {
					$e->add(t('Redirect Page on success is invalid or empty'));
				}
			}
		}

        return $e;
    }

	public function save($args) {

		// format jquery/javascript callback
		$args['options']['onloadCallback'] = base64_encode($args['options']['onloadCallback']);
		$args['options']['errorsCallback'] = base64_encode($args['options']['errorsCallback']);
		$args['options']['successCallback'] = base64_encode($args['options']['successCallback']);

		$data = array(
			'formID' => (int)$args['formID'],
			'options' => (new Json())->encode($args['options'])
		);
		parent::save($data);
	}

	public function action_submit()
	{
		$request = $this->app['request'];

		// if there is another form (not formidable) the block controller shouldn't break the other form
		// therefore just return formidable. It should be fine...
		if ((int)$request->post('bID') == 0) {
			return;
		}

		$b = Block::getByID($request->post('bID'));
		if (!is_object($b)) {
			$this->setResponse(['message' => t('Formidable Form Block can\'t found')], 'error')->send();
			exit();
		}

		$cnt = $b->getController();

		$ff = Form::getByID($cnt->formID);
		if (!$ff) {
			$this->setResponse(['message' => t('Formidable Form can\'t found')], 'error')->send();
			exit();
		}

		$errors = [];

		// validate if formID matches set form in block
		if ($request->post('formID') != $ff->getItemID()) {
			$errors['form'][] = t('Formidable Form ID doesn\'t match set form');
		}

		// honeypot
		if (!empty($request->post('emailaddress'))) {
			$errors['form'][] = t('Formidable Form is illegally submitted (honeypot)');
		}

		// validate token
		if (!$this->app->make(Token::class)->validate($this->pkgHandle)) {
			$errors['form'][] = t('Invalid token, please reload page and try again');
		}

		// Validate IP
		if ($this->app->make('failed_login')->isDenylisted()) {
			$errors['form'][] = t("Unable to complete action due to a banned IP. Please contact the administrator of this site for more information.");
		}

		// Check for spammers...
		if (!$this->app->make(Antispam::class)->check(@implode("\n\r", $request->post()), $this->pkgHandle)) {
			$errors['form'][] = t("Unable to complete action due to our spam policy. Please contact the administrator of this site for more information.");
		}

        //captcha check
        $captcha = $this->app->make('helper/validation/captcha');
        if (!$captcha->check()) {
            $errors['form'][] = t('Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary.');
        }

		// validate elements
		$elements = $ff->getElements();
		foreach ($elements as $el) {
			$validate = $el->validate();
			if ($validate !== true) {
				foreach ($validate->getList() as $err) {
					$errors[$el->getHandle()][] = (string)$err;
				}
				continue;
			}
		}

		// set errors
		if (count($errors)) {
			$params = [
				'errors' => $errors,
				'ccm_token' => $this->ccm_token
			];
			$this->setResponse($params, 'error')->send();
			exit();
		}

		// load the current settings of this block
		$options = (new Json())->decode($cnt->options, true);

		// get current user
		$u = new User();

		// get current page
		$c = Page::getByID($request->post('cID'));

		// save result
		$result = new Result();
		$result->setForm($ff);
		$result->setUser($u);
		$result->setPage($c);
		$result->setBlock($b);

		if (!$ff->getPrivacy() || !$ff->getPrivacyIP()) {
			$result->setIP(CommonHelper::getIP());
		}
		if (!$ff->getPrivacy() || !$ff->getPrivacyBrowser()) {
			$info = CommonHelper::getBrowserData();
			if (count($info)) {
				$result->setBrowser($info['browser_title']);
				$result->setDevice(ucfirst($info['device_type']));
				$result->setOperatingSystem($info['os_title']);
			}
			$result->setResolution($request->post('resolution'));
		}
		$result->setLocale(CommonHelper::getLocale());
		$result->setDateAdded(new \DateTime());
		$result->setDateModified(new \DateTime());

		//Custom_Sync
        $enable_sync = Config::get('concrete.formidable_enable_sync');
        $base_uri = '';
        $formData = [
            "campaignIdSpecified" => false,
            "customerIdSpecified" => false,
            "civilIdSpecified" => false,
            "mobilePhoneSpecified" => false,
            "homePhoneSpecified" => false,
            "workPhoneSpecified" => false,
            "emailSpecified" => false,
            "dateOfBirthSpecified" => false,
            "nationalitySpecified" => false,
            "genderSpecified" => false,
            "firmNameSpecified" => false,
        ];

		// get data for each element
		foreach ($elements as $el) {

			// skip non-searchable elements
			if (!$el->getTypeObject()->isEditableOption('searchable', 'bool')) {
				continue;
			}

			// transform post data to database value (json)
			$postData = $el->getProcessedData();

			// skip empty data
			if ((int)$options['saveEmptyData'] != 1 && empty($postData)) {
				continue;
			}

			// transform data for display methods
			$displayData = $el->getDisplayData($postData, 'plain'); // just plain text for now

            //Check fields
            if($enable_sync) {
                switch ($el->getHandle()) {
                    case 'first_name':
                        $formData["customerName"] = $displayData;
                        break;

                    case 'last_name':
                        $formData["customerName"] .= " " . $displayData;
                        break;

                    case 'civil_id_no':
                        $formData["civilIdSpecified"] = true;
                        $formData["civilId"] = $displayData;
                        break;

                    case 'mobile_number':
                        $formData["mobilePhoneSpecified"] = true;
                        $formData["mobilePhone"] = $displayData;
                        break;

                    case 'home_phone':
                        $formData["homePhoneSpecified"] = true;
                        $formData["homePhone"] = $displayData;
                        break;

                    case 'work_phone':
                        $formData["workPhoneSpecified"] = true;
                        $formData["workPhone"] = $displayData;
                        break;

                    case 'e_mail':
                        $formData["emailSpecified"] = true;
                        $formData["email"] = $displayData;
                        break;

                    case 'date_of_birth':
                        $formData["dateOfBirthSpecified"] = true;
                        $formData["dateOfBirth"] = $displayData;
                        break;

                    case 'national_of':
                        $formData["nationalitySpecified"] = true;
                        $formData["nationality"] = $displayData;
                        break;

                    case 'gender':
                        $formData["genderSpecified"] = true;
                        $formData["gender"] = $displayData;
                        break;

                    case 'lead_source':
                        $formData["leadSourceSpecified"] = true;
                        $formData["leadSource"] = $displayData;
                        break;

                    case 'lead_type_name':
                        $formData["leadTypeNameSpecified"] = true;
                        $formData["leadTypeName"] = $displayData;
                        break;

                    case 'parent_lead_type_name':
                        $formData["parentLeadTypeNameSpecified"] = true;
                        $formData["parentLeadTypeName"] = $displayData;
                        break;

                    case 'age_range':
                        $formData["additionalLeadData"]["Age Range"] = $displayData;
                        break;

                    case 'salary':
                        $formData["additionalLeadData"]["Salary"] = $displayData;
                        break;

                    case 'preferred_contact_time':
                        $formData["additionalLeadData"]["PreferredContactTime"] = $displayData;
                        break;

                    case 'employer':
                        $formData["additionalLeadData"]["Employer"] = $displayData;
                        break;

                    case 'employer_name':
                        $formData["additionalLeadData"]["EmployerName"] = $displayData;
                        break;

                    case 'base_uri':
                        $base_uri = $displayData;
                        break;
                }
            }

            $re = new ResultElement();
			$re->setResult($result);
			$re->setElement($el);

			// set data
			$re->setPostValue($postData);
			$re->setDisplayValue($displayData);

			// add to result
			$result->addElementData($el, $re);
		}

		// save the result
		$result->save();

		//Custom_Sync
        if ($enable_sync && $base_uri) {
            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument("formData", $formData);
            $event->setArgument("base_uri", $base_uri);

            \Events::dispatch('on_formidable_submit_sf', $event);
        }

		// log result
		CommonHelper::createLog($result, t('Created'), $u);

		// send mails
		foreach ($ff->getMails() as $mail) {
			try {
				$mail->setResult($result);
				$mail->send();
				// log mail
				CommonHelper::createLog($result, t('Mail "%s" send to: %s', $mail->getName(), $mail->getToDisplayPlain()), $u);
			}
			catch(Exception $e) {
				// log mail
				CommonHelper::createLog($result, t('Mail "%s" unsuccessful: %s', $mail->getName(), $e->getMessage()), $u);
			}
		}

		// response to block
		if ($options['success'] == 'redirect') {
			$page = Page::getByID($options['successPage']);
			if ($page) {
				$response['redirect'] = $page->getCollectionLink();
			}
		}
		else {
			$message = $this->app->make(Text::class)->decodeEntities($options['successContent']);

			$converter = new Converter();
			$converter->setForm($ff);
			$converter->setResult($result);
			$converter->setSkipEmpty(true);
			$message = $converter->convertTags($message);

			$response = [
				'message' => $message
			];
		}
		$this->setResponse($response, 'success')->send();
		exit();
	}

	public function action_upload()
	{
		$request = $this->app['request'];

		if ((int)$request->post('bID') == 0) {
			$this->setResponse(['message' => t('Formidable Form Block can\'t found')], 'error')->send();
			exit();
		}

		$b = Block::getByID($request->post('bID'));
		if (!is_object($b)) {
			$this->setResponse(['message' => t('Formidable Form Block can\'t found')], 'error')->send();
			exit();
		}

		$cnt = $b->getController();

		$ff = Form::getByID($cnt->formID);
		if (!$ff) {
			$this->setResponse(['message' => t('Formidable Form can\'t found')], 'error')->send();
			exit();
		}

		$errors = [];

		// validate if formID matches set form in block
		if ($request->post('formID') != $ff->getItemID()) {
			$errors['form'][] = t('Formidable Form ID doesn\'t match set form');
		}

		$element = Element::getByHandle($request->post('handle'), $ff);
		if (!$element) {
			$this->setResponse(['message' => t('Element can\'t found')], 'error')->send();
			exit();
		}

		// upload file
		$response = $element->getTypeObject()->upload();
		if (isset($response['errors'])) {
			$errors = [];
			foreach ((array)$response['errors'] as $err) {
				$errors[] = (string)$err;
			}
			$params = [
				'errors' => $errors,
				'ccm_token' => $this->ccm_token
			];
			$this->setResponse($params, 'error')->send();
			exit();
		}

		// response to block
		$this->setResponse($response, 'success')->send();
		exit();
	}

	public function action_delete()
	{
		$request = $this->app['request'];

		if ((int)$request->post('bID') == 0) {
			$this->setResponse(['message' => t('Formidable Form Block can\'t found')], 'error')->send();
			exit();
		}

		$b = Block::getByID($request->post('bID'));
		if (!is_object($b)) {
			$this->setResponse(['message' => t('Formidable Form Block can\'t found')], 'error')->send();
			exit();
		}

		$cnt = $b->getController();

		$ff = Form::getByID($cnt->formID);
		if (!$ff) {
			$this->setResponse(['message' => t('Formidable Form can\'t found')], 'error')->send();
			exit();
		}

		$errors = [];

		// validate if formID matches set form in block
		if ($request->post('formID') != $ff->getItemID()) {
			$errors['form'][] = t('Formidable Form ID doesn\'t match set form');
		}

		$element = Element::getByHandle($request->post('handle'), $ff);
		if (!$element) {
			$this->setResponse(['message' => t('Element can\'t found')], 'error')->send();
			exit();
		}

		// delete file
		$response = $element->getTypeObject()->delete($request->post('file'));
		if (isset($response['errors'])) {
			$errors = [];
			foreach ((array)$response['errors'] as $err) {
				$errors[] = (string)$err;
			}
			$params = [
				'errors' => $errors,
				'ccm_token' => $this->ccm_token
			];
			$this->setResponse($params, 'error')->send();
			exit();
		}

		// response to block
		$this->setResponse($response, 'success')->send();
		exit();
	}

	public function action_data()
	{
		$request = $this->app['request'];

		$ff = Form::getByID((int)$request->post('formID'));
		if (!$ff) {
			$this->setResponse(['message' => t('Formidable Form can\'t found')], 'error')->send();
			exit();
		}

		@ob_start();
		View::element('dialog/data', ['ff' => $ff], 'formidable');
        $data = ob_get_contents();
        ob_end_clean();

		$this->setResponse(['data' => $data], 'success')->send();
		exit();
	}

	private function setResponse($params, $type = 'success')
    {
        $http = JsonResponse::HTTP_OK;
        if ($type == 'error') {
            $http = JsonResponse::HTTP_BAD_REQUEST;
		}
        return new JsonResponse($params, $http);
    }

	private function loadAssets()
	{
		$this->set('formID', (int)$this->formID);

		$options = (new Json())->decode($this->options, true);

		// format jquery/javascript callback
		$options['onloadCallback'] = isset($options['onloadCallback'])?base64_decode($options['onloadCallback']):'';
		$options['errorsCallback'] = isset($options['errorsCallback'])?base64_decode($options['errorsCallback']):'';
		$options['successCallback'] = isset($options['successCallback'])?base64_decode($options['successCallback']):'';

		$this->set('options', $options);
	}

	private function redirectToPage($page)
	{
		if ($page) {
			$this->buildRedirect($page->getCollectionLink())->send();
			return;
		}
		return false;
	}

	private function convertMessage($message)
	{
		$message = $this->app->make(Text::class)->decodeEntities($message);

		$converter = new Converter();
		$converter->setForm($this->ff);

		$message = $converter->convertTags($message);

		return $message;
	}

}
