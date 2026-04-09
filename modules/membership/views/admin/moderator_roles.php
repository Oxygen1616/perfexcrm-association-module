<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_moderator_roles'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <a href="<?php echo admin_url('membership/moderator_roles'); ?>" class="btn btn-primary">
                            <?php echo _l('membership_add_role'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($roles) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_role_name'); ?></th>
                                        <th><?php echo _l('membership_role_permissions'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><?php echo $role['name']; ?></td>
                                            <td>
                                                <?php 
                                                $permissions = json_decode($role['permissions'], true);
                                                if ($permissions && count($permissions) > 0) {
                                                    echo '<span class="label label-info">' . count($permissions) . ' ' . _l('membership_permissions') . '</span>';
                                                } else {
                                                    echo '<span class="label label-default">' . _l('membership_no_permissions') . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('membership/moderator_roles/' . $role['id']); ?>" class="btn btn-default btn-xs">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </a>
                                                <a href="<?php echo admin_url('membership/delete_moderator_role/' . $role['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_roles'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo isset($role) ? _l('membership_edit_role') : _l('membership_add_role'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/moderator_roles' . (isset($role) ? '/' . $role['id'] : ''))); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_role_name'); ?> *</label>
                            <input type="text" name="name" class="form-control" required value="<?php echo isset($role) ? $role['name'] : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_role_permissions'); ?></label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" value="view" <?php echo isset($role) && in_array('view', json_decode($role['permissions'], true)) ? 'checked' : ''; ?>>
                                    <?php echo _l('permission_view') . ' (' . _l('permission_global') . ')'; ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" value="create" <?php echo isset($role) && in_array('create', json_decode($role['permissions'], true)) ? 'checked' : ''; ?>>
                                    <?php echo _l('permission_create'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" value="edit" <?php echo isset($role) && in_array('edit', json_decode($role['permissions'], true)) ? 'checked' : ''; ?>>
                                    <?php echo _l('permission_edit'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" value="delete" <?php echo isset($role) && in_array('delete', json_decode($role['permissions'], true)) ? 'checked' : ''; ?>>
                                    <?php echo _l('permission_delete'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" value="manage_members" <?php echo isset($role) && in_array('manage_members', json_decode($role['permissions'], true)) ? 'checked' : ''; ?>>
                                    <?php echo _l('membership_manage_members'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" value="manage_elections" <?php echo isset($role) && in_array('manage_elections', json_decode($role['permissions'], true)) ? 'checked' : ''; ?>>
                                    <?php echo _l('membership_manage_elections'); ?>
                                </label>
                            </div>
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

<?php if (isset($role)): ?>
<script>
$(document).ready(function() {
    $('#roleModal').modal('show');
});
</script>
<?php endif; ?>

<?php init_tail(); ?>
