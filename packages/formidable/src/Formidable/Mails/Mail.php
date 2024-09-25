<?php
namespace Concrete\Package\Formidable\Src\Formidable\Mails;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\File\File;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Package\Formidable\Src\Formidable\Forms\Form;
use Concrete\Package\Formidable\Src\Formidable\Templates\Template;
use Concrete\Core\Http\Service\Json;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Text;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Converter;
use Concrete\Package\Formidable\Src\Formidable\Helpers\Mail as MailHelper;
use Concrete\Package\Formidable\Src\Formidable\Forms\Elements\Element;
use Concrete\Package\Formidable\Src\Formidable\Results\Result;
use Exception;

/**
 * @ORM\Entity
 * @ORM\Table(name="FormidableMail", indexes={
 *     @ORM\Index(name="idx_mailHandle", columns={"mailHandle"}),
 * })
 */

class Mail {

    const MAIL_FROM_CUSTOM = 'custom';
    const MAIL_FROM_ELEMENT = 'element';

    const MAIL_REPLY_CUSTOM = 'custom';
    const MAIL_REPLY_ELEMENT = 'element';
    const MAIL_REPLY_FROM = 'from';


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $mailID;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $mailHandle;

	/**
     * @ORM\Column(type="string", length=150)
     */
    protected $mailName;


    /**
     * @ORM\Column(type="string", length=10, options={"default" : "custom"})
     */
    protected $mailFrom = 'custom';

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $mailFromElement = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $mailFromName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $mailFromEmail;


    /**
     * @ORM\Column(type="string", length=10, options={"default" : "custom"})
     */
    protected $mailReplyTo = 'from';

    /**
     * @ORM\Column(type="integer", length=10, options={"default" : 0})
     */
    protected $mailReplyToElement = 0;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $mailReplyToName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $mailReplyToEmail;


    /**
     * @ORM\Column(type="text")
     */
	protected $mailTo;

    /**
     * @ORM\Column(type="text")
     */
	protected $mailToEmail;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $mailToUseCC = 0;


    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $mailSubject = '';

	/**
     * @ORM\Column(type="text")
     */
	protected $mailMessage;


    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $mailSkipEmpty = 0;

    /**
     * @ORM\Column(type="integer", length=1, options={"default" : 0})
     */
    protected $mailSkipLayout = 0;


    /**
     * @ORM\Column(type="text")
     */
	protected $mailAttachments;

    /**
     * @ORM\Column(type="text")
     */
	protected $mailAttachmentFiles;

    /**
     * @ORM\Column(type="text")
     */
	protected $mailDependencies;


    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $mailDateAdded;

    /**
     * @ORM\Column(type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    protected $mailDateModified;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Forms\Form", inversedBy="mails")
     * @ORM\JoinColumn(name="formID", referencedColumnName="formID")
     */
    protected $form;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Package\Formidable\Src\Formidable\Templates\Template", inversedBy="mails")
     * @ORM\JoinColumn(name="templateID", referencedColumnName="templateID", nullable=true, onDelete="SET NULL")
     */
    protected $template;


    protected $mailFromElementObject;
    protected $mailReplyToElementObject;
    protected $mailToElementObject;
    protected $mailAttachmentFilesObject;
    protected $mailTemplateObject;

    protected $result;

    public function __clone()
    {
        if ($this->mailID) {
            $this->mailID = null;

            $this->setDateAdded(new \DateTime());
            $this->setDateModified(new \DateTime());
        }
    }

    public static function getByID($mailID)
    {
        $em = dbORM::entityManager();
        return $em->find(get_class(), $mailID);
    }

    public static function getByHandle($mailHandle, $form)
    {
        //$em = dbORM::entityManager();
        //return $em->getRepository(get_class())->findOneBy(['mailHandle' => $mailHandle, 'form' => $form]);
        $db = Application::getFacadeApplication()->make('database');
        $query = $db->connection()->createQueryBuilder();
        $query->select('m.mailID')
              ->from('FormidableMail', 'm')
              ->leftjoin('m', 'FormidableForm', 'f', 'm.formID = f.formID')
              ->where('m.mailHandle = :mailHandle AND f.formID = :formID');
        $query->setParameters(['mailHandle' => $mailHandle, 'formID' => $form->getItemID()]);
        $item = $query->execute()->fetchOne();
        if ($item) {
            return self::getByID($item);
        }
        return false;
    }

