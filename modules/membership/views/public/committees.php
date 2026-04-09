<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_committees'); ?></h4>

<div class="tw-mt-4">
    <?php if (count($committees) > 0): ?>
        <div class="row">
            <?php foreach ($committees as $committee): ?>
                <div class="col-md-6 tw-mb-4">
                    <div class="panel_s cursor-pointer" onclick="showCommitteePopup(<?= $committee['id']; ?>)">
                        <div class="panel-body">
                            <h5 class="tw-font-semibold"><?= e($committee['name']); ?></h5>
                            <?php if (!empty($committee['category_name'])): ?>
                                <span class="label label-default"><?= e($committee['category_name']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($committee['description'])): ?>
                                <p class="tw-text-sm tw-text-neutral-500 tw-mt-2"><?= e($committee['description']); ?></p>
                            <?php endif; ?>
                            <span class="label label-<?= $committee['status'] == 'active' ? 'success' : 'default'; ?>">
                                <?= ucfirst($committee['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="panel_s">
            <div class="panel-body">
                <p class="text-center text-muted"><?= _l('membership_no_committees'); ?></p>
            </div>
        </div>
    <?php endif; ?>

<!-- Committee Popup Modal -->
<div id="committee-popup" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="committee-popup-title"></h4>
            </div>
            <div class="modal-body" id="committee-popup-body">
                <!-- Committee details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    function showCommitteePopup(committeeId) {
        // Show loading state
        $('#committee-popup-title').text('<?= _l('membership_loading') ?>...');
        $('#committee-popup-body').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
        $('#committee-popup').modal('show');

        // Fetch committee details via AJAX
        $.get("<?= site_url('membership/get_committee_details') ?>/" + committeeId, function(response) {
            if (response.success) {
                $('#committee-popup-title').text(response.committee.name);
                $('#committee-popup-body').html(`
                    <p><strong><?= _l('membership_category') ?>:</strong> ${response.committee.category_name || '-'}</p>
                    <p><strong><?= _l('membership_description') ?>:</strong> ${response.committee.description || '-'}</p>
                    <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(response.committee.status || '')}</p>
                    ${response.committee.term_start ? `<p><strong><?= _l('membership_term_start') ?>:</strong> ${response.committee.term_start}</p>` : ''}
                    ${response.committee.term_end ? `<p><strong><?= _l('membership_term_end') ?>:</strong> ${response.committee.term_end}</p>` : ''}
                `);
            } else {
                $('#committee-popup-body').html('<p class="text-danger">' + response.message + '</p>');
            }
        });
    }
</script>
