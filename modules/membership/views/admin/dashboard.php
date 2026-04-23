<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h1 class="mtop0"><?php echo $members_count; ?></h1>
                        <p><?php echo _l('membership_total_members'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h1 class="mtop0 text-warning"><?php echo $pending_count; ?></h1>
                        <p><?php echo _l('membership_pending_members'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h1 class="mtop0 text-success"><?php echo $active_count; ?></h1>
                        <p><?php echo _l('membership_active_members'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo _l('membership_upcoming_events'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php if (count($upcoming_events) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_event_title'); ?></th>
                                        <th><?php echo _l('membership_event_date'); ?></th>
                                        <th><?php echo _l('membership_event_end_date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_events as $event): ?>
                                        <tr>
                                            <td><?php echo e($event['title']); ?></td>
                                            <td><?php echo _dt($event['event_date']); ?></td>
                                            <td><?php echo $event['event_end_date'] ? _dt($event['event_end_date']) : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted"><?php echo _l('membership_no_upcoming_events'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>