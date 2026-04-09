<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_election_symbols'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#symbolModal">
                            <?php echo _l('membership_add_election_symbol'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
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
                                            <td><?php echo $symbol['name']; ?></td>
                                            <td>
                                                <?php if ($symbol['symbol_image']): ?>
                                                    <img src="<?php echo $symbol['symbol_image']; ?>" alt="<?php echo $symbol['name']; ?>" style="max-width: 50px; max-height: 50px;">
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $symbol['description'] ?: '-'; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-xs" onclick="editSymbol(<?php echo $symbol['id']; ?>, '<?php echo htmlspecialchars($symbol['name']); ?>', '<?php echo htmlspecialchars($symbol['symbol_image']); ?>', '<?php echo htmlspecialchars($symbol['description']); ?>')">
                                                    <i class="fa fa-pencil"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_election_symbol/' . $symbol['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_election_symbols'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="symbolModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_election_symbol'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/election_symbols')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="symbol_id" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_symbol_name'); ?> *</label>
                            <input type="text" name="name" id="symbol_name" class="form-control" required value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_symbol_image_url'); ?></label>
                            <input type="text" name="symbol_image" id="symbol_image" class="form-control" placeholder="https://..." value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_description'); ?></label>
                            <textarea name="description" id="symbol_description" class="form-control" rows="3"></textarea>
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

<script>
function editSymbol(id, name, symbol_image, description) {
    document.getElementById('symbol_id').value = id;
    document.getElementById('symbol_name').value = name;
    document.getElementById('symbol_image').value = symbol_image;
    document.getElementById('symbol_description').value = description;
    document.querySelector('#symbolModal .modal-title').textContent = '<?php echo _l('membership_edit_election_symbol'); ?>';
    $('#symbolModal').modal('show');
}

$('#symbolModal').on('hidden.bs.modal', function() {
    document.getElementById('symbol_id').value = '';
    document.getElementById('symbol_name').value = '';
    document.getElementById('symbol_image').value = '';
    document.getElementById('symbol_description').value = '';
    document.querySelector('#symbolModal .modal-title').textContent = '<?php echo _l('membership_add_election_symbol'); ?>';
});
</script>

<?php init_tail(); ?>
