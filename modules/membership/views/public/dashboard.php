<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_dashboard'); ?></h4>

<!-- ── Row 1: Pending Actions + Activity Feed ────────────────────────── -->
<div class="row tw-mt-4">

    <!-- Pending Actions -->
    <div class="col-md-6">
        <div class="panel_s">
            <div class="panel-heading">
                <h5 class="tw-font-semibold tw-text-neutral-600 tw-mb-0">
                    <i class="fa fa-exclamation-circle tw-mr-1 tw-text-red-500"></i>
                    <?= _l('membership_pending_actions'); ?>
                    <?php if (!empty($pending_actions)): ?>
                        <span class="badge" style="background:#e53e3e;"><?= count($pending_actions); ?></span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="panel-body">
                <?php if (!empty($pending_actions)): ?>
                    <?php foreach ($pending_actions as $action): ?>
                        <div class="tw-flex tw-items-center tw-gap-3 tw-py-2 tw-border-b tw-border-neutral-100">
                            <i class="fa <?= $action['icon']; ?> <?= $action['color']; ?> tw-w-4 tw-text-center tw-shrink-0"></i>
                            <div class="tw-flex-1 tw-min-w-0">
                                <p class="tw-text-sm tw-text-neutral-700 tw-mb-0"><?= $action['text']; ?></p>
                            </div>
                            <a href="<?= $action['url']; ?>" class="btn btn-xs <?= $action['btn']; ?> tw-shrink-0">
                                <?= $action['label']; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted tw-text-sm tw-py-2">
                        <i class="fa fa-check-circle text-success tw-mr-1"></i><?= _l('membership_no_pending_actions'); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="col-md-6">
        <div class="panel_s">
            <div class="panel-heading">
                <h5 class="tw-font-semibold tw-text-neutral-600 tw-mb-0">
                    <i class="fa fa-bell-o tw-mr-1 tw-text-primary-500"></i>
                    <?= _l('membership_activity_feed'); ?>
                </h5>
            </div>
            <div class="panel-body">
                <?php if (!empty($activity_feed)): ?>
                    <?php foreach ($activity_feed as $activity): ?>
                        <?php
                        $d = $activity['data'];
                        switch ($activity['type']) {
                            case 'job':
                                $icon  = 'fa-briefcase';
                                $color = 'tw-text-blue-500';
                                $badge = '<span class="label label-info" style="font-size:10px;">' . _l('membership_job') . '</span>';
                                $url   = site_url('membership/client/jobs');
                                break;
                            case 'story':
                                $icon  = 'fa-newspaper-o';
                                $color = 'tw-text-green-500';
                                $badge = '<span class="label label-success" style="font-size:10px;">' . _l('membership_story') . '</span>';
                                $url   = site_url('membership/client/stories');
                                break;
                            case 'event':
                                $icon  = 'fa-calendar-o';
                                $color = 'tw-text-purple-500';
                                $badge = '<span class="label label-primary" style="font-size:10px;">' . _l('membership_event') . '</span>';
                                $url   = site_url('membership/client/events');
                                break;
                            case 'new_member':
                                $icon  = 'fa-user-plus';
                                $color = 'tw-text-teal-500';
                                $badge = '<span class="label label-default" style="font-size:10px;">' . _l('membership_member') . '</span>';
                                $url   = site_url('membership/client/board_members');
                                break;
                            case 'election':
                                $icon  = 'fa-bullhorn';
                                $color = 'tw-text-orange-500';
                                $badge = '<span class="label label-warning" style="font-size:10px;">' . _l('membership_election') . '</span>';
                                $url   = site_url('membership/client/cast_vote');
                                break;
                            case 'announcement':
                                $pri   = $d['priority'] ?? 'normal';
                                $icon  = 'fa-bell';
                                $color = $pri === 'urgent' ? 'tw-text-red-500' : ($pri === 'important' ? 'tw-text-amber-500' : 'tw-text-neutral-500');
                                $badge = '<span class="label label-' . ($pri === 'urgent' ? 'danger' : ($pri === 'important' ? 'warning' : 'default')) . '" style="font-size:10px;">' . ucfirst($pri) . '</span>';
                                $url   = '';
                                break;
                            default:
                                $icon  = 'fa-circle';
                                $color = 'tw-text-neutral-400';
                                $badge = '';
                                $url   = '';
                        }
                        ?>
                        <div class="tw-flex tw-items-start tw-gap-3 tw-py-2 tw-border-b tw-border-neutral-100">
                            <i class="fa <?= $icon; ?> <?= $color; ?> tw-mt-1 tw-w-4 tw-text-center tw-shrink-0"></i>
                            <div class="tw-flex-1 tw-min-w-0">
                                <div class="tw-flex tw-items-center tw-gap-2 tw-flex-wrap">
                                    <?= $badge; ?>
                                    <?php if ($url): ?>
                                        <a href="<?= $url; ?>" class="tw-text-sm tw-font-medium tw-text-neutral-700 tw-mb-0 tw-truncate hover:tw-text-primary-600" style="text-decoration:none;">
                                            <?= e($d['title']); ?>
                                        </a>
                                    <?php else: ?>
                                        <p class="tw-text-sm tw-font-medium tw-text-neutral-700 tw-mb-0 tw-truncate">
                                            <?= e($d['title']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <small class="tw-text-neutral-400"><?= _dt($activity['date']); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted tw-text-sm tw-py-2"><?= _l('membership_no_activity'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- ── Row 2: Upcoming Events ────────────────────────────────────────── -->
<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-heading">
                <h5 class="tw-font-semibold tw-text-neutral-600 tw-mb-0">
                    <i class="fa fa-calendar tw-mr-1 tw-text-primary-500"></i>
                    <?= _l('membership_upcoming_events'); ?>
                    <a href="<?= site_url('membership/client/events'); ?>" class="tw-text-xs tw-font-normal tw-text-primary-500 pull-right tw-mt-1">
                        <?= _l('membership_view_all_events'); ?> &rarr;
                    </a>
                </h5>
            </div>
            <div class="panel-body">
                <?php
                $has_any = !empty($this_week_events) || !empty($next_week_events) || !empty($later_events);

                $render_event_row = function($event) use ($my_registrations, $member, $reg_open_map) {
                    $reg_open = isset($reg_open_map[$event['id']]) ? (bool)$reg_open_map[$event['id']] : true;
                ?> <tr>
                        <td class="tw-font-medium"><?= e($event['title']); ?></td>
                        <td class="tw-text-sm tw-text-neutral-500">
                            <i class="fa fa-clock-o tw-mr-1"></i><?= _dt($event['event_date']); ?>
                        </td>
                        <td class="tw-text-right">
                            <?php if (!empty($my_registrations[$event['id']])): ?>
                                <span class="label label-success"><?= _l('membership_registered'); ?></span>
                            <?php elseif (!$reg_open): ?>
                                <span class="label label-default" title="<?= _l('membership_event_registration_closed_msg'); ?>">
                                    <i class="fa fa-lock tw-mr-1"></i><?= _l('membership_closed'); ?>
                                </span>
                            <?php elseif ($member && $member['status'] == 'active'): ?>
                                <a href="<?= site_url('membership/client/register_event/' . $event['id']); ?>" class="btn btn-xs btn-primary">
                                    <i class="fa fa-check-square tw-mr-1"></i><?= _l('membership_register'); ?>
                                </a>
                            <?php else: ?>
                                <span class="label label-default"><?= _l('membership_view_details'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php };
                ?>

                <?php if ($has_any): ?>
                    <div class="row">

                        <!-- This Week -->
                        <div class="col-md-4">
                            <p class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                <?= _l('membership_this_week'); ?>
                                <span class="badge badge-primary"><?= count($this_week_events); ?></span>
                            </p>
                            <?php if (!empty($this_week_events)): ?>
                                <table class="table table-hover tw-mb-0">
                                    <tbody>
                                        <?php foreach ($this_week_events as $event): $render_event_row($event); endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="tw-text-sm text-muted"><?= _l('membership_no_events_this_week'); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Next Week -->
                        <div class="col-md-4">
                            <p class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                <?= _l('membership_next_week'); ?>
                                <span class="badge"><?= count($next_week_events); ?></span>
                            </p>
                            <?php if (!empty($next_week_events)): ?>
                                <table class="table table-hover tw-mb-0">
                                    <tbody>
                                        <?php foreach ($next_week_events as $event): $render_event_row($event); endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="tw-text-sm text-muted"><?= _l('membership_no_events_next_week'); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Later -->
                        <div class="col-md-4">
                            <p class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                <?= _l('membership_later_events'); ?>
                                <span class="badge"><?= count($later_events); ?></span>
                            </p>
                            <?php if (!empty($later_events)): ?>
                                <table class="table table-hover tw-mb-0">
                                    <tbody>
                                        <?php foreach ($later_events as $event): $render_event_row($event); endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="tw-text-sm text-muted"><?= _l('membership_later_events_none'); ?></p>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php else: ?>
                    <p class="text-center text-muted"><?= _l('membership_no_upcoming_events'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Row 3: Invoices ───────────────────────────────────────────────── -->
<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-heading">
                <h5 class="tw-font-semibold tw-text-neutral-600 tw-mb-0">
                    <i class="fa fa-file-text-o tw-mr-1 tw-text-primary-500"></i>
                    <?= _l('membership_invoices'); ?>
                    <a href="<?= site_url('clients/statement'); ?>" class="tw-text-xs tw-font-normal tw-text-primary-500 pull-right tw-mt-1">
                        <i class="fa fa-book tw-mr-1"></i><?= _l('view_account_statement'); ?> &rarr;
                    </a>
                </h5>
            </div>
            <div class="panel-body">
                <?php
                $this->load->model('invoices_model');
                $invoices = $this->invoices_model->get('', ['clientid' => get_client_user_id()]);
                // Perfex status constants: 1=Unpaid, 2=Paid, 3=Partially Paid, 4=Overdue, 5=Cancelled, 6=Draft
                $unpaid_statuses = [
                    Invoices_model::STATUS_UNPAID,    // 1
                    Invoices_model::STATUS_PARTIALLY, // 3
                    Invoices_model::STATUS_OVERDUE,   // 4
                ];
                ?>
                <?php if (!empty($invoices)): ?>
                    <div class="table-responsive">
                    <table class="table table-hover tw-mb-0">
                        <thead>
                            <tr>
                                <th><?= _l('membership_invoice_number'); ?></th>
                                <th class="hidden-xs"><?= _l('membership_invoice_date'); ?></th>
                                <th><?= _l('membership_invoice_amount'); ?></th>
                                <th><?= _l('membership_invoice_status'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                                <?php
                                $inv_status = (int)$invoice['status'];
                                $inv_label  = $inv_status === Invoices_model::STATUS_PAID      ? 'success'
                                            : ($inv_status === Invoices_model::STATUS_CANCELLED ? 'default'
                                            : ($inv_status === Invoices_model::STATUS_UNPAID    ? 'warning'
                                            : 'danger'));
                                $inv_url    = site_url('invoice/' . $invoice['id'] . '/' . $invoice['hash']);
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?= $inv_url; ?>" class="invoice-number">
                                            #<?= format_invoice_number($invoice['id']); ?>
                                        </a>
                                        <div class="visible-xs tw-text-xs text-muted"><?= _d($invoice['date']); ?></div>
                                    </td>
                                    <td class="hidden-xs"><?= _d($invoice['date']); ?></td>
                                    <td><?= app_format_money($invoice['total'], get_base_currency()); ?></td>
                                    <td>
                                        <span class="label label-<?= $inv_label; ?>">
                                            <?= format_invoice_status($invoice['status']); ?>
                                        </span>
                                    </td>
                                    <td class="tw-text-right" style="white-space:nowrap;">
                                        <?php if (in_array($inv_status, $unpaid_statuses)): ?>
                                            <a href="<?= $inv_url; ?>" class="btn btn-xs btn-danger">
                                                <i class="fa fa-credit-card tw-mr-1"></i><?= _l('membership_pay_now'); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= $inv_url; ?>" class="btn btn-xs btn-default">
                                                <i class="fa fa-eye tw-mr-1"></i><?= _l('view'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <div class="tw-text-right tw-mt-1">
                        <a href="<?= site_url('clients/invoices'); ?>" class="tw-text-xs tw-text-primary-500">
                            <?= _l('clients_my_invoices'); ?> &rarr;
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted"><?= _l('membership_no_invoices'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
