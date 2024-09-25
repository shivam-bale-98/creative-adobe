<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Url;

?>

<div class="ccm-header-search-form ccm-ui" data-header="page-manager">
    <form method="get" class="row row-cols-auto g-0 align-items-center" action="<?=$headerSearchAction;?>">

        <div class="ccm-header-search-form-input input-group">
            <?php if (isset($query)): ?>
                <a href="javascript:void(0);"
                   data-launch-dialog="advanced-search"
                   class="ccm-header-launch-advanced-search"
                   data-advanced-search-dialog-url="<?=Url::to('/formidable/dialog/dashboard/results/advanced_search');?>"
                   data-advanced-search-query="advanced-search-query">
                    <?=t('Advanced'); ?>
                    <script type="text/concrete-query" data-query="advanced-search-query">
                        <?=$query; ?>
                    </script>
                </a>
            <?php else: ?>
                <a href="javascript:void(0);"
                   data-launch-dialog="advanced-search"
                   class="ccm-header-launch-advanced-search"
                   data-advanced-search-dialog-url="<?=Url::to('/formidable/dialog/dashboard/results/advanced_search');?>">
                    <?=t('Advanced');?>
                </a>
            <?php endif; ?>

            <?php
                echo $form->search('keywords', [
                    'placeholder' => t('Search'),
                    'class' => 'form-control border-end-0',
                    'autocomplete' => 'off'
                ]);
            ?>

            <button type="submit" class="input-group-icon">
                <svg width="16" height="16">
                    <use xlink:href="#icon-search"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    (function ($) {
        $(function () {
            ConcreteEvent.subscribe('SavedSearchCreated', function () {
                window.location.reload();
            });

            ConcreteEvent.subscribe('SavedPresetSubmit', function (e, url) {
                window.location.href = url;
            });
        });
    })(jQuery);
</script>
