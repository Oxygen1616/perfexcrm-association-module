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

        <div id="candidates-container" class="tw-mt-4">
            <?php if (isset($candidates) && !empty($candidates)): ?>
                <?php $this->load->view('public/partials/_candidates_list', ['candidates' => $candidates, 'election_id' => $selected_election]); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#election_select').change(function() {
        var electionId = $(this).val();
        var $container = $('#candidates-container');

        if (!electionId) {
            $container.html('');
            return;
        }

        // Show loading
        $container.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading candidates...</div>');

        // Load candidates via AJAX
        $.get('<?= site_url('membership/client/get_candidates'); ?>?election_id=' + electionId, function(response) {
            $container.html(response);
        }).fail(function() {
            $container.html('<div class="alert alert-danger">Failed to load candidates. Please try again.</div>');
        });
    });

    // Trigger change if election is already selected on page load
    <?php if (!empty($selected_election)): ?>
        $('#election_select').trigger('change');
    <?php endif; ?>
});
</script>