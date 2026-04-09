<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_jobs'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($jobs) > 0): ?>
                            <div class="row">
                                <?php foreach ($jobs as $job): ?>
                                    <div class="col-md-4">
                                        <div class="panel_s job-item">
                                            <div class="panel-body">
                                                <h5 class="job-title"><?php echo $job['title']; ?></h5>
                                                <?php if ($job['company']): ?>
                                                    <p><strong><?php echo _l('membership_job_company'); ?>:</strong> <?php echo $job['company']; ?></p>
                                                <?php endif; ?>
                                                <?php if ($job['location']): ?>
                                                    <p><strong><?php echo _l('membership_job_location'); ?>:</strong> <?php echo $job['location']; ?></p>
                                                <?php endif; ?>
                                                <?php if ($job['salary_range']): ?>
                                                    <p><strong><?php echo _l('membership_job_salary'); ?>:</strong> <?php echo $job['salary_range']; ?></p>
                                                <?php endif; ?>
                                                <p class="job-description"><?php echo nl2br($job['description']); ?></p>
                                                <div class="text-right mtop10">
                                                    <a href="<?php echo site_url('membership/jobs/' . $job['id']); ?>" class="btn btn-default btn-xs">
                                                        <i class="fa fa-eye"></i> <?php echo _l('membership_view_jobs'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_jobs'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>