<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_members'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" onclick="openAddMemberModal()">
                            <?php echo _l('membership_new_member'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($members) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_member_name'); ?></th>
                                        <th><?php echo _l('membership_member_email'); ?></th>
                                        <th><?php echo _l('membership_member_status'); ?></th>
                                        <th><?php echo _l('membership_member_type'); ?></th>
                                        <th><?php echo _l('membership_member_profession'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member): ?>
                                        <?php
                                        $this->load->model('clients_model');
                                        $contact = $this->clients_model->get_contact($member['contact_id']);
                                        $name = $contact ? $contact->firstname . ' ' . $contact->lastname : 'Unknown';
                                        $email = $contact ? $contact->email : '';
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="showMemberDetails(<?php echo $member['id']; ?>)"
                                                   data-toggle="modal" data-target="#memberModal"
                                                   data-load-url="<?= admin_url('membership/ajax_get_member') ?>/<?php echo $member['id']; ?>">
                                                    <?php echo e($name); ?>
                                                </a>
                                                <div class="row-options">
                                                    <a href="#" onclick="editMember(<?php echo $member['id']; ?>)"
                                                       data-toggle="modal" data-target="#memberModal">
                                                        <?php echo _l('membership_edit'); ?>
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('membership/delete_member/' . $member['id']); ?>"
                                                       onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                        <?php echo _l('membership_delete'); ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($contact): ?>
                                                    <a href="#" onclick="showMemberDetails(<?php echo $member['id']; ?>)"
                                                       data-toggle="modal" data-target="#memberModal">
                                                        <?php echo e($email); ?>
                                                    </a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo ($member['status'] == 'active' ? 'success' : ($member['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                    <?php echo _l('membership_status_' . $member['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $member['membership_type'] ?: '-'; ?></td>
                                            <td><?php echo $member['profession'] ?: '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_members'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Member Modal -->
<div class="modal fade" id="memberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="memberModalLabel"><?php echo _l('membership_member_details'); ?></h4>
            </div>
            <div class="modal-body" id="memberModalBody">
                <!-- Member details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="memberFormTitle"><?php echo _l('membership_add_member'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/members')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="memberId">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_select_contact'); ?> *</label>
                            <select name="contact_id" id="member_contact_id" class="form-control selectpicker" data-live-search="true" required>
                                <option value=""><?php echo _l('membership_select_contact'); ?></option>
                                <?php if (isset($contacts) && count($contacts) > 0): ?>
                                    <?php foreach ($contacts as $contact): ?>
                                        <option value="<?php echo $contact['id']; ?>">
                                            <?php echo $contact['firstname'] . ' ' . $contact['lastname'] . ' (' . $contact['email'] . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_status'); ?></label>
                            <select name="status" class="form-control">
                                <option value="pending"><?php echo _l('membership_status_pending'); ?></option>
                                <option value="active"><?php echo _l('membership_status_active'); ?></option>
                                <option value="suspended"><?php echo _l('membership_status_suspended'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_type'); ?></label>
                            <input type="text" name="membership_type" class="form-control" id="member_membership_type">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_profession'); ?></label>
                            <input type="text" name="profession" class="form-control" id="member_profession">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_graduation_year'); ?></label>
                            <input type="number" name="graduation_year" class="form-control" id="member_graduation_year">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <input type="checkbox" name="show_in_directory" value="1" id="member_show_in_directory" checked>
                            <label><?php echo _l('membership_show_in_directory'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('membership_submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function openAddMemberModal() {
    $('#memberId').val('');
    $('#memberFormTitle').text('<?= _l('membership_add_member') ?>');
    $('#member_contact_id').val('');
    $('#member_membership_type').val('');
    $('#member_profession').val('');
    $('#member_graduation_year').val('');
    $('#member_show_in_directory').prop('checked', true);
    $('#addMemberModal').modal('show');
}

function showMemberDetails(memberId) {
    $('#memberModalLabel').text('<?= _l('membership_loading') ?>...');
    $('#memberModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
    $('#memberModal').modal('show');

    $.get("<?= admin_url('membership/ajax_get_member') ?>/" + memberId, function(response) {
        if (response.success) {
            var member = response.member;
            $('#memberModalLabel').text(member.firstname + ' ' + member.lastname);
            $('#memberModalBody').html(`
                <p><strong><?= _l('membership_member_email') ?>:</strong> ${member.email || '-'}</p>
                <p><strong><?= _l('membership_membership_type') ?>:</strong> ${member.membership_type || '-'}</p>
                <p><strong><?= _l('membership_member_profession') ?>:</strong> ${member.profession || '-'}</p>
                <p><strong><?= _l('membership_graduation_year') ?>:</strong> ${member.graduation_year || '-'}</p>
                <p><strong><?= _l('membership_show_in_directory') ?>:</strong> ${member.show_in_directory ? '<?= _l('membership_yes') ?>' : '<?= _l('membership_no') ?>'}</p>
                <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(member.status || '')}</p>
                ${member.notes ? `<p><strong><?= _l('membership_notes') ?>:</strong> ${member.notes}</p>` : ''}
            `);
        } else {
            $('#memberModalBody').html('<p class="text-danger">' + response.message + '</p>');
        }
    });
}

function editMember(memberId) {
    $('#memberId').val(memberId);
    $('#memberFormTitle').text('<?= _l('membership_edit_member') ?>');

    $.get("<?= admin_url('membership/ajax_get_member') ?>/" + memberId, function(response) {
        if (response.success) {
            var member = response.member;
            $('#member_contact_id').val(member.contact_id || '');
            $('#member_membership_type').val(member.membership_type || '');
            $('#member_profession').val(member.profession || '');
            $('#member_graduation_year').val(member.graduation_year || '');
            $('#member_show_in_directory').prop('checked', member.show_in_directory == 1);
            $('#addMemberModal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}

$('#memberModal').on('hidden.bs.modal', function () {
    $('#memberModalLabel').text('<?= _l('membership_member_details') ?>');
    $('#memberModalBody').empty();
});

$('#addMemberModal').on('hidden.bs.modal', function () {
    $('#memberId').val('');
    $('#memberFormTitle').text('<?= _l('membership_add_member') ?>');
    $('#member_contact_id').val('');
    $('#member_membership_type').val('');
    $('#member_profession').val('');
    $('#member_graduation_year').val('');
    $('#member_show_in_directory').prop('checked', true);
});
</script>
<?php init_tail(); ?>