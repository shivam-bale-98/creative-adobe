<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\Formidable\Src\Formidable\Results\Result;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(Result $r)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to('/dashboard/formidable/results/details', $r->getForm()->getItemID(), $r->getItemID()),
                t('View')
            )
        );

        $this->addItem(
            new LinkItem(
                'javascript:;',
                '<i class="fa-fw fa fa-envelope"></i> '.t('Resend'),
                [
                    'class' => 'small',
                    'data-resend-result' => $r->getItemID(),
                ]
            )
        );

        $this->addItem(new DividerItem());

        $this->addItem(
            new LinkItem(
                'javascript:;',
                '<i class="fa-fw fa fa-trash"></i> '.t('Delete'),
                [
                    'class' => 'small text-danger',
                    'data-delete-result' => $r->getItemID(),
                ]
            )
        );
    }
}