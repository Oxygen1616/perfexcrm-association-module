<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'membership_jobs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_jobs` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `contact_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `company` varchar(255) DEFAULT NULL,
      `description` text NOT NULL,
      `location` varchar(255) DEFAULT NULL,
      `salary_range` varchar(100) DEFAULT NULL,
      `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `contact_id` (`contact_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_stories')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_stories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `contact_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `content` text NOT NULL,
      `image_url` varchar(500) DEFAULT NULL,
      `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `contact_id` (`contact_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_events')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_events` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `location` varchar(255) DEFAULT NULL,
      `event_date` datetime NOT NULL,
      `event_end_date` datetime DEFAULT NULL,
      `max_attendees` int(11) DEFAULT NULL,
      `created_by` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `created_by` (`created_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_event_registrations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_event_registrations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `event_id` int(11) NOT NULL,
      `contact_id` int(11) NOT NULL,
      `qr_code` varchar(100) DEFAULT NULL,
      `registered_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `event_id` (`event_id`),
      KEY `contact_id` (`contact_id`),
      UNIQUE KEY `unique_registration` (`event_id`, `contact_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_elections')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_elections` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `nomination_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
      `start_date` datetime NOT NULL,
      `end_date` datetime NOT NULL,
      `status` enum('draft','active','closed') NOT NULL DEFAULT 'draft',
      `created_by` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `created_by` (`created_by`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_candidates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_candidates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `election_id` int(11) NOT NULL,
      `contact_id` int(11) NOT NULL,
      `bio` text DEFAULT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `election_id` (`election_id`),
      KEY `contact_id` (`contact_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_votes')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_votes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `election_id` int(11) NOT NULL,
      `candidate_id` int(11) NOT NULL,
      `contact_id` int(11) NOT NULL,
      `voted_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `election_id` (`election_id`),
      KEY `candidate_id` (`candidate_id`),
      KEY `contact_id` (`contact_id`),
      UNIQUE KEY `one_vote_per_member` (`election_id`, `contact_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_members')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_members` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `contact_id` int(11) NOT NULL,
      `status` enum('pending','active','suspended') NOT NULL DEFAULT 'pending',
      `membership_type` varchar(100) DEFAULT NULL,
      `profession` varchar(255) DEFAULT NULL,
      `graduation_year` year(4) DEFAULT NULL,
      `show_in_directory` tinyint(1) NOT NULL DEFAULT '1',
      `membership_start` date DEFAULT NULL,
      `membership_end` date DEFAULT NULL,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `contact_id` (`contact_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_positions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_positions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_payments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_payments` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL,
      `amount` decimal(10,2) NOT NULL,
      `payment_date` date NOT NULL,
      `payment_method` varchar(100) DEFAULT NULL,
      `transaction_id` varchar(255) DEFAULT NULL,
      `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
      `invoice_id` int(11) DEFAULT NULL,
      `notes` text DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `member_id` (`member_id`),
      KEY `payment_date` (`payment_date`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_notice_categories')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_notice_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_notices')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_notices` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `category_id` int(11) DEFAULT NULL,
      `title` varchar(255) NOT NULL,
      `content` text NOT NULL,
      `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
      `created_by` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `category_id` (`category_id`),
      KEY `created_by` (`created_by`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_moderator_roles')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_moderator_roles` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `permissions` text DEFAULT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_moderators')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_moderators` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) NOT NULL,
      `role_id` int(11) NOT NULL,
      `status` enum('active','inactive') NOT NULL DEFAULT 'active',
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `staff_id` (`staff_id`),
      KEY `role_id` (`role_id`),
      UNIQUE KEY `unique_staff` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_event_transactions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_event_transactions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `event_id` int(11) NOT NULL,
      `member_id` int(11) NOT NULL,
      `amount` decimal(10,2) NOT NULL,
      `ticket_type` varchar(100) DEFAULT NULL,
      `payment_method` varchar(100) DEFAULT NULL,
      `transaction_id` varchar(255) DEFAULT NULL,
      `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
      `transaction_date` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `event_id` (`event_id`),
      KEY `member_id` (`member_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_subscription_transactions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_subscription_transactions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL,
      `plan_name` varchar(255) NOT NULL,
      `amount` decimal(10,2) NOT NULL,
      `billing_cycle` varchar(50) DEFAULT NULL,
      `start_date` date NOT NULL,
      `end_date` date DEFAULT NULL,
      `payment_method` varchar(100) DEFAULT NULL,
      `transaction_id` varchar(255) DEFAULT NULL,
      `status` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
      `transaction_date` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `member_id` (`member_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_committee_categories')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_committee_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `status` enum('active','inactive') NOT NULL DEFAULT 'active',
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_committee_designations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_committee_designations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_committees')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_committees` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `category_id` int(11) DEFAULT NULL,
      `name` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `status` enum('active','inactive') NOT NULL DEFAULT 'active',
      `created_by` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `category_id` (`category_id`),
      KEY `created_by` (`created_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_committee_members')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_committee_members` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `committee_id` int(11) NOT NULL,
      `member_id` int(11) NOT NULL,
      `designation_id` int(11) DEFAULT NULL,
      `term_start` date DEFAULT NULL,
      `term_end` date DEFAULT NULL,
      `status` enum('active','inactive') NOT NULL DEFAULT 'active',
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `committee_id` (`committee_id`),
      KEY `member_id` (`member_id`),
      KEY `designation_id` (`designation_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_election_symbols')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_election_symbols` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `symbol_image` varchar(500) DEFAULT NULL,
      `description` text DEFAULT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_nominations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_nominations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `election_id` int(11) NOT NULL,
      `committee_id` int(11) DEFAULT NULL,
      `member_id` int(11) NOT NULL,
      `position` varchar(255) NOT NULL,
      `manifesto` text DEFAULT NULL,
      `symbol_id` int(11) DEFAULT NULL,
      `photo` varchar(255) DEFAULT NULL,
      `nid` varchar(255) DEFAULT NULL,
      `declaration` tinyint(1) NOT NULL DEFAULT 0,
      `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
      `nominated_at` datetime NOT NULL,
      `reviewed_at` datetime DEFAULT NULL,
      `reviewed_by` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `election_id` (`election_id`),
      KEY `committee_id` (`committee_id`),
      KEY `member_id` (`member_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_nomination_transactions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_nomination_transactions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nomination_id` int(11) NOT NULL,
      `member_id` int(11) NOT NULL,
      `amount` decimal(10,2) NOT NULL,
      `currency` varchar(10) NOT NULL DEFAULT 'USD',
      `payment_method` varchar(100) DEFAULT NULL,
      `transaction_id` varchar(255) DEFAULT NULL,
      `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
      `transaction_date` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `nomination_id` (`nomination_id`),
      KEY `member_id` (`member_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_board_members')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_board_members` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `member_id` int(11) NOT NULL,
      `position` varchar(255) NOT NULL,
      `election_id` int(11) DEFAULT NULL,
      `term_start` date NOT NULL,
      `term_end` date DEFAULT NULL,
      `status` enum('active','inactive') NOT NULL DEFAULT 'active',
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `member_id` (`member_id`),
      KEY `election_id` (`election_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}

if (!$CI->db->table_exists(db_prefix() . 'membership_vote_comments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "membership_vote_comments` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `election_id` int(11) NOT NULL,
      `candidate_id` int(11) NOT NULL,
      `member_id` int(11) NOT NULL,
      `comment` text NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `election_id` (`election_id`),
      KEY `candidate_id` (`candidate_id`),
      KEY `member_id` (`member_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set);
}