<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_settings'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('membership/settings')); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="monthly_dues"><?php echo _l('membership_setting_monthly_dues'); ?></label>
                                        <input type="number" class="form-control" id="monthly_dues" name="monthly_dues" value="<?php echo get_option('membership_monthly_dues') ?: '30.00'; ?>" step="0.01" min="0">
                                        <small class="text-muted"><?php echo _l('membership_currency_helper'); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="default_type"><?php echo _l('membership_setting_default_type'); ?></label>
                                        <input type="text" class="form-control" id="default_type" name="default_type" value="<?php echo get_option('membership_default_type') ?: 'Regular'; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="auto_approve_jobs" id="auto_approve_jobs" value="1" <?php echo get_option('membership_auto_approve_jobs') ? 'checked' : ''; ?>>
                                            <label for="auto_approve_jobs"><?php echo _l('membership_setting_auto_approve_jobs'); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="auto_approve_stories" id="auto_approve_stories" value="1" <?php echo get_option('membership_auto_approve_stories') ? 'checked' : ''; ?>>
                                            <label for="auto_approve_stories"><?php echo _l('membership_setting_auto_approve_stories'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mtop15"><?php echo _l('membership_save_settings'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>