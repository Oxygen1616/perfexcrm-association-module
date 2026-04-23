<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_nominations'); ?></h4>

<div class="tw-mt-4">
    <button type="button" class="btn btn-primary tw-mb-4" data-toggle="modal" data-target="#addNominationModal">
        <?php echo _l('membership_add_nomination'); ?>
    </button>

    <div class="panel_s">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo _l('membership_available_elections'); ?></h4>
        </div>
        <div class="panel-body" style="padding:0;">
            <?php if (count($elections) > 0): ?>
                <ul class="list-group" style="margin:0;">
                    <?php foreach ($elections as $election): ?>
                    <li class="list-group-item tw-flex tw-items-center tw-justify-between"
                        onclick="showElectionDetail(<?php echo $election['id']; ?>)"
                        style="cursor:pointer;">
                        <span><?php echo e($election['title']); ?></span>
                        <span class="text-muted small" style="margin-left:8px; white-space:nowrap;">
                            <?php echo date('M d, Y', strtotime($election['start_date'])); ?> &mdash; <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-center text-muted tw-py-4"><?php echo _l('membership_no_elections'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($past_elections)): ?>
    <div class="panel_s tw-mt-4">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo _l('membership_past_elections'); ?></h4>
        </div>
        <div class="panel-body" style="padding:0;">
            <ul class="list-group" style="margin:0;">
                <?php foreach ($past_elections as $el): ?>
                <li class="list-group-item tw-flex tw-items-center tw-justify-between"
                    onclick="showElectionDetail(<?php echo $el['id']; ?>)"
                    style="cursor:pointer;">
                    <span><?php echo e($el['title']); ?></span>
                    <span class="text-muted small" style="margin-left:8px; white-space:nowrap;">
                        <?php echo date('M d, Y', strtotime($el['start_date'])); ?> &mdash; <?php echo date('M d, Y', strtotime($el['end_date'])); ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="panel_s tw-mt-4">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo _l('membership_my_nominations'); ?></h4>
        </div>
        <div class="panel-body" style="padding:0;">
            <?php if (!empty($my_nominations)): ?>
            <ul class="list-group" style="margin:0;">
                <?php foreach ($my_nominations as $nom): ?>
                <li class="list-group-item tw-flex tw-items-center tw-justify-between"
                    onclick="showNominationDetail(<?php echo $nom['id']; ?>)"
                    style="cursor:pointer;">
                    <div>
                        <span><?php echo e($nom['election_title'] ?? '-'); ?></span>
                        <span class="text-muted small" style="margin-left:6px;">
                            <?php echo e($nom['firstname'] . ' ' . $nom['lastname']); ?> &mdash; <?php echo e($nom['position']); ?>
                        </span>
                    </div>
                    <div style="text-align:right; white-space:nowrap; margin-left:8px;">
                        <span class="label label-<?php echo $nom['status'] == 'approved' ? 'success' : ($nom['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                            <?php echo ucfirst($nom['status']); ?>
                        </span>
                        <span class="text-muted small" style="margin-left:6px;"><?php echo date('M d, Y', strtotime($nom['nominated_at'])); ?></span>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <p class="text-center text-muted tw-py-4"><?php echo _l('membership_no_nominations'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Nomination Modal -->
<div class="modal fade" id="addNominationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_nomination'); ?></h4>
            </div>
            <?php if (!empty($nomination_rules)): ?>
            <div class="modal-body" style="padding-bottom:0;">
                <div class="alert alert-info">
                    <strong><?php echo _l('membership_nomination_rules_title'); ?></strong><br>
                    <?php echo e($nomination_rules); ?>
                </div>
            </div>
            <?php endif; ?>
            <?php echo form_open_multipart(site_url('membership/client/apply_nomination')); ?>
            <div class="modal-body">
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
                    <select name="election_id" id="nom_modal_election_id" class="form-control" required onchange="nomModalElectionChanged(this.value)">
                        <option value=""><?php echo _l('membership_select_election'); ?></option>
                        <?php foreach ($elections as $election): ?>
                            <option value="<?php echo $election['id']; ?>" data-fee="<?php echo isset($election['nomination_fee']) ? $election['nomination_fee'] : '0.00'; ?>">
                                <?php echo e($election['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_position'); ?> *</label>
                    <select name="position" id="nom_modal_position" class="form-control" required onchange="nomModalPositionOther(this.value)">
                        <option value=""><?php echo _l('membership_select_election_first'); ?></option>
                    </select>
                    <input type="text" name="position_custom" id="nom_modal_position_custom" class="form-control tw-mt-1" style="display:none;"
                           placeholder="<?php echo _l('membership_or_type_position'); ?>">
                    <small class="text-muted"><?php echo _l('membership_position_hint'); ?></small>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_manifesto'); ?> *</label>
                    <textarea name="manifesto" class="form-control" rows="5" required placeholder="<?php echo _l('membership_manifesto_placeholder'); ?>"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_nomination_fee'); ?></label>
                            <input type="text" name="total_fees" class="form-control" id="modal-total-fees" value="<?php echo $nomination_fee; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_currency'); ?></label>
                            <input type="text" class="form-control" value="<?php echo $nomination_currency; ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit_nomination'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Nomination Detail Modal -->
<div class="modal fade" id="nominationDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="nominationDetailTitle"></h4>
            </div>
            <div class="modal-body" id="nominationDetailBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Election Detail Modal -->
<div class="modal fade" id="electionDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="electionDetailTitle"></h4>
            </div>
            <div class="modal-body" id="electionDetailBody">
                <p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
// Pre-load my nominations data to power the detail popup
var myNominationsData = <?php echo json_encode(array_map(function($n) {
    return [
        'id'           => $n['id'],
        'election'     => $n['election_title'] ?? '-',
        'nominee'      => trim($n['firstname'] . ' ' . $n['lastname']),
        'position'     => $n['position'],
        'manifesto'    => $n['manifesto'] ?? '',
        'status'       => $n['status'],
        'nominated_at' => date('M d, Y', strtotime($n['nominated_at'])),
    ];
}, $my_nominations ?? [])); ?>;

function showNominationDetail(id) {
    var nom = myNominationsData.find(function(n) { return n.id == id; });
    if (!nom) { return; }

    var statusClass = nom.status === 'approved' ? 'success' : (nom.status === 'pending' ? 'warning' : 'danger');
    var body =
        '<p><strong><?php echo _l('membership_election'); ?>:</strong> ' + $('<span>').text(nom.election).html() + '</p>' +
        '<p><strong><?php echo _l('membership_select_member'); ?>:</strong> ' + $('<span>').text(nom.nominee).html() + '</p>' +
        '<p><strong><?php echo _l('membership_position'); ?>:</strong> ' + $('<span>').text(nom.position).html() + '</p>' +
        '<p><strong><?php echo _l('membership_status'); ?>:</strong> <span class="label label-' + statusClass + '">' + nom.status.charAt(0).toUpperCase() + nom.status.slice(1) + '</span></p>' +
        '<p><strong><?php echo _l('membership_date'); ?>:</strong> ' + nom.nominated_at + '</p>';

    if (nom.manifesto) {
        body += '<p><strong><?php echo _l('membership_manifesto'); ?>:</strong></p><div class="well well-sm" style="white-space:pre-wrap;">' + $('<span>').text(nom.manifesto).html() + '</div>';
    }

    $('#nominationDetailTitle').text(nom.election);
    $('#nominationDetailBody').html(body);
    $('#nominationDetailModal').modal('show');
}

// Pre-load elections data (current + past) to power the detail popup
var electionsData = <?php
$all_for_js = array_merge($elections, $past_elections ?? []);
echo json_encode(array_map(function($e) use ($positions_by_election) {
    return [
        'id'               => $e['id'],
        'title'            => $e['title'],
        'description'      => $e['description'],
        'start_date'       => date('M d, Y', strtotime($e['start_date'])),
        'end_date'         => date('M d, Y', strtotime($e['end_date'])),
        'status'           => $e['status'],
        'nomination_fee'   => isset($e['nomination_fee']) ? $e['nomination_fee'] : '',
        'nomination_currency' => isset($e['nomination_currency']) ? $e['nomination_currency'] : '',
        'positions'        => isset($positions_by_election[$e['id']]) ? $positions_by_election[$e['id']] : [],
    ];
}, $all_for_js));
?>;

function showElectionDetail(electionId) {
    var election = electionsData.find(function(e) { return e.id == electionId; });
    if (!election) { return; }

    $('#electionDetailTitle').text(election.title);
    var body = '<p><strong><?php echo _l('membership_description'); ?>:</strong> ' + (election.description || '-') + '</p>' +
               '<p><strong><?php echo _l('membership_period'); ?>:</strong> ' + election.start_date + ' &mdash; ' + election.end_date + '</p>' +
               '<p><strong><?php echo _l('membership_status'); ?>:</strong> ' + ucfirst(election.status) + '</p>';

    if (election.positions && election.positions.length > 0) {
        var escapedPos = $.map(election.positions, function(pos) {
            return $('<span>').text(pos).html();
        });
        body += '<p><strong><?php echo _l('membership_position'); ?>:</strong> ' + escapedPos.join(', ') + '</p>';
    } else {
        body += '<p><strong><?php echo _l('membership_position'); ?>:</strong> -</p>';
    }

    if (election.nomination_fee) {
        body += '<p><strong><?php echo _l('membership_nomination_fee'); ?>:</strong> ' + election.nomination_fee + ' ' + election.nomination_currency + '</p>';
    }
    $('#electionDetailBody').html(body);
    $('#electionDetailModal').modal('show');
}

function nomModalElectionChanged(election_id) {
    // Update fee display
    var selectedOption = document.querySelector('#nom_modal_election_id option[value="' + election_id + '"]');
    var fee = selectedOption ? selectedOption.getAttribute('data-fee') : '<?php echo $nomination_fee; ?>';
    document.getElementById('modal-total-fees').value = fee || '0.00';

    // Load positions via AJAX
    var $posSelect = $('#nom_modal_position');
    var $posCustom = $('#nom_modal_position_custom');

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

function nomModalPositionOther(val) {
    var $posSelect = $('#nom_modal_position');
    var $posCustom = $('#nom_modal_position_custom');
    if (val === '__other__') {
        $posCustom.show().attr('required', true).attr('name', 'position');
        $posSelect.removeAttr('name');
    } else {
        $posCustom.hide().removeAttr('required').removeAttr('name').val('');
        $posSelect.attr('name', 'position');
    }
}

function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Reset modal when it closes
$('#addNominationModal').on('hidden.bs.modal', function() {
    $('#nom_modal_election_id').val('');
    $('#nom_modal_position').html('<option value=""><?php echo _l('membership_select_election_first'); ?></option>').attr('name', 'position');
    $('#nom_modal_position_custom').hide().removeAttr('required').removeAttr('name').val('');
    document.getElementById('modal-total-fees').value = '<?php echo $nomination_fee; ?>';
});

$(document).ready(function() {
    // global.js applies selectpicker to ALL selects — destroy it on the election
    // and position selects so native onchange fires reliably
    if (typeof $.fn.selectpicker !== 'undefined') {
        $('#nom_modal_election_id').selectpicker('destroy');
        $('#nom_modal_position').selectpicker('destroy');
    }

    $('#addNominationModal').on('shown.bs.modal', function() {
        // Refresh the member selectpicker when modal opens
        if (typeof $.fn.selectpicker !== 'undefined') {
            $(this).find('select[name="nominated_member_id"]').selectpicker('refresh');
        }
        // Re-destroy in case selectpicker was re-applied during modal show
        if (typeof $.fn.selectpicker !== 'undefined') {
            $('#nom_modal_election_id').selectpicker('destroy');
            $('#nom_modal_position').selectpicker('destroy');
        }
    });
});
</script>
