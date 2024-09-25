<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Application;

$form = Application::getFacadeApplication()->make('helper/form');
?>

<div class="form-group">
    <?=$form->label('removeContent', t('Remove all')); ?>
    <div class="checkbox">
        <label>
        	<?=$form->checkbox('removeContent', 1, 1); ?>
        	<span><?=t('Yes, I want to remove all the data from Formidable (forms, templates and results)'); ?></span><br>
        </label>
    </div>
    <div class="help-block bg-danger text-white remove_data"><?=t('Beware: This deletes all Formidable data!');?></div>
</div>

<script>
    $(function() {
        $('[name="removeContent"]').on('change', function() {
            $('div.remove_data').hide();
            if ($(this).is(':checked')) {
                $('div.remove_data').show();
            }
        }).trigger('change');
    });
</script>

