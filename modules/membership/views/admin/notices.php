<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_notices'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <a href="<?php echo admin_url('membership/notices'); ?>" class="btn btn-primary">
                            <?php echo _l('membership_add_notice'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($notices) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_notice_title'); ?></th>
                                        <th><?php echo _l('membership_notice_category'); ?></th>
                                        <th><?php echo _l('membership_notice_status'); ?></th>
                                        <th><?php echo _l('membership_notice_date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notices as $notice): ?>
                                        <tr>
                                            <td>
                                                <?php echo $notice['title']; ?>
                                                <div class="row-options">
                                                    <a href="<?php echo admin_url('membership/notices/' . $notice['id']); ?>">
                                                        <?php echo _l('membership_edit'); ?>
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('membership/delete_notice/' . $notice['id']); ?>" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                        <?php echo _l('membership_delete'); ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                $category = null;
                                                foreach($categories as $cat) {
                                                    if($cat['id'] == $notice['category_id']) {
                                                        $category = $cat;
                                                        break;
                                                    }
                                                }
                                                echo $category ? $category['name'] : '-';
                                                ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo ($notice['status'] == 'published' ? 'success' : ($notice['status'] == 'draft' ? 'warning' : 'default')); ?>">
                                                    <?php echo _l('membership_notice_status_' . $notice['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($notice['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_notices'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="noticeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo isset($notice) ? _l('membership_edit_notice') : _l('membership_add_notice'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/notices' . (isset($notice) ? '/' . $notice['id'] : ''))); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_notice_title'); ?> *</label>
                            <input type="text" name="title" class="form-control" required value="<?php echo isset($notice) ? $notice['title'] : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_notice_category'); ?></label>
                            <select name="category_id" class="form-control">
                                <option value=""><?php echo _l('membership_select_category'); ?></option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo isset($notice) && $notice['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_notice_status'); ?></label>
                            <select name="status" class="form-control">
                                <option value="draft" <?php echo isset($notice) && $notice['status'] == 'draft' ? 'selected' : ''; ?>><?php echo _l('membership_notice_status_draft'); ?></option>
                                <option value="published" <?php echo isset($notice) && $notice['status'] == 'published' ? 'selected' : ''; ?>><?php echo _l('membership_notice_status_published'); ?></option>
                                <option value="archived" <?php echo isset($notice) && $notice['status'] == 'archived' ? 'selected' : ''; ?>><?php echo _l('membership_notice_status_archived'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_notice_content'); ?> *</label>
                            <textarea name="content" class="form-control" rows="8" required><?php echo isset($notice) ? $notice['content'] : ''; ?></textarea>
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

<?php if (isset($notice)): ?>
<script>
$(document).ready(function() {
    $('#noticeModal').modal('show');
});
</script>
<?php endif; ?>

<?php init_tail(); ?>
