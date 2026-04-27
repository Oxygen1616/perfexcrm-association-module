<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_announcements'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" onclick="openAnnouncementModal()">
                            <i class="fa fa-plus"></i> <?php echo _l('membership_new_announcement'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (!empty($announcements)): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_announcement_title'); ?></th>
                                        <th><?php echo _l('membership_announcement_priority'); ?></th>
                                        <th><?php echo _l('membership_date_posted'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($announcements as $ann): ?>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="editAnnouncement(<?php echo $ann['id']; ?>, <?php echo htmlspecialchars(json_encode($ann), ENT_QUOTES); ?>); return false;">
                                                    <strong><?php echo e($ann['title']); ?></strong>
                                                </a>
                                                <p class="text-muted tw-text-sm tw-mb-0"><?php echo e(substr($ann['body'], 0, 120)) . (strlen($ann['body']) > 120 ? '…' : ''); ?></p>
                                                <div class="row-options">
                                                    <a href="#" onclick="editAnnouncement(<?php echo $ann['id']; ?>, <?php echo htmlspecialchars(json_encode($ann), ENT_QUOTES); ?>); return false;">
                                                        <?php echo _l('membership_edit'); ?>
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('membership/delete_announcement/' . $ann['id']); ?>"
                                                       onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                        <?php echo _l('membership_delete'); ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo ($ann['priority'] == 'urgent' ? 'danger' : ($ann['priority'] == 'important' ? 'warning' : 'default')); ?>">
                                                    <?php echo ucfirst($ann['priority']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo _dt($ann['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center text-muted"><?php echo _l('membership_no_announcements'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="announcementModalTitle"><?php echo _l('membership_new_announcement'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/announcements'), ['id' => 'announcementForm']); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php echo _l('membership_announcement_title'); ?> *</label>
                    <input type="text" name="title" id="ann_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_announcement_priority'); ?></label>
                    <select name="priority" id="ann_priority" class="form-control">
                        <option value="normal"><?php echo _l('membership_priority_normal'); ?></option>
                        <option value="important"><?php echo _l('membership_priority_important'); ?></option>
                        <option value="urgent"><?php echo _l('membership_priority_urgent'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_announcement_body'); ?> *</label>
                    <textarea name="body" id="ann_body" class="form-control" rows="6" required></textarea>
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
function openAnnouncementModal() {
    $('#announcementModalTitle').text('<?= _l('membership_new_announcement'); ?>');
    $('#announcementForm').attr('action', '<?= admin_url('membership/announcements'); ?>');
    $('#ann_title').val('');
    $('#ann_priority').val('normal');
    $('#ann_body').val('');
    $('#announcementModal').modal('show');
}

function editAnnouncement(id, ann) {
    $('#announcementModalTitle').text('<?= _l('membership_edit_announcement'); ?>');
    $('#announcementForm').attr('action', '<?= admin_url('membership/announcements/'); ?>' + id);
    $('#ann_title').val(ann.title);
    $('#ann_priority').val(ann.priority);
    $('#ann_body').val(ann.body);
    $('#announcementModal').modal('show');
}

$('#announcementModal').on('hidden.bs.modal', function() {
    $('#ann_title').val('');
    $('#ann_priority').val('normal');
    $('#ann_body').val('');
});
</script>
<?php init_tail(); ?>
