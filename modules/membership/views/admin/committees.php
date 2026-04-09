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
                        <button type="button" class="btn btn-primary" onclick="openAddCommitteeModal()">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($committees as $committee): ?>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="showCommitteeDetails(<?php echo $committee['id']; ?>)"
                                                   data-toggle="modal" data-target="#committeeModal">
                                                    <?php echo e($committee['name']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($committee['category_name'])): ?>
                                                    <span class="label label-default"><?= e($committee['category_name']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo $committee['description'] ? substr(e($committee['description']), 0, 50) . '...' : '-'; ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo $committee['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($committee['status']); ?>
                                                </span>
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

<!-- Committee Modal -->
<div class="modal fade" id="committeeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="committeeModalLabel"><?php echo _l('membership_committee_details'); ?></h4>
            </div>
            <div class="modal-body" id="committeeModalBody">
                <!-- Committee details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-default" onclick="editCommitteeModal(<?php echo $committee['id']; ?>)">
                            <?php echo _l('membership_edit'); ?>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteCommitteeModal(<?php echo $committee['id']; ?>)">
                            <?php echo _l('membership_delete'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Committee Modal -->
<div class="modal fade" id="addCommitteeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="committeeFormTitle"><?php echo _l('membership_add_committee'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/committees')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="committee_id">
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
                                    <option value="<?php echo $category['id']; ?>"><?php echo e($category['name']); ?></option>
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
                    <div class="col-md-12>
                        <div class="form-group">
                            <label><?php echo _l('membership_committee_description'); ?></label>
                            <textarea name="description" id="committee_description" class="form-control" rows="4"></textarea>
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
function openAddCommitteeModal() {
    $('#committee_id').val('');
    $('#committeeFormTitle').text('<?= _l('membership_add_committee') ?>');
    $('#committee_name').val('');
    $('#committee_category_id').val('');
    $('#committee_description').val('');
    $('#committee_status').val('active');
    $('#addCommitteeModal').modal('show');
}

function showCommitteeDetails(committeeId) {
    $('#committeeModalLabel').text('<?= _l('membership_loading') ?>...');
    $('#committeeModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');

    // Set up buttons for edit/delete
    $('#committeeModal .modal-footer').html(`
        <div class="row">
            <div class="col-md-6">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close') ?></button>
            </div>
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-default" onclick="editCommitteeModal(${committeeId})">
                    <?= _l('membership_edit') ?>
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteCommitteeModal(${committeeId})">
                    <?= _l('membership_delete') ?>
                </button>
            </div>
        </div>
    `);

    $('#committeeModal').modal('show');

    $.get("<?= admin_url('membership/ajax_get_committee') ?>/" + committeeId, function(response) {
        if (response.success) {
            var committee = response.committee;
            $('#committeeModalLabel').text(committee.name || '<?= _l('membership_committee_name') ?>');
            $('#committeeModalBody').html(`
                <p><strong><?= _l('membership_committee_category') ?>:</strong> ${committee.category_name || '-'}</p>
                <p><strong><?= _l('membership_committee_description') ?>:</strong> ${committee.description || '-'}</p>
                <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(committee.status || '')}</p>
            `);
        } else {
            $('#committeeModalBody').html('<p class="text-danger">' + response.message + '</p>');
        }
    });
}

function editCommitteeModal(committeeId) {
    $('#committeeModal').modal('hide');

    // Show loading in edit modal
    $('#committee_id').val(committeeId);
    $('#committeeFormTitle').text('<?= _l('membership_edit_committee') ?>');

    $.get("<?= admin_url('membership/ajax_get_committee') ?>/" + committeeId, function(response) {
        if (response.success) {
            var committee = response.committee;
            $('#committee_name').val(committee.name || '');
            $('#committee_category_id').val(committee.category_id || '');
            $('#committee_description').val(committee.description || '');
            $('#committee_status').val(committee.status || 'active');
            $('#addCommitteeModal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}

function deleteCommitteeModal(committeeId) {
    if (confirm('<?= _l('membership_delete_confirm') ?>')) {
        window.location.href = "<?= admin_url('membership/delete_committee/') ?>" + committeeId;
    }
}

$('#committeeModal').on('hidden.bs.modal', function () {
    $('#committeeModalLabel').text('<?= _l('membership_committee_details') ?>');
    $('#committeeModalBody').empty();
    $('#committeeModal .modal-footer').empty();
});

$('#addCommitteeModal').on('hidden.bs.modal', function () {
    $('#committee_id').val('');
    $('#committeeFormTitle').text('<?= _l('membership_add_committee') ?>');
    $('#committee_name').val('');
    $('#committee_category_id').val('');
    $('#committee_description').val('');
    $('#committee_status').val('active');
});
</script>
<?php init_tail(); ?>