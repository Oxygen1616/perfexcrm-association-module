<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_directory'); ?></h4>

<div class="row tw-mt-4">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="_buttons mbottom20">
                    <input type="text" class="form-control input-sm" id="member-search"
                           placeholder="<?= _l('membership_search_member'); ?>">
                </div>

                <div id="member-directory-container">
                    <?php if (!empty($members)): ?>
                        <div class="row mtop15" id="member-cards-row">
                            <?php foreach ($members as $member): ?>
                                <?php if ($member['show_in_directory']): ?>
                                    <div class="col-md-3 col-sm-6 member-card-col">
                                        <div class="panel_s text-center">
                                            <div class="panel-body">
                                                <div class="tw-mb-2">
                                                    <i class="fa fa-user-circle fa-3x text-muted"></i>
                                                </div>
                                                <h5 class="tw-font-semibold tw-mb-1">
                                                    <?= e($member['firstname'] . ' ' . $member['lastname']); ?>
                                                </h5>
                                                <div class="text-muted tw-text-sm"><?= e($member['email']); ?></div>
                                                <?php if (!empty($member['company'])): ?>
                                                    <div class="tw-text-sm tw-mt-1"><?= e($member['company']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($member['profession'])): ?>
                                                    <div class="tw-text-sm text-muted tw-mt-1">
                                                        <?= _l('membership_profession'); ?>: <?= e($member['profession']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($member['graduation_year'])): ?>
                                                    <div class="tw-text-sm text-muted">
                                                        <?= _l('membership_graduation_year'); ?>: <?= (int)$member['graduation_year']; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center mtop20">
                            <p class="text-muted"><?= _l('membership_no_members_found'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    $('#member-search').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('#member-cards-row .member-card-col').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>
