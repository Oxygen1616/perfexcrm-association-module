<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_vote_list'); ?></h4>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label><?php echo _l('membership_select_election'); ?></label>
                                <select class="form-control" id="election_filter">
                                    <option value=""><?php echo _l('membership_select_election'); ?></option>
                                    <?php foreach ($elections as $e): ?>
                                        <option value="<?php echo $e['id']; ?>" <?php echo isset($selected_election) && $selected_election == $e['id'] ? 'selected' : ''; ?>>
                                            <?php echo e($e['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div id="vote_list_container">
                            <?php $this->load->view('admin/partials/_vote_list_table', ['votes' => $votes ?? []]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load initial vote list if an election is selected
    if ($('#election_filter').val()) {
        loadVoteList($('#election_filter').val());
    }

    // Handle election dropdown changes
    $('#election_filter').on('change', function() {
        var electionId = $(this).val();
        if (electionId) {
            loadVoteList(electionId);
        } else {
            $('#vote_list_container').html('<p class="text-muted"><?= _l('membership_select_election') ?></p>');
        }
    });
}

// Function to load vote list via AJAX
function loadVoteList(electionId) {
    $.get("<?= admin_url('membership/ajax_get_votes') ?>/" + electionId, function(response) {
        if (response.success) {
            $('#vote_list_container').html(response.votes_html);
        } else {
            $('#vote_list_container').html('<p class="text-danger">' + response.message + '</p>');
        }
    }).fail(function() {
        $('#vote_list_container').html('<p class="text-danger"><?= _l('membership_error_loading_votes') ?></p>');
    });
}
</script>
<?php init_tail(); ?>