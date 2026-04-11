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
                        <div class="row">
                            <div class="col-md-5">
                                <label><?php echo _l('membership_select_election'); ?></label>
                                <select class="form-control" id="election_filter" onchange="filterByElection(this.value)">
                                    <option value=""><?php echo _l('membership_select_election'); ?></option>
                                    <?php foreach ($elections as $e): ?>
                                        <option value="<?php echo $e['id']; ?>" <?php echo isset($selected_election) && $selected_election == $e['id'] ? 'selected' : ''; ?>>
                                            <?php echo e($e['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php if (isset($selected_election) && $selected_election): ?>
                            <?php if (isset($votes) && count($votes) > 0): ?>
                                <div class="mtop15">
                                    <table class="table table-striped table-bordered dt-table" id="votes_table">
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
                                                <td><?php echo e(($vote['voter_firstname'] ?? '') . ' ' . ($vote['voter_lastname'] ?? '')); ?></td>
                                                <td><?php echo e(($vote['candidate_firstname'] ?? '') . ' ' . ($vote['candidate_lastname'] ?? '')); ?></td>
                                                <td><?php echo e($vote['election_title'] ?? '-'); ?></td>
                                                <td><?php echo e($vote['voted_at'] ?? '-'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="mtop15">
                                    <p class="text-muted"><?php echo _l('membership_no_votes'); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="mtop15">
                                <p class="text-muted"><?php echo _l('membership_select_election'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterByElection(id) {
    if (id) {
        window.location.href = '<?php echo admin_url('membership/vote_list'); ?>/' + id;
    } else {
        window.location.href = '<?php echo admin_url('membership/vote_list'); ?>';
    }
}

</script>
<?php init_tail(); ?>
