<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php use Concrete\Core\View\View; ?>

<div class="formidableLoader"></div>

<?php if ($this->controller->getAction() == 'props') { ?>

    <form method="post" action="<?=$view->action('props', $ff?$ff->getItemID():0); ?>">
        <?php View::element('form/edit', ['ff' => $ff], 'formidable'); ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/formidable/forms'); ?>" class="btn btn-secondary float-start"><?= t('Cancel'); ?></a>
                <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
            </div>
        </div>
    </form>

<?php } else if ($this->controller->getAction() == 'layout' && is_object($ff)) { ?>

    <?php View::element('form/layout', ['ff' => $ff], 'formidable'); ?>

<?php } else if ($this->controller->getAction() == 'row' && is_object($ff)) { ?>

    <form method="post" action="<?=$view->action('row', $ff->getItemID(), $row?$row->getItemID():0); ?>">
        <?php View::element('form/layout/row', ['ff' => $ff, 'row' => $row?$row:0], 'formidable'); ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/formidable/forms/layout', $ff->getItemID()); ?>" class="btn btn-secondary float-start"><?= t('Cancel'); ?></a>
                <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
            </div>
        </div>
    </form>

<?php } else if ($this->controller->getAction() == 'column' && is_object($ff) && is_object($row)) { ?>

    <form method="post" action="<?=$view->action('column', $ff->getItemID(), $row->getItemID(), $column?$column->getItemID():0); ?>">
        <?php View::element('form/layout/column', ['ff' => $ff, 'row' => $row, 'column' => $column?$column:0], 'formidable'); ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/formidable/forms/layout', $ff->getItemID()); ?>" class="btn btn-secondary float-start"><?= t('Cancel'); ?></a>
                <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
            </div>
        </div>
    </form>

<?php } else if ($this->controller->getAction() == 'element' && is_object($ff) && is_object($column)) { ?>

    <form method="post" action="<?=$view->action('element', $ff->getItemID(), $column->getItemID(), $element?$element->getItemID():0); ?>">
        <?php View::element('form/layout/element', ['ff' => $ff, 'column' => $column, 'element' => $element?$element:0], 'formidable'); ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/formidable/forms/layout', $ff->getItemID()); ?>" class="btn btn-secondary float-start"><?= t('Cancel'); ?></a>
                <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
            </div>
        </div>
    </form>

    <?php
        View::element('dialog/element/option/clear', ['ff' => $ff], 'formidable');
        View::element('dialog/element/option/multiple', ['ff' => $ff], 'formidable');
    ?>

<?php } else if ($this->controller->getAction() == 'mails' && is_object($ff)) { ?>

    <?php if (count($mails) > 0) {  ?>

        <div class="table-responsive">
            <table class="table table-hover ccm-search-results-table">
                <thead>
                    <tr>
                        <th><?= t('Name'); ?></th>
                        <th><?= t('Subject'); ?></th>
                        <th><?= t('From'); ?></th>
                        <th><?= t('To'); ?></th>
                        <th class="text-end">
                            <a href="<?=Url::to('/dashboard/formidable/forms/mail', $ff->getItemID(), 0); ?>" class="btn btn-sm text-white btn-success d-none d-lg-inline-block"><?=t('Add new'); ?></a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mails as $mail) { ?>
                        <tr>
                            <td class="mail-name align-middle">
                                <?=$mail->getName(); ?>
                                <span class="small text-muted">(<?=$mail->getHandle();?>)</span>
                            </td>
                            <td class="mail-subject align-middle">
                                <?=$mail->getSubject(); ?>
                            </td>
                            <td class="mail-subject align-middle ">
                                <?=$mail->getFromDisplay(); ?>
                            </td>
                            <td class="mail-subject align-middle">
                                <?=$mail->getToDisplay(); ?>
                            </td>
                            <td class="mail-tasks text-end">

                                <div class="btn-group d-lg-none">
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
                                        <li><a href="<?=$view->action('mail', $ff->getItemID(), $mail->getItemID()); ?>" class="dropdown-item"><?=t('Details'); ?></a><li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a href="#" data-copy-mail="<?=$mail->getItemID(); ?>" class="dropdown-item"><?=t('Copy'); ?></a><li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a href="#" data-delete-mail="<?=$mail->getItemID(); ?>" class="dropdown-item text-danger"><?=t('Delete'); ?></a><li>
                                    </ul>
                                </div>

                                <div class="d-none d-lg-inline">
                                    <div class="btn-group">
                                        <a href="<?=$view->action('mail', $ff->getItemID(), $mail->getItemID()); ?>" class="btn btn-sm btn-secondary"><?=t('Edit'); ?></a>
                                        <a href="#" data-copy-mail="<?=$mail->getItemID(); ?>" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-title="<?=t('Copy');?>">
                                            <i class="fa-fw fa fa-copy"></i>
                                        </a>
                                        <a href="#" data-delete-mail="<?=$mail->getItemID(); ?>" class="btn btn-sm btn-secondary text-danger" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
                                            <i class="fa-fw fa fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    <?php } else { ?>

        <div class="help-block mt-0 text-center">
            <?= t('You have not created any mailings yet.'); ?>
            <br><br><a href="<?=$view->action('mail', $ff->getItemID(), 0); ?>" class="btn btn-secondary"><?=t('Add mailing'); ?></a>
        </div>

    <?php } ?>

    <?php
        View::element('dialog/mail/copy', ['ff' => $ff], 'formidable');
        View::element('dialog/mail/delete', ['ff' => $ff], 'formidable');
    ?>

<?php } else if ($this->controller->getAction() == 'mail' && is_object($ff)) { ?>

    <form method="post" action="<?=$view->action('mail', $ff->getItemID(), $mail?$mail->getItemID():0); ?>">
        <?php View::element('form/mail/edit', ['ff' => $ff, 'mail' => $mail?$mail:0], 'formidable'); ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/formidable/forms/mails', $ff->getItemID()); ?>" class="btn btn-secondary float-start"><?= t('Cancel'); ?></a>
                <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
            </div>
        </div>
    </form>

<?php } else if ($this->controller->getAction() == 'import') { ?>

    <form method="post" action="<?=$view->action('import'); ?>">
        <?= app('helper/validation/token')->output('import_formidable'); ?>
        <?php View::element('dashboard/install', [], 'formidable'); ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= URL::to('/dashboard/formidable/forms'); ?>" class="btn btn-secondary float-start"><?= t('Cancel'); ?></a>
                <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
            </div>
        </div>
    </form>

<?php } else { ?>

    <?php if (isset($formidable_full)) { ?>

        <div class="help-block bg-dark text-white d-flex justify-content-between">
            <span><?=t("You currently have Formidable Full installed (or Formidable Full Database-tables) on this site. You can import / convert the old data to the new Formidable(V9).");?></span>
            <a href="<?=$this->action('import');?>" class="btn btn-sm btn-info"><?=t("Click to import/convert");?></a>
        </div>

    <?php } ?>

    <?php if (count($forms) > 0) {  ?>

        <div class="table-responsive">
            <table class="table table-hover ccm-search-results-table">
                <thead>
                    <tr>
                        <th><?= t('Name'); ?></th>
                        <th><?= t('Last submission'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($forms as $form) { ?>
                        <tr class="<?=$form->getEnabled()!=1?'disabled':'enabled'; ?>">
                            <td class="form-name align-middle">
                                <?=$form->getName(); ?>
                                <span class="small text-muted">(<?=$form->getHandle();?>)</span>
                                <?php if ($form->getEnabled() != 1) { ?>
                                    <span class="small text-danger ms-2" data-bs-toggle="tooltip" data-bs-title="<?=t('Disabled');?>">
                                        <i class="fa-fw fa fa-exclamation-circle"></i>
                                    </span>
                                <?php } else { ?>
                                    <?php if ((int)$form->getLimit() == 1) { ?>
                                        <span class="small text-<?=$form->isLimited()?'danger':'success';?> ms-2" data-bs-toggle="tooltip" data-bs-title="<?=$form->isLimited()?t('Disabled by limits'):t('Limits');?>">
                                            <i class="fa-fw fa fa-hourglass-end"></i>
                                        </span>
                                    <?php } ?>
                                    <?php if ((int)$form->getSchedule() == 1) { ?>
                                        <span class="small text-<?=$form->isScheduled()?'danger':'success';?> ms-2" data-bs-toggle="tooltip" data-bs-title="<?=$form->isScheduled()?t('Disabled by schedule'):t('Scheduled');?>">
                                            <i class="fa-fw fa fa-clock"></i>
                                        </span>
                                    <?php } ?>
                                    <?php if ((int)$form->getPrivacy() == 1) { ?>
                                        <span class="small text-warning ms-2" data-bs-toggle="tooltip" data-bs-title="<?=t('Privacy settings');?>">
                                            <i class="fa-fw fa fa-eye-slash"></i>
                                        </span>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                            <td class="form-result align-middle">
                                <?=$form->getLastResult()?$form->getLastResult()->getDateAdded(true):'<em>'.t('No submissions yet').'</em>'; ?>
                            </td>
                            <td class="form-tasks">
                                <?php View::element('form/menu', ['ff' => $form], 'formidable'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

	<?php } else { ?>

		<div class="help-block mt-0 text-center">
            <?= t('You have not created any forms yet.'); ?>
            <br><br><a href="<?=$view->action('props'); ?>" class="btn btn-secondary"><?=t('Add new'); ?></a>
        </div>

    <?php } ?>

<?php } ?>

<?php
    View::element('dialog/form/copy', ['ff' => $ff], 'formidable');
    View::element('dialog/form/delete', ['ff' => $ff], 'formidable');

    if ($ff) {
        View::element('dialog/data', ['ff' => $ff], 'formidable');
    }
?>

<script>
    $(function() {

        /* copy form */
        $('a[data-copy-form]').on('click', function() {
            var itemID = $(this).attr('data-copy-form');
            jQuery.fn.dialog.open({
                element: 'div[data-copy-form]',
                modal: true,
                width: 400,
                title: '<?=t('Duplicate form'); ?>',
                height: 400,
                onOpen: function() {
                    $('form[data-copy-form] input[id="formID"]').val(itemID);
                }
            });
        });

        /* delete form */
        $('a[data-delete-form]').on('click', function() {
            var itemID = $(this).attr('data-delete-form');
            jQuery.fn.dialog.open({
                element: 'div[data-delete-form]',
                modal: true,
                width: 400,
                title: '<?=t('Delete form'); ?>',
                height: 225,
                onOpen: function() {
                    $('form[data-delete-form] input[id="formID"]').val(itemID);
                }
            });
        });

        /* copy mail */
        $('a[data-copy-mail]').on('click', function() {
            var itemID = $(this).attr('data-copy-mail');
            jQuery.fn.dialog.open({
                element: 'div[data-copy-mail]',
                modal: true,
                width: 400,
                title: '<?=t('Duplicate mail'); ?>',
                height: 400,
                onOpen: function() {
                    $('form[data-copy-mail] input[id="mailID"]').val(itemID);
                }
            });
        });

        /* delete mail */
        $('a[data-delete-mail]').on('click', function() {
            var itemID = $(this).attr('data-delete-mail');
            jQuery.fn.dialog.open({
                element: 'div[data-delete-mail]',
                modal: true,
                width: 400,
                title: '<?=t('Delete mail'); ?>',
                height: 175,
                onOpen: function() {
                    $('form[data-delete-mail] input[id="mailID"]').val(itemID);
                }
            });
        });
    });

</script>

<?php
    // show flash messages
    if (isset($flash)) {
        echo $flash;
    }