    /**
     * ID
     */
    public function getItemID()
    {
        return $this->mailID;
    }

    /**
     * Handle
     */
    public function getHandle()
    {
        return $this->mailHandle;
    }
    public function setHandle($mailHandle)
    {
        $this->mailHandle = $mailHandle;
    }

    /**
     * Name
     */
    public function getName()
    {
        return $this->mailName;
    }
    public function setName($mailName)
    {
        $this->mailName = $mailName;
    }


    /**
     * From
     */
    public function getFrom()
    {
        return $this->mailFrom;
    }
    public function setFrom($mailFrom)
    {
        $this->mailFrom = $mailFrom;
    }
    public static function getFroms()
    {
		$obj = new self;
        $available = [
			self::MAIL_FROM_CUSTOM,
            self::MAIL_FROM_ELEMENT,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getFromText($v);
		}
		return $options;
	}
    public function getFromText($option = null)
    {
        if (empty($option)) {
           $option = $this->getFrom();
        }
        $text = '';
        switch ($option) {
			case self::MAIL_FROM_CUSTOM:
                $text = t('Custom sender');
			break;
			case self::MAIL_FROM_ELEMENT:
                $text = t('Use data from element on form');
            break;

		}
		return $text;
    }

    public function getFromElement()
    {
        return (int)$this->mailFromElement;
    }
    public function getFromElementObject()
    {
        if (is_object($this->mailFromElementObject)) {
            return $this->mailFromElementObject;
        }
        $this->mailFromElementObject = Element::getByID($this->mailFromElement);
        return $this->mailFromElementObject;
    }
    public function setFromElement($mailFromElement)
    {
        $this->mailFromElement = (int)$mailFromElement;
    }

    public function getFromName()
    {
        return $this->mailFromName;
    }
    public function setFromName($mailFromName)
    {
        $this->mailFromName = $mailFromName;
    }

    public function getFromEmail()
    {
        return $this->mailFromEmail;
    }
    public function setFromEmail($mailFromEmail)
    {
        $this->mailFromEmail = $mailFromEmail;
    }

    public function getFromDisplay()
    {
        if ($this->getFrom() == self::MAIL_FROM_CUSTOM) {
            return t('%s <span class="small text-muted">&lt;%s&gt;</span>', $this->getFromName(), $this->getFromEmail());
        }
        $element = $this->getFromElementObject();
        if ($element) {
            return $element->getName();
        }
        return t('(unknown)');
    }


    /**
     * Reply To
     */
    public function getReplyTo()
    {
        return $this->mailReplyTo;
    }
    public function setReplyTo($mailReplyTo)
    {
        $this->mailReplyTo = $mailReplyTo;
    }
    public static function getReplyTos()
    {
		$obj = new self;
        $available = [
            self::MAIL_REPLY_FROM,
			self::MAIL_REPLY_CUSTOM,
            self::MAIL_REPLY_ELEMENT,
		];
		$options = [];
		foreach($available as $v) {
			$options[$v] = $obj->getReplyToText($v);
		}
		return $options;
	}
    public function getReplyToText($option = null)
    {
        if (empty($option)) {
           $option = $this->getReplyTo();
        }
        $text = '';
        switch ($option) {
            case self::MAIL_REPLY_FROM:
                $text = t('Use the "From"-details');
			break;
			case self::MAIL_REPLY_CUSTOM:
                $text = t('Custom Reply To');
			break;
			case self::MAIL_REPLY_ELEMENT:
                $text = t('Use data from element on form');
            break;

		}
		return $text;
    }

    public function getReplyToElement()
    {
        return (int)$this->mailReplyToElement;
    }
    public function getReplyToElementObject()
    {
        if (is_object($this->mailReplyToElementObject)) {
            return $this->mailReplyToElementObject;
        }
        $this->mailReplyToElementObject = Element::getByID($this->mailReplyToElement);
        return $this->mailReplyToElementObject;
    }
    public function setReplyToElement($mailReplyToElement)
    {
        $this->mailReplyToElement = (int)$mailReplyToElement;
    }

    public function getReplyToName()
    {
        return $this->mailReplyToName;
    }
    public function setReplyToName($mailReplyToName)
    {
        $this->mailReplyToName = $mailReplyToName;
    }

