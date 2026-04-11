<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_election_results'); ?></h4>
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
                            <?php if (isset($results) && count($results) > 0): ?>
                                <div class="mtop15">
                                    <p><strong><?php echo _l('membership_total_votes'); ?>:</strong> <?php echo isset($total_votes) ? $total_votes : 0; ?></p>
                                    <table class="table table-striped table-bordered dt-table" id="results_table">
                                        <thead>
                                            <tr>
                                                <th><?php echo _l('membership_candidate'); ?></th>
                                                <th><?php echo _l('membership_position'); ?></th>
                                                <th><?php echo _l('membership_votes'); ?></th>
                                                <th><?php echo _l('membership_percentage'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total = isset($total_votes) ? $total_votes : 0;
                                            foreach ($results as $result):
                                                $pct = $total > 0 ? round(($result['vote_count'] / $total) * 100, 2) : 0;
                                            ?>
                                            <tr>
                                                <td><?php echo e($result['firstname'] . ' ' . $result['lastname']); ?></td>
                                                <td><?php echo e($result['position'] ?? '-'); ?></td>
                                                <td><?php echo $result['vote_count']; ?></td>
                                                <td>
                                                    <div class="progress" style="margin-bottom:0;">
                                                        <div class="progress-bar" role="progressbar" style="width:<?php echo $pct; ?>%">
                                                            <?php echo $pct; ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="mtop15">
                                    <p class="text-muted"><?php echo _l('membership_no_results'); ?></p>
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
        window.location.href = '<?php echo admin_url('membership/election_results'); ?>/' + id;
    } else {
        window.location.href = '<?php echo admin_url('membership/election_results'); ?>';
    }
}

</script>
<?php init_tail(); ?>
