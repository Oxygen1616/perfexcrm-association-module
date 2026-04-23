<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h4 class="tw-mt-0 tw-mb-0"><?php echo _l('membership_events'); ?></h4>
                    <a href="<?php echo admin_url('utilities/calendar'); ?>" class="btn btn-primary">
                        <i class="fa fa-calendar tw-mr-1"></i> <?php echo _l('membership_manage_in_calendar'); ?>
                    </a>
                </div>
                <p class="text-muted"><?php echo _l('membership_events_calendar_hint'); ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (!empty($events)): ?>
                        <table class="table dt-table">
                            <thead>
                                <tr>
                                    <th><?php echo _l('membership_event_title'); ?></th>
                                    <th><?php echo _l('membership_event_date'); ?></th>
                                    <th><?php echo _l('membership_event_end_date'); ?></th>
                                    <th><?php echo _l('membership_registrations'); ?></th>
                                    <th><?php echo _l('membership_event_registration_open'); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                <?php
                                $reg_cnt  = isset($reg_counts[$event['id']]) ? (int)$reg_counts[$event['id']] : 0;
                                // Default open unless explicitly closed in settings table
                                $is_open  = isset($reg_open_map[$event['id']]) ? (bool)$reg_open_map[$event['id']] : true;
                                ?>
                                <tr id="event-row-<?php echo (int)$event['id']; ?>">
                                    <td><?php echo e($event['title']); ?></td>
                                    <td><?php echo _dt($event['event_date']); ?></td>
                                    <td><?php echo $event['event_end_date'] ? _dt($event['event_end_date']) : '-'; ?></td>
                                    <td>
                                        <?php if ($reg_cnt > 0): ?>
                                            <a href="#" onclick="viewRegistrations(<?php echo (int)$event['id']; ?>, '<?php echo addslashes(e($event['title'])); ?>'); return false;"
                                               class="badge badge-primary" style="font-size:12px; cursor:pointer;">
                                                <?php echo $reg_cnt; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="#"
                                           id="reg-toggle-<?php echo (int)$event['id']; ?>"
                                           onclick="toggleEventRegistration(<?php echo (int)$event['id']; ?>); return false;"
                                           class="label <?php echo $is_open ? 'label-success' : 'label-danger'; ?>"
                                           style="cursor:pointer; font-size:11px;">
                                            <?php if ($is_open): ?>
                                                <i class="fa fa-unlock tw-mr-1"></i><?php echo _l('membership_reg_open'); ?>
                                            <?php else: ?>
                                                <i class="fa fa-lock tw-mr-1"></i><?php echo _l('membership_reg_closed'); ?>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <a href="#"
                                           onclick="view_event(<?php echo (int)$event['id']; ?>); return false;"
                                           class="btn btn-xs btn-default" title="<?php echo _l('edit'); ?>">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-center text-muted"><?php echo _l('membership_no_events'); ?></p>
                        <p class="text-center">
                            <a href="<?php echo admin_url('utilities/calendar'); ?>" class="btn btn-default">
                                <i class="fa fa-calendar tw-mr-1"></i> <?php echo _l('membership_open_calendar'); ?>
                            </a>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Perfex native event edit modal is injected here by view_event() from main.js -->
<div id="event"></div>

<!-- Registrations Modal -->
<div class="modal fade" id="registrationsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="registrationsModalTitle"></h4>
            </div>
            <div class="modal-body" id="registrationsModalBody">
                <p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEventRegistration(eventId) {
    var $btn = $('#reg-toggle-' + eventId);
    $btn.css('opacity', 0.5);
    $.getJSON('<?php echo admin_url('membership/ajax_toggle_event_registration'); ?>/' + eventId, function(data) {
        $btn.css('opacity', 1);
        if (data.open) {
            $btn.removeClass('label-danger').addClass('label-success')
                .html('<i class="fa fa-unlock" style="margin-right:3px;"></i><?php echo _l('membership_reg_open'); ?>');
        } else {
            $btn.removeClass('label-success').addClass('label-danger')
                .html('<i class="fa fa-lock" style="margin-right:3px;"></i><?php echo _l('membership_reg_closed'); ?>');
        }
    }).fail(function() {
        $btn.css('opacity', 1);
        alert_float('danger', '<?php echo _l('membership_error_loading'); ?>');
    });
}

function viewRegistrations(eventId, eventTitle) {
    $('#registrationsModalTitle').text(eventTitle + ' — <?php echo _l('membership_registrations'); ?>');
    $('#registrationsModalBody').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></p>');
    $('#registrationsModal').modal('show');

    $.getJSON('<?php echo admin_url('membership/ajax_event_registrations'); ?>/' + eventId, function(data) {
        var regs = data.registrations;
        if (!regs || regs.length === 0) {
            $('#registrationsModalBody').html('<p class="text-center text-muted"><?php echo _l('membership_no_registrations'); ?></p>');
            return;
        }
        var html = '<div class="table-responsive"><table class="table table-hover">'
                 + '<thead><tr>'
                 + '<th>#</th>'
                 + '<th><?php echo _l('membership_member_name'); ?></th>'
                 + '<th><?php echo _l('membership_email'); ?></th>'
                 + '<th><?php echo _l('membership_registered_at'); ?></th>'
                 + '</tr></thead><tbody>';
        for (var i = 0; i < regs.length; i++) {
            var r = regs[i];
            var name = ((r.firstname || '') + ' ' + (r.lastname || '')).trim() || '<span class="text-muted">—</span>';
            var email = r.email || '<span class="text-muted">—</span>';
            html += '<tr>'
                  + '<td>' + (i + 1) + '</td>'
                  + '<td>' + name + '</td>'
                  + '<td>' + email + '</td>'
                  + '<td>' + (r.registered_at || '—') + '</td>'
                  + '</tr>';
        }
        html += '</tbody></table></div>';
        $('#registrationsModalBody').html(html);
    }).fail(function() {
        $('#registrationsModalBody').html('<p class="text-center text-danger"><?php echo _l('membership_error_loading'); ?></p>');
    });
}
</script>

<?php init_tail(); ?>
