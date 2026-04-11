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
hooks()->add_action('after_cron_run', 'membership_cron_tasks');
hooks()->add_action('customers_navigation_end', 'membership_client_menu');
hooks()->add_action('after_email_templates_init', 'membership_email_templates');

hooks()->add_filter('migration_tables_to_replace_old_links', 'membership_migration_tables');

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
            <i class="fa fa-users menu-icon"></i>
            <span class="menu-text">' . _l('membership_dashboard') . '</span>
        </a>
    </li>';
    
    echo '<li class="customers-nav-item-custom">
        <a href="' . site_url('membership/client/committees') . '">
            <i class="fa fa-sitemap menu-icon"></i>
            <span class="menu-text">' . _l('membership_committees') . '</span>
        </a>
    </li>';
    
    echo '<li class="customers-nav-item-custom">
        <a href="' . site_url('membership/client/board_members') . '">
            <i class="fa fa-black-tie menu-icon"></i>
            <span class="menu-text">' . _l('membership_board_members') . '</span>
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

    $CI->db->where('slug', 'membership-welcome');
    $template = $CI->db->get(db_prefix() . 'emailtemplates')->row();

    if (!$template) {
        $CI->db->insert(db_prefix() . 'emailtemplates', [
            'name'          => 'Membership - Welcome Email',
            'slug'          => 'membership-welcome',
            'subject'       => 'Welcome to {company_name} Membership',
            'body'          => '<p>Dear {contact_first_name},</p><p>Welcome to our membership program! Your account has been created and is pending approval.</p><p>Best regards,<br>{company_name}</p>',
            'fromname'      => '{company_name}',
            'fromemail'     => '{company_email}',
            'active'        => 1,
            'order'         => 0,
        ]);
    }

    $CI->db->where('slug', 'membership-approved');
    $template = $CI->db->get(db_prefix() . 'emailtemplates')->row();

    if (!$template) {
        $CI->db->insert(db_prefix() . 'emailtemplates', [
            'name'          => 'Membership - Approved',
            'slug'          => 'membership-approved',
            'subject'       => 'Your Membership Application Approved - {company_name}',
            'body'          => '<p>Dear {contact_first_name},</p><p>Your membership application has been approved! You can now access the member dashboard.</p><p>Best regards,<br>{company_name}</p>',
            'fromname'      => '{company_name}',
            'fromemail'     => '{company_email}',
            'active'        => 1,
            'order'         => 0,
        ]);
    }

    $CI->db->where('slug', 'membership-event-reminder');
    $template = $CI->db->get(db_prefix() . 'emailtemplates')->row();

    if (!$template) {
        $CI->db->insert(db_prefix() . 'emailtemplates', [
            'name'          => 'Membership - Event Reminder',
            'slug'          => 'membership-event-reminder',
            'subject'       => 'Reminder: {event_name} - {company_name}',
            'body'          => '<p>Dear {contact_first_name},</p><p>This is a reminder that {event_name} is coming up on {event_date}.</p><p>Best regards,<br>{company_name}</p>',
            'fromname'      => '{company_name}',
            'fromemail'     => '{company_email}',
            'active'        => 1,
            'order'         => 0,
        ]);
    }
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
            'position' => 0,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_members',
            'name'     => 'All Members',
            'href'     => admin_url('membership/members'),
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_events',
            'name'     => 'Events',
            'href'     => admin_url('membership/events'),
            'position' => 2,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_elections',
            'name'     => 'Elections',
            'href'     => admin_url('membership/elections'),
            'position' => 4,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_positions',
            'name'     => 'Positions',
            'href'     => admin_url('membership/positions'),
            'position' => 5,
        ]);

$CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_moderator',
            'name'     => 'Moderator',
            'href'     => admin_url('membership/moderators'),
            'position' => 8,
        ]);

        $CI->app_menu->add_sidebar_children_item('membership', [
            'slug'     => 'membership_settings',
            'name'     => 'Settings',
            'href'     => admin_url('membership/settings'),
            'position' => 9,
        ]);
    }
}

hooks()->add_action('admin_init', 'manage_committee_menu');

function manage_committee_menu()
{
    if (!is_admin()) {
        return;
    }

    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('manage_committee', [
        'name'     => _l('membership_manage_committee'),
        'href'     => admin_url('membership/committee_categories'),
        'position' => 10,
        'icon'     => 'fa-solid fa-users-rectangle',
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'committee_categories',
        'name'     => _l('membership_committee_categories'),
        'href'     => admin_url('membership/committee_categories'),
        'position' => 1,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'committee_designations',
        'name'     => _l('membership_committee_designations'),
        'href'     => admin_url('membership/committee_designations'),
        'position' => 2,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'committees',
        'name'     => _l('membership_committees'),
        'href'     => admin_url('membership/committees'),
        'position' => 3,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'committee_members',
        'name'     => _l('membership_committee_members'),
        'href'     => admin_url('membership/committee_members'),
        'position' => 4,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'committee_election',
        'name'     => _l('membership_committee_election'),
        'href'     => admin_url('membership/vote_elections'),
        'position' => 5,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'election_symbols',
        'name'     => _l('membership_election_symbols'),
        'href'     => admin_url('membership/election_symbols'),
        'position' => 6,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'nominations',
        'name'     => _l('membership_nominations'),
        'href'     => admin_url('membership/nominations'),
        'position' => 7,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'nomination_transactions',
        'name'     => _l('membership_nomination_transactions'),
        'href'     => admin_url('membership/nomination_transactions'),
        'position' => 8,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_committee', [
        'slug'     => 'board_members',
        'name'     => _l('membership_board_members'),
        'href'     => admin_url('membership/board_members'),
        'position' => 9,
    ]);
}

hooks()->add_action('admin_init', 'manage_vote_menu');

function manage_vote_menu()
{
    if (!is_admin()) {
        return;
    }

    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('manage_vote', [
        'name'     => _l('membership_manage_vote'),
        'href'     => admin_url('membership/vote_elections'),
        'position' => 11,
        'icon'     => 'fa-solid fa-check-to-slot',
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_vote', [
        'slug'     => 'vote_elections',
        'name'     => _l('membership_elections'),
        'href'     => admin_url('membership/vote_elections'),
        'position' => 1,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_vote', [
        'slug'     => 'vote_nominations',
        'name'     => _l('membership_nominations'),
        'href'     => admin_url('membership/vote_nominations'),
        'position' => 2,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_vote', [
        'slug'     => 'pending_candidates',
        'name'     => _l('membership_pending_candidates'),
        'href'     => admin_url('membership/pending_candidates'),
        'position' => 3,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_vote', [
        'slug'     => 'final_candidates',
        'name'     => _l('membership_final_candidates'),
        'href'     => admin_url('membership/final_candidates'),
        'position' => 4,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_vote', [
        'slug'     => 'candidate_comments',
        'name'     => _l('membership_candidate_comments'),
        'href'     => admin_url('membership/candidate_comments'),
        'position' => 5,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_vote', [
        'slug'     => 'vote_list',
        'name'     => _l('membership_vote_list'),
        'href'     => admin_url('membership/vote_list'),
        'position' => 6,
    ]);

    $CI->app_menu->add_sidebar_children_item('manage_vote', [
        'slug'     => 'election_results',
        'name'     => _l('membership_election_results'),
        'href'     => admin_url('membership/election_results'),
        'position' => 7,
    ]);
}