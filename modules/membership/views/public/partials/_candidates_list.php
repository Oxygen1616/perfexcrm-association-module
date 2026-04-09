<?php if (count($candidates) > 0): ?>
<div class="panel_s tw-mt-4">
    <div class="panel-heading">
        <h4 class="panel-title"><?= _l('membership_select_candidate'); ?></h4>
    </div>
    <div class="panel-body">
        <?= form_open(site_url('membership/client/cast_vote')); ?>
            <input type="hidden" name="election_id" value="<?= $election_id; ?>">

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
<?php else: ?>
<div class="alert alert-info tw-mt-4">
    <?= _l('membership_no_candidates'); ?>
</div>
<?php endif; ?>