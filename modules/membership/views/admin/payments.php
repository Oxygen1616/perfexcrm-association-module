<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_payments'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <!-- No add payment button as payments are typically generated from invoices -->
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($payments) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_payment_member'); ?></th>
                                        <th><?php echo _l('membership_payment_amount'); ?></th>
                                        <th><?php echo _l('membership_payment_date'); ?></th>
                                        <th><?php echo _l('membership_payment_method'); ?></th>
                                        <th><?php echo _l('membership_payment_status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <?php
                                        $this->load->model('membership/membership_model');
                                        $member = $this->membership_model->get_member($payment['member_id']);
                                        $this->load->model('clients_model');
                                        $contact = $member ? $this->clients_model->get_contact($member['contact_id']) : null;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php if ($contact): ?>
                                                    <?php echo $contact->firstname . ' ' . $contact->lastname; ?>
                                                <?php else: ?>
                                                    Unknown
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo format_money($payment['amount']); ?></td>
                                            <td><?php echo _d($payment['payment_date']); ?></td>
                                            <td><?php echo $payment['payment_method'] ?: '-'; ?></td>
                                            <td>
                                                <span class="label label-<?php echo ($payment['status'] == 'completed' ? 'success' : ($payment['status'] == 'failed' ? 'danger' : 'warning')); ?>">
                                                    <?php echo _l('membership_payment_status_' . $payment['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_payments'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>