<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_nominations'); ?></h4>

<div class="tw-mt-4">
    <button type="button" class="btn btn-primary tw-mb-4" data-toggle="modal" data-target="#addNominationModal">
        <?php echo _l('membership_add_nomination'); ?>
    </button>

    <div class="panel_s">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo _l('membership_available_elections'); ?></h4>
        </div>
        <div class="panel-body">
            <?php if (count($elections) > 0): ?>
                <div class="row">
                    <?php foreach ($elections as $election): ?>
                        <div class="col-md-6 tw-mb-4">
                            <div class="panel panel-default" onclick="showElectionDetail(<?php echo $election['id']; ?>)" style="cursor:pointer;">
                                <div class="panel-heading">
                                    <h5><?php echo e($election['title']); ?></h5>
                                </div>
                                <div class="panel-body">
                                    <p><?php echo $election['description'] ? e(substr($election['description'], 0, 100)) . '...' : '-'; ?></p>
                                    <p><strong><?php echo _l('membership_period'); ?>:</strong>
                                        <?php echo date('M d, Y', strtotime($election['start_date'])); ?> -
                                        <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
                                    </p>
                                    <p>
                                        <span class="label label-<?php echo $election['status'] == 'active' ? 'success' : ($election['status'] == 'draft' ? 'warning' : 'default'); ?>">
                                            <?php echo ucfirst($election['status']); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted"><?php echo _l('membership_no_elections'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($my_nominations) && count($my_nominations) > 0): ?>
    <div class="panel_s tw-mt-4">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo _l('membership_my_nominations'); ?></h4>
        </div>
        <div class="panel-body">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th><?php echo _l('membership_election'); ?></th>
                        <th><?php echo _l('membership_position'); ?></th>
                        <th><?php echo _l('membership_status'); ?></th>
                        <th><?php echo _l('membership_date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_nominations as $nom): ?>
                        <tr>
                            <td><?php echo e($nom['election_title'] ?? '-'); ?></td>
                            <td><?php echo e($nom['position']); ?></td>
                            <td>
                                <span class="label label-<?php echo $nom['status'] == 'approved' ? 'success' : ($nom['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($nom['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($nom['nominated_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Add Nomination Modal -->
<div class="modal fade" id="addNominationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_nomination'); ?></h4>
            </div>
            <?php if (!empty($nomination_rules)): ?>
            <div class="modal-body" style="padding-bottom:0;">
                <div class="alert alert-info">
                    <strong><?php echo _l('membership_nomination_rules_title'); ?></strong><br>
                    <?php echo $nomination_rules; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php echo form_open_multipart(site_url('membership/client/apply_nomination')); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php echo _l('membership_select_member'); ?> *</label>
                    <select name="nominated_member_id" class="form-control selectpicker" data-live-search="true" required>
                        <option value=""><?php echo _l('membership_select_member'); ?></option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?php echo $m['id']; ?>" data-subtext="<?php echo e($m['email'] ?? ''); ?>">
                                <?php echo e($m['firstname'] . ' ' . $m['lastname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_election'); ?> *</label>
                    <select name="election_id" class="form-control" required onchange="updateNominationFeeModal(this.value)">
                        <option value=""><?php echo _l('membership_select_election'); ?></option>
                        <?php foreach ($elections as $election): ?>
                            <option value="<?php echo $election['id']; ?>" data-fee="<?php echo isset($election['nomination_fee']) ? $election['nomination_fee'] : '0.00'; ?>">
                                <?php echo e($election['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_manifesto'); ?> *</label>
                    <textarea name="manifesto" class="form-control" rows="5" required placeholder="<?php echo _l('membership_manifesto_placeholder'); ?>"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_nomination_fee'); ?></label>
                            <input type="text" name="total_fees" class="form-control" id="modal-total-fees" value="<?php echo $nomination_fee; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_currency'); ?></label>
                            <input type="text" class="form-control" value="<?php echo $nomination_currency; ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit_nomination'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Election Detail Modal -->
<div class="modal fade" id="electionDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="electionDetailTitle"></h4>
            </div>
            <div class="modal-body" id="electionDetailBody">
                <p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
// Pre-load elections data to avoid AJAX issues
var electionsData = <?php echo json_encode(array_map(function($e) {
    return [
        'id'               => $e['id'],
        'title'            => $e['title'],
        'description'      => $e['description'],
        'start_date'       => date('M d, Y', strtotime($e['start_date'])),
        'end_date'         => date('M d, Y', strtotime($e['end_date'])),
        'status'           => $e['status'],
        'nomination_fee'   => isset($e['nomination_fee']) ? $e['nomination_fee'] : '',
        'nomination_currency' => isset($e['nomination_currency']) ? $e['nomination_currency'] : '',
    ];
}, $elections)); ?>;

function showElectionDetail(electionId) {
    var election = electionsData.find(function(e) { return e.id == electionId; });
    if (!election) { return; }

    $('#electionDetailTitle').text(election.title);
    var body = '<p><strong><?php echo _l('membership_description'); ?>:</strong> ' + (election.description || '-') + '</p>' +
               '<p><strong><?php echo _l('membership_period'); ?>:</strong> ' + election.start_date + ' &mdash; ' + election.end_date + '</p>' +
               '<p><strong><?php echo _l('membership_status'); ?>:</strong> ' + ucfirst(election.status) + '</p>';
    if (election.nomination_fee) {
        body += '<p><strong><?php echo _l('membership_nomination_fee'); ?>:</strong> ' + election.nomination_fee + ' ' + election.nomination_currency + '</p>';
    }
    $('#electionDetailBody').html(body);
    $('#electionDetailModal').modal('show');
}

function updateNominationFeeModal(election_id) {
    var selectedOption = document.querySelector('#addNominationModal select[name="election_id"] option[value="' + election_id + '"]');
    var fee = selectedOption ? selectedOption.getAttribute('data-fee') : '<?php echo $nomination_fee; ?>';
    document.getElementById('modal-total-fees').value = fee || '0.00';
}

function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

$(document).ready(function() {
    $('#addNominationModal').on('shown.bs.modal', function() {
        if (typeof $.fn.selectpicker !== 'undefined') {
            $(this).find('.selectpicker').selectpicker('refresh');
        }
    });
});
</script>
