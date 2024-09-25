<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\View\View;

$form = app('helper/form');
$token = app('helper/validation/token');

if ($ff->getItemID() <= 0) {

    View::element('system_errors', ['format' => 'block', 'error' => t('Please create a form first')]);

} else { ?>

    <style>
        .card {
            border: 1px solid rgba(0,0,0,.025);
        }
        .card.hover {
            border: 1px solid rgba(74,144,226,.2);
            box-shadow: 0 2px 3px rgba(74,144,226,.1);
        }
        .card.form-element .btn-group {
            display:none;
        }
        .card.form-element.hover .btn-group {
            display:inline-flex;
        }
        .btn-back svg {
            height: 20px;
            transition: fill .1s ease-in-out;
            width: 20px;
        }
        .ui-sortable-handle:hover {
            cursor: grab;
        }
        .ui-sortable-handle:active {
            cursor: grabbing;
        }

    </style>

    <div class="form-rows mb-3">

        <div class="row form-row form-row-empty mb-3" style="display:none">
            <div class="col">
                <div class="card small text-muted">
                    <div class="card-body">
                        <?=t('(empty form)');?>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($ff->getRows() as $row) { ?>
            <div class="row form-row mb-3">
                <div class="col">
                    <div class="card" data-row-id="<?=$row->getItemID();?>">
                        <div class="card-header form-row-header">
                            <?=$row->getName();?>
                            <div class="btn-group float-end">
                                <a href="<?=$view->action('column', $ff->getItemID(), $row->getItemID());?>" class="btn btn-secondary btn-sm" data-add-column="<?=$row->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Add column');?>"><i class="fa-fw fa fa-plus"></i></a>
                                <a href="<?=$view->action('row', $ff->getItemID(), $row->getItemID());?>" class="btn btn-secondary btn-sm" data-update-row="<?=$row->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Edit');?>"><i class="fa-fw fa fa-edit"></i></a>
                                <a href="javascript:;" class="btn btn-secondary btn-sm" data-copy-row="<?=$row->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Copy');?>"><i class="fa-fw fa fa-copy"></i></a>
                                <a href="javascript:;" class="btn btn-secondary btn-sm text-danger" data-delete-row="<?=$row->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row form-columns">

                                <div class="col form-column form-column-empty empty small text-muted" style="<?=count($row->getColumns())!=0?'display:none':'';?>">
                                    <div class="card small text-muted">
                                        <div class="card-body">
                                            <?=t('(empty row)');?>
                                        </div>
                                    </div>
                                </div>

                                <?php foreach ($row->getColumns() as $col) { ?>
                                    <div class="col col-<?=$col->getWidth();?> form-column">
                                        <div class="card" data-column-id="<?=$col->getItemID();?>">
                                            <div class="card-header form-column-header">
                                                <?=$col->getName();?>
                                                <?php if ($col->getWidth() > 3) { ?>
                                                    <div class="btn-group float-end">
                                                        <a href="<?=$view->action('element', $ff->getItemID(), $col->getItemID());?>" class="btn btn-secondary btn-sm" data-add-element="<?=$col->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Add element');?>"><i class="fa-fw fa fa-plus"></i></a>
                                                        <a href="<?=$view->action('column', $ff->getItemID(), $row->getItemID(), $col->getItemID());?>" class="btn btn-secondary btn-sm" data-update-column="<?=$col->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Edit');?>"><i class="fa-fw fa fa-edit"></i></a>
                                                        <a href="javascript:;" class="btn btn-secondary btn-sm" data-copy-column="<?=$col->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Copy');?>"><i class="fa-fw fa fa-copy"></i></a>
                                                        <a href="javascript:;" class="btn btn-secondary btn-sm text-danger" data-delete-column="<?=$col->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>"><i class="fa-fw fa fa-trash"></i></a>
                                                    </div>
                                                <?php } else { ?>
                                                    <button class="btn btn-secondary btn-sm float-end" data-bs-toggle="dropdown" data-bs-auto-close="true">
                                                        <span data-bs-toggle="tooltip" data-bs-title="<?=t('Options');?>"><i class="fa-fw fas fa-ellipsis-v"></i></span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item small" data-add-element="<?=$col->getItemID();?>" href="<?=$view->action('element', $ff->getItemID(), $col->getItemID());?>"><i class="fa-fw fa fa-plus"></i> <?=t('Add element');?></a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item small" data-update-column="<?=$col->getItemID();?>" href="<?=$view->action('column', $ff->getItemID(), $row->getItemID(), $col->getItemID());?>"><i class="fa-fw fa fa-edit"></i> <?=t('Edit');?></a></li>
                                                        <li><a class="dropdown-item small" data-copy-column="<?=$col->getItemID();?>" href="javascript:;"><i class="fa-fw fa fa-copy"></i> <?=t('Copy');?></a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item small text-danger" data-delete-column="<?=$col->getItemID();?>" href="javascript:;"><i class="fa-fw fa fa-trash"></i> <?=t('Delete');?></a></li>
                                                    </ul>
                                                <?php } ?>

                                            </div>
                                            <div class="card-body form-elements">

                                                <div class="card form-element form-element-empty small text-muted" style="<?=count($col->getElements())!=0?'display:none':'';?>">
                                                    <div class="card-body">
                                                        <?=t('(empty column)');?>
                                                    </div>
                                                </div>

                                                <?php foreach ($col->getElements() as $el) { ?>
                                                    <div class="card form-element" data-element-id="<?=$el->getItemID();?>">
                                                        <div class="card-body form-element-header">
                                                            <div class="float-start">
                                                                <?=$el->getName();?> <?=$el->isRequired()?'<sup class="text-danger">*</sup>':'';?>
                                                                <div class="text-muted small"><?=$el->getTypeName();?></div>
                                                            </div>
                                                            <?php if ($col->getWidth() > 3) { ?>
                                                                <div class="btn-group float-end">
                                                                    <a href="<?=$view->action('element', $ff->getItemID(), $col->getItemID(), $el->getItemID());?>" class="btn btn-secondary btn-sm" data-update-element="<?=$el->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Edit');?>"><i class="fa-fw fa fa-edit"></i></a>
                                                                    <a href="javascript:;" class="btn btn-secondary btn-sm" data-copy-element="<?=$el->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Copy');?>"><i class="fa-fw fa fa-copy"></i></a>
                                                                    <a href="javascript:;" class="btn btn-secondary btn-sm text-danger" data-delete-element="<?=$el->getItemID();?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Delete');?>"><i class="fa-fw fa fa-trash"></i></a>
                                                                </div>
                                                            <?php } else { ?>
                                                                <button class="btn btn-secondary btn-sm float-end" data-bs-toggle="dropdown" data-bs-auto-close="true">
                                                                    <span data-bs-toggle="tooltip" data-bs-title="<?=t('Options');?>"><i class="fa-fw fas fa-ellipsis-v"></i></span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item small" data-update-element="<?=$el->getItemID();?>" href="<?=$view->action('element', $ff->getItemID(), $col->getItemID(), $el->getItemID());?>"><i class="fa-fw fa fa-edit"></i> <?=t('Edit');?></a></li>
                                                                    <li><a class="dropdown-item small" data-copy-element="<?=$el->getItemID();?>" href="javascript:;"><i class="fa-fw fa fa-copy"></i> <?=t('Copy');?></a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li><a class="dropdown-item small text-danger" data-delete-element="<?=$el->getItemID();?>" href="javascript:;"><i class="fa-fw fa fa-trash"></i> <?=t('Delete');?></a></li>
                                                                </ul>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <div class="row mt-3">
                                                    <div class="col-12 text-start">
                                                        <a href="<?=$view->action('element', $ff->getItemID(), $col->getItemID());?>" data-add-element="<?=$col->getItemID();?>" class="btn btn-sm btn-primary">
                                                            <?=t('Add element');?>
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mt-3">
                                    <div class="col-12 text-start">
                                        <a href="<?=$view->action('column', $ff->getItemID(), $row->getItemID());?>" data-add-column="<?=$row->getItemID();?>" class="btn btn-sm btn-primary">
                                            <?=t('Add column');?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row mt-3">
        <div class="col-12 text-start">
            <a href="<?=$view->action('row', $ff->getItemID());?>" class="btn btn-sm btn-primary" data-add-row>
                <?=t('Add row');?>
            </a>
        </div>
     </div>

    <?php
        View::element('dialog/row/copy', ['ff' => $ff], 'formidable');
        View::element('dialog/row/delete', ['ff' => $ff], 'formidable');

        View::element('dialog/column/copy', ['ff' => $ff], 'formidable');
        View::element('dialog/column/delete', ['ff' => $ff], 'formidable');

        View::element('dialog/element/copy', ['ff' => $ff], 'formidable');
        View::element('dialog/element/delete', ['ff' => $ff], 'formidable');
    ?>

    <script>
        $(function() {

            /* copy row */
            $('a[data-copy-row]').on('click', function() {
                var itemID = $(this).attr('data-copy-row');
                jQuery.fn.dialog.open({
                    element: 'div[data-copy-row]',
                    modal: true,
                    width: 400,
                    title: '<?=t('Duplicate row'); ?>',
                    height: 400,
                    onOpen: function() {
                        $('form[data-copy-row] input[id="rowID"]').val(itemID);
                    }
                });
            });


            /* delete row */
            $('a[data-delete-row]').on('click', function() {
                var itemID = $(this).attr('data-delete-row');
                jQuery.fn.dialog.open({
                    element: 'div[data-delete-row]',
                    modal: true,
                    width: 400,
                    title: '<?=t('Delete row'); ?>',
                    height: 250,
                    onOpen: function() {
                        $('form[data-delete-row] input[id="rowID"]').val(itemID);
                    }
                });
            });

            FormidableCheckRows();

            /* copy column */
            $('a[data-copy-column]').on('click', function() {
                var itemID = $(this).attr('data-copy-column');
                jQuery.fn.dialog.open({
                    element: 'div[data-copy-column]',
                    modal: true,
                    width: 400,
                    title: '<?=t('Duplicate column'); ?>',
                    height: 400,
                    onOpen: function() {
                        $('form[data-copy-column] input[id="columnID"]').val(itemID);
                    }
                });
            });

            /* delete column */
            $('a[data-delete-column]').on('click', function() {
                var itemID = $(this).attr('data-delete-column');
                jQuery.fn.dialog.open({
                    element: 'div[data-delete-column]',
                    modal: true,
                    width: 400,
                    title: '<?=t('Delete column'); ?>',
                    height: 250,
                    onOpen: function() {
                        $('form[data-delete-column] input[id="columnID"]').val(itemID);
                    }
                });
            });

            FormidableCheckColumns();

             /* copy column */
            $('a[data-copy-element]').on('click', function() {
                var itemID = $(this).attr('data-copy-element');
                jQuery.fn.dialog.open({
                    element: 'div[data-copy-element]',
                    modal: true,
                    width: 400,
                    title: '<?=t('Duplicate element'); ?>',
                    height: 400,
                    onOpen: function() {
                        $('form[data-copy-element] input[id="elementID"]').val(itemID);
                    }
                });
            });

            /* delete element */
            $('a[data-delete-element]').on('click', function() {
                var itemID = $(this).attr('data-delete-element');
                jQuery.fn.dialog.open({
                    element: 'div[data-delete-element]',
                    modal: true,
                    width: 400,
                    title: '<?=t('Delete element'); ?>',
                    height: 175,
                    onOpen: function() {
                        $('form[data-delete-element] input[id="elementID"]').val(itemID);
                    }
                });
            });

            // TODO
            $('.card').on('mouseenter', function() {
                $(this).addClass('hover');
                $(this).parents('.card').removeClass('hover');
            }).on('mouseleave', function() {
                $(this).removeClass('hover');
            });

        });

        function FormidableSaveSorting()
        {
            var sort = {
                'ccm_token': '<?=$token->generate('sort_form');?>',
                'rows': {}
            };
            $('[data-row-id]').each(function(r, row) {
                var rowID = $(row).attr('data-row-id');
                if (rowID == 0) return;
                sort.rows[r] = {'rowID': rowID, 'columns': {}};
                $('[data-column-id]', $(this)).each(function(c, column) {
                    var colID = $(column).attr('data-column-id');
                    if (colID == 0) return;
                    sort.rows[r].columns[c] = {'columnID': colID, 'elements': {}};
                    $('[data-element-id]', $(this)).each(function(e, el) {
                        var elID = $(el).attr('data-element-id');
                        if (elID == 0) return;
                        sort.rows[r].columns[c].elements[e] = elID;
                    });
                });
            });

            $.ajax({
                url: '<?=$view->action('sort', $ff->getItemID());?>',
                method: 'POST',
                dataType: 'json',
                data: sort,
                beforeSend: function() {
                    jQuery.fn.dialog.showLoader();
                },
                error: function(response) {
                    var data = response.responseJSON;
                    ConcreteAlert.error({
                        title: '<?=t('Error');?>',
                        message: data.error
                    });
                },
                success: function(data) {
                    ConcreteAlert.notify({
                        title: '<?=t('Success');?>',
                        message: data.success
                    });
                    jQuery.fn.dialog.hideLoader();
                }
            });
        }

    </script>

<?php }