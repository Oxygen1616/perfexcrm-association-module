<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?= _l('membership_positions'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <?php if (staff_can('create', 'membership')): ?>
                        <a href="#" onclick="init_position_form(); return false;" class="btn btn-primary">
                            <i class="fa fa-plus tw-mr-1"></i><?= _l('membership_new_position'); ?>
                        </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Election filter -->
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?= _l('membership_filter_by_election'); ?></label>
                    <select id="election-filter" class="form-control" onchange="filterByElection(this.value)">
                        <option value=""><?= _l('membership_all_elections'); ?></option>
                        <?php foreach ($elections as $el): ?>
                            <option value="<?= (int)$el['id']; ?>" <?= $election_filter == $el['id'] ? 'selected' : ''; ?>>
                                <?= e($el['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (!empty($positions)): ?>
                        <table class="table dt-table">
                            <thead>
                                <tr>
                                    <th><?= _l('membership_position_name'); ?></th>
                                    <th><?= _l('membership_election'); ?></th>
                                    <th><?= _l('membership_position_description'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($positions as $position): ?>
                                <?php
                                $linked_election = '';
                                if (!empty($position['election_id'])) {
                                    foreach ($elections as $el) {
                                        if ($el['id'] == $position['election_id']) {
                                            $linked_election = e($el['title']);
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td>
                                        <a href="#" onclick="position_form(<?= (int)$position['id']; ?>); return false;">
                                            <?= e($position['name']); ?>
                                        </a>
                                        <div class="row-options">
                                            <a href="#" onclick="position_form(<?= (int)$position['id']; ?>); return false;">
                                                <?= _l('membership_edit'); ?>
                                            </a>
                                            |
                                            <a href="<?= admin_url('membership/delete_position/' . (int)$position['id']); ?>"
                                               onclick="return confirm('<?= _l('membership_confirm_position_delete'); ?>')">
                                                <?= _l('membership_delete'); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td><?= $linked_election ?: '<span class="text-muted">—</span>'; ?></td>
                                    <td><?= e($position['description'] ?? ''); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <?= $election_filter ? _l('membership_no_positions_for_election') : _l('membership_no_positions_found'); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Position Modal -->
<div id="position-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="position-modal-title"></h4>
            </div>
            <div class="modal-body">
                <?= form_open(admin_url('membership/positions')); ?>
                <input type="hidden" name="id" id="position-id" value="">

                <div class="form-group">
                    <label for="position-election"><?= _l('membership_election'); ?></label>
                    <select name="election_id" id="position-election" class="form-control selectpicker"
                            data-none-selected-text="<?= _l('membership_select_election'); ?>">
                        <option value=""><?= _l('membership_no_election'); ?></option>
                        <?php foreach ($elections as $el): ?>
                            <option value="<?= (int)$el['id']; ?>"><?= e($el['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted"><?= _l('membership_position_election_hint'); ?></small>
                </div>

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
                    <button type="submit" class="btn btn-primary"><?= _l('membership_submit'); ?></button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
function filterByElection(val) {
    var url = '<?= admin_url('membership/positions'); ?>';
    if (val) url += '?election_id=' + val;
    window.location.href = url;
}

function _set_position_election(val) {
    $('#position-election').val(val || '');
    if (typeof $.fn.selectpicker !== 'undefined') {
        $('#position-election').selectpicker('refresh');
    }
}

function init_position_form() {
    $('#position-id').val('');
    $('#position-modal-title').text('<?= _l('membership_new_position'); ?>');
    $('#position-name').val('');
    $('#position-description').val('');
    // Pre-select the current election filter if one is active
    _set_position_election('<?= $election_filter ? (int)$election_filter : ''; ?>');
    $('#position-modal').modal('show');
}

function position_form(id) {
    $('#position-id').val(id);
    $('#position-modal-title').text('<?= _l('membership_edit_position'); ?>');

    $.get('<?= admin_url('membership/ajax_get_position'); ?>/' + id, function(response) {
        if (response.success) {
            $('#position-name').val(response.position.name);
            $('#position-description').val(response.position.description || '');
            _set_position_election(response.position.election_id || '');
            $('#position-modal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}
</script>
<?php init_tail(); ?>
