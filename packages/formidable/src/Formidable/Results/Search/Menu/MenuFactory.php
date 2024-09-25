<?php
namespace Concrete\Package\Formidable\Src\Formidable\Results\Search\Menu;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Config\Repository\Repository;

class MenuFactory
{
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function createBulkMenu()
    {
        $menu = new DropdownMenu();
        $menu->addItem(
            new LinkItem(
                'javascript:;',
                '<i class="fa-fw fa fa-paper-plane"></i> '.t('Resend'),
                [
                    'class' => 'small',
                    'data-resend-result' => 'bulk',
                ]
            )
        );

        $menu->addItem(new DividerItem());

        $menu->addItem(
            new LinkItem(
                'javascript:;',
                '<i class="fa-fw fa fa-trash"></i> '.t('Delete'),
                [
                    'class' => 'small text-danger',
                    'data-delete-result' => 'bulk',
                ]
            )
        );
        return $menu;
    }

}
