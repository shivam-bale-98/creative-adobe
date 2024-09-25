<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Url;
?>

<div class="row row-cols-auto g-0 align-items-center mb-3 mb-lg-0">

    <div class="btn-group me-4">

        <?php if (!empty($formOptions)): ?>
            <button
                    type="button"
                    class="btn btn-secondary p-2 dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                <span id="selected-option">
                    <?=$formName;?>
                </span>
            </button>

            <ul class="dropdown-menu">
                <li class="dropdown-header">
                    <?=t('Select form');?>
                </li>

                <?php foreach ($formOptions as $formOptionID => $formOptionName): ?>
                    <li data-form-id="<?=$formOptionID; ?>">
                        <a class="dropdown-item <?=($formOptionID === $formID) ? 'active' : ''; ?>"
                        href="<?=Url::to('/dashboard/formidable/results', $formOptionID);?>">
                            <?=$formOptionName; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($itemsPerPageOptions)): ?>
            <button
                    type="button"
                    class="btn btn-secondary p-2 dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">

                <span id="selected-option">
                    <?=$itemsPerPage;?>
                </span>
            </button>

            <ul class="dropdown-menu">
                <li class="dropdown-header">
                    <?=t('Items per page');?>
                </li>

                <?php foreach ($itemsPerPageOptions as $itemsPerPageOption): ?>
                    <?php
                    $url = $urlHelper->setVariable([
                        'itemsPerPage' => $itemsPerPageOption
                    ]);
                    ?>
                    <li data-items-per-page="<?=$itemsPerPageOption; ?>">
                        <a class="dropdown-item <?=($itemsPerPageOption === $itemsPerPage) ? 'active' : ''; ?>"
                        href="<?=$url;?>">
                            <?=$itemsPerPageOption; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>

    <div class="btn-group me-4">
        <button
            type="button"
            class="btn btn-secondary p-2 dropdown-toggle"
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
            <li><a href="<?=Url::to('/dashboard/formidable/forms/props', $formID); ?>" class="dropdown-item"><?=t('Properties'); ?></a><li>
            <li><a href="<?=Url::to('/dashboard/formidable/forms/layout', $formID); ?>" class="dropdown-item"><?=t('Fields'); ?></a><li>
            <li><a href="<?=Url::to('/dashboard/formidable/forms/mails', $formID); ?>" class="dropdown-item"><?=t('Mails'); ?></a><li>
        </ul>
    </div>

    <a class="ccm-hover-icon" href="<?=Url::to('/dashboard/formidable/results/export', $formID);?>" data-bs-toggle="tooltip" data-bs-title="<?=t('Export to CSV');?>">
        <i class="fas fa-download" aria-hidden="true"></i>
    </a>

</div>

