<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_notice_categories'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <a href="<?php echo admin_url('membership/notice_categories'); ?>" class="btn btn-primary">
                            <?php echo _l('membership_add_category'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($categories) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_category_name'); ?></th>
                                        <th><?php echo _l('membership_category_description'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo $category['name']; ?></td>
                                            <td><?php echo $category['description'] ?: '-'; ?></td>
                                            <td>
                                                <a href="<?php echo admin_url('membership/notice_categories/' . $category['id']); ?>" class="btn btn-default btn-xs">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </a>
                                                <a href="<?php echo admin_url('membership/delete_notice_category/' . $category['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_categories'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo isset($category) ? _l('membership_edit_category') : _l('membership_add_category'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/notice_categories' . (isset($category) ? '/' . $category['id'] : ''))); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_category_name'); ?> *</label>
                            <input type="text" name="name" class="form-control" required value="<?php echo isset($category) ? $category['name'] : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_category_description'); ?></label>
                            <textarea name="description" class="form-control" rows="3"><?php echo isset($category) ? $category['description'] : ''; ?></textarea>
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

<?php if (isset($category)): ?>
<script>
$(document).ready(function() {
    $('#categoryModal').modal('show');
});
</script>
<?php endif; ?>

<?php init_tail(); ?>
