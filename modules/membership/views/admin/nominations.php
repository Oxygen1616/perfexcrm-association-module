<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="<?php echo $status_filter == '' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations'); ?>"><?php echo _l('membership_all'); ?></a>
                            </li>
                            <li role="presentation" class="<?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations/pending'); ?>"><?php echo _l('membership_pending'); ?></a>
                            </li>
                            <li role="presentation" class="<?php echo $status_filter == 'approved' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations/approved'); ?>"><?php echo _l('membership_approved'); ?></a>
                            </li>
                            <li role="presentation" class="<?php echo $status_filter == 'rejected' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations/rejected'); ?>"><?php echo _l('membership_rejected'); ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nominationModal">
                            <?php echo _l('membership_add_nomination'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4><?php echo _l('membership_nominations'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php if (count($nominations) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_member_name'); ?></th>
                                        <th><?php echo _l('membership_election'); ?></th>
                                        <th><?php echo _l('membership_position'); ?></th>
                                        <th><?php echo _l('membership_symbol'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                        <th><?php echo _l('membership_nominated_at'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($nominations as $nom): ?>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="viewNomination(<?php echo $nom['id']; ?>); return false;">
                                                    <?php echo e($nom['firstname'] . ' ' . $nom['lastname']); ?>
                                                </a>
                                                <div class="row-options">
                                                    <?php if ($nom['status'] == 'pending'): ?>
                                                        <a href="<?php echo admin_url('membership/approve_nomination/' . $nom['id']); ?>">
                                                            <?php echo _l('membership_approve'); ?>
                                                        </a>
                                                        |
                                                        <a href="<?php echo admin_url('membership/reject_nomination/' . $nom['id']); ?>">
                                                            <?php echo _l('membership_reject'); ?>
                                                        </a>
                                                        |
                                                    <?php endif; ?>
                                                    <a href="#" onclick="viewNomination(<?php echo $nom['id']; ?>); return false;">
                                                        <?php echo _l('membership_view'); ?>
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('membership/delete_nomination/' . $nom['id']); ?>"
                                                       onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                        <?php echo _l('membership_delete'); ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td><?php echo e($nom['election_title'] ?: '-'); ?></td>
                                            <td><?php echo e($nom['position']); ?></td>
                                            <td><?php echo e($nom['symbol_name'] ?: '-'); ?></td>
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
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_nominations'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Nomination Modal -->
<div class="modal fade" id="nominationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_nomination'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/nominations')); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php echo _l('membership_select_member'); ?> *</label>
                    <select name="member_id" id="nomination_member_id" class="form-control selectpicker" data-live-search="true" required>
                        <option value=""><?php echo _l('membership_select_member'); ?></option>
                        <?php
                        $this->load->model('clients_model');
                        $all_contacts = $this->clients_model->get_contacts();
                        foreach($all_contacts as $contact):
                        ?>
                            <option value="<?php echo $contact['id']; ?>"><?php echo $contact['firstname'] . ' ' . $contact['lastname']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_election'); ?> *</label>
                    <select name="election_id" id="nomination_election_id" class="form-control" required>
                        <option value=""><?php echo _l('membership_select_election'); ?></option>
                        <?php foreach ($elections as $election): ?>
                            <option value="<?php echo $election['id']; ?>"><?php echo e($election['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_position'); ?> *</label>
                    <input type="text" name="position" id="nomination_position" class="form-control" required value="">
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_manifesto'); ?> *</label>
                    <textarea name="manifesto" id="nomination_manifesto" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_status'); ?></label>
                    <select name="status" id="nomination_status" class="form-control">
                        <option value="pending"><?php echo _l('membership_pending'); ?></option>
                        <option value="approved"><?php echo _l('membership_approved'); ?></option>
                        <option value="rejected"><?php echo _l('membership_rejected'); ?></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- View Nomination Modal -->
<div class="modal fade" id="viewNominationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_nomination_details'); ?></h4>
            </div>
            <div class="modal-body" id="viewNominationBody">
                <p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
// Inline nomination data for modal view
var nominationsData = <?php echo json_encode(array_map(function($n) {
    return [
        'id'            => $n['id'],
        'member_name'   => $n['firstname'] . ' ' . $n['lastname'],
        'election'      => $n['election_title'] ?: '-',
        'position'      => $n['position'],
        'status'        => $n['status'],
        'manifesto'     => $n['manifesto'],
        'nominated_at'  => date('M d, Y', strtotime($n['nominated_at'])),
        'symbol'        => $n['symbol_name'] ?: '-',
    ];
}, $nominations)); ?>;

function viewNomination(id) {
    var nom = nominationsData.find(function(n) { return n.id == id; });
    if (!nom) { return; }
    var statusClass = nom.status === 'approved' ? 'success' : (nom.status === 'pending' ? 'warning' : 'danger');
    $('#viewNominationBody').html(
        '<p><strong><?php echo _l('membership_member_name'); ?>:</strong> ' + nom.member_name + '</p>' +
        '<p><strong><?php echo _l('membership_election'); ?>:</strong> ' + nom.election + '</p>' +
        '<p><strong><?php echo _l('membership_position'); ?>:</strong> ' + nom.position + '</p>' +
        '<p><strong><?php echo _l('membership_symbol'); ?>:</strong> ' + nom.symbol + '</p>' +
        '<p><strong><?php echo _l('membership_status'); ?>:</strong> <span class="label label-' + statusClass + '">' + nom.status.charAt(0).toUpperCase() + nom.status.slice(1) + '</span></p>' +
        '<p><strong><?php echo _l('membership_nominated_at'); ?>:</strong> ' + nom.nominated_at + '</p>' +
        '<p><strong><?php echo _l('membership_manifesto'); ?>:</strong></p>' +
        '<div class="well">' + (nom.manifesto || '<?php echo _l('membership_no_manifesto'); ?>') + '</div>'
    );
    $('#viewNominationModal').modal('show');
}

$(document).ready(function() {
    // Sort table by Election column (index 1) by default
    if ($.fn.DataTable) {
        $('.dt-table').DataTable({
            'order': [[1, 'asc']]
        });
    }
});
</script>

<?php init_tail(); ?>
