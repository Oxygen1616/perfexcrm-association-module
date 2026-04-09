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