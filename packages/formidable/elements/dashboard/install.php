<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Package\Formidable\Src\Formidable\Helpers\FormidableFull;

if (FormidableFull::exists()) {

    $form = app()->make(Form::class); ?>

    <h3><?=t('Import / Convert from Formidable Full');?></h3>

    <p><?=t('You have Formidable Full installed on this site!');?><br><?=t('Formidable is able to import / convert the data from Formidable Full to the Formidable (v9) version you are currenlty running.');?></p>

    <div class="help-block">
        <?=t('Forms with there layout and elements, mails, results and templates will be imported.');?><br>
        <?=t('After importing data you need to check and validate all the data. It could be that some data is imported or converted unsuccesfully.');?><br>
        <?=t('Block data won\'t ne converted, so you need to add the forms manually on the different pages / stacks.');?><br>
    </div>

    <div class="form-group mb-0">
        <?=$form->label('convertFromFormidableFull', t('Convert/Import data from Formidable Full')); ?>
        <div class="checkbox">
            <label>
                <?=$form->checkbox('convertFromFormidableFull', 1, 0); ?>
                <span><?=t('Yes, I want to convert existing Formidable Full data to new Formidable data (forms, layout, elements, templates and results)'); ?></span><br>
            </label>
        </div>
    </div>

    <div class="import_data" style="display:none">

        <div class="help-block bg-warning">
            <?=t('Warning: Please create a full database backup first!');?>
        </div>

        <div class="form-group mt-4">
            <?=$form->label('removeFormidableFull', t('Uninstall Formidable Full')); ?>
            <div class="checkbox">
                <label>
                    <?=$form->checkbox('removeFormidableFull', 1, 0); ?>
                    <span><?=t('After conversion remove the old Formidable Full package, including data.'); ?></span><br>
                </label>
            </div>
            <div class="help-block bg-danger text-white remove_data"><?=t('Beware: This deletes all old Formidable Full data!');?></div>
        </div>
    </div>

    <script>
        $(function() {
            $('[name="convertFromFormidableFull"]').on('change', function() {
                $('div.import_data').hide();
                if ($(this).is(':checked')) {
                    $('div.import_data').show();
                }
            }).trigger('change');

            $('[name="removeFormidableFull"]').on('change', function() {
                $('div.remove_data').hide();
                if ($(this).is(':checked')) {
                    $('div.remove_data').show();
                }
            }).trigger('change');
        });
    </script>

<?php }