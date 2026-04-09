<?php if (isset($results) && count($results) > 0): ?>
    <div class="row mtop15">
        <div class="col-md-12">
            <h5><?= _l('membership_total_votes'); ?>: <?= $total_votes; ?></h5>
        </div>
    </div>
    <table class="table dt-table mtop15">
        <thead>
            <tr>
                <th><?= _l('membership_candidate'); ?></th>
                <th><?= _l('membership_votes'); ?></th>
                <th><?= _l('membership_percentage'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <?php $percentage = $total_votes > 0 ? round(($result['vote_count'] / $total_votes) * 100, 2) : 0; ?>
                <tr>
                    <td><?= $result['firstname'] . ' ' . $result['lastname']; ?></td>
                    <td><?= $result['vote_count']; ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage; ?>%">
                                <?= $percentage; ?>%
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif(isset($selected_election)): ?>
    <div class="text-center mtop15">
        <p><?= _l('membership_no_results'); ?></p>
    </div>
<?php endif; ?>