<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_final_candidates'); ?></h4>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($candidates) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_candidate_name'); ?></th>
                                        <th><?php echo _l('membership_email'); ?></th>
                                        <th><?php echo _l('membership_position'); ?></th>
                                        <th><?php echo _l('membership_symbol'); ?></th>
                                        <th><?php echo _l('membership_manifesto'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($candidates as $candidate): ?>
                                        <tr>
                                            <td><?php echo $candidate['firstname'] . ' ' . $candidate['lastname']; ?></td>
                                            <td><?php echo $candidate['email']; ?></td>
                                            <td><?php echo $candidate['position']; ?></td>
                                            <td><?php echo $candidate['symbol_name'] ?: '-'; ?></td>
                                            <td><?php echo $candidate['manifesto'] ? substr($candidate['manifesto'], 0, 100) . '...' : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_final_candidates'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
