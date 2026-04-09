<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('membership_add_member'); ?></h4>
                        <?php echo form_open(admin_url('membership/add_member')); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_select_contact'); ?> *</label>
                                    <select name="contact_id" class="form-control selectpicker" data-live-search="true" required>
                                        <option value=""><?php echo _l('membership_select_contact'); ?></option>
                                        <?php foreach ($contacts as $contact): ?>
                                            <option value="<?php echo $contact['id']; ?>">
                                                <?php echo $contact['firstname'] . ' ' . $contact['lastname'] . ' (' . $contact['email'] . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_member_status'); ?></label>
                                    <select name="status" class="form-control">
                                        <option value="pending"><?php echo _l('membership_status_pending'); ?></option>
                                        <option value="active"><?php echo _l('membership_status_active'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_member_type'); ?></label>
                                    <input type="text" name="membership_type" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_member_profession'); ?></label>
                                    <input type="text" name="profession" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('membership_graduation_year'); ?></label>
                                    <input type="number" name="graduation_year" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkbox mtop25">
                                    <input type="checkbox" name="show_in_directory" value="1" checked>
                                    <label><?php echo _l('membership_show_in_directory'); ?></label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit'); ?></button>
                        <a href="<?php echo admin_url('membership/members'); ?>" class="btn btn-default"><?php echo _l('membership_cancel'); ?></a>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
