<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_elections'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <a href="<?php echo admin_url('membership/add_election'); ?>" class="btn btn-primary">
                            <?php echo _l('membership_add_election'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($elections) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_election_title'); ?></th>
                                        <th><?php echo _l('membership_election_period'); ?></th>
                                        <th><?php echo _l('membership_election_status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($elections as $election): ?>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="showElectionDetails(<?php echo $election['id']; ?>)"
                                                   data-toggle="modal" data-target="#electionModal">
                                                    <?php echo e($election['title']); ?>
                                                </a>
                                                <div class="row-options">
                                                    <a href="#" onclick="editElection(<?php echo $election['id']; ?>)"
                                                       data-toggle="modal" data-target="#electionModal">
                                                        <?php echo _l('membership_edit'); ?>
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('membership/delete_election/' . $election['id']); ?>"
                                                       onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                        <?php echo _l('membership_delete'); ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo _dt($election['start_date']); ?> - <?php echo _dt($election['end_date']); ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo ($election['status'] == 'active' ? 'success' : ($election['status'] == 'draft' ? 'warning' : 'default')); ?>">
                                                    <?php echo _l('membership_election_status_' . $election['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_elections'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>