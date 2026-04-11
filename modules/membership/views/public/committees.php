<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_committees'); ?></h4>

<div class="tw-mt-4">
    <?php if (count($committees) > 0): ?>
        <div class="panel_s">
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= _l('membership_committee_name'); ?></th>
                            <th><?= _l('membership_status'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($committees as $committee): ?>
                        <tr style="cursor:pointer;" onclick="showCommitteePopup(<?= $committee['id']; ?>)">
                            <td>
                                <strong><?= e($committee['name']); ?></strong>
                                <?php if (!empty($committee['category_name'])): ?>
                                    <br><small class="text-muted"><?= e($committee['category_name']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="label label-<?= $committee['status'] == 'active' ? 'success' : 'default'; ?>">
                                    <?= ucfirst($committee['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="panel_s">
            <div class="panel-body">
                <p class="text-center text-muted"><?= _l('membership_no_committees'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Committee Popup Modal -->
<div id="committee-popup" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="committee-popup-title"></h4>
            </div>
            <div class="modal-body" id="committee-popup-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
var committeesData = <?php echo json_encode(array_map(function($c) {
    return [
        'id'            => $c['id'],
        'name'          => $c['name'],
        'category_name' => isset($c['category_name']) ? $c['category_name'] : '',
        'description'   => isset($c['description']) ? $c['description'] : '',
        'status'        => $c['status'],
        'term_start'    => isset($c['term_start']) ? $c['term_start'] : '',
        'term_end'      => isset($c['term_end']) ? $c['term_end'] : '',
    ];
}, $committees)); ?>;

function showCommitteePopup(committeeId) {
    var committee = committeesData.find(function(c) { return c.id == committeeId; });
    if (!committee) { return; }

    $('#committee-popup-title').text(committee.name);

    var statusClass = committee.status === 'active' ? 'success' : 'default';
    var body = '';
    if (committee.category_name) {
        body += '<p><strong><?= _l('membership_category'); ?>:</strong> ' + committee.category_name + '</p>';
    }
    if (committee.description) {
        body += '<p><strong><?= _l('membership_description'); ?>:</strong> ' + committee.description + '</p>';
    }
    body += '<p><strong><?= _l('membership_status'); ?>:</strong> <span class="label label-' + statusClass + '">' + committee.status.charAt(0).toUpperCase() + committee.status.slice(1) + '</span></p>';
    if (committee.term_start) {
        body += '<p><strong><?= _l('membership_term_start'); ?>:</strong> ' + committee.term_start + '</p>';
    }
    if (committee.term_end) {
        body += '<p><strong><?= _l('membership_term_end'); ?>:</strong> ' + committee.term_end + '</p>';
    }

    $('#committee-popup-body').html(body);
    $('#committee-popup').modal('show');
}
</script>
