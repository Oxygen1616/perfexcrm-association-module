<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_all_transactions'); ?></h4>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($transactions) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_transaction_id'); ?></th>
                                        <th><?php echo _l('membership_transaction_member'); ?></th>
                                        <th><?php echo _l('membership_transaction_type'); ?></th>
                                        <th><?php echo _l('membership_transaction_description'); ?></th>
                                        <th><?php echo _l('membership_transaction_amount'); ?></th>
                                        <th><?php echo _l('membership_transaction_method'); ?></th>
                                        <th><?php echo _l('membership_transaction_status'); ?></th>
                                        <th><?php echo _l('membership_transaction_date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $txn): ?>
                                        <tr>
                                            <td><?php echo $txn['id']; ?></td>
                                            <td><?php echo $txn['firstname'] . ' ' . $txn['lastname']; ?></td>
                                            <td>
                                                <span class="label label-<?php 
                                                    echo $txn['type'] == 'membership' ? 'info' : ($txn['type'] == 'event' ? 'warning' : 'success'); 
                                                ?>">
                                                    <?php echo ucfirst($txn['type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $txn['description']; ?></td>
                                            <td><?php echo app_format_money($txn['amount'], get_base_currency()); ?></td>
                                            <td><?php echo $txn['payment_method'] ?: '-'; ?></td>
                                            <td>
                                                <span class="label label-<?php 
                                                    echo $txn['status'] == 'completed' || $txn['status'] == 'active' ? 'success' : ($txn['status'] == 'pending' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo _l('membership_transaction_status_' . $txn['status']); ?>
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
