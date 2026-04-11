<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_board_members'); ?></h4>

<div class="tw-mt-4">
    <?php if (count($board_members) > 0): ?>
        <div class="row">
            <?php foreach ($board_members as $member): ?>
                <div class="col-md-4 tw-mb-4">
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <div class="tw-text-neutral-300 tw-mb-3">
                                <i class="fa fa-user fa-4x"></i>
                            </div>
                            <h5 class="tw-font-semibold"><?= e($member['firstname'] . ' ' . $member['lastname']); ?></h5>
                            <p class="tw-font-medium"><?= e($member['position']); ?></p>
                            <p class="tw-text-sm tw-text-neutral-500"><?= e($member['email']); ?></p>
                            <span class="label label-<?= $member['status'] == 'active' ? 'success' : 'default'; ?>">
                                <?= ucfirst($member['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="panel_s">
            <div class="panel-body">
                <p class="text-center text-muted"><?= _l('membership_no_board_members'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
