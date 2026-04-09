<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_vote_list'); ?></h4>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($votes) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_voter'); ?></th>
                                        <th><?php echo _l('membership_candidate'); ?></th>
                                        <th><?php echo _l('membership_election'); ?></th>
                                        <th><?php echo _l('membership_voted_at'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($votes as $vote): ?>
                                        <tr>
                                            <td><?php echo isset($vote['voter_firstname']) ? $vote['voter_firstname'] . ' ' . $vote['voter_lastname'] : '-'; ?></td>
                                            <td><?php echo isset($vote['candidate_firstname']) ? $vote['candidate_firstname'] . ' ' . $vote['candidate_lastname'] : '-'; ?></td>
                                            <td><?php echo $vote['election_title'] ?: '-'; ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($vote['voted_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_votes'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
