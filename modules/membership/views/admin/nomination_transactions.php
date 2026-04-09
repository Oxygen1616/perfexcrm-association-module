<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="<?php echo $status_filter == '' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/nomination_transactions'); ?>"><?php echo _l('membership_all'); ?></a>
                    </li>
                    <li role="presentation" class="<?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/nomination_transactions/pending'); ?>"><?php echo _l('membership_pending'); ?></a>
                    </li>
                    <li role="presentation" class="<?php echo $status_filter == 'completed' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/nomination_transactions/completed'); ?>"><?php echo _l('membership_completed'); ?></a>
                    </li>
                    <li role="presentation" class="<?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">
                        <a href="<?php echo admin_url('membership/nomination_transactions/cancelled'); ?>"><?php echo _l('membership_cancelled'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4><?php echo _l('membership_nomination_transactions'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php if (count($transactions) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_transaction_id'); ?></th>
                                        <th><?php echo _l('membership_member_name'); ?></th>
                                        <th><?php echo _l('membership_position'); ?></th>
                                        <th><?php echo _l('membership_amount'); ?></th>
                                        <th><?php echo _l('membership_payment_method'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                        <th><?php echo _l('membership_date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $txn): ?>
                                        <tr>
                                            <td><?php echo $txn['id']; ?></td>
                                            <td><?php echo $txn['firstname'] . ' ' . $txn['lastname']; ?></td>
                                            <td><?php echo $txn['position'] ?: '-'; ?></td>
                                            <td><?php echo app_format_money($txn['amount'], get_base_currency()); ?></td>
                                            <td><?php echo $txn['payment_method'] ?: '-'; ?></td>
                                            <td>
                                                <span class="label label-<?php echo $txn['status'] == 'completed' ? 'success' : ($txn['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                    <?php echo ucfirst($txn['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($txn['transaction_date'])); ?></td>
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
