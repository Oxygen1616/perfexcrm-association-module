<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (!empty($candidates)): ?>

    <?php if (!empty($has_voted)): ?>
        <div class="alert alert-success tw-mt-4">
            <i class="fa fa-check-circle tw-mr-2"></i>
            <strong><?= _l('membership_already_voted'); ?></strong>
        </div>
    <?php endif; ?>

    <div class="panel_s tw-mt-3">
        <div class="panel-heading">
            <h4 class="panel-title">
                <?= !empty($has_voted) ? _l('membership_candidates') : _l('membership_select_candidate'); ?>
            </h4>
        </div>
        <div class="panel-body">

            <?php if (!empty($positions)): ?>
            <!-- Position filter dropdown -->
            <div class="form-group tw-mb-4">
                <label for="position-filter"><?= _l('membership_filter_by_position'); ?></label>
                <select id="position-filter" class="form-control" style="max-width:320px;">
                    <option value=""><?= _l('membership_all_positions'); ?></option>
                    <?php foreach ($positions as $pos): ?>
                        <option value="<?= e($pos); ?>"><?= e($pos); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if (empty($has_voted)): ?>
                <?= form_open(site_url('membership/client/cast_vote')); ?>
                <input type="hidden" name="election_id" value="<?= (int)$election_id; ?>">
            <?php endif; ?>

            <div class="row" id="candidates-row">
                <?php foreach ($candidates as $candidate): ?>
                    <div class="col-md-4 tw-mb-4 candidate-card"
                         data-position="<?= e($candidate['position'] ?? ''); ?>">
                        <div class="panel_s" style="<?= !empty($has_voted) ? 'opacity:0.75;' : ''; ?>">
                            <div class="panel-body text-center">
                                <?php if (!empty($candidate['position'])): ?>
                                    <span class="label label-default tw-mb-2 tw-inline-block">
                                        <?= e($candidate['position']); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (empty($has_voted)): ?>
                                    <input type="radio" name="candidate_id"
                                           value="<?= (int)$candidate['id']; ?>"
                                           id="candidate_<?= (int)$candidate['id']; ?>"
                                           required>
                                    <label for="candidate_<?= (int)$candidate['id']; ?>"
                                           class="tw-cursor-pointer tw-block tw-p-3 tw-rounded hover:tw-bg-neutral-100">
                                <?php else: ?>
                                    <label class="tw-block tw-p-3 tw-rounded">
                                <?php endif; ?>
                                        <?php if (!empty($candidate['photo'])): ?>
                                            <img src="<?= base_url('uploads/membership/nominations/' . e($candidate['photo'])); ?>"
                                                 class="img-circle tw-mb-2"
                                                 style="width:60px;height:60px;object-fit:cover;" alt="">
                                        <?php else: ?>
                                            <div class="tw-mb-2">
                                                <i class="fa fa-user-circle fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <h5 class="tw-font-semibold tw-mb-1">
                                            <?= e($candidate['firstname'] . ' ' . $candidate['lastname']); ?>
                                        </h5>
                                        <?php if (!empty($candidate['bio'])): ?>
                                            <p class="tw-text-sm tw-text-neutral-500 tw-mb-0"><?= e($candidate['bio']); ?></p>
                                        <?php endif; ?>
                                    </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="no-candidates-msg" class="alert alert-info tw-mt-2" style="display:none;">
                <?= _l('membership_no_candidates_for_position'); ?>
            </div>

            <?php if (empty($has_voted)): ?>
                <div class="text-center tw-mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-check-square tw-mr-1"></i><?= _l('membership_submit_vote'); ?>
                    </button>
                </div>
                <?= form_close(); ?>
            <?php endif; ?>

        </div>
    </div>

    <script>
    $('#position-filter').on('change', function() {
        var selected = $(this).val();
        var cards    = $('#candidates-row .candidate-card');

        if (!selected) {
            cards.show();
            $('#no-candidates-msg').hide();
            return;
        }

        var visible = 0;
        cards.each(function() {
            if ($(this).data('position') === selected) {
                $(this).show();
                visible++;
            } else {
                $(this).hide();
            }
        });

        $('#no-candidates-msg').toggle(visible === 0);
    });
    </script>

<?php else: ?>
    <div class="alert alert-info tw-mt-4">
        <i class="fa fa-info-circle tw-mr-1"></i><?= _l('membership_no_candidates'); ?>
    </div>
<?php endif; ?>
