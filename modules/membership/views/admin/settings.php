<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_settings'); ?></h4>
            </div>
        </div>

        <!-- Membership Types -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_types'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" onclick="openMembershipTypeModal()">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_add_type'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($membership_types)): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_type_name'); ?></th>
                                        <th><?php echo _l('membership_type_amount'); ?></th>
                                        <th><?php echo _l('membership_description'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($membership_types as $mt): ?>
                                    <tr>
                                        <td>
                                            <?php echo e($mt['name']); ?>
                                            <div class="row-options">
                                                <a href="#" onclick="editMembershipType(<?php echo $mt['id']; ?>, '<?php echo addslashes($mt['name']); ?>', '<?php echo $mt['amount']; ?>', '<?php echo addslashes($mt['description']); ?>', '<?php echo $mt['status']; ?>'); return false;"><?php echo _l('membership_edit'); ?></a>
                                                <span class="text-muted"> | </span>
                                                <a href="<?php echo admin_url('membership/delete_membership_type/' . $mt['id']); ?>" class="text-danger" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')"><?php echo _l('membership_delete'); ?></a>
                                            </div>
                                        </td>
                                        <td><?php echo app_format_money($mt['amount'], get_option('currency')); ?></td>
                                        <td><?php echo e($mt['description'] ?: '-'); ?></td>
                                        <td>
                                            <span class="label label-<?php echo $mt['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($mt['status']); ?>
                                            </span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted text-center"><?php echo _l('membership_no_types_found'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Card Display Settings -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-id-card mright5"></i><?php echo _l('membership_card_display_settings'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open(admin_url('membership/save_card_settings')); ?>
                        <div class="row">

                            <!-- Members Card -->
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold tw-mb-3 tw-border-b tw-pb-2">
                                    <i class="fa fa-users mright5 tw-text-neutral-500"></i><?php echo _l('membership_members_card'); ?>
                                </h5>
                                <p class="text-muted small"><?php echo _l('membership_card_settings_desc'); ?></p>

                                <?php
                                $memberFields = [
                                    'card_show_photo'           => _l('membership_card_field_photo'),
                                    'card_show_membership_type' => _l('membership_card_field_membership_type'),
                                    'card_show_profession'      => _l('membership_card_field_profession'),
                                    'card_show_email'           => _l('membership_card_field_email'),
                                    'card_show_phone'           => _l('membership_card_field_phone'),
                                    'card_show_status'          => _l('membership_card_field_status'),
                                ];
                                foreach ($memberFields as $key => $label):
                                    $checked = get_option('membership_' . $key);
                                    $checked = ($checked === '' || $checked === false) ? true : (bool)$checked; // default on
                                ?>
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" <?php echo $checked ? 'checked' : ''; ?>>
                                        <label for="<?php echo $key; ?>"><?php echo $label; ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="form-group mtop10">
                                    <label for="card_per_page"><?php echo _l('membership_cards_per_page'); ?></label>
                                    <input type="number" name="card_per_page" id="card_per_page" class="form-control" min="1" max="100" value="<?php echo get_option('membership_card_per_page') ?: 9; ?>" style="width:100px;">
                                </div>
                            </div>

                            <!-- Board Members Card -->
                            <div class="col-md-6">
                                <h5 class="tw-font-semibold tw-mb-3 tw-border-b tw-pb-2">
                                    <i class="fa fa-black-tie mright5 tw-text-neutral-500"></i><?php echo _l('membership_board_members_card'); ?>
                                </h5>
                                <p class="text-muted small"><?php echo _l('membership_card_settings_desc'); ?></p>

                                <?php
                                $boardFields = [
                                    'board_card_show_photo'    => _l('membership_card_field_photo'),
                                    'board_card_show_position' => _l('membership_card_field_position'),
                                    'board_card_show_election' => _l('membership_card_field_election'),
                                    'board_card_show_email'    => _l('membership_card_field_email'),
                                    'board_card_show_phone'    => _l('membership_card_field_phone'),
                                    'board_card_show_status'   => _l('membership_card_field_status'),
                                ];
                                foreach ($boardFields as $key => $label):
                                    $checked = get_option('membership_' . $key);
                                    $checked = ($checked === '' || $checked === false) ? true : (bool)$checked;
                                ?>
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" <?php echo $checked ? 'checked' : ''; ?>>
                                        <label for="<?php echo $key; ?>"><?php echo $label; ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="form-group mtop10">
                                    <label for="board_card_per_page"><?php echo _l('membership_cards_per_page'); ?></label>
                                    <input type="number" name="board_card_per_page" id="board_card_per_page" class="form-control" min="1" max="100" value="<?php echo get_option('membership_board_card_per_page') ?: 9; ?>" style="width:100px;">
                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary mtop10"><?php echo _l('membership_save_settings'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Positions -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_positions'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="#" onclick="init_position_form(); return false;" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_new_position'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">

                        <!-- Election filter -->
                        <div class="row tw-mb-3">
                            <div class="col-md-4">
                                <div class="form-group tw-mb-0">
                                    <label><?php echo _l('membership_filter_by_election'); ?></label>
                                    <select id="position-election-filter" class="form-control" onchange="filterPositionsByElection(this.value)">
                                        <option value=""><?php echo _l('membership_all_elections'); ?></option>
                                        <?php foreach ($elections as $el): ?>
                                            <option value="<?php echo (int)$el['id']; ?>" <?php echo isset($election_filter) && $election_filter == $el['id'] ? 'selected' : ''; ?>>
                                                <?php echo e($el['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <?php if (count($positions) > 0): ?>
                        <table class="table dt-table">
                            <thead>
                                <tr>
                                    <th><?php echo _l('membership_position_name'); ?></th>
                                    <th><?php echo _l('membership_election'); ?></th>
                                    <th><?php echo _l('membership_position_description'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($positions as $position): ?>
                                <?php
                                $linked_election = '';
                                if (!empty($position['election_id'])) {
                                    foreach ($elections as $el) {
                                        if ($el['id'] == $position['election_id']) {
                                            $linked_election = e($el['title']);
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td>
                                        <a href="#" onclick="position_form(<?php echo $position['id']; ?>); return false;">
                                            <?php echo e($position['name']); ?>
                                        </a>
                                        <div class="row-options">
                                            <a href="#" onclick="position_form(<?php echo $position['id']; ?>); return false;"><?php echo _l('membership_edit'); ?></a>
                                            |
                                            <a href="<?php echo admin_url('membership/delete_position/' . $position['id']); ?>"
                                               onclick="return confirm('<?php echo _l('membership_confirm_position_delete'); ?>')"><?php echo _l('membership_delete'); ?></a>
                                        </div>
                                    </td>
                                    <td><?php echo $linked_election ?: '<span class="text-muted">—</span>'; ?></td>
                                    <td><?php echo e($position['description']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <?php echo isset($election_filter) && $election_filter ? _l('membership_no_positions_for_election') : _l('membership_no_positions_found'); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Election Symbols -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_election_symbols'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#symbolModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_add_election_symbol'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (count($symbols) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_symbol_name'); ?></th>
                                        <th><?php echo _l('membership_symbol_image'); ?></th>
                                        <th><?php echo _l('membership_description'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($symbols as $symbol): ?>
                                        <tr>
                                            <td><?php echo e($symbol['name']); ?></td>
                                            <td>
                                                <?php if ($symbol['symbol_image']): ?>
                                                    <img src="<?php echo $symbol['symbol_image']; ?>" alt="<?php echo e($symbol['name']); ?>" style="max-width: 50px; max-height: 50px;">
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($symbol['description'] ?: '-'); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs"
                                                    onclick="editSymbol(<?php echo $symbol['id']; ?>, '<?php echo addslashes($symbol['name']); ?>', '<?php echo addslashes($symbol['symbol_image']); ?>', '<?php echo addslashes($symbol['description']); ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_election_symbol/' . $symbol['id']); ?>"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center"><?php echo _l('membership_no_election_symbols'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Committee Categories -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_committee_categories'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoryModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_add_category'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (count($categories) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_category_name'); ?></th>
                                        <th><?php echo _l('membership_category_description'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo e($category['name']); ?></td>
                                            <td><?php echo e($category['description'] ?: '-'); ?></td>
                                            <td>
                                                <span class="label label-<?php echo $category['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($category['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs"
                                                    onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo addslashes($category['name']); ?>', '<?php echo addslashes($category['description']); ?>', '<?php echo $category['status']; ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_committee_category/' . $category['id']); ?>"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center"><?php echo _l('membership_no_categories'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Committee Designations -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="panel-title"><?php echo _l('membership_committee_designations'); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#designationModal">
                                    <i class="fa fa-plus"></i> <?php echo _l('membership_add_designation'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (count($designations) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_designation_name'); ?></th>
                                        <th><?php echo _l('membership_designation_description'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($designations as $designation): ?>
                                        <tr>
                                            <td><?php echo e($designation['name']); ?></td>
                                            <td><?php echo e($designation['description'] ?: '-'); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs"
                                                    onclick="editDesignation(<?php echo $designation['id']; ?>, '<?php echo addslashes($designation['name']); ?>', '<?php echo addslashes($designation['description']); ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_committee_designation/' . $designation['id']); ?>"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center"><?php echo _l('membership_no_designations'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Membership Type Modal -->
<div class="modal fade" id="membershipTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="membershipTypeModalTitle"><?php echo _l('membership_add_type'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/membership_types'), ['id' => 'membershipTypeForm']); ?>
            <div class="modal-body">
                <input type="hidden" name="type_id" id="type_id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_type_name'); ?> *</label>
                    <input type="text" name="name" id="type_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_type_amount'); ?> *</label>
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo get_option('currency_symbol') ?: '$'; ?></span>
                        <input type="number" name="amount" id="type_amount" class="form-control" step="0.01" min="0" value="0.00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_description'); ?></label>
                    <textarea name="description" id="type_description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_status'); ?></label>
                    <select name="status" id="type_status" class="form-control">
                        <option value="active"><?php echo _l('membership_active'); ?></option>
                        <option value="inactive"><?php echo _l('membership_inactive'); ?></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Committee Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="categoryModalTitle"><?php echo _l('membership_add_category'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/committee_categories')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="category_id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_category_name'); ?> *</label>
                    <input type="text" name="name" id="category_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_category_description'); ?></label>
                    <textarea name="description" id="category_description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_status'); ?></label>
                    <select name="status" id="category_status" class="form-control">
                        <option value="active"><?php echo _l('membership_active'); ?></option>
                        <option value="inactive"><?php echo _l('membership_inactive'); ?></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Position Modal -->
<div id="position-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="position-modal-title"></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(admin_url('membership/positions')); ?>
                <input type="hidden" name="id" id="position-id" value="">
                <input type="hidden" name="return_to" value="settings">

                <div class="form-group">
                    <label for="position-election"><?php echo _l('membership_election'); ?></label>
                    <select name="election_id" id="position-election" class="form-control selectpicker"
                            data-none-selected-text="<?php echo _l('membership_select_election'); ?>">
                        <option value=""><?php echo _l('membership_no_election'); ?></option>
                        <?php foreach ($elections as $el): ?>
                            <option value="<?php echo (int)$el['id']; ?>"><?php echo e($el['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted"><?php echo _l('membership_position_election_hint'); ?></small>
                </div>

                <div class="form-group">
                    <label><?php echo _l('membership_position_name'); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="position-name" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_position_description'); ?></label>
                    <textarea class="form-control" name="description" id="position-description" rows="3"></textarea>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('membership_submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Election Symbol Modal -->
<div class="modal fade" id="symbolModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="symbolModalTitle"><?php echo _l('membership_add_election_symbol'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/election_symbols')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="symbol_id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_symbol_name'); ?> *</label>
                    <input type="text" name="name" id="symbol_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_symbol_image_url'); ?></label>
                    <input type="text" name="symbol_image" id="symbol_image" class="form-control" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_description'); ?></label>
                    <textarea name="description" id="symbol_description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Committee Designation Modal -->
<div class="modal fade" id="designationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="designationModalTitle"><?php echo _l('membership_add_designation'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/committee_designations')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="designation_id" value="">
                <div class="form-group">
                    <label><?php echo _l('membership_designation_name'); ?> *</label>
                    <input type="text" name="name" id="designation_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_designation_description'); ?></label>
                    <textarea name="description" id="designation_description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
// Category modal
function editCategory(id, name, description, status) {
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = name;
    document.getElementById('category_description').value = description;
    document.getElementById('category_status').value = status;
    document.getElementById('categoryModalTitle').textContent = '<?php echo _l('membership_edit_category'); ?>';
    $('#categoryModal').modal('show');
}
$('#categoryModal').on('hidden.bs.modal', function() {
    document.getElementById('category_id').value = '';
    document.getElementById('category_name').value = '';
    document.getElementById('category_description').value = '';
    document.getElementById('category_status').value = 'active';
    document.getElementById('categoryModalTitle').textContent = '<?php echo _l('membership_add_category'); ?>';
});

// Position filter
function filterPositionsByElection(val) {
    var url = '<?php echo admin_url('membership/settings'); ?>';
    if (val) url += '?election_id=' + val;
    window.location.href = url;
}

function _set_position_election(val) {
    $('#position-election').val(val || '');
    if (typeof $.fn.selectpicker !== 'undefined') {
        $('#position-election').selectpicker('refresh');
    }
}

function init_position_form() {
    $('#position-id').val('');
    $('#position-modal-title').text('<?php echo _l('membership_new_position'); ?>');
    $('#position-name').val('');
    $('#position-description').val('');
    // Pre-select the active election filter so new positions inherit it
    _set_position_election('<?php echo isset($election_filter) ? (int)$election_filter : ''; ?>');
    $('#position-modal').modal('show');
}

function position_form(id) {
    $('#position-id').val(id);
    $('#position-modal-title').text('<?php echo _l('membership_edit_position'); ?>');
    $.get('<?php echo admin_url('membership/ajax_get_position'); ?>/' + id, function(response) {
        if (response.success) {
            $('#position-name').val(response.position.name);
            $('#position-description').val(response.position.description || '');
            _set_position_election(response.position.election_id || '');
            $('#position-modal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}

// Symbol modal
function editSymbol(id, name, symbol_image, description) {
    document.getElementById('symbol_id').value = id;
    document.getElementById('symbol_name').value = name;
    document.getElementById('symbol_image').value = symbol_image;
    document.getElementById('symbol_description').value = description;
    document.getElementById('symbolModalTitle').textContent = '<?php echo _l('membership_edit_election_symbol'); ?>';
    $('#symbolModal').modal('show');
}
$('#symbolModal').on('hidden.bs.modal', function() {
    document.getElementById('symbol_id').value = '';
    document.getElementById('symbol_name').value = '';
    document.getElementById('symbol_image').value = '';
    document.getElementById('symbol_description').value = '';
    document.getElementById('symbolModalTitle').textContent = '<?php echo _l('membership_add_election_symbol'); ?>';
});

// Designation modal
function editDesignation(id, name, description) {
    document.getElementById('designation_id').value = id;
    document.getElementById('designation_name').value = name;
    document.getElementById('designation_description').value = description;
    document.getElementById('designationModalTitle').textContent = '<?php echo _l('membership_edit_designation'); ?>';
    $('#designationModal').modal('show');
}
$('#designationModal').on('hidden.bs.modal', function() {
    document.getElementById('designation_id').value = '';
    document.getElementById('designation_name').value = '';
    document.getElementById('designation_description').value = '';
    document.getElementById('designationModalTitle').textContent = '<?php echo _l('membership_add_designation'); ?>';
});

function openMembershipTypeModal() {
    document.getElementById('membershipTypeModalTitle').textContent = '<?php echo _l('membership_add_type'); ?>';
    document.getElementById('membershipTypeForm').action = '<?php echo admin_url('membership/membership_types'); ?>';
    document.getElementById('type_id').value = '';
    document.getElementById('type_name').value = '';
    document.getElementById('type_amount').value = '0.00';
    document.getElementById('type_description').value = '';
    document.getElementById('type_status').value = 'active';
    $('#membershipTypeModal').modal('show');
}

function editMembershipType(id, name, amount, description, status) {
    document.getElementById('membershipTypeModalTitle').textContent = '<?php echo _l('membership_edit_type'); ?>';
    document.getElementById('membershipTypeForm').action = '<?php echo admin_url('membership/membership_types'); ?>/' + id;
    document.getElementById('type_id').value = id;
    document.getElementById('type_name').value = name;
    document.getElementById('type_amount').value = amount;
    document.getElementById('type_description').value = description;
    document.getElementById('type_status').value = status;
    $('#membershipTypeModal').modal('show');
}
</script>

<?php init_tail(); ?>
