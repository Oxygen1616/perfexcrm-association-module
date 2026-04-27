<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_post_story'); ?></h4>

<div class="row tw-mt-4">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel_s">
            <div class="panel-body">
                <?= form_open(site_url('membership/client/post_story'), ['id' => 'post_story_form']); ?>
                    <div class="form-group">
                        <label><?= _l('membership_story_title'); ?> *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><?= _l('membership_story_content'); ?> *</label>
                        <textarea name="content" class="form-control" rows="10" required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><?= _l('membership_submit'); ?></button>
                        <a href="<?= site_url('membership/client/stories'); ?>" class="btn btn-default"><?= _l('membership_cancel'); ?></a>
                    </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
