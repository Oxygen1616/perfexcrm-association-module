<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
    <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_billing'); ?></h4>
    <a href="<?= site_url('clients/statement'); ?>" class="btn btn-default btn-sm">
        <i class="fa fa-book tw-mr-1"></i><?= _l('view_account_statement'); ?>
    </a>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h5 class="tw-font-semibold tw-text-neutral-600 tw-mb-3">
                    <i class="fa fa-file-text-o tw-mr-1 tw-text-primary-500"></i>
                    <?= _l('membership_invoices'); ?>
                </h5>
                <?php
                // Perfex status constants: 1=Unpaid, 2=Paid, 3=Partially Paid, 4=Overdue, 5=Cancelled, 6=Draft
                $unpaid_statuses = [
                    Invoices_model::STATUS_UNPAID,    // 1
                    Invoices_model::STATUS_PARTIALLY, // 3
                    Invoices_model::STATUS_OVERDUE,   // 4
                ];
                ?>
                <?php if (!empty($invoices)): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?= _l('membership_invoice_number'); ?></th>
                                <th><?= _l('membership_invoice_date'); ?></th>
                                <th><?= _l('invoice_due_date'); ?></th>
                                <th><?= _l('membership_invoice_amount'); ?></th>
                                <th><?= _l('membership_invoice_status'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td>
                                        <a href="<?= site_url('invoice/' . $invoice['id'] . '/' . $invoice['hash']); ?>" class="invoice-number">
                                            #<?= format_invoice_number($invoice['id']); ?>
                                        </a>
                                    </td>
                                    <td><?= _d($invoice['date']); ?></td>
                                    <td><?= !empty($invoice['duedate']) ? _d($invoice['duedate']) : '-'; ?></td>
                                    <td><?= app_format_money($invoice['total'], get_base_currency()); ?></td>
                                    <td>
                                        <?php
                                        $inv_status = (int)$invoice['status'];
                                        $inv_label  = $inv_status === Invoices_model::STATUS_PAID       ? 'success'
                                                    : ($inv_status === Invoices_model::STATUS_CANCELLED  ? 'default'
                                                    : ($inv_status === Invoices_model::STATUS_UNPAID     ? 'warning'
                                                    : 'danger'));
                                        ?>
                                        <span class="label label-<?= $inv_label; ?>">
                                            <?= format_invoice_status($invoice['status']); ?>
                                        </span>
                                    </td>
                                    <td class="tw-text-right">
                                        <?php if (in_array($invoice['status'], $unpaid_statuses)): ?>
                                            <a href="<?= site_url('invoice/' . $invoice['id'] . '/' . $invoice['hash']); ?>" class="btn btn-xs btn-danger">
                                                <i class="fa fa-credit-card tw-mr-1"></i><?= _l('membership_pay_now'); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= site_url('invoice/' . $invoice['id'] . '/' . $invoice['hash']); ?>" class="btn btn-xs btn-default">
                                                <i class="fa fa-eye tw-mr-1"></i><?= _l('view'); ?>
                                            </a>
                                        <?php endif; ?>
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
