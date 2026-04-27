<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_stories'); ?></h4>

<div class="row tw-mt-4">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <?php if (!empty($stories)): ?>
                    <div class="row">
                        <?php foreach ($stories as $story): ?>
                            <div class="col-md-4">
                                <div class="panel_s story-item">
                                    <div class="panel-body">
                                        <h5 class="story-title"><?= e($story['title']); ?></h5>
                                        <p class="story-author text-muted">
                                            <small><?= _l('membership_by') . ' ' . e($story['firstname']) . ' ' . e($story['lastname']); ?></small>
                                        </p>
                                        <div class="story-content">
                                            <?= nl2br(e($story['content'])); ?>
                                        </div>
                                        <?php if (!empty($story['image_url'])): ?>
                                            <div class="text-center mtop10">
                                                <img src="<?= e($story['image_url']); ?>"
                                                     alt="<?= e($story['title']); ?>"
                                                     class="img-thumbnail" style="max-width:100%;">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted"><?= _l('membership_no_stories'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
