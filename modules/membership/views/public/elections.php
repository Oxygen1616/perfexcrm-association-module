<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_elections'); ?></h4>

<div class="row tw-mt-4">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <?php if ($active_election): ?>
                    <h5 class="tw-font-semibold tw-text-neutral-600 tw-mb-3">
                        <i class="fa fa-bullhorn tw-mr-1 tw-text-primary-500"></i>
                        <?= _l('membership_active_election'); ?>
                    </h5>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h5 class="cursor-pointer tw-text-primary-600"
                                onclick="showElectionPopup(<?= (int)$active_election['id']; ?>)">
                                <?= e($active_election['title']); ?>
                                <small class="text-muted tw-ml-2"><i class="fa fa-info-circle"></i></small>
                            </h5>
                            <p class="text-muted">
                                <?= _l('membership_election_period'); ?>:
                                <?= _dt($active_election['start_date']); ?> &ndash; <?= _dt($active_election['end_date']); ?>
                            </p>
                            <?php if ($active_election['description']): ?>
                                <p><?= nl2br(e($active_election['description'])); ?></p>
                            <?php endif; ?>

                            <?php if (!$has_voted && count($candidates) > 0): ?>
                                <?= form_open(site_url('membership/client/cast_vote')); ?>
                                    <input type="hidden" name="election_id" value="<?= (int)$active_election['id']; ?>">
                                    <div class="form-group">
                                        <label for="candidate_id"><?= _l('membership_select_candidate'); ?></label>
                                        <select name="candidate_id" id="candidate_id" class="form-control" required>
                                            <option value=""><?= _l('membership_select_candidate'); ?></option>
                                            <?php foreach ($candidates as $candidate): ?>
                                                <option value="<?= (int)$candidate['id']; ?>">
                                                    <?php
                                                    $this->load->model('clients_model');
                                                    $contact = $this->clients_model->get_contact($candidate['contact_id']);
                                                    echo $contact ? e($contact->firstname . ' ' . $contact->lastname) : 'Unknown';
                                                    ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-square tw-mr-1"></i><?= _l('membership_cast_vote'); ?>
                                    </button>
                                <?= form_close(); ?>
                            <?php elseif ($has_voted): ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-check-circle"></i> <?= _l('membership_already_voted'); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted"><?= _l('membership_no_candidates'); ?></p>
                            <?php endif; ?>

                            <?php if (count($results) > 0): ?>
                                <div class="mtop15">
                                    <h5><?= _l('membership_election_results'); ?></h5>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><?= _l('membership_candidate'); ?></th>
                                                <th><?= _l('membership_votes'); ?></th>
                                                <th><?= _l('membership_percentage'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($results as $result): ?>
                                                <?php
                                                $contact    = $this->clients_model->get_contact($result['contact_id']);
                                                $percentage = $total_votes > 0 ? round(($result['vote_count'] / $total_votes) * 100) : 0;
                                                ?>
                                                <tr>
                                                    <td><?= $contact ? e($contact->firstname . ' ' . $contact->lastname) : 'Unknown'; ?></td>
                                                    <td><?= (int)$result['vote_count']; ?></td>
                                                    <td><?= (int)$percentage; ?>%</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <p class="text-right"><small><?= _l('membership_total_votes'); ?>: <?= $total_votes; ?></small></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <p class="text-muted"><?= _l('membership_no_active_election'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Election details modal -->
<div id="election-popup" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="election-popup-title"></h4>
            </div>
            <div class="modal-body" id="election-popup-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
function showElectionPopup(electionId) {
    $('#election-popup-title').text('<?= _l('membership_loading'); ?>...');
    $('#election-popup-body').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></p>');
    $('#election-popup').modal('show');

    $.get('<?= site_url('membership/client/get_election_details'); ?>/' + electionId, function(response) {
        if (response && response.success) {
            $('#election-popup-title').text(response.election.title);
            var body = '<p><strong><?= _l('membership_description'); ?>:</strong> ' + (response.election.description || '-') + '</p>'
                     + '<p><strong><?= _l('membership_election_period'); ?>:</strong> ' + response.election.start_date_formatted + ' &ndash; ' + response.election.end_date_formatted + '</p>'
                     + '<p><strong><?= _l('membership_status'); ?>:</strong> ' + response.election.status + '</p>';
            if (response.election.nomination_fee > 0) {
                body += '<p><strong><?= _l('membership_nomination_fee'); ?>:</strong> ' + response.election.currency + ' ' + response.election.nomination_fee + '</p>';
            }
            $('#election-popup-body').html(body);
        } else {
            $('#election-popup-body').html('<p class="text-danger">' + (response ? response.message : '<?= _l('membership_error'); ?>') + '</p>');
        }
    }).fail(function() {
        $('#election-popup-body').html('<p class="text-danger"><?= _l('membership_error'); ?></p>');
    });
}
</script>
