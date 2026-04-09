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
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#boardMemberModal">
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
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($board_members as $member): ?>
                                        <tr>
                                            <td><?php echo $member['firstname'] . ' ' . $member['lastname']; ?></td>
                                            <td><?php echo $member['email']; ?></td>
                                            <td><?php echo $member['position']; ?></td>
                                            <td>
                                                <?php echo date('M Y', strtotime($member['term_start'])); ?> - 
                                                <?php echo $member['term_end'] ? date('M Y', strtotime($member['term_end'])) : 'Present'; ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo $member['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($member['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs" onclick="editBoardMember(<?php echo $member['id']; ?>, <?php echo $member['member_id']; ?>, '<?php echo htmlspecialchars($member['position']); ?>', '<?php echo $member['election_id']; ?>', '<?php echo $member['term_start']; ?>', '<?php echo $member['term_end']; ?>', '<?php echo $member['status']; ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_board_member/' . $member['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
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

<div class="modal fade" id="boardMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_board_member'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/board_members')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="bmember_id" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_select_member'); ?> *</label>
                            <select name="member_id" id="bmember_member_id" class="form-control" required>
                                <option value=""><?php echo _l('membership_select_member'); ?></option>
                                <?php foreach ($members as $m): ?>
                                    <?php
                                    $this->load->model('clients_model');
                                    $contact = $this->clients_model->get_contact($m['contact_id']);
                                    $name = $contact ? $contact->firstname . ' ' . $contact->lastname : 'Unknown';
                                    ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo $name; ?></option>
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
                                    <option value="<?php echo $e['id']; ?>"><?php echo $e['title']; ?></option>
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
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function editBoardMember(id, member_id, position, election_id, term_start, term_end, status) {
    document.getElementById('bmember_id').value = id;
    document.getElementById('bmember_member_id').value = member_id || '';
    document.getElementById('bmember_position').value = position;
    document.getElementById('bmember_election_id').value = election_id || '';
    document.getElementById('bmember_term_start').value = term_start || '';
    document.getElementById('bmember_term_end').value = term_end || '';
    document.getElementById('bmember_status').value = status;
    document.querySelector('#boardMemberModal .modal-title').textContent = '<?php echo _l('membership_edit_board_member'); ?>';
    $('#boardMemberModal').modal('show');
}

$('#boardMemberModal').on('hidden.bs.modal', function() {
    document.getElementById('bmember_id').value = '';
    document.getElementById('bmember_member_id').value = '';
    document.getElementById('bmember_position').value = '';
    document.getElementById('bmember_election_id').value = '';
    document.getElementById('bmember_term_start').value = '';
    document.getElementById('bmember_term_end').value = '';
    document.getElementById('bmember_status').value = 'active';
    document.querySelector('#boardMemberModal .modal-title').textContent = '<?php echo _l('membership_add_board_member'); ?>';
});
</script>

<?php init_tail(); ?>
