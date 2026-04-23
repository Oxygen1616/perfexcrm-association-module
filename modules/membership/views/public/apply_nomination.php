<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?php echo _l('membership_add_nomination'); ?></h4>

<div class="panel_s tw-mt-4">
    <div class="panel-body">
        <?php if (!empty($nomination_rules)): ?>
        <div class="alert alert-info">
            <h5><strong><?php echo _l('membership_nomination_rules_title'); ?></strong></h5>
            <?php echo e($nomination_rules); ?>
        </div>
        <?php endif; ?>

        <?php echo form_open_multipart(site_url('membership/client/apply_nomination')); ?>

            <div class="form-group">
                <label><?php echo _l('membership_select_member'); ?> *</label>
                <select name="nominated_member_id" class="form-control selectpicker" data-live-search="true" required>
                    <option value=""><?php echo _l('membership_select_member'); ?></option>
                    <?php foreach ($members as $m): ?>
                        <option value="<?php echo $m['id']; ?>">
                            <?php echo e($m['firstname'] . ' ' . $m['lastname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo _l('membership_election'); ?> *</label>
                <select name="election_id" id="nom_election_id" class="form-control" required onchange="nominationElectionChanged(this.value)">
                    <option value=""><?php echo _l('membership_select_election'); ?></option>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?php echo $election['id']; ?>" data-fee="<?php echo e($election['nomination_fee']); ?>">
                            <?php echo e($election['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo _l('membership_position'); ?> *</label>
                <select name="position" id="nom_position" class="form-control" required onchange="handleNomPositionOther(this.value)">
                    <option value=""><?php echo _l('membership_select_election_first'); ?></option>
                </select>
                <input type="text" name="position_custom" id="nom_position_custom" class="form-control tw-mt-1" style="display:none;"
                       placeholder="<?php echo _l('membership_or_type_position'); ?>">
                <small class="text-muted"><?php echo _l('membership_position_hint'); ?></small>
            </div>

            <div class="form-group">
                <label><?php echo _l('membership_manifesto'); ?> *</label>
                <textarea name="manifesto" class="form-control" rows="6" required
                          placeholder="<?php echo _l('membership_manifesto_placeholder'); ?>"></textarea>
            </div>

            <?php if ($nomination_fee > 0): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_nomination_fee'); ?></label>
                        <input type="text" name="total_fees" class="form-control" id="total-fees"
                               value="<?php echo $nomination_fee; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_currency'); ?></label>
                        <input type="text" class="form-control"
                               value="<?php echo $nomination_currency; ?>" readonly>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group mtop20">
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit_nomination'); ?></button>
                <a href="<?php echo site_url('membership/client/nominations'); ?>" class="btn btn-default"><?php echo _l('membership_cancel'); ?></a>
            </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
function nominationElectionChanged(election_id) {
    // Update fee display
    var feeInput = document.getElementById('total-fees');
    if (feeInput) {
        var selectedOption = document.querySelector('#nom_election_id option[value="' + election_id + '"]');
        var fee = selectedOption ? selectedOption.getAttribute('data-fee') : '<?php echo $nomination_fee; ?>';
        feeInput.value = fee || '0.00';
    }

    // Load positions via AJAX
    var $posSelect = $('#nom_position');
    var $posCustom = $('#nom_position_custom');

    $posSelect.html('<option value=""><?php echo _l('membership_loading'); ?></option>');
    $posCustom.hide().removeAttr('required').removeAttr('name');
    $posSelect.attr('name', 'position');

    if (!election_id) {
        $posSelect.html('<option value=""><?php echo _l('membership_select_election_first'); ?></option>');
        return;
    }

    $.ajax({
        url: '<?php echo site_url('membership/client/ajax_get_positions_by_election'); ?>/' + election_id,
        type: 'GET',
        dataType: 'json',
        success: function(resp) {
            $posSelect.html('<option value=""><?php echo _l('membership_select_position'); ?></option>');
            if (resp.success && resp.positions.length > 0) {
                $.each(resp.positions, function(i, pos) {
                    $posSelect.append($('<option>').val(pos).text(pos));
                });
                $posSelect.append('<option value="__other__"><?php echo _l('membership_other'); ?></option>');
            } else {
                // No positions configured — show free-text field
                $posSelect.html('<option value="__other__"><?php echo _l('membership_type_position'); ?></option>');
                $posCustom.show().attr('required', true).attr('name', 'position');
                $posSelect.removeAttr('name');
            }
        },
        error: function() {
            $posSelect.html('<option value=""><?php echo _l('membership_error_loading'); ?></option>');
        }
    });
}

function handleNomPositionOther(val) {
    var $posSelect = $('#nom_position');
    var $posCustom = $('#nom_position_custom');
    if (val === '__other__') {
        $posCustom.show().attr('required', true).attr('name', 'position');
        $posSelect.removeAttr('name');
    } else {
        $posCustom.hide().removeAttr('required').removeAttr('name').val('');
        $posSelect.attr('name', 'position');
    }
}

$(document).ready(function() {
    // global.js applies selectpicker to ALL selects on the page —
    // destroy it on election/position so native onchange fires reliably
    if (typeof $.fn.selectpicker !== 'undefined') {
        $('#nom_election_id').selectpicker('destroy');
        $('#nom_position').selectpicker('destroy');
    }
});
</script>
