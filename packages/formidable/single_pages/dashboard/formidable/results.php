<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php use Concrete\Core\View\View; ?>

<div class="formidableLoader"></div>

<?php if ($this->controller->getAction() == 'details' && is_object($ff) && is_object($result)) { ?>

    <form method="post" action="<?=$view->action('details', $ff->getItemID(), $result->getItemID()); ?>">
        <?php View::element('results/details', ['ff' => $ff, 'result' => $result], 'formidable'); ?>
    </form>

<?php } else { ?>

    <?php if (count($result->getItems())) { ?>
        <div id="ccm-search-results-table">
            <div class="table-responsive">
                <table class="ccm-search-results-table" data-search-results="files">
                    <thead>
                    <tr>
                        <th class="ccm-search-results-bulk-selector">
                            <div class="btn-group dropdown">
                                <span class="btn btn-secondary" data-search-checkbox-button="select-all">
                                    <input type="checkbox" data-search-checkbox="select-all"/>
                                </span>

                                <button
                                    type="button"
                                    disabled="disabled"
                                    data-search-checkbox-button="dropdown"
                                    class="btn btn-secondary dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown"
                                    data-reference="parent">

                                    <span class="sr-only">
                                        <?php echo t("Toggle Dropdown"); ?>
                                    </span>
                                </button>

                                <?php echo $resultsBulkMenu->getMenuElement(); ?>
                            </div>
                        </th>
                        <?php foreach ($result->getColumns() as $column) { ?>
                            <th class="<?=$column->getColumnStyleClass()?>">
                                <?php if ($column->isColumnSortable()) { ?>
                                    <a href="<?=$column->getColumnSortURL()?>"><?=$column->getColumnTitle()?></a>
                                <?php } else { ?>
                                    <span><?=$column->getColumnTitle()?></span>
                                <?php } ?>
                            </th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($result->getItems() as $item) {
                            $entry = $item->getItem();
                            ?>
                            <tr data-details-url="javascript:void(0)">
                                <td class="ccm-search-results-checkbox">
                                    <input data-search-checkbox="individual" type="checkbox" data-item-id="<?php echo $entry->getItemID() ?>"/>
                                </td>
                                <?php
                                    $i = 0;
                                    foreach ($item->getColumns() as $column) {
                                        $class = '';
                                        /*
                                        if ($column->getColumn() instanceof \Concrete\Core\File\Search\ColumnSet\Column\NameColumn) {
                                            $class = 'ccm-search-results-name';
                                            $value = '<a href="' . $item->getDetailsURL() . '">' . $column->getColumnValue($item) . '</a>';
                                        } else {
                                            $value = $column->getColumnValue($item);
                                        }
                                        */
                                        if ($column->getColumnKey() === "result.resultID") { ?>
                                            <td class="ccm-search-results-name">
                                                <a href="<?=Url::to('/dashboard/formidable/results/details', $ff->getItemID(), $entry->getItemID())?>">
                                                    <?php echo $column->getColumnValue(); ?>
                                                </a>
                                            </td>
                                        <?php } else { ?>
                                            <td class="<?=$class?>">
                                                <?=$column->getColumnValue($item);?>
                                            </td>
                                        <?php } ?>
                                    <?php
                                    }
                                ?>
                                <td class="ccm-search-results-button text-end">
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
                                            <li><a href="<?=Url::to('/dashboard/formidable/results/details', $ff->getItemID(), $entry->getItemID()); ?>" class="dropdown-item"><?=t('Details'); ?></a><li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a href="#" data-resend-result="<?=$entry->getItemID(); ?>" class="dropdown-item"><?=t('Resend'); ?></a><li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a href="#" data-delete-result="<?=$ff->getItemID(); ?>" class="dropdown-item text-danger"><?=t('Delete'); ?></a><li>
                                        </ul>
                                    </div>

                                    <div class="d-none d-lg-inline-block">
                                        <div class="btn-group">
                                            <a href="<?=Url::to('/dashboard/formidable/results/details', $ff->getItemID(), $entry->getItemID()); ?>" class="btn btn-sm btn-secondary"><?=t('Details'); ?></a>
                                            <a href="#" data-resend-result="<?=$entry->getItemID(); ?>" class="btn btn-sm btn-secondary"><?=t('Resend'); ?></a>
                                            <a href="#" data-delete-result="<?=$entry->getItemID(); ?>" class="btn btn-sm btn-secondary text-danger" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>">
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
        </div>

        <?=$result->getPagination()?$result->getPagination()->renderView('dashboard'):''?>

    <?php } else { ?>

        <div id="ccm-dashboard-content-regular">
            <div class="help-block mt-0 text-center">
                <?= t('No results yet!'); ?>
            </div>
        </div>

    <?php } ?>

<?php } ?>

<?php
    View::element('dialog/result/delete', ['ff' => $ff], 'formidable');
    View::element('dialog/result/resend', ['ff' => $ff], 'formidable');
?>

<style>

</style>

<script>
    (function ($) {

        /* delete result */
        $('a[data-delete-result]').on('click', function() {
            var itemID = $(this).attr('data-delete-result');
            if (itemID == 'bulk') {
                var items = [];
                $('[data-search-checkbox="individual"]:checked').each(function(i, check) {
                    items.push($(check).data('item-id'));
                });
            }
            jQuery.fn.dialog.open({
                element: 'div[data-delete-result]',
                modal: true,
                width: 400,
                title: '<?=t('Delete result(s)'); ?>',
                height: 175,
                onOpen: function() {
                    $('form[data-delete-result] input[id="resultID"]').val(itemID=='bulk'?items.join(','):itemID);
                }
            });
        });

        /* resend result */
        $('a[data-resend-result]').on('click', function() {
            var itemID = $(this).attr('data-resend-result');
            if (itemID == 'bulk') {
                var items = [];
                $('[data-search-checkbox="individual"]:checked').each(function(i, check) {
                    items.push($(check).data('item-id'));
                });
            }
            jQuery.fn.dialog.open({
                element: 'div[data-resend-result]',
                modal: true,
                width: 600,
                title: '<?=t('Resend result(s)'); ?>',
                height: 400,
                onOpen: function() {
                    $('form[data-resend-result] input[id="resultID"]').val(itemID=='bulk'?items.join(','):itemID);
                }
            });
        });

    })(jQuery);

</script>

<?php
    // show flash messages
    if (isset($flash)) {
        echo $flash;
    }
