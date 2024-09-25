<?php
defined('C5_EXECUTE') or die("Access Denied.");

$token = app('helper/validation/token');

if (!isset($action)) {
    $action = 'view';
}

?>
<?php if ($template->getItemID() > 0) { ?>

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
            <li><a href="<?=$view->action('props', $template->getItemID()); ?>" class="dropdown-item"><?=t('Details'); ?></a><li>
            <li><hr class="dropdown-divider"></li>
            <li><a href="#" data-copy-template="<?=$template->getItemID(); ?>" class="dropdown-item"><?=t('Copy'); ?></a><li>
            <li><a href="#" data-delete-template="<?=$template->getItemID(); ?>" class="dropdown-item text-danger"><?=t('Delete'); ?></a><li>
        </ul>
    </div>

    <div class="d-none d-lg-inline-block">
        <div class="btn-group">
            <a href="<?=$view->action('props', $template->getItemID()); ?>" class="btn <?=$action == 'props'?'btn-primary':'btn-secondary';?> btn-sm"><?=t('Details'); ?></a>
            <a href="#" data-copy-template="<?=$template->getItemID(); ?>" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-title="<?=t('Copy');?>">
                <i class="fa-fw fa fa-copy"></i>
            </a>
            <a href="#" data-delete-template="<?=$template->getItemID(); ?>" class="btn btn-sm btn-secondary text-danger" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                <i class="fa-fw fa fa-trash"></i>
            </a>
        </div>
    </div>

<?php } else { ?>

    <?php if ($action == 'view') { ?>
        <a href="<?=$view->action('props'); ?>" class="btn btn-success btn-sm"><?=t('Add new'); ?></a>
    <?php } ?>

<?php }