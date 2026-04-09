<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_cast_vote'); ?></h4>

<div class="panel_s tw-mt-4">
    <div class="panel-body">
        <?= form_open(site_url('membership/client/cast_vote')); ?>
            <div class="form-group">
                <label><?= _l('membership_select_election'); ?> *</label>
                <select name="election_id" class="form-control" required id="election_select">
                    <option value=""><?= _l('membership_select_election'); ?></option>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?= $election['id']; ?>" <?= $selected_election == $election['id'] ? 'selected' : ''; ?>>
                            <?= isset($election['title']) && $election['title'] !== '' ? e($election['title']) : _l('membership_election_untitled'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?= form_close(); ?>
    </div>
</div>