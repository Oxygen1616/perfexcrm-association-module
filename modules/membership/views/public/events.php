<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>

        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <i class="fa fa-calendar tw-mr-2 tw-text-primary-500"></i>
                    <?= _l('membership_events'); ?>
                </h4>
            </div>
        </div>

        <?php
        // Helper to render a single event row
        function render_event_row($event, $my_registrations, $member, $reg_open_map = []) {
            // Default open unless explicitly set to 0 in the map
            $registration_open = isset($reg_open_map[$event['id']]) ? (bool)$reg_open_map[$event['id']] : true;
        ?>
            <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-3 tw-py-3 tw-border-b tw-border-neutral-100 last:tw-border-0">
                <!-- Date badge -->
                <div class="tw-text-center tw-shrink-0" style="min-width:48px;">
                    <div class="tw-bg-primary-500 tw-text-white tw-rounded tw-px-2 tw-py-1 tw-text-xs tw-font-bold">
                        <?= date('M', strtotime($event['event_date'])); ?>
                    </div>
                    <div class="tw-text-xl tw-font-bold tw-text-neutral-700 tw-leading-tight">
                        <?= date('d', strtotime($event['event_date'])); ?>
                    </div>
                </div>
                <!-- Details -->
                <div class="tw-flex-1 tw-min-w-0">
                    <p class="tw-font-semibold tw-text-neutral-800 tw-mb-0"><?= e($event['title']); ?></p>
                    <small class="tw-text-neutral-500">
                        <i class="fa fa-clock-o tw-mr-1"></i><?= date('H:i', strtotime($event['event_date'])); ?>
                        <?php if (!empty($event['event_end_date'])): ?>
                            &nbsp;&ndash;&nbsp;<?= date('H:i', strtotime($event['event_end_date'])); ?>
                        <?php endif; ?>
                    </small>
                    <?php if (!empty($event['description'])): ?>
                        <p class="tw-text-sm tw-text-neutral-500 tw-mt-1 tw-mb-0"><?= e(substr($event['description'], 0, 120)) . (strlen($event['description']) > 120 ? '…' : ''); ?></p>
                    <?php endif; ?>
                </div>
                <!-- Action -->
                <div class="tw-shrink-0">
                    <?php if (!empty($my_registrations[$event['id']])): ?>
                        <span class="label label-success"><i class="fa fa-check tw-mr-1"></i><?= _l('membership_registered'); ?></span>
                    <?php elseif (!$registration_open): ?>
                        <span class="label label-default" title="<?= _l('membership_event_registration_closed_msg'); ?>">
                            <i class="fa fa-lock tw-mr-1"></i><?= _l('membership_closed'); ?>
                        </span>
                    <?php elseif ($member && $member['status'] == 'active'): ?>
                        <a href="<?= site_url('membership/client/register_event/' . $event['id']); ?>" class="btn btn-xs btn-primary">
                            <i class="fa fa-check-square-o"></i> <?= _l('membership_register'); ?>
                        </a>
                    <?php else: ?>
                        <span class="label label-default"><?= _l('membership_members_only'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>

        <!-- ── This Week ─────────────────────────────────────────── -->
        <div class="row tw-mt-4">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-gap-3 tw-mb-3">
                            <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                                <i class="fa fa-star tw-mr-1 tw-text-amber-500"></i>
                                <?= _l('membership_this_week'); ?>
                            </h5>
                            <span class="badge badge-primary"><?= count($this_week_events); ?></span>
                        </div>
                        <?php if (!empty($this_week_events)): ?>
                            <?php foreach ($this_week_events as $event): ?>
                                <?php render_event_row($event, $my_registrations, $member, $reg_open_map); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-muted tw-py-4"><?= _l('membership_no_events_this_week'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Next Week ─────────────────────────────────────────── -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-gap-3 tw-mb-3">
                            <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                                <i class="fa fa-calendar-o tw-mr-1 tw-text-blue-500"></i>
                                <?= _l('membership_next_week'); ?>
                            </h5>
                            <span class="badge"><?= count($next_week_events); ?></span>
                        </div>
                        <?php if (!empty($next_week_events)): ?>
                            <?php foreach ($next_week_events as $event): ?>
                                <?php render_event_row($event, $my_registrations, $member, $reg_open_map); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-muted tw-py-4"><?= _l('membership_no_events_next_week'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Later / All Upcoming ──────────────────────────────── -->
        <?php if (!empty($later_events)): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-items-center tw-gap-3 tw-mb-3">
                            <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-0">
                                <i class="fa fa-calendar-plus-o tw-mr-1 tw-text-neutral-500"></i>
                                <?= _l('membership_later_events'); ?>
                            </h5>
                            <span class="badge"><?= count($later_events); ?></span>
                        </div>
                        <?php foreach ($later_events as $event): ?>
                            <?php render_event_row($event, $my_registrations, $member, $reg_open_map); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── My Registrations ──────────────────────────────────── -->
        <?php if (!empty($my_registrations)): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-3">
                            <i class="fa fa-ticket tw-mr-1 tw-text-green-500"></i>
                            <?= _l('membership_my_registrations'); ?>
                        </h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?= _l('membership_event_title'); ?></th>
                                    <th><?= _l('membership_event_date'); ?></th>
                                    <th><?= _l('membership_qr_code'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_values($my_registrations) as $registration): ?>
                                    <tr>
                                        <td><?= e($registration['event_title']); ?></td>
                                        <td><?= _dt($registration['event_date']); ?></td>
                                        <td>
                                            <?php if ($registration['qr_code']): ?>
                                                <img src="<?= site_url('timthumb.php?src=' . base_url() . 'uploads/qrcodes/' . $registration['qr_code'] . '.png&h=80&w=80'); ?>"
                                                     alt="QR Code" class="img-thumbnail" style="max-width:80px;">
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

</div>
