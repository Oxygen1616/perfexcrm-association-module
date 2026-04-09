<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4><?php echo _l('membership_directory'); ?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons mbottom20">
                            <input type="text" class="form-control input-sm" id="search" placeholder="<?php echo _l('membership_search_member'); ?>" />
                        </div>

                        <div id="member-directory-container">
                            <?php if (count($members) > 0): ?>
                                <div class="row mtop15">
                                    <?php foreach ($members as $member): ?>
                                        <?php if ($member['show_in_directory']): ?>
                                            <div class="col-md-3 col-sm-6">
                                                <div class="member-card text-center">
                                                    <div class="member-avatar">
                                                        <i class="fa fa-user-circle fa-3x"></i>
                                                    </div>
                                                    <div class="member-info">
                                                        <h5 class="member-name">
                                                            <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                        </h5>
                                                        <div class="member-email text-muted">
                                                            <?php echo $member['email']; ?>
                                                        </div>
                                                        <?php if ($member['company']): ?>
                                                            <div class="member-company">
                                                                <small><?php echo $member['company']; ?></small>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ($member['profession']): ?>
                                                            <div class="member-profession">
                                                                <small><?php echo _l('membership_profession'); ?>: <?php echo $member['profession']; ?></small>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ($member['graduation_year']): ?>
                                                            <div class="member-graduation">
                                                                <small><?php echo _l('membership_graduation_year'); ?>: <?php echo $member['graduation_year']; ?></small>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center mtop20">
                                    <p><?php echo _l('membership_no_members_found'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        $("#search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#member-directory-container .col-md-3, #member-directory-container .col-sm-6").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>