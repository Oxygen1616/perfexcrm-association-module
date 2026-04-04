<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_add_nomination'); ?></h4>

<div class="panel_s tw-mt-4">
    <div class="panel-body">
        <?php if (!empty($nomination_rules)): ?>
        <div class="alert alert-info">
            <h5><strong><?php echo _l('membership_nomination_rules_title'); ?></strong></h5>
            <?php echo $nomination_rules; ?>
        </div>
        <?php endif; ?>
        
        <?php echo form_open_multipart(site_url('membership/client/apply_nomination')); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?php echo _l('membership_select_member'); ?> *</label>
                        <select name="nominated_member_id" class="form-control selectpicker" data-live-search="true" required>
                            <option value=""><?php echo _l('membership_select_member'); ?></option>
                            <?php foreach ($members as $m): ?>
                                <option value="<?php echo $m['id']; ?>" data-subtext="<?php echo e($m['email'] ?? ''); ?>"><?php echo e($m['firstname'] . ' ' . $m['lastname']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?php echo _l('membership_election'); ?> *</label>
                        <select name="election_id" class="form-control" required onchange="updateNominationFee(this.value)">
                            <option value=""><?php echo _l('membership_select_election'); ?></option>
                            <?php foreach ($elections as $election): ?>
                                <option value="<?php echo $election['id']; ?>" data-fee="<?php echo $election['nomination_fee']; ?>"><?php echo e($election['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_position'); ?> *</label>
                        <select name="position" class="form-control selectpicker" data-live-search="true" required>
                            <option value=""><?php echo _l('membership_select_position'); ?></option>
                            <?php foreach ($positions as $position): ?>
                                <option value="<?php echo e($position); ?>"><?php echo e($position); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_committee'); ?></label>
                        <select name="committee_id" class="form-control selectpicker" data-live-search="true">
                            <option value=""><?php echo _l('membership_select_committee'); ?></option>
                            <?php foreach ($committees as $committee): ?>
                                <option value="<?php echo $committee['id']; ?>"><?php echo e($committee['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_election_symbol'); ?></label>
                        <select name="symbol_id" class="form-control selectpicker" data-live-search="true" onchange="show_symbol_preview(this.value)">
                            <option value=""><?php echo _l('membership_select_symbol'); ?></option>
                            <?php foreach ($symbols as $symbol): ?>
                                <option value="<?php echo $symbol['id']; ?>" data-image="<?php echo site_url('uploads/membership/symbols/'.$symbol['image']); ?>"><?php echo e($symbol['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_photo'); ?> (jpg, jpeg, png)</label>
                        <input type="file" name="photo" class="form-control" accept="image/jpeg,image/jpg,image/png">
                    </div>
                </div>
            </div>
            
            <div class="row" id="symbol-preview-section" style="display:none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?php echo _l('membership_selected_symbol'); ?></label>
                        <div id="symbol-preview-container"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label><?php echo _l('membership_manifesto'); ?> *</label>
                <textarea name="manifesto" class="form-control" rows="6" required placeholder="<?php echo _l('membership_manifesto_placeholder'); ?>"></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_nomination_fee'); ?></label>
                        <input type="text" name="total_fees" class="form-control" id="total-fees" value="<?php echo $nomination_fee; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo _l('membership_currency'); ?></label>
                        <input type="text" class="form-control" value="<?php echo $nomination_currency; ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="form-group mtop20">
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit_nomination'); ?></button>
                <a href="<?php echo site_url('membership/client/nominations'); ?>" class="btn btn-default"><?php echo _l('membership_cancel'); ?></a>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
function updateNominationFee(election_id) {
    var selectedOption = document.querySelector('select[name="election_id"] option[value="' + election_id + '"]');
    var fee = selectedOption ? selectedOption.getAttribute('data-fee') : '<?php echo $nomination_fee; ?>';
    document.getElementById('total-fees').value = fee || '0.00';
}

function show_symbol_preview(symbol_id) {
    var select = document.querySelector('select[name="symbol_id"]');
    var selectedOption = select.options[select.selectedIndex];
    var imageUrl = selectedOption.getAttribute('data-image');
    
    if (imageUrl && symbol_id) {
        document.getElementById('symbol-preview-section').style.display = 'block';
        document.getElementById('symbol-preview-container').innerHTML = '<img src="' + imageUrl + '" style="max-width: 200px; height: auto;">';
    } else {
        document.getElementById('symbol-preview-section').style.display = 'none';
        document.getElementById('symbol-preview-container').innerHTML = '';
    }
}
</script>
