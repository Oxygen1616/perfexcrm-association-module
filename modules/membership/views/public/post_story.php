<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_post_story'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(site_url('membership/post_story'), ['id' => 'post_story_form']); ?>
                            <div class="form-group">
                                <label><?php echo _l('membership_story_title'); ?> *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('membership_story_content'); ?> *</label>
                                <textarea name="content" class="form-control" rows="10" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit'); ?></button>
                                <a href="<?php echo site_url('membership/stories'); ?>" class="btn btn-default"><?php echo _l('membership_cancel'); ?></a>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
