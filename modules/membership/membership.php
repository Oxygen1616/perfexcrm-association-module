<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Membership
Description: Membership Dashboard module for community organizations
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MEMBERSHIP_MODULE_NAME', 'membership');

hooks()->add_action('admin_init', 'membership_module_init_menu_items');
hooks()->add_action('admin_init', 'membership_permissions');
hooks()->add_action('admin_init', 'membership_email_templates');
hooks()->add_action('after_cron_run', 'membership_cron_tasks');
hooks()->add_action('customers_navigation_end', 'membership_client_menu');
hooks()->add_action('after_email_templates', 'membership_email_templates_section');

hooks()->add_filter('migration_tables_to_replace_old_links', 'membership_migration_tables');

// Register membership merge fields so they appear in the email template editor sidebar
register_merge_fields('membership/merge_fields/membership_merge_fields');

function membership_migration_tables($tables)
{
    $tables[] = [
        'table' => db_prefix() . 'membership_events',
        'field' => 'description',
    ];
    $tables[] = [
        'table' => db_prefix() . 'membership_jobs',
        'field' => 'description',
    ];
    $tables[] = [
        'table' => db_prefix() . 'membership_stories',
        'field' => 'content',
    ];

    return $tables;
}

function membership_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'          => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create'        => _l('permission_create'),
        'edit'          => _l('permission_edit'),
        'delete'        => _l('permission_delete'),
        'manage_members'=> _l('membership_manage_members'),
        'manage_elections'=> _l('membership_manage_elections'),
    ];

    register_staff_capabilities('membership', $capabilities, _l('membership'));
}

function membership_client_menu()
{
    $CI = &get_instance();

    if (!is_client_logged_in()) {
        return;
    }

    $contact_id = get_contact_user_id();
    $CI->load->model('membership/membership_model');
    $member = $CI->membership_model->get_member_by_contact_id($contact_id);

    echo '<li class="customers-nav-item-custom">
        <a href="' . site_url('membership/client') . '">
            <span class="menu-text">' . _l('membership_dashboard') . '</span>
        </a>
    </li>';

    echo '<li class="customers-nav-item-custom">
        <a href="' . site_url('membership/client/jobs') . '">
            <span class="menu-text">' . _l('membership_jobs') . '</span>
        </a>
    </li>';

    echo '<li class="customers-nav-item-custom">
        <a href="' . site_url('membership/client/committees') . '">
            <span class="menu-text">' . _l('membership_committees') . '</span>
        </a>
    </li>';

    echo '<li class="customers-nav-item-custom">
        <a href="' . site_url('membership/client/board_members') . '">
            <span class="menu-text">' . _l('membership_members') . '</span>
        </a>
    </li>';
    
    if ($member && $member['status'] == 'active') {
        echo '<li class="customers-nav-item-custom">
            <a href="' . site_url('membership/client/nominations') . '">
                <i class="fa fa-hand-paper-o menu-icon"></i>
                <span class="menu-text">' . _l('membership_nominations') . '</span>
            </a>
        </li>';
        
        echo '<li class="customers-nav-item-custom">
            <a href="' . site_url('membership/client/cast_vote') . '">
                <i class="fa fa-check-square-o menu-icon"></i>
                <span class="menu-text">' . _l('membership_cast_vote') . '</span>
            </a>
        </li>';
    }
}

function membership_cron_tasks()
{
    $CI = &get_instance();
    $CI->load->model('membership/membership_model');

    $CI->membership_model->check_membership_expiration();
    $CI->membership_model->send_event_reminders();
}

