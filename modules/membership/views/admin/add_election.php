<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('membership_add_election'); ?></h4>
                        <?php echo form_open(admin_url('membership/add_election')); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_election_title'); ?> *</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_election_status'); ?></label>
                                    <select name="status" class="form-control">
                                        <option value="draft"><?php echo _l('membership_election_status_draft'); ?></option>
                                        <option value="active"><?php echo _l('membership_election_status_active'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_start_date'); ?> *</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_end_date'); ?> *</label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo _l('membership_description'); ?></label>
                                    <textarea name="description" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit'); ?></button>
                        <a href="<?php echo admin_url('membership/elections'); ?>" class="btn btn-default"><?php echo _l('membership_cancel'); ?></a>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