    public function getReplyToEmail()
    {
        return $this->mailReplyToEmail;
    }
    public function setReplyToEmail($mailReplyToEmail)
    {
        $this->mailReplyToEmail = $mailReplyToEmail;
    }

    public function getReplyToDisplay()
    {
        if ($this->getReplyTo() == self::MAIL_FROM_CUSTOM) {
            return t('%s <span class="small text-muted">&lt;%s&gt;</span>', $this->getReplyToName(), $this->getReplyToEmail());
        }
        $element = $this->getReplyToElementObject();
        if ($element) {
            return $element->getName();
        }
        return t('(unknown)');
    }

    /**
     * Send to
     */
    public function getTo()
    {
        return (array)(new Json())->decode($this->mailTo, true);
    }
    public function setTo($mailTo = [])
    {
        $this->mailTo = (new Json())->encode($mailTo);
    }
    public function getToEmail()
    {
        return (array)(new Json())->decode($this->mailToEmail, true);
    }
    public function setToEmail($mailToEmail)
    {
        $this->mailToEmail = (new Json())->encode($mailToEmail);
    }
    public function getToElementObject()
    {
        if (is_array($this->mailToElementObject) && count($this->mailToElementObject)) {
            return $this->mailToElementObject;
        }
        foreach ($this->getTo() as $elementID) {
            $this->mailToElementObject[] = Element::getByID($elementID);
        }
        return $this->mailToElementObject;
    }
    public function getToDisplay()
    {
        $to = [];
        $emails = $this->getToEmail();
        if (count($emails)) {
            $emails = array_map(function($email) { return t('<span class="small text-muted">&lt;%s&gt;</span>', $email); }, $emails);
            $to[] = implode(' ', $emails);
        }
        $elements = (array)$this->getToElementObject();
        foreach ($elements as $element) {
            if (is_object($element)) {
                $to[] = t('%s <span class="small text-muted">&lt;element&gt;</span>', $element->getName());
            }
        }
        if (count($to)) {
            return implode(', ', $to);
        }
        return t('(unknown)');
    }
    public function getToDisplayPlain()
    {
        $to = $this->getToEmail();
        $elements = (array)$this->getToElementObject();
        foreach ($elements as $element) {
            if (is_object($element)) {
                $to[] = $element->getName();
            }
        }
        if (count($to)) {
            return implode(', ', $to);
        }
        return t('(unknown)');
    }

    /**
     * Use CC instead of BCC
     */
    public function getUseCC()
    {
        return (int)$this->mailToUseCC==1?true:false;
    }
    public function setUseCC($mailToUseCC)
    {
        $this->mailToUseCC = (int)$mailToUseCC;
    }

    /**
     * Subject
     */
    public function getSubject()
    {
        return $this->mailSubject;
    }
    public function setSubject($mailSubject)
    {
        $this->mailSubject = $mailSubject;
    }

    /**
     * Message
     */
    public function getMessage()
    {
        return $this->mailMessage;
    }
    public function setMessage($mailMessage)
    {
        $this->mailMessage = $mailMessage;
    }

    /**
     * Discard empty
     */
    public function getSkipEmpty()
    {
        return (int)$this->mailSkipEmpty==1?true:false;
    }
    public function setSkipEmpty($mailSkipEmpty)
    {
        $this->mailSkipEmpty = (int)$mailSkipEmpty;
    }

    /**
     * Discard layout
     */
    public function getSkipLayout()
    {
        return (int)$this->mailSkipLayout==1?true:false;
    }
    public function setSkipLayout($mailSkipLayout)
    {
        $this->mailSkipLayout = (int)$mailSkipLayout;
    }

    /**
     * Attachments
     */
    public function getAttachments()
    {
        return (array)(new Json())->decode($this->mailAttachments, true);
    }
    public function setAttachments($mailAttachments = [])
    {
        $this->mailAttachments = (new Json())->encode($mailAttachments);
    }

    /**
     * Attachment files
     */
    public function getAttachmentFiles()
    {
        return (array)(new Json())->decode($this->mailAttachmentFiles, true);
    }
    public function setAttachmentFiles($mailAttachmentFiles = [])
    {
        $this->mailAttachmentFiles = (new Json())->encode($mailAttachmentFiles);
    }
    public function getAttachmentFilesObject()
    {
        if (is_array($this->mailAttachmentFilesObject) && count($this->mailAttachmentFilesObject)) {
            return $this->mailAttachmentFilesObject;
        }
        foreach ($this->getAttachmentFiles() as $fileID) {
            $this->mailAttachmentFilesObject[] = File::getByID($fileID);
        }
        return $this->mailAttachmentFilesObject;
    }