function membership_email_templates()
{
    $CI = &get_instance();

    $all_slugs = [
        'membership-welcome',
        'membership-approved',
        'membership-event-reminder',
        'membership-nomination-submitted-nominee',
        'membership-nomination-submitted-nominator',
        'membership-nomination-approved',
        'membership-nomination-rejected',
    ];

    // Skip if all templates already exist — count only English rows (avoids counting multi-language duplicates)
    $count = $CI->db
        ->where('type', 'membership')
        ->where('language', 'english')
        ->where_in('slug', $all_slugs)
        ->count_all_results(db_prefix() . 'emailtemplates');

    if ($count >= count($all_slugs)) {
        return;
    }

    $templates = [
        [
            'slug'    => 'membership-welcome',
            'name'    => 'Membership - Welcome Email',
            'subject' => 'Welcome to {company_name} Membership',
            'message' => '<p>Dear {contact_firstname},</p>
<p>Thank you for joining <strong>{company_name}</strong>! Your membership account has been created and is currently <strong>pending review</strong>.</p>
<p>Our team will review your application shortly and notify you once approved.</p>
<p><a href="{dashboard_link}" style="display:inline-block;padding:10px 24px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">View Member Dashboard</a></p>
<p>Best regards,<br>{company_name}</p>',
        ],
        [
            'slug'    => 'membership-approved',
            'name'    => 'Membership - Application Approved',
            'subject' => 'Your Membership Has Been Approved – {company_name}',
            'message' => '<p>Dear {contact_firstname},</p>
<p>Great news! Your membership with <strong>{company_name}</strong> has been <strong>approved</strong>.</p>
<p>You now have full access to the member portal — view events, cast votes, submit nominations, and more.</p>
<p><a href="{dashboard_link}" style="display:inline-block;padding:10px 24px;background:#16a34a;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">Go to Member Dashboard</a></p>
<p>Best regards,<br>{company_name}</p>',
        ],
        [
            'slug'    => 'membership-event-reminder',
            'name'    => 'Membership - Event Reminder',
            'subject' => 'Reminder: {event_name} is Tomorrow – {company_name}',
            'message' => '<p>Dear {contact_firstname},</p>
<p>This is a friendly reminder that you are registered for an upcoming event:</p>
<table style="border-collapse:collapse;width:100%;max-width:500px;margin:16px 0;">
  <tr style="background:#f3f4f6;"><td style="padding:10px;font-weight:bold;">Event</td><td style="padding:10px;">{event_name}</td></tr>
  <tr><td style="padding:10px;font-weight:bold;">Date &amp; Time</td><td style="padding:10px;">{event_date}</td></tr>
  <tr style="background:#f3f4f6;"><td style="padding:10px;font-weight:bold;">Location</td><td style="padding:10px;">{event_location}</td></tr>
</table>
<p><a href="{dashboard_link}" style="display:inline-block;padding:10px 24px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">View Member Dashboard</a></p>
<p>Best regards,<br>{company_name}</p>',
        ],
        [
            'slug'    => 'membership-nomination-submitted-nominee',
            'name'    => 'Membership - Nomination Received (Nominee)',
            'subject' => 'You Have Been Nominated – {company_name}',
            'message' => '<p>Dear {contact_firstname},</p>
<p>You have been nominated by <strong>{nominator_name}</strong> to stand as a candidate in the <strong>{election_title}</strong> election.</p>
<p>Your nomination is currently <strong>pending review</strong> by the administration. You will be notified once a decision has been made.</p>
<p><a href="{dashboard_link}" style="display:inline-block;padding:10px 24px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">View Nominations</a></p>
<p>Best regards,<br>{company_name}</p>',
        ],
        [
            'slug'    => 'membership-nomination-submitted-nominator',
            'name'    => 'Membership - Nomination Submitted (Nominator)',
            'subject' => 'Nomination Submitted Successfully – {company_name}',
            'message' => '<p>Dear {contact_firstname},</p>
<p>Your nomination of <strong>{nominee_name}</strong> for the <strong>{election_title}</strong> election has been received and is pending review.</p>
<p>You will be notified once the administration has reviewed the nomination.</p>
<p><a href="{dashboard_link}" style="display:inline-block;padding:10px 24px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">View Nominations</a></p>
<p>Best regards,<br>{company_name}</p>',
        ],
        [
            'slug'    => 'membership-nomination-approved',
            'name'    => 'Membership - Nomination Approved',
            'subject' => 'Your Nomination Has Been Approved – {company_name}',
            'message' => '<p>Dear {contact_firstname},</p>
<p>Congratulations! Your nomination for the <strong>{election_title}</strong> election has been <strong>approved</strong>.</p>
<p>You are now a confirmed candidate. Good luck!</p>
<p><a href="{dashboard_link}" style="display:inline-block;padding:10px 24px;background:#16a34a;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">View Nominations</a></p>
<p>Best regards,<br>{company_name}</p>',
        ],
        [
            'slug'    => 'membership-nomination-rejected',
            'name'    => 'Membership - Nomination Rejected',
            'subject' => 'Update on Your Nomination – {company_name}',
            'message' => '<p>Dear {contact_firstname},</p>
<p>We regret to inform you that your nomination for the <strong>{election_title}</strong> election has not been approved at this time.</p>
<p>Please contact the administration if you have any questions.</p>
<p><a href="{dashboard_link}" style="display:inline-block;padding:10px 24px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">View Nominations</a></p>
<p>Best regards,<br>{company_name}</p>',
        ],
    ];

    foreach ($templates as $tpl) {
        $CI->db->where('slug', $tpl['slug']);
        $existing = $CI->db->get(db_prefix() . 'emailtemplates')->row();

        if (!$existing) {
            $CI->db->insert(db_prefix() . 'emailtemplates', [
                'slug'      => $tpl['slug'],
                'name'      => $tpl['name'],
                'subject'   => $tpl['subject'],
                'message'   => $tpl['message'],
                'type'      => 'membership',
                'fromname'  => '{company_name}',
                'active'    => 1,
                'order'     => 0,
                'plaintext' => 0,
                'language'  => 'english',
            ]);
        } else {
            // Fix any existing rows that were inserted without 'type' or 'message'
            $update = [];
            if (empty($existing->type))    $update['type']    = 'membership';
            if (empty($existing->message)) $update['message'] = $tpl['message'];
            if (!empty($update)) {
                $CI->db->where('slug', $tpl['slug']);
                $CI->db->update(db_prefix() . 'emailtemplates', $update);
            }
        }
    }
}

