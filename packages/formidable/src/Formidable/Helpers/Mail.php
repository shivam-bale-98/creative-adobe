<?php
namespace Concrete\Package\Formidable\Src\Formidable\Helpers;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Mail\Service;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\GroupLogger;
use Exception;
use Monolog\Logger;
use Throwable;
use Laminas\Mail\Header\MessageId as MessageIdHeader;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;

class Mail extends Service {

    protected $enableLogging = true;

    public function setLogging($enableLogging = true)
    {
        $this->enableLogging = $enableLogging;
    }

    public function getLogging()
    {
        return $this->enableLogging;
    }

    public function sendMail($resetData = true)
    {
        $config = $this->app->make('config');
        $fromStr = $this->generateEmailStrings([$this->from]);
        $toStr = $this->generateEmailStrings($this->to);
        $replyStr = $this->generateEmailStrings($this->replyto);

        $mail = (new Message())->setEncoding(APP_CHARSET);

        if (is_array($this->from) && count($this->from)) {
            if ($this->from[0] != '') {
                $from = $this->from;
            }
        }
        if (!isset($from)) {
            $from = [$config->get('concrete.email.default.address'), $config->get('concrete.email.default.name')];
            $fromStr = $config->get('concrete.email.default.address');
        }

        // The currently included Zend library has a bug in setReplyTo that
        // adds the Reply-To address as a recipient of the email. We must
        // set the Reply-To before any header with addresses and then clear
        // all recipients so that a copy is not sent to the Reply-To address.
        if (is_array($this->replyto)) {
            foreach ($this->replyto as $reply) {
                $mail->setReplyTo($reply[0], $reply[1]);
            }
        }

        $mail->setFrom($from[0], $from[1]);
        $mail->setSubject($this->subject);
        foreach ($this->to as $to) {
            $mail->addTo($to[0], $to[1]);
        }

        if (is_array($this->cc) && count($this->cc)) {
            foreach ($this->cc as $cc) {
                $mail->addCc($cc[0], $cc[1]);
            }
        }

        if (is_array($this->bcc) && count($this->bcc)) {
            foreach ($this->bcc as $bcc) {
                $mail->addBcc($bcc[0], $bcc[1]);
            }
        }
        $headers = $mail->getHeaders();
        if ($headers->has('messageid')) {
            $messageIdHeader = $headers->get('messageid');
        } else {
            $messageIdHeader = new MessageIdHeader();
            $headers->addHeader($messageIdHeader);
        }

        $headers->addHeaders($this->headers);

        $messageIdHeader->setId();

        $body = new MimeMessage();
        $textPart = $this->buildTextPart();
        $htmlPart = $this->buildHtmlPart();
        if ($textPart === null && $htmlPart === null) {
            $emptyPart = new MimePart('');
            $emptyPart->setType(Mime::TYPE_TEXT);
            $emptyPart->setCharset(APP_CHARSET);
            $body->addPart($emptyPart);
        } elseif ($textPart !== null && $htmlPart !== null) {
            $alternatives = new MimeMessage();
            $alternatives->addPart($textPart);
            $alternatives->addPart($htmlPart);
            $alternativesPart = new MimePart($alternatives->generateMessage());
            $alternativesPart->setType(Mime::MULTIPART_ALTERNATIVE);
            $alternativesPart->setBoundary($alternatives->getMime()->boundary());
            $body->addPart($alternativesPart);
        } else {
            if ($textPart !== null) {
                $body->addPart($textPart);
            }
            if ($htmlPart !== null) {
                $body->addPart($htmlPart);
            }
        }
        foreach ($this->attachments as $attachment) {
            if (!$this->isInlineAttachment($attachment)) {
                $body->addPart($attachment);
            }
        }

        $mail->setBody($body);

        $sendError = null;
        if ($config->get('concrete.email.enabled')) {
            try {
                $this->transport->send($mail);
            } catch (Exception $x) {
                $sendError = $x;
            } catch (Throwable $x) {
                $sendError = $x;
            }
        }
        if ($sendError !== null) {
            if ($this->getTesting()) {
                throw $sendError;
            }

            $l = new GroupLogger(Channels::CHANNEL_EXCEPTIONS, Logger::CRITICAL);
            $l->write(t('Formidable Mail Exception. Unable to send mail: ') . $sendError->getMessage());
            if ($this->getLogging()) {
                $l->write($sendError->getTraceAsString());
                if ($config->get('concrete.log.emails')) {
                    $l->write(t('Template Used') . ': ' . $this->template);
                    $l->write(t('To') . ': ' . $toStr);
                    $l->write(t('From') . ': ' . $fromStr);
                    if (isset($this->replyto)) {
                        $l->write(t('Reply-To') . ': ' . $replyStr);
                    }
                    $l->write(t('Subject') . ': ' . $this->subject);
                    $l->write(t('Body') . ': ' . $this->body);
                }

            }
            else {
                $l->write(t('Formidable Privacy Log setting prevents details to be logged.'));
                $l->write(t('To') . ': ' . $toStr);
            }
            $l->close();
        }

        // add email to log
        if ($config->get('concrete.log.emails') && !$this->getTesting()) {
            $l = new GroupLogger(Channels::CHANNEL_EMAIL, Logger::NOTICE);
            if ($config->get('concrete.email.enabled')) {
                if ($sendError === null) {
                    $l->write('**' . t('EMAILS ARE ENABLED. THIS EMAIL HAS BEEN SENT') . '**');
                } else {
                    $l->write('**' . t('EMAILS ARE ENABLED. THIS EMAIL HAS NOT BEEN SENT') . '**');
                }
            } else {
                $l->write('**' . t('EMAILS ARE DISABLED. THIS EMAIL WAS LOGGED BUT NOT SENT') . '**');
            }
            if ($this->getLogging()) {
                $l->write(t('Template Used') . ': ' . $this->template);
                $detail = $mail->toString();
                $encoding = $mail->getHeaders()->get('Content-Transfer-Encoding');
                if (is_object($encoding) && $encoding->getFieldValue() === 'quoted-printable') {
                    $detail = quoted_printable_decode($detail);
                }
                $l->write(t('Mail Details: %s', $detail));
            }
            else {
                $l->write(t('Formidable Privacy Log setting prevents details to be logged.'));
                $l->write(t('To') . ': ' . $toStr);
            }
            $l->close();
        }

        if ($sendError !== null && $this->isThrowOnFailure()) {
            if ($resetData) {
                $this->reset();
            }
            throw $sendError;
        }

        // clear data if applicable
        if ($resetData) {
            $this->reset();
        }

        return $sendError === null;
    }
}