<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_stories'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($stories) > 0): ?>
                            <div class="row">
                                <?php foreach ($stories as $story): ?>
                                    <div class="col-md-4">
                                        <div class="panel_s story-item">
                                            <div class="panel-body">
                                                <h5 class="story-title"><?php echo $story['title']; ?></h5>
                                                <p class="story-author text-muted">
                                                    <small><?php echo _l('membership_by') . ' ' . $story['firstname'] . ' ' . $story['lastname']; ?></small>
                                                </p>
                                                <div class="story-content">
                                                    <?php echo nl2br($story['content']); ?>
                                                </div>
                                                <?php if ($story['image_url']): ?>
                                                    <div class="text-center mtop10">
                                                        <img src="<?php echo $story['image_url']; ?>" alt="<?php echo $story['title']; ?>" class="img-thumbnail" style="max-width:100%;">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_stories'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>