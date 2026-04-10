<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_moderators'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#moderatorModal">
                            <?php echo _l('membership_add_moderator'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($moderators) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_moderator_name'); ?></th>
                                        <th><?php echo _l('membership_moderator_email'); ?></th>
                                        <th><?php echo _l('membership_moderator_role'); ?></th>
                                        <th><?php echo _l('membership_moderator_status'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($moderators as $moderator): ?>
                                        <tr>
                                            <td><?php echo $moderator['staff_firstname'] . ' ' . $moderator['staff_lastname']; ?></td>
                                            <td><?php echo $moderator['email']; ?></td>
                                            <td><?php echo $moderator['role_name']; ?></td>
                                            <td>
                                                <span class="label label-<?php echo ($moderator['status'] == 'active' ? 'success' : 'danger'); ?>">
                                                    <?php echo _l('membership_moderator_status_' . $moderator['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('membership/moderators/' . $moderator['id']); ?>" class="btn btn-default btn-xs">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </a>
                                                <a href="<?php echo admin_url('membership/delete_moderator/' . $moderator['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_moderators'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="moderatorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo isset($moderator) ? _l('membership_edit_moderator') : _l('membership_add_moderator'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/moderators' . (isset($moderator) ? '/' . $moderator['id'] : ''))); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_moderator_staff'); ?> *</label>
                            <select name="staff_id" class="form-control" required>
                                <option value=""><?php echo _l('membership_select_staff'); ?></option>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?php echo $s['staffid']; ?>" <?php echo isset($moderator) && $moderator['staff_id'] == $s['staffid'] ? 'selected' : ''; ?>>
                                        <?php echo $s['firstname'] . ' ' . $s['lastname'] . ' (' . $s['email'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_moderator_role'); ?> *</label>
                            <select name="role_id" class="form-control" required>
                                <option value=""><?php echo _l('membership_select_role'); ?></option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?php echo $r['id']; ?>" <?php echo isset($moderator) && $moderator['role_id'] == $r['id'] ? 'selected' : ''; ?>>
                                        <?php echo $r['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_moderator_status'); ?></label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo isset($moderator) && $moderator['status'] == 'active' ? 'selected' : ''; ?>><?php echo _l('membership_moderator_status_active'); ?></option>
                                <option value="inactive" <?php echo isset($moderator) && $moderator['status'] == 'inactive' ? 'selected' : ''; ?>><?php echo _l('membership_moderator_status_inactive'); ?></option>
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

<?php if (isset($moderator) || !$this->input->get('id')): ?>
<script>
$(document).ready(function() {
    $('#moderatorModal').modal('show');
});
</script>
<?php endif; ?>

<?php init_tail(); ?>
