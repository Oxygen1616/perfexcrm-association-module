<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_vote_history'); ?></h4>
<div class="panel_s tw-mt-4">
    <div class="panel-body">
        <?php if (count($vote_history) > 0): ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><?= _l('membership_election'); ?></th>
                        <th><?= _l('membership_candidate'); ?></th>
                        <th><?= _l('membership_position'); ?></th>
                        <th><?= _l('membership_voted_at'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vote_history as $vote): ?>
                        <tr>
                            <td><?= e($vote['election_title']); ?></td>
                            <td><?= e($vote['candidate_firstname'] . ' ' . $vote['candidate_lastname']); ?></td>
                            <td><?= e($vote['candidate_position']); ?></td>
                            <td><?= _dt($vote['voted_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted"><?= _l('membership_no_votes'); ?></p>
        <?php endif; ?>
    </div>
</div>