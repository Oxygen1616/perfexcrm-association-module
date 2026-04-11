<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_cast_vote'); ?></h4>

<div class="panel_s tw-mt-4">
    <div class="panel-body">
        <div class="form-group">
            <label><?= _l('membership_select_election'); ?> *</label>
            <select class="form-control" id="election_select">
                <option value=""><?= _l('membership_select_election'); ?></option>
                <?php foreach ($elections as $election): ?>
                    <option value="<?= $election['id']; ?>" <?= $selected_election == $election['id'] ? 'selected' : ''; ?>>
                        <?= isset($election['title']) && $election['title'] !== '' ? e($election['title']) : _l('membership_election_untitled'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div id="candidates-container"></div>

<script>
$(document).ready(function() {
    <?php if ($selected_election): ?>
    loadCandidates(<?= (int)$selected_election; ?>);
    <?php endif; ?>

    $('#election_select').on('change', function() {
        var electionId = $(this).val();
        if (electionId) {
            loadCandidates(electionId);
        } else {
            $('#candidates-container').html('');
        }
    });
});

function loadCandidates(electionId) {
    $('#candidates-container').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l('membership_loading'); ?>...</p>');
    $.get('<?= site_url('membership/client/get_candidates'); ?>', { election_id: electionId })
        .done(function(html) {
            $('#candidates-container').html(html);
        })
        .fail(function() {
            $('#candidates-container').html('<div class="alert alert-danger"><?= _l('membership_error_loading_candidates'); ?></div>');
        });
}
</script>
