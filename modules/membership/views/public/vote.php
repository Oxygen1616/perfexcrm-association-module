<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_cast_vote'); ?></h4>

<div class="panel_s tw-mt-4">
    <div class="panel-body">
        <?= form_open(site_url('membership/client/cast_vote')); ?>
            <div class="form-group">
                <label><?= _l('membership_select_election'); ?> *</label>
                <select name="election_id" class="form-control" required onchange="window.location.href='<?= site_url('membership/client/cast_vote'); ?>?election='+this.value">
                    <option value=""><?= _l('membership_select_election'); ?></option>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?= $election['id']; ?>" <?= $selected_election == $election['id'] ? 'selected' : ''; ?>><?= e($election['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?= form_close(); ?>
    </div>
</div>

<?php if ($selected_election && isset($candidates) && count($candidates) > 0): ?>
<div class="panel_s tw-mt-4">
    <div class="panel-heading">
        <h4 class="panel-title"><?= _l('membership_select_candidate'); ?></h4>
    </div>
    <div class="panel-body">
        <?= form_open(site_url('membership/client/cast_vote')); ?>
            <input type="hidden" name="election_id" value="<?= $selected_election; ?>">

            <div class="row">
                <?php foreach ($candidates as $candidate): ?>
                    <div class="col-md-4 tw-mb-4">
                        <div class="panel_s">
                            <div class="panel-body text-center">
                                <input type="radio" name="candidate_id" value="<?= $candidate['id']; ?>" id="candidate_<?= $candidate['id']; ?>" required>
                                <label for="candidate_<?= $candidate['id']; ?>" class="tw-cursor-pointer tw-block tw-p-3 tw-rounded hover:tw-bg-neutral-100">
                                    <h5 class="tw-font-semibold"><?= e($candidate['firstname'] . ' ' . $candidate['lastname']); ?></h5>
                                    <?php if (!empty($candidate['bio'])): ?>
                                        <p class="tw-text-sm tw-text-neutral-500"><?= e($candidate['bio']); ?></p>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center tw-mt-3">
                <button type="submit" class="btn btn-primary"><?= _l('membership_submit_vote'); ?></button>
            </div>
        <?= form_close(); ?>
    </div>
</div>
<?php elseif ($selected_election): ?>
<div class="alert alert-info tw-mt-4">
    <?= _l('membership_no_candidates'); ?>
</div>
<?php endif; ?>
