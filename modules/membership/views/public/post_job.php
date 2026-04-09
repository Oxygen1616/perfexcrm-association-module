<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_post_job'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(site_url('membership/post_job'), ['id' => 'post_job_form']); ?>
                            <div class="form-group">
                                <label><?php echo _l('membership_job_title'); ?> *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('membership_job_company'); ?></label>
                                <input type="text" name="company" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('membership_job_location'); ?></label>
                                <input type="text" name="location" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('membership_job_salary'); ?></label>
                                <input type="text" name="salary_range" class="form-control" placeholder="e.g. $50,000 - $70,000">
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('membership_job_description'); ?> *</label>
                                <textarea name="description" class="form-control" rows="6" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit'); ?></button>
                                <a href="<?php echo site_url('membership/jobs'); ?>" class="btn btn-default"><?php echo _l('membership_cancel'); ?></a>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
