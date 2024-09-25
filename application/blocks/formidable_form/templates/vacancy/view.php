<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="formidable" id="formidable_<?= $bID; ?>">

    <?php if ($c && $c->isEditMode()) { ?>

        <?php // disable form in editmode 
        ?>
        <div class="alert alert-info" data-formidable-message>
            <?= t('Formidable Form isn\'t viewable when page is in Edit Mode'); ?>
        </div>

    <?php } elseif (!isset($ff) || isset($no_elements)) { ?>

        <?php // no ff set?! 
        ?>
        <div class="alert alert-danger" data-formidable-message>
            <?= t('Formidable Form isn\'t found or available'); ?>
        </div>

    <?php } elseif (isset($not_found) || isset($disabled)) { ?>

        <?php // form isn't available or found message 
        ?>
        <div class="alert alert-info" data-formidable-message>
            <?= t('Formidable Form is no longer available'); ?>
        </div>

    <?php } elseif (isset($limited)) { ?>

        <?php // limits message 
        ?>
        <div class="alert alert-warning" data-formidable-message>
            <?= $limited; ?>
        </div>

    <?php } elseif (isset($scheduled)) { ?>

        <?php // scheduling message 
        ?>
        <div class="alert alert-warning" data-formidable-message>
            <?= $scheduled ?>
        </div>

    <?php } else { ?>

        <div id="formidable_<?= $ff->getHandle(); ?>" class="<?= isset($options['formAdditionalClass']) ? $options['formAdditionalClass'] : ''; ?>">

            <?php // error messaging //; 
            ?>
            <div class="alert alert-danger hide" data-formidable-message>
            </div>

            <?php // start form 
            ?>
            <form action="<?php echo \URL::to('/formidable/dialog/formidable'); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data" data-form-handle="formidable_<?= $ff->getHandle(); ?>">

                <?php // some hidden fields for processing the form 
                ?>
                <?= $form->hidden('formID', $ff->getItemID()); ?>
                <?= $form->hidden('ccm_token', $ccm_token); ?>
                <?= $form->hidden('bID', $bID); ?>
                <?= $form->hidden('cID', $c->getCollectionID()); ?>
                <?= $form->hidden('action', 'submit'); ?>
                <?= $form->hidden('g-recaptcha-response'); ?>

                <?php // generate form 
                ?>
                <?php foreach ($ff->getRows() as $row) { ?>

                    <?php
                    // render row
                    // default format is: <div class="%s">
                    // you can add your own like: $row->renderStart('<div class="%s">');
                    ?>
                    <?= $row->renderStart(); ?>

                    <?php foreach ($row->getColumns() as $col) { ?>

                        <?php
                        // render column
                        // default format is: <div class="%s">
                        // the width of the column is also set as class: 'col-x';
                        // you can add your own like: $col->renderStart('<div class="%s">');
                        ?>
                        <?= $col->renderStart(); ?>

                        <?php foreach ($col->getElements() as $el) { ?>

                            <?php
                            // render element
                            // default format is: <div class="form-group" data-formidable-type="%s">
                            // the 'data-formidable-type' is necessary for error-reporting
                            // you can add your own like: $el->renderStart('<div class="form-group" data-formidable-type="%s">');
                            ?>
                            <?= $el->renderStart(); ?>

                            <?php // generate label 
                            ?>
                            <?php if (!$el->getProperty('no_label')) {
                                $label_format = !$el->getProperty('no_label') && $el->isRequired() ?
                                    '<label for="%s">%s&nbsp;<span class="small text-danger">*</span></label>' :
                                    '<label for="%s">%s</label>';
                            ?>
                                <?= $el->label($label_format); ?>
                            <?php } ?>

                            <!-- <?php // is required? 
                                    ?>
                            <?php if (!$el->getProperty('no_label') && $el->isRequired()) { ?>
                                <span class="small text-danger">*</span>
                            <?php } ?> -->

                            <?php // generate field 
                            ?>
                            <?= $el->field(); ?>

                            <?php
                            // specific fields have multiple options for showing fields
                            // this way you have more control
                            // example: "address"-field
                            // if ($el->getType() == 'address') {
                            //     // this will get "address1"-field
                            //     echo $el->getTypeObject()->getField('street');
                            // }
                            ?>

                            <?php // help / description? 
                            ?>
                            <?php if ($el->getProperty('help')) { ?>
                                <div class="help-block small text-muted">
                                    <?= $el->getProperty('help_value'); ?>
                                </div>
                            <?php } ?>

                            <?= $el->renderEnd(); ?>

                        <?php } ?>

                        <?= $col->renderEnd(); ?>

                    <?php } ?>

                    <?= $row->renderEnd(); ?>

                <?php } ?>

                <?php // add the submit button 
                ?>
                <!--  -->

                <?php if (!$ff->hasSubmitButton()) { ?>
                    <div class="row mt-[4rem]">
                        <div class="col-12">
                            <button id="<?php echo 'btnSubmit_' . $ff->getItemID(); ?>" name="<?php echo 'btnSubmit_' . $ff->getItemID(); ?>" type="submit" class="btn btn-primary  channeline-btn channeline-btn--arrow channeline-btn--border channeline-btn--white relative">
                                <span class="text z-2 relative"><?= t('Submit'); ?></span>
                                <div class="shape absolute">
                                    <i class="icon-right_button_arrow absolute z-2">

                                    </i>
                                    <span class="bg absolute inset-0 size-full"></span>
                                </div>
                            </button>
                        </div>
                    </div>
                <?php } ?>

            </form>

        </div>

        <script>
            var formidable_<?= $ff->getHandle(); ?>;
            $(function() {
                var settings = {
                    locale: '<?= $locale; ?>',

                    errors: <?= isset($options['errors']) ? "'" . $options['errors'] . "'" : "'element'" ?>,
                    errorsAdditionalClass: <?= isset($options['errorsAdditionalClass']) ? "'" . $options['errorsAdditionalClass'] . "'" : "''" ?>,

                    successAdditionalClass: <?= isset($options['successAdditionalClass']) ? "'" . $options['successAdditionalClass'] . "'" : "''" ?>,
                    successHideForm: <?= isset($options['successHideForm']) && (int)$options['successHideForm'] ? "true" : "false" ?>,

                    scrollToTop: <?= isset($options['scrollToTop']) && (int)$options['scrollToTop'] ? "true" : "false" ?>,
                    scrollOffset: <?= isset($options['scrollOffset']) ? (int)$options['scrollOffset'] : 0 ?>,
                    scrollTime: <?= isset($options['scrollTime']) ? (int)$options['scrollTime'] : 0 ?>,

                    dependencies: <?= $ff->getDependenciesJson(); ?>,

                    onloadCallback: <?= isset($options['onloadCallback']) && !empty($options['onloadCallback']) ? $options['onloadCallback'] : "''" ?>,
                    errorsCallback: <?= isset($options['errorsCallback']) && !empty($options['errorsCallback']) ? $options['errorsCallback'] : "''" ?>,
                    successCallback: <?= isset($options['successCallback']) && !empty($options['successCallback']) ? $options['successCallback'] : "''" ?>,
                };
                formidable_<?= $ff->getHandle(); ?> = $('[id="formidable_<?= $ff->getHandle(); ?>"]').formidable(settings);
            });
        </script>

    <?php } ?>

</div>