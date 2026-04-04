<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_board_members'); ?></h4>

<div class="tw-mt-4">
    <?php if (count($board_members) > 0): ?>
        <div class="row">
            <?php foreach ($board_members as $member): ?>
                <div class="col-md-4 tw-mb-4">
                    <div class="panel_s cursor-pointer" onclick="showBoardMemberPopup(<?= $member['id']; ?>)">
                        <div class="panel-body text-center">
                            <div class="tw-text-neutral-300 tw-mb-3">
                                <i class="fa fa-user fa-4x"></i>
                            </div>
                            <h5 class="tw-font-semibold"><?= e($member['firstname'] . ' ' . $member['lastname']); ?></h5>
                            <p class="tw-font-medium"><?= e($member['position']); ?></p>
                            <p class="tw-text-sm tw-text-neutral-500"><?= e($member['email']); ?></p>
                            <span class="label label-<?= $member['status'] == 'active' ? 'success' : 'default'; ?>">
                                <?= ucfirst($member['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="panel_s">
            <div class="panel-body">
                <p class="text-center text-muted"><?= _l('membership_no_board_members'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Board Member Popup Modal -->
<div id="board-member-popup" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="board-member-popup-title"></h4>
            </div>
            <div class="modal-body" id="board-member-popup-body">
                <!-- Board member details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    function showBoardMemberPopup(memberId) {
        // Show loading state
        $('#board-member-popup-title').text('<?= _l('membership_loading') ?>...');
        $('#board-member-popup-body').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i></p>');
        $('#board-member-popup').modal('show');

        // Fetch board member details via AJAX
        $.get("<?= site_url('membership/get_board_member_details') ?>/" + memberId, function(response) {
            if (response.success) {
                $('#board-member-popup-title').text(response.member.firstname + ' ' + response.member.lastname);
                $('#board-member-popup-body').html(`
                    <p><strong><?= _l('membership_position') ?>:</strong> ${response.member.position || '-'}</p>
                    <p><strong><?= _l('membership_email') ?>:</strong> ${response.member.email || '-'}</p>
                    <p><strong><?= _l('membership_status') ?>:</strong> ${ucfirst(response.member.status || '')}</p>
                    ${response.member.manifesto ? `<p><strong><?= _l('membership_manifesto') ?>:</strong> ${response.member.manifesto}</p>` : ''}
                `);
            } else {
                $('#board-member-popup-body').html('<p class="text-danger">' + response.message + '</p>');
            }
        });
    }
</script>
