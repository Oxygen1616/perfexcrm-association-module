<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_events'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($events) > 0): ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_event_title'); ?></th>
                                        <th><?php echo _l('membership_event_date'); ?></th>
                                        <th><?php echo _l('membership_event_location'); ?></th>
                                        <th><?php echo _l('membership_register'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($events as $event): ?>
                                        <tr>
                                            <td><?php echo $event['title']; ?></td>
                                            <td><?php echo _dt($event['event_date']); ?></td>
                                            <td><?php echo $event['location'] ?: '-'; ?></td>
                                            <td>
                                                <?php
                                                $registered = $this->membership_model->get_event_registration($event['id'], $this->contact_user_id);
                                                if ($registered) {
                                                    echo '<span class="label label-success">' . _l('membership_registered') . '</span>';
                                                } else {
                                                    // Check if event is full
                                                    if (!empty($event['max_attendees'])) {
                                                        $registered_count = $this->membership_model->get_event_registration_count($event['id']);
                                                        if ($registered_count >= $event['max_attendees']) {
                                                            echo '<span class="label label-danger">' . _l('membership_event_full') . '</span>';
                                                        } else {
                                                            ?>
                                                            <a href="<?php echo site_url('membership/register_event/' . $event['id']); ?>" class="btn btn-xs btn-primary">
                                                                <i class="fa fa-check-square-o"></i> <?php echo _l('membership_register'); ?>
                                                            </a>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <a href="<?php echo site_url('membership/register_event/' . $event['id']); ?>" class="btn btn-xs btn-primary">
                                                            <i class="fa fa-check-square-o"></i> <?php echo _l('membership_register'); ?>
                                                        </a>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_upcoming_events'); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="mtop15">
                            <h5><?php echo _l('membership_my_registrations'); ?></h5>
                            <?php if (count($my_registrations) > 0): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('membership_event_title'); ?></th>
                                            <th><?php echo _l('membership_event_date'); ?></th>
                                            <th><?php echo _l('membership_event_location'); ?></th>
                                            <th><?php echo _l('membership_qr_code'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($my_registrations as $registration): ?>
                                            <tr>
                                                <td><?php echo $registration['event_title']; ?></td>
                                                <td><?php echo _dt($registration['event_date']); ?></td>
                                                <td><?php echo $registration['location'] ?: '-'; ?></td>
                                                <td>
                                                    <?php if ($registration['qr_code']): ?>
                                                        <img src="<?php echo site_url('timthumb.php?src=' . base_url() . 'uploads/qrcodes/' . $registration['qr_code'] . '.png&h=100&w=100'); ?>" alt="QR Code" class="img-thumbnail">
                                                    <?php else: ?>
                                                        No QR Code
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted"><?php echo _l('membership_no_registrations'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>