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
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member): ?>
                                        <?php
                                        $this->load->model('clients_model');
                                        $contact = $this->clients_model->get_contact($member['contact_id']);
                                        ?>
                                        <tr>
                                            <td>
                                                <?php if ($contact): ?>
                                                    <?php echo $contact->firstname . ' ' . $contact->lastname; ?>
                                                <?php else: ?>
                                                    Unknown
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($contact): ?>
                                                    <?php echo $contact->email; ?>
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
                                            <td>
                                                <?php
                                                $this->load->model('clients_model');
                                                $contact = $this->clients_model->get_contact($member['contact_id']);
                                                $name = $contact ? $contact->firstname . ' ' . $contact->lastname : 'Unknown';
                                                $email = $contact ? $contact->email : '';
                                                ?>
                                                <button type="button" class="btn btn-default btn-xs" onclick="openEditModal(<?php echo $member['id']; ?>, '<?php echo addslashes($name); ?>', '<?php echo addslashes($email); ?>', '<?php echo addslashes($member['status']); ?>', '<?php echo addslashes($member['membership_type']); ?>', '<?php echo addslashes($member['profession']); ?>', '<?php echo $member['graduation_year']; ?>', <?php echo $member['show_in_directory']; ?>)">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_member/' . $member['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
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

<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_member'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/add_member')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_select_contact'); ?> *</label>
                            <select name="contact_id" class="form-control selectpicker" data-live-search="true" required>
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
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_type'); ?></label>
                            <input type="text" name="membership_type" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_profession'); ?></label>
                            <input type="text" name="profession" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_graduation_year'); ?></label>
                            <input type="number" name="graduation_year" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <input type="checkbox" name="show_in_directory" value="1" id="add_show_in_directory" checked>
                            <label><?php echo _l('membership_show_in_directory'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_edit_member'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/members')); ?>
            <div class="modal-body">
                <input type="hidden" name="member_id" id="edit_member_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_name'); ?></label>
                            <input type="text" class="form-control" id="edit_name" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_email'); ?></label>
                            <input type="text" class="form-control" id="edit_email" disabled>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_status'); ?></label>
                            <select name="status" class="form-control" id="edit_status">
                                <option value="pending"><?php echo _l('membership_status_pending'); ?></option>
                                <option value="active"><?php echo _l('membership_status_active'); ?></option>
                                <option value="suspended"><?php echo _l('membership_status_suspended'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_type'); ?></label>
                            <input type="text" name="membership_type" class="form-control" id="edit_membership_type">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_member_profession'); ?></label>
                            <input type="text" name="profession" class="form-control" id="edit_profession">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_graduation_year'); ?></label>
                            <input type="number" name="graduation_year" class="form-control" id="edit_graduation_year">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <input type="checkbox" name="show_in_directory" value="1" id="edit_show_in_directory">
                            <label><?php echo _l('membership_show_in_directory'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_update'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function openAddMemberModal() {
    $('#addMemberModal').modal('show');
}

function openEditModal(id, name, email, status, membership_type, profession, graduation_year, show_in_directory) {
    document.getElementById('edit_member_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_membership_type').value = membership_type || '';
    document.getElementById('edit_profession').value = profession || '';
    document.getElementById('edit_graduation_year').value = graduation_year || '';
    document.getElementById('edit_show_in_directory').checked = show_in_directory == 1;
    
    $('#editMemberModal').modal('show');
}
</script>
<?php init_tail(); ?>
