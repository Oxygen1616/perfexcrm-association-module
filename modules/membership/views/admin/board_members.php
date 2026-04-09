<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_board_members'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" onclick="openAddBoardMemberModal()">
                            <?php echo _l('membership_add_board_member'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($board_members) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_member_name'); ?></th>
                                        <th><?php echo _l('membership_email'); ?></th>
                                        <th><?php echo _l('membership_position'); ?></th>
                                        <th><?php echo _l('membership_term'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($board_members as $member): ?>
                                        <?php
                                        $this->load->model('clients_model');
                                        $contact = $this->clients_model->get_contact($member['member_id']);
                                        $name = $contact ? $contact->firstname . ' ' . $contact->lastname : 'Unknown';
                                        $email = $contact ? $contact->email : '';
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="showBoardMemberDetails(<?php echo $member['id']; ?>)"
                                                   data-toggle="modal" data-target="#boardMemberModal">
                                                    <?php echo e($name); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($contact): ?>
                                                    <a href="#" onclick="showBoardMemberDetails(<?php echo $member['id']; ?>)"
                                                       data-toggle="modal" data-target="#boardMemberModal">
                                                        <?php echo e($email); ?>
                                                    </a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($contact): ?>
                                                    <a href="#" onclick="showBoardMemberDetails(<?php echo $member['id']; ?>)"
                                                       data-toggle="modal" data-target="#boardMemberModal">
                                                        <?php echo e($member['position']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?php echo e($member['position']); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo date('M Y', strtotime($member['term_start'])); ?> -
                                                <?php echo $member['term_end'] ? date('M Y', strtotime($member['term_end'])) : 'Present'; ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo $member['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($member['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_board_members'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Board Member Modal -->
<div class="modal fade" id="boardMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="boardMemberModalLabel"><?php echo _l('membership_board_member_details'); ?></h4>
            </div>
            <div class="modal-body" id="boardMemberModalBody">
                <!-- Board member details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-default" onclick="editBoardMemberModal(<?php echo $member['id']; ?>)">
                            <?php echo _l('membership_edit'); ?>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteBoardMemberModal(<?php echo $member['id']; ?>)">
                            <?php echo _l('membership_delete'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Board Member Modal -->
<div class="modal fade" id="addBoardMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="boardMemberFormTitle"><?php echo _l('membership_add_board_member'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/board_members')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="bmember_id">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_select_member'); ?> *</label>
                            <select name="member_id" id="bmember_member_id" class="form-control selectpicker" data-live-search="true" required>
                                <option value=""><?php echo _l('membership_select_member'); ?></option>
                                <?php foreach ($members as $m): ?>
                                    <?php
                                    $this->load->model('clients_model');
                                    $contact = $this->clients_model->get_contact($m['contact_id']);
                                    $contact_name = $contact ? $contact->firstname . ' ' . $contact->lastname : 'Unknown';
                                    ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo e($contact_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_position'); ?> *</label>
                            <input type="text" name="position" id="bmember_position" class="form-control" required value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_election'); ?></label>
                            <select name="election_id" id="bmember_election_id" class="form-control">
                                <option value=""><?php echo _l('membership_select_election'); ?></option>
                                <?php foreach ($elections as $e): ?>
                                    <option value="<?php echo $e['id']; ?>"><?php echo e($e['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_term_start'); ?></label>
                            <input type="date" name="term_start" id="bmember_term_start" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_term_end'); ?></label>
                            <input type="date" name="term_end" id="bmember_term_end" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_status'); ?></label>
                            <select name="status" id="bmember_status" class="form-control">
                                <option value="active"><?php echo _l('membership_active'); ?></option>
                                <option value="inactive"><?php echo _l('membership_inactive'); ?></option>
                            </select>
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
function openAddBoardMemberModal() {
    $('#bmember_id').val('');
    $('#boardMemberFormTitle').text('<?= _l('membership_add_board_member') ?>');
    $('#bmember_member_id').val('');
    $('#bmember_position').val('');
    $('#bmember_election_id').val('');
    $('#bmember_term_start').val('');
    $('#bmember_term_end').val('');
    $('#bmember_status').val('active');
    $('#addBoardMemberModal').modal('show');
}

function showBoardMemberDetails(memberId) {
    $('#boardMemberModalLabel').text('<?= _l('membership_loading') ?>...');
    $('#boardMemberModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');

    // Set up buttons for edit/delete
    $('#boardMemberModal .modal-footer').html(`
        <div class="row">
            <div class="col-md-6">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close') ?></button>
            </div>
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-default" onclick="editBoardMemberModal(${memberId})">
                    <?= _l('membership_edit') ?>
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteBoardMemberModal(${memberId})">
                    <?= _l('membership_delete') ?>
                </button>
            </div>
        </div>
    ");

    $('#boardMemberModal').modal('show');

    $.get("<?= admin_url('membership/ajax_get_board_member') ?>/" + memberId, function(response) {
        if (response.success) {
            var member = response.member;
            $('#boardMemberModalLabel').text(member.firstname + ' ' + member.lastname);
            $('#boardMemberModalBody').html(`
                <p><strong><?= _l('membership_position') ?>:</strong> ${member.position || '-'}</p>
                <p><strong><?= _l('membership_email') ?>:</strong> ${member.email || '-'}</p>
                <p><strong><?= _l('membership_election') ?>:</strong> ${member.election_title || '-'}</p>
                <p><strong><?= _l('membership_term') ?>:</strong>
                    ${member.term_start ? <?= _dt('member.term_start') ?> : '-'} -
                    ${member.term_end ? <?= _dt('member.term_end') ?> : '<?= _l('membership_present') ?>'}
                </p>
                <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(member.status || '')}</p>
                ${member.manifesto ? `<p><strong><?= _l('membership_manifesto') ?>:</strong> ${member.manifesto}</p>` : ''}
            `);
        } else {
            $('#boardMemberModalBody').html('<p class="text-danger">' + response.message + '</p>');
        }
    });
}

function editBoardMemberModal(memberId) {
    $('#boardMemberModal').modal('hide');

    // Show loading in edit modal
    $('#bmember_id').val(memberId);
    $('#boardMemberFormTitle').text('<?= _l('membership_edit_board_member') ?>');

    $.get("<?= admin_url('membership/ajax_get_board_member') ?>/" + memberId, function(response) {
        if (response.success) {
            var member = response.member;
            $('#bmember_member_id').val(member.member_id || '');
            $('#bmember_position').val(member.position || '');
            $('#bmember_election_id').val(member.election_id || '');
            $('#bmember_term_start').val(member.term_start || '');
            $('#bmember_term_end').val(member.term_end || '');
            $('#bmember_status').val(member.status || 'active');
            $('#addBoardMemberModal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}

function deleteBoardMemberModal(memberId) {
    if (confirm('<?= _l('membership_delete_confirm') ?>')) {
        window.location.href = "<?= admin_url('membership/delete_board_member/') ?>" + memberId;
    }
}

$('#boardMemberModal').on('hidden.bs.modal', function () {
    $('#boardMemberModalLabel').text('<?= _l('membership_board_member_details') ?>');
    $('#boardMemberModalBody').empty();
    $('#boardMemberModal .modal-footer').empty();
});

$('#addBoardMemberModal').on('hidden.bs.modal', function () {
    $('#bmember_id').val('');
    $('#boardMemberFormTitle').text('<?= _l('membership_add_board_member') ?>');
    $('#bmember_member_id').val('');
    $('#bmember_position').val('');
    $('#bmember_election_id').val('');
    $('#bmember_term_start').val('');
    $('#bmember_term_end').val('');
    $('#bmember_status').val('active');
});
</script>
<?php init_tail(); ?>