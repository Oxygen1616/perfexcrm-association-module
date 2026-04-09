<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_profile'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <form action="<?php echo site_url('membership/profile'); ?>" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="membership_type"><?php echo _l('membership_member_type'); ?></label>
                                        <input type="text" class="form-control" id="membership_type" name="membership_type" value="<?php echo $member['membership_type'] ?: ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profession"><?php echo _l('membership_member_profession'); ?></label>
                                        <input type="text" class="form-control" id="profession" name="profession" value="<?php echo $member['profession'] ?: ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="graduation_year"><?php echo _l('membership_graduation_year'); ?></label>
                                        <input type="number" class="form-control" id="graduation_year" name="graduation_year" value="<?php echo $member['graduation_year'] ?: ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="show_in_directory"><?php echo _l('membership_show_in_directory'); ?></label>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="show_in_directory" id="show_in_directory" value="1" <?php echo $member['show_in_directory'] ? 'checked' : ''; ?>>
                                            <label for="show_in_directory"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mtop15"><?php echo _l('membership_submit'); ?></button>
                            <a href="<?php echo site_url('membership'); ?>" class="btn btn-default mtop15"><?php echo _l('membership_cancel'); ?></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>