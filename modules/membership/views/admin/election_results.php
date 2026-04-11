<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_election_results'); ?></h4>
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
                        <div id="election_results_container">
                            <?php $this->load->view('admin/partials/_election_results_table', ['results' => $results ?? null, 'total_votes' => $total_votes ?? 0, 'selected_election' => $selected_election ?? null]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load initial results if an election is selected
    if ($('#election_filter').val()) {
        loadElectionResults($('#election_filter').val());
    }

    // Handle election dropdown changes
    $('#election_filter').on('change', function() {
        var electionId = $(this).val();
        if (electionId) {
            loadElectionResults(electionId);
        } else {
            $('#election_results_container').html('<p class="text-muted"><?= _l('membership_select_election') ?></p>');
        }
    });
});

// Function to load election results via AJAX
function loadElectionResults(electionId) {
    $('#election_results_container').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i></div>');
    $.get("<?= admin_url('membership/ajax_get_election_results') ?>/" + electionId, function(response) {
        if (response.success) {
            var html = '';
            if (response.results && response.results.length > 0) {
                html = '<div class="row mtop15"><div class="col-md-12"><h5><?= _l('membership_total_votes') ?>: ' + response.total_votes + '</h5></div></div>' +
                    '<table class="table dt-table mtop15"><thead><tr>' +
                    '<th><?= _l('membership_candidate') ?></th>' +
                    '<th><?= _l('membership_votes') ?></th>' +
                    '<th><?= _l('membership_percentage') ?></th>' +
                    '</tr></thead><tbody>';
                for (var i = 0; i < response.results.length; i++) {
                    var result = response.results[i];
                    var percentage = response.total_votes > 0 ? Math.round((result.vote_count / response.total_votes) * 100) : 0;
                    html += '<tr>' +
                        '<td>' + result.firstname + ' ' + result.lastname + '</td>' +
                        '<td>' + result.vote_count + '</td>' +
                        '<td><div class="progress"><div class="progress-bar" role="progressbar" style="width: ' + percentage + '%">' + percentage + '%</div></div></td>' +
                        '</tr>';
                }
                html += '</tbody></table>';
            } else {
                html = '<div class="text-center mtop15"><p><?= _l('membership_no_results') ?></p></div>';
            }
            $('#election_results_container').html(html);
        } else {
            $('#election_results_container').html('<p class="text-danger">' + response.message + '</p>');
        }
    }).fail(function() {
        $('#election_results_container').html('<p class="text-danger"><?= _l('membership_error_loading_results') ?></p>');
    });
}
</script>
<?php init_tail(); ?>