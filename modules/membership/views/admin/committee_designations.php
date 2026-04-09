<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_committee_designations'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#designationModal">
                            <?php echo _l('membership_add_designation'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($designations) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_designation_name'); ?></th>
                                        <th><?php echo _l('membership_designation_description'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($designations as $designation): ?>
                                        <tr>
                                            <td><?php echo $designation['name']; ?></td>
                                            <td><?php echo $designation['description'] ?: '-'; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs" onclick="editDesignation(<?php echo $designation['id']; ?>, '<?php echo htmlspecialchars($designation['name']); ?>', '<?php echo htmlspecialchars($designation['description']); ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_committee_designation/' . $designation['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_designations'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="designationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_designation'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/committee_designations')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="designation_id" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_designation_name'); ?> *</label>
                            <input type="text" name="name" id="designation_name" class="form-control" required value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_designation_description'); ?></label>
                            <textarea name="description" id="designation_description" class="form-control" rows="3"></textarea>
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
function editDesignation(id, name, description) {
    document.getElementById('designation_id').value = id;
    document.getElementById('designation_name').value = name;
    document.getElementById('designation_description').value = description;
    document.querySelector('#designationModal .modal-title').textContent = '<?php echo _l('membership_edit_designation'); ?>';
    $('#designationModal').modal('show');
}

$('#designationModal').on('hidden.bs.modal', function() {
    document.getElementById('designation_id').value = '';
    document.getElementById('designation_name').value = '';
    document.getElementById('designation_description').value = '';
    document.querySelector('#designationModal .modal-title').textContent = '<?php echo _l('membership_add_designation'); ?>';
});
</script>

<?php init_tail(); ?>
