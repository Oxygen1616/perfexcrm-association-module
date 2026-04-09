<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="<?php echo $status_filter == '' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/subscription_transactions'); ?>"><?php echo _l('membership_all'); ?></a>
                    </li>
                    <li role="presentation" class="<?php echo $status_filter == 'active' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/subscription_transactions/active'); ?>"><?php echo _l('membership_active'); ?></a>
                    </li>
                    <li role="presentation" class="<?php echo $status_filter == 'expired' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/subscription_transactions/expired'); ?>"><?php echo _l('membership_expired'); ?></a>
                    </li>
                    <li role="presentation" class="<?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/subscription_transactions/cancelled'); ?>"><?php echo _l('membership_cancelled'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4><?php echo _l('membership_subscription_transactions'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php if (count($transactions) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_transaction_id'); ?></th>
                                        <th><?php echo _l('membership_transaction_member'); ?></th>
                                        <th><?php echo _l('membership_subscription_plan'); ?></th>
                                        <th><?php echo _l('membership_subscription_amount'); ?></th>
                                        <th><?php echo _l('membership_subscription_cycle'); ?></th>
                                        <th><?php echo _l('membership_subscription_dates'); ?></th>
                                        <th><?php echo _l('membership_transaction_method'); ?></th>
                                        <th><?php echo _l('membership_transaction_status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $txn): ?>
                                        <tr>
                                            <td><?php echo $txn['id']; ?></td>
                                            <td><?php echo $txn['firstname'] . ' ' . $txn['lastname']; ?></td>
                                            <td><?php echo $txn['plan_name']; ?></td>
                                            <td><?php echo app_format_money($txn['amount'], get_base_currency()); ?></td>
                                            <td><?php echo $txn['billing_cycle'] ?: '-'; ?></td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($txn['start_date'])); ?>
                                                <?php if ($txn['end_date']): ?>
                                                    - <?php echo date('M d, Y', strtotime($txn['end_date'])); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $txn['payment_method'] ?: '-'; ?></td>
                                            <td>
                                                <span class="label label-<?php 
                                                    echo $txn['status'] == 'active' ? 'success' : ($txn['status'] == 'expired' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo _l('membership_subscription_status_' . $txn['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_transactions'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
