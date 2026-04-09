<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_dashboard'); ?></h4>

<div class="row tw-mt-4">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-600"><?= _l('membership_activity_feed'); ?></h5>
                <?php if (count($activity_feed) > 0): ?>
                    <?php foreach ($activity_feed as $activity): ?>
                        <div class="tw-flex tw-items-start tw-gap-3 tw-py-3 tw-border-b tw-border-neutral-100">
                            <div class="tw-mt-1">
                                <?php
                                $icon = 'fa-briefcase';
                                switch ($activity['type']) {
                                    case 'job':    $icon = 'fa-briefcase'; break;
                                    case 'story':  $icon = 'fa-pencil-alt'; break;
                                    case 'event':  $icon = 'fa-calendar-alt'; break;
                                }
                                ?>
                                <i class="fa <?= $icon; ?> tw-text-primary-500"></i>
                            </div>
                            <div>
                                <strong>
                                    <?php
                                    switch ($activity['type']) {
                                        case 'job':   echo _l('membership_new_job'); break;
                                        case 'story': echo _l('membership_new_story'); break;
                                        case 'event': echo _l('membership_upcoming_event'); break;
                                    }
                                    ?>
                                </strong>
                                <p class="tw-text-sm tw-text-neutral-500"><?= e($activity['data']['title']); ?></p>
                                <small class="tw-text-neutral-400"><?= _dt($activity['date']); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted"><?= _l('membership_no_activity'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row tw-mt-4">
    <div class="col-md-6">
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-600"><?= _l('membership_upcoming_events'); ?></h5>
                <?php if (count($upcoming_events) > 0): ?>
                    <table class="table table-hover tw-mt-2">
                        <thead>
                            <tr>
                                <th><?= _l('membership_event_title'); ?></th>
                                <th><?= _l('membership_event_date'); ?></th>
                                <th><?= _l('membership_event_location'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcoming_events as $event): ?>
                                <tr>
                                    <td><?= e($event['title']); ?></td>
                                    <td><?= _dt($event['event_date']); ?></td>
                                    <td><?= $event['location'] ?: '-'; ?></td>
                                    <td>
                                        <?php if (!empty($my_registrations[$event['id']])): ?>
                                            <span class="label label-success"><?= _l('membership_registered'); ?></span>
                                        <?php else: ?>
                                            <a href="<?= site_url('membership/register_event/' . $event['id']); ?>" class="btn btn-xs btn-primary">
                                                <?= _l('membership_register'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted"><?= _l('membership_no_upcoming_events'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-600"><?= _l('membership_invoices'); ?></h5>
                <?php
                // Load invoice model to get invoices
                $this->load->model('invoices_model');
                $invoices = $this->invoices_model->get('', [
                    'clientid' => get_client_user_id(),
                ]);
                ?>
                <?php if (count($invoices) > 0): ?>
                    <table class="table table-hover tw-mt-2">
                        <thead>
                            <tr>
                                <th><?= _l('membership_invoice_number'); ?></th>
                                <th><?= _l('membership_invoice_date'); ?></th>
                                <th><?= _l('membership_invoice_amount'); ?></th>
                                <th><?= _l('membership_invoice_status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td>#<?php echo format_invoice_number($invoice['id']); ?></td>
                                    <td><?php echo _d($invoice['date']); ?></td>
                                    <td><?php echo app_format_money($invoice['total']); ?></td>
                                    <td>
                                        <span class="label label-<?php echo ($invoice['status'] == 3 ? 'success' : ($invoice['status'] == 4 ? 'default' : ($invoice['status'] == 1 ? 'warning' : 'danger'))); ?>">
                                            <?php echo format_invoice_status($invoice['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted"><?= _l('membership_no_invoices'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
