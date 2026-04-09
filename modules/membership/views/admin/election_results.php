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
                            <div class="col-md-6">
                                <label><?php echo _l('membership_select_election'); ?></label>
                                <select class="form-control" onchange="window.location.href='<?php echo admin_url('membership/election_results'); ?>/'+this.value">
                                    <option value=""><?php echo _l('membership_select_election'); ?></option>
                                    <?php foreach ($elections as $e): ?>
                                        <option value="<?php echo $e['id']; ?>" <?php echo isset($selected_election) && $selected_election == $e['id'] ? 'selected' : ''; ?>>
                                            <?php echo $e['title']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php if (isset($results) && count($results) > 0): ?>
                            <div class="row mtop15">
                                <div class="col-md-12">
                                    <h5><?php echo _l('membership_total_votes'); ?>: <?php echo $total_votes; ?></h5>
                                </div>
                            </div>
                            <table class="table dt-table mtop15">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_candidate'); ?></th>
                                        <th><?php echo _l('membership_votes'); ?></th>
                                        <th><?php echo _l('membership_percentage'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $result): ?>
                                        <?php $percentage = $total_votes > 0 ? round(($result['vote_count'] / $total_votes) * 100, 2) : 0; ?>
                                        <tr>
                                            <td><?php echo $result['firstname'] . ' ' . $result['lastname']; ?></td>
                                            <td><?php echo $result['vote_count']; ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%">
                                                        <?php echo $percentage; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php elseif(isset($selected_election)): ?>
                            <div class="text-center mtop15">
                                <p><?php echo _l('membership_no_results'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