    /**
     * Dependencies
     */
    public function getDependencies()
    {
        return (array)(new Json())->decode($this->mailDependencies, true);
    }
    public function setDependencies($mailDependencies = [])
    {
        $this->mailDependencies = (new Json())->encode($mailDependencies);
    }


    public function getForm()
    {
        return $this->form;
    }
    public function setForm($form)
    {
        if (!is_object($form)) {
            $form = Form::getByID($form);
        }
        $this->form = $form;
    }


    public function getTemplate()
    {
        return $this->template;
    }
    public function setTemplate($template)
    {
        if (!is_object($template)) {
            $template = Template::getByID($template);
        }
        $this->template = $template;
    }

    public function getDateAdded($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->mailDateAdded;
        }
        return $this->mailDateAdded->format($format);
    }
    public function setDateAdded($mailDateAdded)
    {
        $this->mailDateAdded = $mailDateAdded;
	}

    public function getDateModified($display = false, $format = 'Y-m-d H:i:s')
    {
        if (!$display) {
            return $this->mailDateModified;
        }
        return $this->mailDateModified->format($format);
    }
    public function setDateModified($mailDateModified)
    {
        $this->mailDateModified = $mailDateModified;
	}

    public function setResult($result)
    {
        if (!is_object($result)) {
            $result = Result::getByID($result);
        }
        if ($result->getForm()->getItemID() != $this->getForm()->getItemID()) {
            throw new Exception(t('Unmatching FormIDs for Result and Mail objects'));
        }
        $this->result = $result;
    }
    public function getResult()
    {
        return $this->result;
    }

    public function send()
    {
        $result = $this->getResult();
        if (!is_object($result)) {
            throw new Exception(t('No Result object set for Mail object'));
        }
        if ($result->getForm()->getItemID() != $this->getForm()->getItemID()) {
            throw new Exception(t('Unmatching FormIDs for Result and Mail objects'));
        }

        /* check dependencies */
        $this->checkDependecies();

        $app = Application::getFacadeApplication();

        $th = $app->make(Text::class);
        $mh = $app->make(MailHelper::class);
        $mh->setIsThrowOnFailure(true);

        $logging = true;
        if ($this->getForm()->getPrivacy() && $this->getForm()->getPrivacyLog()) {
            $logging = false;
        }
        $mh->setLogging($logging);

        // set from
        $fromEmail = $fromName = '';
        if ($this->getFrom() == self::MAIL_FROM_CUSTOM) {
            $fromEmail = $this->getFromEmail();
            $fromName = $this->getFromName();
        }
        elseif ($this->getFrom() == self::MAIL_FROM_ELEMENT && $this->getFromElementObject()) {
            $fromEmail = (string)$result->getElementDataByElement($this->getFromElementObject()->getItemID());
        }
        if (empty($fromEmail)) {
            throw new Exception(t('No FROM set or empty for this mailing'));
        }
        $mh->from($fromEmail, $fromName);

        // set replyto
        $replyToEmail = $replyToName = '';
        if ($this->getReplyTo() == self::MAIL_REPLY_FROM) {
            $replyToEmail = $fromEmail;
            $replyToName = $fromName;
        }
        elseif ($this->getReplyTo() == self::MAIL_REPLY_CUSTOM) {
            $replyToEmail = $this->getReplyToEmail();
            $replyToName = $this->getReplyToName();
        }
        elseif ($this->getReplyTo() == self::MAIL_REPLY_ELEMENT && $this->getReplyToElementObject()) {
            $replyToEmail = (string)$result->getElementDataByElement($this->getReplyToElementObject()->getItemID());
        }
        if (empty($replyToEmail)) {
            throw new Exception(t('No REPLYTO set or empty for this mailing'));
        }
        $mh->replyto($replyToEmail, $replyToName);

        // set to
        $to = $this->getToEmail();
        $elements = (array)$this->getToElementObject();
        foreach ((array)$elements as $element) {
            if (is_object($element)) {
                $to[] = (string)$result->getElementDataByElement($element->getItemID());
            }
        }
        if (!count($to)) {
            throw new Exception(t('No TO set or empty for this mailing'));
        }
        $mh->to($to[0]);
        foreach ((array)$to as $k => $t) {
            if ($k == 0) {
                continue;
            }
            if (empty($t)) {
                continue;
            }
            if ($this->getUseCC()) {
                $mh->cc($t);
            }
            else {
                $mh->bcc($t);
            }
        }

        $mh->setSubject($th->decodeEntities($this->convertSubject()));
        $mh->setBodyHTML($th->decodeEntities($this->convertMessage()));

        // set attachments
		$files = $this->getAttachmentFilesObject();
        foreach ((array)$files as $f) {
            $mh->addAttachment($f);
        }

		$elements = $this->getAttachments();
        foreach ((array)$elements as $elementID) {
            $data = $result->getElementDataByElement($elementID);
            if ($data) {
                $files = $data->getElement()->getDisplayData($data->getPostValue(), 'object');
                foreach ($files as $f) {
                    $mh->addAttachment($f);
                }
            }
        }

        $mh->sendMail();
		$mh->reset();
    }

	public function save()
    {
        $em = dbORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function delete()
    {
        $em = dbORM::entityManager();
        $em->remove($this);
        $em->flush();
    }

    private function checkDependecies()
    {
        $dependencies = $this->getDependencies();
        if (!count($dependencies)) {
            return true;
        }

        $result = $this->getResult();
        if (!is_object($result)) {
            throw new Exception(t('No Result object set for Mail object'));
        }

        // by default we skip the submission
        // if a rule matches, then send
        $skip = true;

        foreach ($dependencies as $i => $rv) {

            $actions = [];
            foreach ((array)$rv['action'] as $a) {
                $actions[] = $a[0];
            }

            // no actions, just continue
            if (!count($actions)) {
                continue;
            }

            $inverse = false;
            if (array_intersect(['not_send'], $actions)) {
                $inverse = true;
            }

            // by default we ensure there is a match
            // if this condition matches, then validate
            $rule_match = true;

            // if a single selector don't match, break
            // it's an "and"-method
            foreach ((array)$rv['selector'] as $s) {

                $element = $this->getForm()->getElementByID((int)$s['element']);
                if (!$element) {
                    continue;
                }

                // get compare data
                $compare = $result->getElementDataByElement($element);
                if ($compare) {
                    $compare = $compare->getPostValue();
                }

                foreach ((array)$s['condition'] as $c) {

                    // match conditions
                    $match = $element->getTypeObject()->comparePostData(isset($c['value'])?$c['value']:'', $compare, isset($c['condition'])?$c['condition']:'equals');

                    // if a single condition don't match, break
                    // it's an "and"-method...
                    // check next rule
                    if (!$match) {
                        $rule_match = false;
                        break(2);
                    }
                }
            }

            // reverse match
            if ($inverse) {
                $rule_match = !$rule_match;
            }

            if ($rule_match) {
                $skip = false;
                break;
            }
        }

        // all is checked, skip validation.
        if ($skip) {
            throw new Exception(t('Send no mail because of dependency'));
        }

    }

    private function convertSubject()
    {
        $subject = Application::getFacadeApplication()->make('helper/text')->sanitize($this->getSubject());

        $converter = new Converter();
        $converter->setForm($this->getForm());
        $converter->setResult($this->getResult());
        $subject = $converter->convertTags($subject);

        return $subject;
	}

	private function convertMessage()
    {
        $th = Application::getFacadeApplication()->make('helper/text');

        // prepare message
        $message = $th->decodeEntities($this->getMessage());

		// add template if it's there
		$temp = $this->getTemplate();
		if (is_object($temp)) {
            $template = '';
            if ($temp->getStyle()) {
                $template = '<style>'.base64_decode($temp->getStyle()).'</style>';
            }
            $template .= '<body>'.$th->decodeEntities($temp->getContent()).'</body>';
			$message = str_replace(['<p>{%formidable_data%}</p>', '{%formidable_data%}'], $message, $template);
		}

        $converter = new Converter();
        $converter->setForm($this->getForm());
        $converter->setResult($this->getResult());
        $converter->setSkipEmpty($this->getSkipEmpty());
        $converter->setSkipLayout($this->getSkipLayout());
        $message = $converter->convertTags($message);

        return $message;
	}

}


