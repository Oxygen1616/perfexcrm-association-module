<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_committees'); ?></h4>

<div class="tw-mt-4">
    <?php if (count($committees) > 0): ?>
        <div class="row">
            <?php foreach ($committees as $committee): ?>
                <div class="col-md-6 tw-mb-4">
                    <div class="panel_s cursor-pointer" onclick="showCommitteePopup(<?= $committee['id']; ?>)">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold"><?= e($committee['name']); ?></h5>
                            <?php if (!empty($committee['category_name'])): ?>
                                <span class="label label-default"><?= e($committee['category_name']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($committee['description'])): ?>
                                <p class="tw-text-sm tw-text-neutral-500 tw-mt-2"><?= e($committee['description']); ?></p>
                            <?php endif; ?>
                            <span class="label label-<?= $committee['status'] == 'active' ? 'success' : 'default'; ?>">
                                <?= ucfirst($committee['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="panel_s">
            <div class="panel-body">
                <p class="text-center text-muted"><?= _l('membership_no_committees'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
