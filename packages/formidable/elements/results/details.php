<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

$form = app('helper/form');
$date = app('helper/date');
$token = app('helper/validation/token')->output('results_details');

$logs = $result->getLogs();

?>

<div class="row">
    <div class="col-12 col-lg-2">
        <a href="<?= URL::to('/dashboard/formidable/results', $ff->getItemID()); ?>" class="btn btn-sm btn-back mb-3">
            <svg><use xlink:href="#icon-arrow-left"></use></svg> <?=t('Back to results');?>
        </a>

        <ul class="nav nav-pills flex-row flex-lg-column bg-light mb-3" id="elementTab">
            <li class="nav-item flex-fill">
                <a class="nav-link active" href="#basic" data-bs-toggle="tab">
                    <?=t('Basic');?>
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" href="#logs" data-bs-toggle="tab">
                    <?=t('Logs');?>
                </a>
            </li>
        </ul>

    </div>
    <div class="col-12 col-lg-10">

        <?php /* TAB CONTENT */ ?>

        <div class="tab-content">

            <div class="tab-pane active" id="basic">

                <div class="row">
                    <div class="col-12 col-lg-8">
                        <?php
                            $data = $result->getElementData();
                            if (count($data)) { ?>
                            <h4 class="ms-2"><?=t('Data');?></h4>
                            <div class="table-responsive">
                                <table class="table table-hover ccm-search-results-table">
                                    <?php foreach ($data as $d) { ?>
                                        <tr>
                                            <td class="col-sm-4"><?=$d->getElement()->getName();?>:</td>
                                            <td class="col-sm-8"><?=$d->getElement()->getDisplayData($d->getPostValue(), 'html');?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="help-block mt-0">
                                <?=t('There is no data available for this submission');?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="alert bg-light small">
                            <h4><?=t('Details');?></h4>

                            <dl class="row mb-1">
                                <dt class="col-sm-5"><?=t('ID');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getItemID();?></dd>
                            </dl>

                            <hr class="mt-0" />

                            <dl class="row mb-1">
                                <dt class="col-sm-5"><?=t('Send on');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getDateAdded(true); //$date->formatDateTime($result->getDateAdded(false), true);?></dd>

                                <dt class="col-sm-5"><?=t('Page');?>:</dt>
                                <dd class="col-sm-7">
                                    <?php
                                        $page = $result->getPageObject();
                                        if (is_object($page)) { ?>
                                            <a href="<?=Url::to($page);?>" target="_blank"><?=$page->getCollectionName()?></a>
                                    <?php } else { ?>
                                            <span class="text-stroke"><?=t('Unknown page'); ?></span>
                                    <?php } ?>
                                    <?=t('(PageID: %s)', $result->getPage()); ?>
                                </dd>

                                <?php /*
                                <dt class="col-sm-5"><?=t('Block');?>:</dt>
                                <dd class="col-sm-7">
                                    <?php
                                        $block = $result->getBlockObject();
                                        if (is_object($block)) { ?>
                                            <?=$block->getBlockTypeName()?>
                                    <?php } else { ?>
                                            <span class="text-stroke"><?=t('Unknown block'); ?></span>
                                    <?php } ?>
                                    <?=t('(BlockID: %s)', $result->getBlock()); ?>
                                </dd>
                                */ ?>

                                <dt class="col-sm-5"><?=t('User');?>:</dt>
                                <dd class="col-sm-7">
                                    <?php
                                        $user = $result->getUserObject();
                                        if (is_object($user)) { ?>
                                            <a href="<?=Url::to('dashboard/users/search/edit/', $user->getUserID());?>" target="_blank"><?=$user->getUserName()?></a>
                                    <?php } else { ?>
                                        <span class="text-stroke">
                                            <?php
                                                if ($result->getUser() == 0) {
                                                    echo t('Guest');
                                                }
                                                else {
                                                    echo t('Unknown user');
                                                }
                                            ?>
                                        </span>
                                    <?php } ?>
                                    <?=t('(UserID: %s)', $result->getUser()); ?>
                                </dd>
                            </dl>

                            <hr class="mt-0" />

                            <dl class="row mb-1">
                                <dt class="col-sm-5"><?=t('IP');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getIP();?></dd>
                            </dl>

                            <hr class="mt-0" />

                            <dl class="row mb-1">
                                <dt class="col-sm-5"><?=t('Device');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getDevice();?></dd>
                                <dt class="col-sm-5"><?=t('Operating System');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getOperatingSystem();?></dd>
                                <dt class="col-sm-5"><?=t('Browser');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getBrowser();?></dd>
                                <dt class="col-sm-5"><?=t('Resolution');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getResolution();?></dd>
                            </dl>

                            <hr class="mt-0" />

                            <dl class="row mb-1">
                                <dt class="col-sm-5"><?=t('Localization');?>:</dt>
                                <dd class="col-sm-7"><?=$result->getLocale(true);?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane hide" id="logs">
                <?php if (count($logs) > 0) {  ?>
                    <div class="table-responsive">
                        <table class="table table-striped ccm-search-results-table">
                            <thead>
                                <tr>
                                    <th><?= t('Date'); ?></th>
                                    <th><?= t('Action'); ?></th>
                                    <th><?= t('By'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log) { ?>
                                    <tr>
                                        <td class="log-date">
                                            <?=$log->getDateAdded(true); ?>
                                        </td>
                                        <td class="log-action">
                                            <?=$log->getAction(); ?>
                                        </td>
                                        <td class="log-user">
                                            <?php
                                                $user = $log->getUserObject();
                                                if (is_object($user)) { ?>
                                                    <a href="<?=Url::to('dashboard/users/search/edit/', $user->getUserID());?>" target="_blank"><?=$user->getUserName()?></a>
                                            <?php } else { ?>
                                                <span class="text-stroke">
                                                    <?php
                                                        if ($log->getUser() == -1) {
                                                            echo t('System');
                                                        }
                                                        elseif ($log->getUser() == 0) {
                                                            echo t('Guest');
                                                        }
                                                        else {
                                                            echo t('Unknown user');
                                                        }
                                                    ?>
                                                </span>
                                            <?php } ?>
                                            <?=t('(UserID: %s)', $log->getUser()); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="help-block mt-0">
                        <?= t('No logs found.'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
