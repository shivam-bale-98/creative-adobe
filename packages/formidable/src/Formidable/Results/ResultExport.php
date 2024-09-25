<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Writer;

defined('C5_EXECUTE') or die('Access Denied.');

class ResultExport
{
    private $writer;
    private $unloadDoctrineEveryTick = 0;
    private $ticksUntilUnload = null;

    protected $appTimezone;
    
    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
        $this->appTimezone = (new Date)->getTimezone('app');
    }

    public function setUnloadDoctrineEveryTick($value)
    {
        $this->unloadDoctrineEveryTick = max(0, (int) $value);
        $this->ticksUntilUnload = $this->unloadDoctrineEveryTick ?: null;
        return $this;
    }

    public function getUnloadDoctrineEveryTick()
    {
        return $this->unloadDoctrineEveryTick;
    }

    public function tick()
    {
        if ($this->ticksUntilUnload !== null) {
            --$this->ticksUntilUnload;
            if ($this->ticksUntilUnload < 1) {
                $this->unloadDoctrineEntities();
                $this->ticksUntilUnload = $this->unloadDoctrineEveryTick;
            }
        }
        return $this;
    }

    public function insertHeaders($elements)
    {
        $this->writer->insertOne(iterator_to_array($this->projectHeaders($elements)));
        return $this;
    }

    public function insertObject(Result $result)
    {
        $this->writer->insertOne(iterator_to_array($this->projectObject($result)));
        return $this;
    }

    public function insertList(ItemList $list)
    {
        $this->writer->insertAll($this->projectList($list));
        return $this;
    }

    protected function getObjectFromListResult(ItemList $list, $listResult)
    {
        return $listResult;
    }

    protected function projectHeaders($elements)
    {
        foreach ($this->getStaticHeaders() as $header) {
            yield $header;
        }

        foreach ($elements as $element) {
            if (!$element->getTypeObject()->isEditableOption('searchable', 'bool')) {
                continue;
            }
            yield $element->getName();
        }
    }

    protected function projectObject(Result $result)
    {
        foreach ($this->getStaticFieldValues($result) as $value) {
            yield $value;
        }

        $elements = $result->getForm()->getElements();
        foreach ($elements as $element) {
            if (!$element->getTypeObject()->isEditableOption('searchable', 'bool')) {
                continue;
            }

            $value = $result->getElementDataByElement($element);
            if (!is_object($value)) {
                yield '';
                continue;
            }

            // now there is an issue with specialchars in exporting to csv
            // some will be converted for utf 8, but many will not.
            // in some cases it could help to rewrite the encoding
            // TODO: could this be a dynamic or config value?
            yield mb_convert_encoding($element->getDisplayData($value->getPostValue(), 'plain'), 'UTF-16LE', 'UTF-8');
            //yield mb_convert_encoding($element->getDisplayData($value->getPostValue(), 'plain'), "CP936", "UTF-8");
            //yield utf8_encode($element->getDisplayData($value->getPostValue(), 'plain'));
            //yield $element->getDisplayData($value->getPostValue(), 'plain');
        }
    }

    protected function getStaticHeaders()
    {
        yield 'ID';
        yield 'DateAdded';
        yield 'PageID';
        yield 'PageName';
        yield 'PageURL';
        yield 'UserID';
        yield 'UserName';
        yield 'UserEmail';
        yield 'IP';
        yield 'Device';
        yield 'Operating System';
        yield 'Browser';
        yield 'Resolution';
        yield 'Locale';
        yield 'Localization';
    }

    protected function getStaticFieldValues($result)
    {
        yield $result->getItemID();

        $yield = '';
        $dateTime = $result->getDateAdded();
        if ($dateTime) {
            $dateTime = clone $dateTime;
            $dateTime->setTimezone($this->appTimezone);
            $yield = $dateTime->format('Y-m-d H:i:s');
        }
        yield $yield;

        yield $result->getPage();
        $yield = '';
        $page = $result->getPageObject();
        if (is_object($page)) {
            yield $page->getCollectionName();
            yield $page->getCollectionLink();
        } else {
            yield '(unknown)';
            yield '';
        }

        yield $result->getUser();
        $yield = '';
        $user = $result->getUserObject();
        if (is_object($user)) {
            yield $user->getUserName();
            yield $user->getUserInfoObject()->getUserEmail();
        } else {
            yield t('guest');
            yield '';
        }

        yield $result->getIP();
        yield $result->getDevice();
        yield $result->getOperatingSystem();
        yield $result->getBrowser();
        yield $result->getResolution();
        yield $result->getLocale(false);
        yield $result->getLocale(true);

    }

    protected function projectList(ItemList $list)
    {
        $sth = $list->deliverQueryObject()->execute();

        foreach ($sth as $row) {
            $listResult = $list->getResult($row);
            $object = $this->getObjectFromListResult($list, $listResult);
            yield iterator_to_array($this->projectObject($object));
            $this->tick();
        }
    }

    protected function unloadDoctrineEntities()
    {
        $this->attributeKeysAndControllers = null;
        $app = Application::getFacadeApplication();
        $entityManager = $app->make(EntityManagerInterface::class);
        $entityManager->clear();        
    }
}
