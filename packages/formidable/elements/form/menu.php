<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Url;

$token = app('helper/validation/token');

if (!isset($action)) {
    $action = 'view';
}

?>
<?php if ($ff && $ff->getItemID() > 0) { ?>


    <a href="<?=Url::to('/dashboard/formidable/results', $ff->getItemID());?>" class="d-none d-lg-inline-block btn <?=$action == 'results'?'btn-primary':'btn-secondary';?> btn-sm">
        <?=t('Results'); ?>
        <?php
            $total = $ff->getTotalResults();
            if ($total > 0) { ?>
                <span class="badge bg-light p-1 mx-1 text-dark"><?=$total;?></span>
        <?php } ?>
    </a>

    <div class="btn-group d-lg-none d-flex flex-column flex-wrap align-content-end">
        <button
            type="button"
            class="btn btn-secondary p-2 dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            <span id="selected-option">
                <i class="fa-fw fa fa-ellipsis-v"></i>
            </span>
        </button>
        <ul class="dropdown-menu">
            <li class="dropdown-header">
                <?=t('Select action');?>
            </li>
            <li><a href="<?=Url::to('/dashboard/formidable/results', $ff->getItemID());?>" class="dropdown-item"><?=t('Results'); ?></a><li>
            <li><hr class="dropdown-divider"></li>
            <li><a href="<?=$view->action('props', $ff->getItemID()); ?>" class="dropdown-item"><?=t('Properties'); ?></a><li>
            <li><a href="<?=$view->action('layout', $ff->getItemID()); ?>" class="dropdown-item"><?=t('Fields'); ?></a><li>
            <li><a href="<?=$view->action('mails', $ff->getItemID()); ?>" class="dropdown-item"><?=t('Mails'); ?></a><li>
            <li><hr class="dropdown-divider"></li>
            <li><a href="#" data-copy-form="<?=$ff->getItemID(); ?>" class="dropdown-item"><?=t('Copy'); ?></a><li>
            <li><a href="#" data-delete-form="<?=$ff->getItemID(); ?>" class="dropdown-item text-danger"><?=t('Delete'); ?></a><li>
        </ul>
    </div>

    <div class="d-none d-lg-inline-block">
        <div class="btn-group">
            <a href="<?=$view->action('props', $ff->getItemID()); ?>" class="btn <?=$action == 'props'?'btn-primary':'btn-secondary';?> btn-sm"><?=t('Properties'); ?></a>
            <a href="<?=$view->action('layout', $ff->getItemID()); ?>" class="btn <?=in_array($action, ['layout', 'row', 'column', 'element'])?'btn-primary':'btn-secondary';?> btn-sm">
                <?=t('Fields'); ?>
                <?php
                    $total = $ff->getTotalElements();
                    if ($total > 0) { ?>
                        <span class="badge bg-light p-1 mx-1 text-dark"><?=$total;?></span>
                <?php } ?>
            </a>
            <a href="<?=$view->action('mails', $ff->getItemID()); ?>" class="btn <?=in_array($action, ['mails', 'mail'])?'btn-primary':'btn-secondary';?> btn-sm">
                <?=t('Mails'); ?>
                <?php
                    $total = $ff->getTotalMails();
                    if ($total > 0) { ?>
                        <span class="badge bg-light p-1 mx-1 text-dark"><?=$total;?></span>
                <?php } ?>
            </a>
            <a href="#" data-copy-form="<?=$ff->getItemID(); ?>" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-title="<?=t('Copy');?>">
                <i class="fa-fw fa fa-copy"></i>
            </a>
            <a href="#" data-delete-form="<?=$ff->getItemID(); ?>" class="btn btn-sm btn-secondary text-danger" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                <i class="fa-fw fa fa-trash"></i>
            </a>
        </div>
    </div>

<?php } else { ?>

    <?php if ($action == 'view') { ?>
        <a href="<?=$view->action('props'); ?>" class="btn btn-success btn-sm"><?=t('Add new'); ?></a>
    <?php } ?>

<?php }