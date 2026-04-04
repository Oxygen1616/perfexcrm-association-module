<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
        <?php if (staff_can('create', 'membership')): ?>
        <a href="#" onclick="init_position_form(); return false;" class="btn btn-primary pull-left mbot15">
            <i class="fa-regular fa-plus"></i> <?= _l('membership_new_position'); ?>
        </a>
        <div class="clearfix"></div>
        <?php endif; ?>

        <?php if (count($positions) > 0): ?>
        <table class="table dt-table">
            <thead>
                <tr>
                    <th><?= _l('membership_position_name'); ?></th>
                    <th><?= _l('membership_position_description'); ?></th>
                    <th class="text-right"><?= _l('membership_options'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($positions as $position): ?>
                <tr>
                    <td><?= e($position['name']); ?></td>
                    <td><?= e($position['description']); ?></td>
                    <td class="text-right">
                        <?php if (staff_can('edit', 'membership')): ?>
                        <a href="#" onclick="position_form(<?= $position['id']; ?>); return false;" class="btn btn-default btn-icon">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (staff_can('delete', 'membership')): ?>
                        <a href="#" onclick="delete_position(<?= $position['id']; ?>); return false;" class="btn btn-danger btn-icon">
                            <i class="fa-regular fa-trash-can"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-info"><?= _l('membership_no_positions_found'); ?></div>
        <?php endif; ?>
    </div>
</div>

<div id="position-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="position-modal-title"></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('membership/positions')); ?>
                <input type="hidden" name="id" id="position-id" value="">

                <div class="form-group">
                    <label for="position-name"><?= _l('membership_position_name'); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="position-name" required>
                </div>

                <div class="form-group">
                    <label for="position-description"><?= _l('membership_position_description'); ?></label>
                    <textarea class="form-control" name="description" id="position-description" rows="3"></textarea>
                </div>

                <div class="text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close'); ?></button>
                    <button type="submit" class="btn btn-info"><?= _l('membership_submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function init_position_form() {
        $('#position-id').val('');
        $('#position-modal-title').text('<?= _l('membership_new_position'); ?>');
        $('#position-name').val('');
        $('#position-description').val('');
        $('#position-modal').modal('show');
    }

    function position_form(id) {
        $('#position-id').val(id);
        $('#position-modal-title').text('<?= _l('membership_edit_position'); ?>');

        // Load position data via AJAX
        $.get(admin_url('membership/get_position/' + id), function(response) {
            if (response.success) {
                $('#position-name').val(response.position.name);
                $('#position-description').val(response.position.description || '');
                $('#position-modal').modal('show');
            } else {
                alert_float('danger', response.message);
            }
        });
    }

    function delete_position(id) {
        if (confirm('<?= _l('membership_confirm_position_delete'); ?>')) {
            $.get(admin_url('membership/delete_position/' + id), function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            });
        }
    }
</script>