/**
 * Render the Membership section on the admin Email Templates page.
 * Hooked to: after_email_templates
 */
function membership_email_templates_section()
{
    $CI = &get_instance();

    $templates = $CI->db
        ->where('type', 'membership')
        ->where('language', 'english')
        ->get(db_prefix() . 'emailtemplates')
        ->result_array();

    if (empty($templates)) {
        return;
    }

    $hasPermissionEdit = staff_can('edit', 'email_templates');
    ?>
    <div class="col-md-12">
        <h4 class="tw-font-semibold email-template-heading">
            Membership
            <?php if ($hasPermissionEdit): ?>
                <a href="<?= admin_url('emails/disable_by_type/membership'); ?>" class="pull-right mleft5 mright25"><small><?= _l('disable_all'); ?></small></a>
                <a href="<?= admin_url('emails/enable_by_type/membership'); ?>" class="pull-right"><small><?= _l('enable_all'); ?></small></a>
            <?php endif; ?>
        </h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><span class="tw-font-semibold"><?= _l('email_templates_table_heading_name'); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $t): ?>
                    <tr>
                        <td class="<?= $t['active'] == 0 ? 'tw-line-through' : ''; ?>">
                            <a href="<?= admin_url('emails/email_template/' . $t['emailtemplateid']); ?>">
                                <?= e($t['name']); ?>
                            </a>
                            <?php if ($hasPermissionEdit): ?>
                                <a href="<?= admin_url('emails/' . ($t['active'] == '1' ? 'disable/' : 'enable/') . $t['emailtemplateid']); ?>" class="pull-right">
                                    <small><?= _l($t['active'] == 1 ? 'disable' : 'enable'); ?></small>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php
}

register_activation_hook(MEMBERSHIP_MODULE_NAME, 'membership_module_activation_hook');

function membership_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

register_language_files(MEMBERSHIP_MODULE_NAME, ['membership']);

function membership_module_init_menu_items()
{
    if (is_admin()) {
        $CI = &get_instance();

        $CI->app_menu->add_sidebar_menu_item('membership', [
            'name'     => 'Membership',
            'href'     => admin_url('membership'),
            'position' => 4,
            'icon'     => 'fa-solid fa-users',
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_dashboard',
            'name'     => 'Dashboard',
            'href'     => admin_url('membership'),
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_members',
            'name'     => 'All Members',
            'href'     => admin_url('membership/members'),
            'position' => 2,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_events',
            'name'     => 'Events',
            'href'     => admin_url('membership/events'),
            'position' => 3,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_elections',
            'name'     => 'Elections',
            'href'     => admin_url('membership/elections'),
            'position' => 4,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_positions',
            'name'     => _l('membership_positions'),
            'href'     => admin_url('membership/positions'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_nominations',
            'name'     => _l('membership_nominations'),
            'href'     => admin_url('membership/nominations'),
            'position' => 6,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_final_candidates',
            'name'     => _l('membership_final_candidates'),
            'href'     => admin_url('membership/final_candidates'),
            'position' => 7,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_vote_list',
            'name'     => _l('membership_vote_list'),
            'href'     => admin_url('membership/vote_list'),
            'position' => 8,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_election_results',
            'name'     => _l('membership_election_results'),
            'href'     => admin_url('membership/election_results'),
            'position' => 9,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_committees',
            'name'     => _l('membership_committees'),
            'href'     => admin_url('membership/committees'),
            'position' => 10,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_committee_members',
            'name'     => _l('membership_committee_members'),
            'href'     => admin_url('membership/committee_members'),
            'position' => 11,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_board_members',
            'name'     => _l('membership_board_members'),
            'href'     => admin_url('membership/board_members'),
            'position' => 12,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_jobs',
            'name'     => _l('membership_jobs'),
            'href'     => admin_url('membership/jobs'),
            'position' => 13,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_announcements',
            'name'     => _l('membership_announcements'),
            'href'     => admin_url('membership/announcements'),
            'position' => 14,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_moderator',
            'name'     => 'Moderator',
            'href'     => admin_url('membership/moderators'),
            'position' => 15,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_settings',
            'name'     => 'Settings',
            'href'     => admin_url('membership/settings'),
            'position' => 16,
        ]);
    }
}