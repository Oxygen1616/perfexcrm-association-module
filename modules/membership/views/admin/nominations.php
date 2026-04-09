<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="<?php echo $status_filter == '' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations'); ?>"><?php echo _l('membership_all'); ?></a>
                            </li>
                            <li role="presentation" class="<?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations/pending'); ?>"><?php echo _l('membership_pending'); ?></a>
                            </li>
                            <li role="presentation" class="<?php echo $status_filter == 'approved' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations/approved'); ?>"><?php echo _l('membership_approved'); ?></a>
                            </li>
                            <li role="presentation" class="<?php echo $status_filter == 'rejected' ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('membership/nominations/rejected'); ?>"><?php echo _l('membership_rejected'); ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nominationModal">
                            <?php echo _l('membership_add_nomination'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4><?php echo _l('membership_nominations'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <?php if (count($nominations) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_member_name'); ?></th>
                                        <th><?php echo _l('membership_election'); ?></th>
                                        <th><?php echo _l('membership_position'); ?></th>
                                        <th><?php echo _l('membership_symbol'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                        <th><?php echo _l('membership_nominated_at'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($nominations as $nom): ?>
                                        <tr>
                                            <td><?php echo $nom['firstname'] . ' ' . $nom['lastname']; ?></td>
                                            <td><?php echo $nom['election_title'] ?: '-'; ?></td>
                                            <td><?php echo $nom['position']; ?></td>
                                            <td><?php echo $nom['symbol_name'] ?: '-'; ?></td>
                                            <td>
                                                <span class="label label-<?php echo $nom['status'] == 'approved' ? 'success' : ($nom['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                    <?php echo ucfirst($nom['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($nom['nominated_at'])); ?></td>
                                            <td>
                                                <?php if ($nom['status'] == 'pending'): ?>
                                                    <a href="<?php echo admin_url('membership/approve_nomination/' . $nom['id']); ?>" class="btn btn-success btn-xs">
                                                        <i class="fa fa-check"></i> <?php echo _l('membership_approve'); ?>
                                                    </a>
                                                    <a href="<?php echo admin_url('membership/reject_nomination/' . $nom['id']); ?>" class="btn btn-danger btn-xs">
                                                        <i class="fa fa-times"></i> <?php echo _l('membership_reject'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-default btn-xs" onclick="viewNomination('<?php echo htmlspecialchars($nom['firstname'] . ' ' . $nom['lastname']); ?>', '<?php echo htmlspecialchars($nom['election_title']); ?>', '<?php echo htmlspecialchars($nom['position']); ?>', '<?php echo htmlspecialchars($nom['manifesto']); ?>')">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_nomination/' . $nom['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_nominations'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nominationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_nomination'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/nominations')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="nomination_id" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_select_member'); ?> *</label>
                            <select name="member_id" id="nomination_member_id" class="form-control" required>
                                <option value=""><?php echo _l('membership_select_member'); ?></option>
                                <?php 
                                $this->load->model('clients_model');
                                $all_contacts = $this->clients_model->get_contacts();
                                foreach($all_contacts as $contact): 
                                ?>
                                    <option value="<?php echo $contact['id']; ?>"><?php echo $contact['firstname'] . ' ' . $contact['lastname']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_election'); ?> *</label>
                            <select name="election_id" id="nomination_election_id" class="form-control" required>
                                <option value=""><?php echo _l('membership_select_election'); ?></option>
                                <?php foreach ($elections as $election): ?>
                                    <option value="<?php echo $election['id']; ?>"><?php echo $election['title']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_position'); ?> *</label>
                            <input type="text" name="position" id="nomination_position" class="form-control" required value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_election_symbol'); ?></label>
                            <select name="symbol_id" id="nomination_symbol_id" class="form-control">
                                <option value=""><?php echo _l('membership_select_symbol'); ?></option>
                                <?php 
                                $this->load->model('membership/membership_model');
                                $symbols = $this->membership_model->get_election_symbols();
                                foreach ($symbols as $symbol): ?>
                                    <option value="<?php echo $symbol['id']; ?>"><?php echo $symbol['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_manifesto'); ?> *</label>
                            <textarea name="manifesto" id="nomination_manifesto" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_status'); ?></label>
                            <select name="status" id="nomination_status" class="form-control">
                                <option value="pending"><?php echo _l('membership_pending'); ?></option>
                                <option value="approved"><?php echo _l('membership_approved'); ?></option>
                                <option value="rejected"><?php echo _l('membership_rejected'); ?></option>
                            </select>
                        </div>
                    </div>
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

<div class="modal fade" id="viewNominationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_nomination_details'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong><?php echo _l('membership_member_name'); ?>:</strong> <span id="view_member_name"></span></p>
                        <p><strong><?php echo _l('membership_election'); ?>:</strong> <span id="view_election"></span></p>
                        <p><strong><?php echo _l('membership_position'); ?>:</strong> <span id="view_position"></span></p>
                        <p><strong><?php echo _l('membership_manifesto'); ?>:</strong></p>
                        <div id="view_manifesto" class="well"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
function viewNomination(member_name, election, position, manifesto) {
    document.getElementById('view_member_name').textContent = member_name;
    document.getElementById('view_election').textContent = election;
    document.getElementById('view_position').textContent = position;
    document.getElementById('view_manifesto').textContent = manifesto || 'No manifesto';
    $('#viewNominationModal').modal('show');
}
</script>

<?php init_tail(); ?>
