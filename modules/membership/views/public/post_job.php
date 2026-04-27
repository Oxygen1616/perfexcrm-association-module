<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <i class="fa fa-plus-circle tw-mr-2 tw-text-primary-500"></i>
                    <?php echo _l('membership_post_job'); ?>
                </h4>
            </div>
        </div>
        <div class="row tw-mt-4">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <p class="text-muted tw-mb-4">
                            <i class="fa fa-info-circle tw-mr-1"></i>
                            <?php echo _l('membership_job_post_notice'); ?>
                        </p>
                        <?php echo form_open(site_url('membership/client/post_job'), ['id' => 'post_job_form']); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('membership_job_title'); ?> *</label>
                                        <input type="text" name="title" class="form-control" required placeholder="e.g. Software Engineer">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('membership_job_company'); ?></label>
                                        <input type="text" name="company" class="form-control" placeholder="Company name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('membership_job_location'); ?></label>
                                        <input type="text" name="location" class="form-control" placeholder="City, Country or Remote">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo _l('membership_job_salary'); ?></label>
                                        <input type="text" name="salary_range" class="form-control" placeholder="e.g. $50,000 – $70,000">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('membership_job_apply_link'); ?></label>
                                <input type="url" name="external_url" class="form-control" placeholder="https://... (optional, where applicants apply)">
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('membership_job_description'); ?> *</label>
                                <textarea name="description" class="form-control" rows="8" required placeholder="Describe the role, responsibilities, and requirements..."></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane tw-mr-1"></i><?php echo _l('membership_submit_for_review'); ?>
                                </button>
                                <a href="<?php echo site_url('membership/client/jobs'); ?>" class="btn btn-default"><?php echo _l('membership_cancel'); ?></a>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
</div>
