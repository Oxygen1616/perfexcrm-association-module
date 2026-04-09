<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_committees'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#committeeModal">
                            <?php echo _l('membership_add_committee'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($committees) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_committee_name'); ?></th>
                                        <th><?php echo _l('membership_committee_category'); ?></th>
                                        <th><?php echo _l('membership_committee_description'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($committees as $committee): ?>
                                        <tr>
                                            <td><?php echo $committee['name']; ?></td>
                                            <td><?php echo $committee['category_name'] ?: '-'; ?></td>
                                            <td><?php echo $committee['description'] ? substr($committee['description'], 0, 50) . '...' : '-'; ?></td>
                                            <td>
                                                <span class="label label-<?php echo $committee['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($committee['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs" onclick="editCommittee(<?php echo $committee['id']; ?>, '<?php echo htmlspecialchars($committee['name']); ?>', '<?php echo htmlspecialchars($committee['description']); ?>', '<?php echo $committee['category_id']; ?>', '<?php echo $committee['status']; ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_committee/' . $committee['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_committees'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="committeeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_committee'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/committees')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="committee_id" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_committee_name'); ?> *</label>
                            <input type="text" name="name" id="committee_name" class="form-control" required value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_committee_category'); ?></label>
                            <select name="category_id" id="committee_category_id" class="form-control">
                                <option value=""><?php echo _l('membership_select_category'); ?></option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_status'); ?></label>
                            <select name="status" id="committee_status" class="form-control">
                                <option value="active"><?php echo _l('membership_active'); ?></option>
                                <option value="inactive"><?php echo _l('membership_inactive'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_committee_description'); ?></label>
                            <textarea name="description" id="committee_description" class="form-control" rows="4"></textarea>
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
function editCommittee(id, name, description, category_id, status) {
    document.getElementById('committee_id').value = id;
    document.getElementById('committee_name').value = name;
    document.getElementById('committee_description').value = description;
    document.getElementById('committee_category_id').value = category_id || '';
    document.getElementById('committee_status').value = status;
    document.querySelector('#committeeModal .modal-title').textContent = '<?php echo _l('membership_edit_committee'); ?>';
    $('#committeeModal').modal('show');
}

$('#committeeModal').on('hidden.bs.modal', function() {
    document.getElementById('committee_id').value = '';
    document.getElementById('committee_name').value = '';
    document.getElementById('committee_description').value = '';
    document.getElementById('committee_category_id').value = '';
    document.getElementById('committee_status').value = 'active';
    document.querySelector('#committeeModal .modal-title').textContent = '<?php echo _l('membership_add_committee'); ?>';
});
</script>

<?php init_tail(); ?>
