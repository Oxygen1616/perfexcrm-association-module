<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_elections'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if ($active_election): ?>
                            <div class="mtop15">
                                <h5><?php echo _l('membership_active_election'); ?></h5>
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <!-- Make election title clickable to show popup -->
                                        <h5 class="cursor-pointer" onclick="showElectionPopup(<?php echo $active_election['id']; ?>)">
                                            <?php echo $active_election['title']; ?>
                                        </h5>
                                        <p class="text-muted">
                                            <?php echo _l('membership_election_period'); ?>:
                                            <?php echo _dt($active_election['start_date']); ?> - <?php echo _dt($active_election['end_date']); ?>
                                        </p>
                                        <?php if ($active_election['description']): ?>
                                            <p><?php echo nl2br($active_election['description']); ?></p>
                                        <?php endif; ?>

                                        <?php if (!$has_voted && count($candidates) > 0): ?>
                                            <form action="<?php echo site_url('membership/vote/' . $active_election['id']); ?>" method="post">
                                                <div class="form-group">
                                                    <label for="candidate_id"><?php echo _l('membership_select_candidate'); ?></label>
                                                    <select name="candidate_id" id="candidate_id" class="form-control" required>
                                                        <option value=""><?php echo _l('membership_select_candidate'); ?></option>
                                                        <?php foreach ($candidates as $candidate): ?>
                                                            <option value="<?php echo $candidate['id']; ?>">
                                                                <?php
                                                                $this->load->model('clients_model');
                                                                $contact = $this->clients_model->get_contact($candidate['contact_id']);
                                                                echo $contact ? $contact->firstname . ' ' . $contact->lastname : 'Unknown';
                                                                ?>
                                                                <?php if ($candidate['bio']): ?>
                                                                    <br><small><?php echo nl2br($candidate['bio']); ?></small>
                                                                <?php endif; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary"><?php echo _l('membership_cast_vote'); ?></button>
                                            </form>
                                        <?php elseif ($has_voted): ?>
                                            <div class="alert alert-info">
                                                <i class="fa fa-check-circle"></i> <?php echo _l('membership_already_voted'); ?>
                                            </div>
                                        <?php elseif (count($candidates) == 0): ?>
                                            <p class="text-muted"><?php echo _l('membership_no_candidates'); ?></p>
                                        <?php endif; ?>

                                        <?php if (count($results) > 0): ?>
                                            <div class="mtop15">
                                                <h5><?php echo _l('membership_election_results'); ?></h5>
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo _l('membership_candidate'); ?></th>
                                                            <th><?php echo _l('membership_votes'); ?></th>
                                                            <th><?php echo _l('membership_percentage'); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($results as $result): ?>
                                                            <?php
                                                            $this->load->model('clients_model');
                                                            $contact = $this->clients_model->get_contact($result['contact_id']);
                                                            $percentage = $total_votes > 0 ? round(($result['vote_count'] / $total_votes) * 100) : 0;
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $contact ? $contact->firstname . ' ' . $contact->lastname : 'Unknown'; ?>
                                                                </td>
                                                                <td><?php echo $result['vote_count']; ?></td>
                                                                <td><?php echo $percentage . '%'; ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-right"><small><?php echo _l('membership_total_votes'); ?>: <?php echo $total_votes; ?></small></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_active_election'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Election Popup Modal -->
        <div id="election-popup" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="election-popup-title"></h4>
                    </div>
                    <div class="modal-body" id="election-popup-body">
                        <!-- Election details will be loaded here via AJAX -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showElectionPopup(electionId) {
                // Show loading state
                $('#election-popup-title').text('<?= _l('membership_loading') ?>...');
                $('#election-popup-body').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
                $('#election-popup').modal('show');

                // Fetch election details via AJAX
                $.get("<?= site_url('membership/get_election_details') ?>/" + electionId, function(response) {
                    if (response.success) {
                        $('#election-popup-title').text(response.election.title);
                        $('#election-popup-body').html(`
                            <p><strong><?= _l('membership_description') ?>:</strong> ${response.election.description || '-'}</p>
                            <p><strong><?= _l('membership_election_period') ?>:</strong> ${response.election.start_date_formatted} - ${response.election.end_date_formatted}</p>
                            <p><strong><?= _l('membership_status') ?>:</strong> ${response.election.status}</p>
                            ${response.election.nomination_fee > 0 ? `<p><strong><?= _l('membership_nomination_fee') ?>:</strong> ${response.election.currency} ${response.election.nomination_fee}</p>` : ''}
                        `);
                    } else {
                        $('#election-popup-body').html('<p class="text-danger">' + response.message + '</p>');
                    }
                });
            }
        </script>
    </div>
</div>
<?php init_tail(); ?>