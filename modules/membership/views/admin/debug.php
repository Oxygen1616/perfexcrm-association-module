<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Debug: Membership Tables</h4>
                        <p>Table exists: <?php echo $this->db->table_exists('tblmembership_members') ? 'YES' : 'NO'; ?></p>
                        
                        <h5>All members:</h5>
                        <pre><?php print_r($this->db->get('tblmembership_members')->result()); ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
