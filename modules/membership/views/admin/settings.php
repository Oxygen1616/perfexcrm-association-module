<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_settings'); ?></h4>
            </div>
        </div>

        <!-- General Settings -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo _l('membership_general_settings'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open(admin_url('membership/settings')); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="monthly_dues"><?php echo _l('membership_setting_monthly_dues'); ?></label>
                                        <input type="number" class="form-control" id="monthly_dues" name="monthly_dues" value="<?php echo get_option('membership_monthly_dues') ?: '30.00'; ?>" step="0.01" min="0">
                                        <small class="text-muted"><?php echo _l('membership_currency_helper'); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="default_type"><?php echo _l('membership_setting_default_type'); ?></label>
                                        <input type="text" class="form-control" id="default_type" name="default_type" value="<?php echo get_option('membership_default_type') ?: 'Regular'; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="auto_approve_jobs" id="auto_approve_jobs" value="1" <?php echo get_option('membership_auto_approve_jobs') ? 'checked' : ''; ?>>
                                            <label for="auto_approve_jobs"><?php echo _l('membership_setting_auto_approve_jobs'); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="auto_approve_stories" id="auto_approve_stories" value="1" <?php echo get_option('membership_auto_approve_stories') ? 'checked' : ''; ?>>
                                            <label for="auto_approve_stories"><?php echo _l('membership_setting_auto_approve_stories'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mtop15"><?php echo _l('membership_save_settings'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Positions -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_positions'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="#" onclick="init_position_form(); return false;" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_new_position'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (count($positions) > 0): ?>
                        <table class="table dt-table">
                            <thead>
                                <tr>
                                    <th><?php echo _l('membership_position_name'); ?></th>
                                    <th><?php echo _l('membership_position_description'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($positions as $position): ?>
                                <tr>
                                    <td>
                                        <a href="#" onclick="position_form(<?php echo $position['id']; ?>); return false;">
                                            <?php echo e($position['name']); ?>
                                        </a>
                                        <div class="row-options">
                                            <a href="#" onclick="position_form(<?php echo $position['id']; ?>); return false;"><?php echo _l('membership_edit'); ?></a>
                                            |
                                            <a href="<?php echo admin_url('membership/delete_position/' . $position['id']); ?>"
                                               onclick="return confirm('<?php echo _l('membership_confirm_position_delete'); ?>')"><?php echo _l('membership_delete'); ?></a>
                                        </div>
                                    </td>
                                    <td><?php echo e($position['description']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="alert alert-info"><?php echo _l('membership_no_positions_found'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Election Symbols -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_election_symbols'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#symbolModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_add_election_symbol'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (count($symbols) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_symbol_name'); ?></th>
                                        <th><?php echo _l('membership_symbol_image'); ?></th>
                                        <th><?php echo _l('membership_description'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($symbols as $symbol): ?>
                                        <tr>
                                            <td><?php echo e($symbol['name']); ?></td>
                                            <td>
                                                <?php if ($symbol['symbol_image']): ?>
                                                    <img src="<?php echo $symbol['symbol_image']; ?>" alt="<?php echo e($symbol['name']); ?>" style="max-width: 50px; max-height: 50px;">
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($symbol['description'] ?: '-'); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs"
                                                    onclick="editSymbol(<?php echo $symbol['id']; ?>, '<?php echo addslashes($symbol['name']); ?>', '<?php echo addslashes($symbol['symbol_image']); ?>', '<?php echo addslashes($symbol['description']); ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_election_symbol/' . $symbol['id']); ?>"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center"><?php echo _l('membership_no_election_symbols'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Committee Categories -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_committee_categories'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoryModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_add_category'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (count($categories) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_category_name'); ?></th>
                                        <th><?php echo _l('membership_category_description'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo e($category['name']); ?></td>
                                            <td><?php echo e($category['description'] ?: '-'); ?></td>
                                            <td>
                                                <span class="label label-<?php echo $category['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($category['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs"
                                                    onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo addslashes($category['name']); ?>', '<?php echo addslashes($category['description']); ?>', '<?php echo $category['status']; ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_committee_category/' . $category['id']); ?>"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center"><?php echo _l('membership_no_categories'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Committee Designations -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_committee_designations'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#designationModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_add_designation'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
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
                                            <td><?php echo e($designation['name']); ?></td>
                                            <td><?php echo e($designation['description'] ?: '-'); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs"
                                                    onclick="editDesignation(<?php echo $designation['id']; ?>, '<?php echo addslashes($designation['name']); ?>', '<?php echo addslashes($designation['description']); ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_committee_designation/' . $designation['id']); ?>"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center"><?php echo _l('membership_no_designations'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Committee Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="categoryModalTitle"><?php echo _l('membership_add_category'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/committee_categories')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="category_id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_category_name'); ?> *</label>
                    <input type="text" name="name" id="category_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_category_description'); ?></label>
                    <textarea name="description" id="category_description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_status'); ?></label>
                    <select name="status" id="category_status" class="form-control">
                        <option value="active"><?php echo _l('membership_active'); ?></option>
                        <option value="inactive"><?php echo _l('membership_inactive'); ?></option>
                    </select>
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

<!-- Position Modal -->
<div id="position-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="position-modal-title"></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(admin_url('membership/positions')); ?>
                <input type="hidden" name="id" id="position-id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_position_name'); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="position-name" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_position_description'); ?></label>
                    <textarea class="form-control" name="description" id="position-description" rows="3"></textarea>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('membership_submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Election Symbol Modal -->
<div class="modal fade" id="symbolModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="symbolModalTitle"><?php echo _l('membership_add_election_symbol'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/election_symbols')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="symbol_id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_symbol_name'); ?> *</label>
                    <input type="text" name="name" id="symbol_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_symbol_image_url'); ?></label>
                    <input type="text" name="symbol_image" id="symbol_image" class="form-control" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_description'); ?></label>
                    <textarea name="description" id="symbol_description" class="form-control" rows="3"></textarea>
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

<!-- Committee Designation Modal -->
<div class="modal fade" id="designationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="designationModalTitle"><?php echo _l('membership_add_designation'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/committee_designations')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="designation_id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_designation_name'); ?> *</label>
                    <input type="text" name="name" id="designation_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_designation_description'); ?></label>
                    <textarea name="description" id="designation_description" class="form-control" rows="3"></textarea>
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
// Category modal
function editCategory(id, name, description, status) {
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = name;
    document.getElementById('category_description').value = description;
    document.getElementById('category_status').value = status;
    document.getElementById('categoryModalTitle').textContent = '<?php echo _l('membership_edit_category'); ?>';
    $('#categoryModal').modal('show');
}
$('#categoryModal').on('hidden.bs.modal', function() {
    document.getElementById('category_id').value = '';
    document.getElementById('category_name').value = '';
    document.getElementById('category_description').value = '';
    document.getElementById('category_status').value = 'active';
    document.getElementById('categoryModalTitle').textContent = '<?php echo _l('membership_add_category'); ?>';
});

// Position modal
function init_position_form() {
    $('#position-id').val('');
    $('#position-modal-title').text('<?php echo _l('membership_new_position'); ?>');
    $('#position-name').val('');
    $('#position-description').val('');
    $('#position-modal').modal('show');
}
function position_form(id) {
    $('#position-id').val(id);
    $('#position-modal-title').text('<?php echo _l('membership_edit_position'); ?>');
    $.get('<?php echo admin_url('membership/ajax_get_position'); ?>/' + id, function(response) {
        if (response.success) {
            $('#position-name').val(response.position.name);
            $('#position-description').val(response.position.description || '');
            $('#position-modal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}

// Symbol modal
function editSymbol(id, name, symbol_image, description) {
    document.getElementById('symbol_id').value = id;
    document.getElementById('symbol_name').value = name;
    document.getElementById('symbol_image').value = symbol_image;
    document.getElementById('symbol_description').value = description;
    document.getElementById('symbolModalTitle').textContent = '<?php echo _l('membership_edit_election_symbol'); ?>';
    $('#symbolModal').modal('show');
}
$('#symbolModal').on('hidden.bs.modal', function() {
    document.getElementById('symbol_id').value = '';
    document.getElementById('symbol_name').value = '';
    document.getElementById('symbol_image').value = '';
    document.getElementById('symbol_description').value = '';
    document.getElementById('symbolModalTitle').textContent = '<?php echo _l('membership_add_election_symbol'); ?>';
});

// Designation modal
function editDesignation(id, name, description) {
    document.getElementById('designation_id').value = id;
    document.getElementById('designation_name').value = name;
    document.getElementById('designation_description').value = description;
    document.getElementById('designationModalTitle').textContent = '<?php echo _l('membership_edit_designation'); ?>';
    $('#designationModal').modal('show');
}
$('#designationModal').on('hidden.bs.modal', function() {
    document.getElementById('designation_id').value = '';
    document.getElementById('designation_name').value = '';
    document.getElementById('designation_description').value = '';
    document.getElementById('designationModalTitle').textContent = '<?php echo _l('membership_add_designation'); ?>';
});
</script>

<?php init_tail(); ?>
