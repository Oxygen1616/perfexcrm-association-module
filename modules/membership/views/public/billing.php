<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_billing'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5><?php echo _l('membership_invoices'); ?></h5>
                        <?php if (count($invoices) > 0): ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_invoice_number'); ?></th>
                                        <th><?php echo _l('membership_invoice_date'); ?></th>
                                        <th><?php echo _l('membership_invoice_amount'); ?></th>
                                        <th><?php echo _l('membership_invoice_status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td>#<?php echo format_invoice_number($invoice['id']); ?></td>
                                            <td><?php echo _d($invoice['date']); ?></td>
                                            <td><?php echo format_money($invoice['total']); ?></td>
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
                            <div class="text-center">
                                <p><?php echo _l('membership_no_invoices'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>