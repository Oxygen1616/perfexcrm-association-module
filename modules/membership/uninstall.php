<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Drop custom tables created by the module
$tables = [
    'membership_jobs',
    'membership_stories',
    'membership_events',
    'membership_event_registrations',
    'membership_elections',
    'membership_candidates',
    'membership_votes',
    'membership_members',
    'membership_payments',
    'membership_notice_categories',
    'membership_notices',
    'membership_moderator_roles',
    'membership_moderators',
    'membership_event_transactions',
    'membership_subscription_transactions',
    'membership_committee_categories',
    'membership_committee_designations',
    'membership_committees',
    'membership_committee_members',
    'membership_election_symbols',
    'membership_nominations',
    'membership_nomination_transactions',
    'membership_board_members',
    'membership_vote_comments',
];

foreach ($tables as $table) {
    if ($CI->db->table_exists(db_prefix() . $table)) {
        $CI->db->query('DROP TABLE `' . db_prefix() . $table . '`');
    }
}

// Remove email templates added by the module
$email_templates = [
    'membership-welcome',
    'membership-approved',
    'membership-event-reminder'
];

foreach ($email_templates as $slug) {
    $CI->db->where('slug', $slug);
    $CI->db->delete(db_prefix() . 'emailtemplates');
}

// Remove options set by the module
$options_to_remove = [
    'membership_monthly_dues',
    'membership_default_type',
    'membership_auto_approve_jobs',
    'membership_auto_approve_stories'
];

foreach ($options_to_remove as $option) {
    delete_option($option);
}