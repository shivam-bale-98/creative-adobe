<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

$token = app('helper/validation/token');

?>
<?php if ($result->getItemID() > 0) { ?>

    <div class="btn-group btn-group-sm me-4">
        <button
            type="button"
            class="btn btn-secondary dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            <span id="selected-option">
                <?=t('To form'); ?>
            </span>
        </button>
        <ul class="dropdown-menu">
            <li class="dropdown-header">
                <?=t('Go to form ...');?>
            </li>
            <li><a href="<?=Url::to('/dashboard/formidable/forms/props', $result->getFormID()); ?>" class="dropdown-item"><?=t('Properties'); ?></a><li>
            <li><a href="<?=Url::to('/dashboard/formidable/forms/layout', $result->getFormID()); ?>" class="dropdown-item"><?=t('Fields'); ?></a><li>
            <li><a href="<?=Url::to('/dashboard/formidable/forms/mails', $result->getFormID()); ?>" class="dropdown-item"><?=t('Mails'); ?></a><li>
        </ul>
    </div>

    <div class="btn-group">
        <a href="#" data-resend-result="<?=$result->getItemID(); ?>" class="btn btn-sm btn-secondary"><?=t('Resend'); ?></a>
        <a href="#" data-delete-result="<?=$result->getItemID(); ?>" class="btn btn-sm btn-secondary text-danger" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
            <i class="fa-fw fa fa-trash"></i>
        </a>
    </div>

<?php }