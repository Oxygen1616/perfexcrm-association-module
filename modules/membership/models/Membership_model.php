<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Membership_model extends CI_Model
{
    protected $table = 'tblmembership_members';
    protected $table_jobs = 'tblmembership_jobs';
    protected $table_stories = 'tblmembership_stories';
    protected $table_events = 'tblmembership_events';
    protected $table_event_registrations = 'tblmembership_event_registrations';
    protected $table_elections = 'tblmembership_elections';
    protected $table_candidates = 'tblmembership_candidates';
    protected $table_votes = 'tblmembership_votes';
    protected $table_payments = 'tblmembership_payments';
    protected $table_moderator_roles = 'tblmembership_moderator_roles';
    protected $table_moderators = 'tblmembership_moderators';
    protected $table_event_transactions = 'tblmembership_event_transactions';
    protected $table_subscription_transactions = 'tblmembership_subscription_transactions';
    protected $table_committee_categories = 'tblmembership_committee_categories';
    protected $table_committee_designations = 'tblmembership_committee_designations';
    protected $table_committees = 'tblmembership_committees';
    protected $table_committee_members = 'tblmembership_committee_members';
    protected $table_election_symbols = 'tblmembership_election_symbols';
    protected $table_nominations = 'tblmembership_nominations';
    protected $table_nomination_transactions = 'tblmembership_nomination_transactions';
    protected $table_board_members = 'tblmembership_board_members';
    protected $table_vote_comments = 'tblmembership_vote_comments';
    protected $table_positions = 'tblmembership_positions';
    protected $table_membership_types   = 'tblmembership_types';
    protected $table_announcements      = 'tblmembership_announcements';
    protected $table_event_settings     = 'tblmembership_event_settings';

    public function __construct()
    {
        parent::__construct();
        $this->check_tables();
        $this->_maybe_migrate();
    }

    private function check_tables()
    {
        $tables = [
            $this->table,
            $this->table_elections,
            $this->table_committees,
            $this->table_board_members,
        ];

        foreach ($tables as $table) {
            if (!$this->db->table_exists($table)) {
                return false;
            }
        }

        // Add join_date column if it doesn't exist (replaces graduation_year)
        if ($this->db->table_exists($this->table)) {
            $q = $this->db->query(
                'SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME   = \'' . $this->db->escape_str($this->table) . '\'
                   AND COLUMN_NAME  = \'join_date\''
            )->row_array();
            if (empty($q['cnt'])) {
                $this->db->query('ALTER TABLE `' . $this->table . '` ADD COLUMN `join_date` DATE DEFAULT NULL');
            }
        }

        return true;
    }

    private function _maybe_migrate()
    {
        // ── Jobs: add new columns if missing ─────────────────────────
        $tbl = db_prefix() . 'membership_jobs';
        if ($this->db->table_exists($tbl)) {
            if (!$this->db->field_exists('posted_by_type', $tbl)) {
                $this->db->query("ALTER TABLE `{$tbl}` ADD COLUMN `posted_by_type` enum('admin','member') NOT NULL DEFAULT 'member' AFTER `status`");
            }
            if (!$this->db->field_exists('posted_by_name', $tbl)) {
                $this->db->query("ALTER TABLE `{$tbl}` ADD COLUMN `posted_by_name` varchar(255) DEFAULT NULL AFTER `posted_by_type`");
            }
            if (!$this->db->field_exists('external_url', $tbl)) {
                $this->db->query("ALTER TABLE `{$tbl}` ADD COLUMN `external_url` varchar(500) DEFAULT NULL AFTER `salary_range`");
            }
        }

        // ── Nominations: add nominated_by_contact_id if missing ──────
        $nom_tbl = db_prefix() . 'membership_nominations';
        if ($this->db->table_exists($nom_tbl) && !$this->db->field_exists('nominated_by_contact_id', $nom_tbl)) {
            $this->db->query("ALTER TABLE `{$nom_tbl}` ADD COLUMN `nominated_by_contact_id` int(11) DEFAULT NULL AFTER `member_id`");
        }

        // ── Elections: add positions column if missing ────────────────
        $elec_tbl = db_prefix() . 'membership_elections';
        if ($this->db->table_exists($elec_tbl) && !$this->db->field_exists('positions', $elec_tbl)) {
            $this->db->query("ALTER TABLE `{$elec_tbl}` ADD COLUMN `positions` text DEFAULT NULL AFTER `description`");
        }

        // ── Positions: add election_id column if missing ──────────────
        $pos_tbl = db_prefix() . 'membership_positions';
        if ($this->db->table_exists($pos_tbl) && !$this->db->field_exists('election_id', $pos_tbl)) {
            $this->db->query("ALTER TABLE `{$pos_tbl}` ADD COLUMN `election_id` int(11) DEFAULT NULL AFTER `id`");
            $this->db->query("ALTER TABLE `{$pos_tbl}` ADD KEY `election_id` (`election_id`)");
        }

        // ── Announcements table ───────────────────────────────────────
        if (!$this->db->table_exists($this->table_announcements)) {
            $this->db->query('CREATE TABLE `' . $this->table_announcements . "` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `body` text NOT NULL,
              `priority` enum('normal','important','urgent') NOT NULL DEFAULT 'normal',
              `staff_id` int(11) NOT NULL,
              `created_at` datetime NOT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $this->db->char_set);
        }

        // ── Per-event registration settings ──────────────────────────
        if (!$this->db->table_exists($this->table_event_settings)) {
            $this->db->query('CREATE TABLE `' . $this->table_event_settings . "` (
              `event_id` int(11) NOT NULL,
              `registration_open` tinyint(1) NOT NULL DEFAULT 1,
              PRIMARY KEY (`event_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $this->db->char_set);
        }
    }

    /**
     * Return all contacts that are NOT already members.
     */
    public function get_available_contacts()
    {
        $this->db->select('c.id, c.firstname, c.lastname, c.email');
        $this->db->from(db_prefix() . 'contacts c');
        $this->db->join(
            $this->table . ' m',
            'm.contact_id = c.id',
            'left'
        );
        $this->db->where('m.id IS NULL');
        $this->db->order_by('c.firstname', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_member_by_contact_id($contact_id)
    {
        if (!$this->db->table_exists($this->table)) {
            return null;
        }
        return $this->db->where('contact_id', $contact_id)
            ->get($this->table)
            ->row_array();
    }

    public function get_all_members($status = null)
    {
        if (!$this->db->table_exists($this->table)) {
            return [];
        }
        if ($status) {
            $this->db->where('tm.status', $status);
        }
        $this->db->select('tm.*, tc.firstname, tc.lastname, tc.email, tc.phonenumber');
        $this->db->from($this->table . ' tm');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id', 'left');
        return $this->db->order_by('tc.firstname', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_membership_types($status = null)
    {
        if (!$this->db->table_exists($this->table_membership_types)) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table_membership_types}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
                `description` text DEFAULT NULL,
                `status` enum('active','inactive') NOT NULL DEFAULT 'active',
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            return [];
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->order_by('name', 'ASC')->get($this->table_membership_types)->result_array();
    }

    public function get_membership_type($id)
    {
        return $this->db->where('id', $id)->get($this->table_membership_types)->row_array();
    }

    public function create_membership_type($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_membership_types, $data);
        return $this->db->insert_id();
    }

    public function update_membership_type($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_membership_types, $data);
    }

    public function delete_membership_type($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_membership_types);
    }

    public function get_member($id)
    {
        return $this->db
            ->select('m.*, c.firstname, c.lastname, c.email')
            ->from($this->table . ' m')
            ->join(db_prefix() . 'contacts c', 'c.id = m.contact_id', 'left')
            ->where('m.id', $id)
            ->get()
            ->row_array();
    }

    public function create_member($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update_member($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_member($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function update_member_status($id, $status)
    {
        $data['status'] = $status;
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($status == 'active') {
            $data['membership_start'] = date('Y-m-d');
        }

        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function get_jobs($status = 'approved')
    {
        if (!$this->db->table_exists($this->table_jobs)) {
            return [];
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->order_by('created_at', 'DESC')
            ->get($this->table_jobs)
            ->result_array();
    }

    public function get_job($id)
    {
        return $this->db->where('id', $id)
            ->get($this->table_jobs)
            ->row_array();
    }

    public function create_job($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_jobs, $data);
        return $this->db->insert_id();
    }

    public function update_job($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table_jobs, $data);
    }

    public function delete_job($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_jobs);
    }

    public function get_pending_jobs()
    {
        return $this->db->where('status', 'pending')
            ->order_by('created_at', 'DESC')
            ->get($this->table_jobs)
            ->result_array();
    }

    public function approve_job($id)
    {
        return $this->update_job($id, ['status' => 'approved']);
    }

    public function reject_job($id)
    {
        return $this->update_job($id, ['status' => 'rejected']);
    }

    public function get_stories($status = 'approved')
    {
        if (!$this->db->table_exists($this->table_stories)) {
            return [];
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->order_by('created_at', 'DESC')
            ->get($this->table_stories)
            ->result_array();
    }

    public function get_story($id)
    {
        return $this->db->where('id', $id)
            ->get($this->table_stories)
            ->row_array();
    }

    public function create_story($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_stories, $data);
        return $this->db->insert_id();
    }

    public function update_story($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table_stories, $data);
    }

    public function delete_story($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_stories);
    }

    public function get_pending_stories()
    {
        return $this->db->where('status', 'pending')
            ->order_by('created_at', 'DESC')
            ->get($this->table_stories)
            ->result_array();
    }

    public function approve_story($id)
    {
        return $this->update_story($id, ['status' => 'approved']);
    }

    public function reject_story($id)
    {
        return $this->update_story($id, ['status' => 'rejected']);
    }

    private function _events_select()
    {
        // false on select() disables CI3 identifier escaping so AS aliases survive;
        // backtick start/end manually because they are MySQL reserved words
        $this->db->select('eventid AS id, title, `start` AS event_date, `end` AS event_end_date, description, color', false);
        $this->db->where('public', 1);
    }

    public function get_events($upcoming = false)
    {
        $this->_events_select();
        if ($upcoming) {
            $this->db->where('`start` >=', date('Y-m-d H:i:s'));
        }
        return $this->db->order_by('`start`', 'ASC')
            ->get(db_prefix() . 'events')
            ->result_array();
    }

    public function get_events_by_week_range($from, $to)
    {
        $this->_events_select();
        $this->db->where('`start` >=', $from);
        $this->db->where('`start` <=', $to);
        return $this->db->order_by('`start`', 'ASC')
            ->get(db_prefix() . 'events')
            ->result_array();
    }

    public function get_event($id)
    {
        $this->_events_select();
        $this->db->where('eventid', $id);
        return $this->db->get(db_prefix() . 'events')->row_array();
    }

    public function create_event($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();
        $this->db->insert($this->table_events, $data);
        return $this->db->insert_id();
    }

    /**
     * Fetch all raw columns from tblevents for the edit form.
     */
    public function get_event_for_edit($id)
    {
        return $this->db->where('eventid', (int) $id)
            ->get(db_prefix() . 'events')
            ->row_array();
    }

    /**
     * Update a Perfex calendar event in tblevents.
     */
    public function update_perfex_event($id, $data)
    {
        $this->db->where('eventid', (int) $id);
        return $this->db->update(db_prefix() . 'events', $data);
    }

    public function update_event($id, $data)
    {
        // Legacy method kept for safety — now delegates to tblevents
        return $this->update_perfex_event($id, $data);
    }

    public function delete_event($id)
    {
        // Remove registrations first, then remove the membership-side settings row
        $this->db->where('event_id', $id);
        $this->db->delete($this->table_event_registrations);

        $this->db->where('event_id', $id);
        $this->db->delete($this->table_event_settings);

        // NOTE: we intentionally do NOT delete from tblevents — use the Perfex calendar for that
    }

    public function register_for_event($event_id, $contact_id)
    {
        $event = $this->get_event($event_id);
        if (!$event) {
            return false;
        }

        $this->db->where('event_id', $event_id);
        $this->db->where('contact_id', $contact_id);
        $existing = $this->db->get($this->table_event_registrations)->row();

        if ($existing) {
            return false;
        }

        // max_attendees is not a field on the core events table — skip capacity check

        $qr_code = $this->generate_qr_code($event_id, $contact_id);

        $data = [
            'event_id' => $event_id,
            'contact_id' => $contact_id,
            'qr_code' => $qr_code,
            'registered_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table_event_registrations, $data);
        return $this->db->insert_id();
    }

    public function get_event_registration_count($event_id)
    {
        $this->db->where('event_id', $event_id);
        return $this->db->count_all_results($this->table_event_registrations);
    }

    public function get_event_registration($event_id, $contact_id)
    {
        return $this->db->where('event_id', $event_id)
            ->where('contact_id', $contact_id)
            ->get($this->table_event_registrations)
            ->row_array();
    }

    public function get_event_registrations($event_id)
    {
        $this->db->select('mer.*, c.firstname, c.lastname, c.email');
        $this->db->from($this->table_event_registrations . ' mer');
        $this->db->join(db_prefix() . 'contacts c', 'c.id = mer.contact_id', 'left');
        $this->db->where('mer.event_id', $event_id);
        $this->db->order_by('mer.registered_at', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_event_registration_counts()
    {
        $rows = $this->db
            ->select('event_id, COUNT(*) as cnt')
            ->group_by('event_id')
            ->get($this->table_event_registrations)
            ->result_array();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['event_id']] = (int) $r['cnt'];
        }
        return $map;
    }

    /**
     * Returns true when registration is open for a specific event.
     * Default is OPEN unless explicitly closed in tblmembership_event_settings.
     */
    public function is_event_registration_open($event_id)
    {
        $row = $this->db->where('event_id', (int) $event_id)
            ->get($this->table_event_settings)
            ->row_array();
        return ($row === null) ? true : (bool)(int) $row['registration_open'];
    }

    /**
     * Returns map of [event_id => registration_open (1/0)] for ALL events
     * that have an explicit setting row. Events with no row default to open.
     */
    public function get_event_registration_open_map()
    {
        $rows = $this->db->get($this->table_event_settings)->result_array();
        $map  = [];
        foreach ($rows as $r) {
            $map[(int) $r['event_id']] = (int) $r['registration_open'];
        }
        return $map;
    }

    /**
     * Open or close registration for a single event (upsert).
     */
    public function set_event_registration($event_id, $open)
    {
        $event_id = (int) $event_id;
        $open     = $open ? 1 : 0;
        $existing = $this->db->where('event_id', $event_id)
            ->get($this->table_event_settings)
            ->row_array();
        if ($existing) {
            $this->db->where('event_id', $event_id)
                ->update($this->table_event_settings, ['registration_open' => $open]);
        } else {
            $this->db->insert($this->table_event_settings, [
                'event_id'          => $event_id,
                'registration_open' => $open,
            ]);
        }
        return true;
    }

    public function get_my_registrations($contact_id)
    {
        $this->db->select('mer.*, e.title as event_title, e.`start` as event_date', false);
        $this->db->from($this->table_event_registrations . ' mer');
        $this->db->join(db_prefix() . 'events e', 'e.eventid = mer.event_id');
        $this->db->where('mer.contact_id', $contact_id);
        $this->db->order_by('e.`start`', 'ASC');
        return $this->db->get()->result_array();
    }

    protected function generate_qr_code($event_id, $contact_id)
    {
        return md5($event_id . $contact_id . time());
    }

    public function get_elections($status = null)
    {
        if (!$this->db->table_exists($this->table_elections)) {
            return [];
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->order_by('start_date', 'DESC')
            ->get($this->table_elections)
            ->result_array();
    }

    public function get_active_election()
    {
        return $this->db->where('status', 'active')
            ->where('start_date <=', date('Y-m-d H:i:s'))
            ->where('end_date >=', date('Y-m-d H:i:s'))
            ->get($this->table_elections)
            ->row_array();
    }

    public function get_election($id)
    {
        return $this->db->where('id', $id)
            ->get($this->table_elections)
            ->row_array();
    }

    public function create_election($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();
        $this->db->insert($this->table_elections, $data);
        return $this->db->insert_id();
    }

    public function update_election($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table_elections, $data);
    }

    public function delete_election($id)
    {
        $this->db->where('election_id', $id);
        $this->db->delete($this->table_votes);

        $this->db->where('election_id', $id);
        $this->db->delete($this->table_candidates);

        $this->db->where('id', $id);
        return $this->db->delete($this->table_elections);
    }

    public function get_candidates($election_id)
    {
        $this->db->select('mc.*, tc.firstname, tc.lastname, tc.email');
        $this->db->from($this->table_candidates . ' mc');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = mc.contact_id');
        $this->db->where('mc.election_id', $election_id);
        return $this->db->get()->result_array();
    }

    public function get_final_candidates($election_id)
    {
        $this->db->select('mn.*, tc.firstname, tc.lastname, tc.email, mn.position, mn.manifesto');
        $this->db->from($this->table_nominations . ' mn');
        $this->db->join($this->table . ' tm', 'tm.id = mn.member_id');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id');
        $this->db->where('mn.election_id', $election_id);
        $this->db->where('mn.status', 'approved');
        return $this->db->get()->result_array();
    }

    public function add_candidate($election_id, $contact_id, $bio = null)
    {
        $data = [
            'election_id' => $election_id,
            'contact_id' => $contact_id,
            'bio' => $bio,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->insert($this->table_candidates, $data);
        return $this->db->insert_id();
    }

    public function remove_candidate($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_candidates);
    }

    public function cast_vote($election_id, $candidate_id, $contact_id)
    {
        $this->db->where('election_id', $election_id);
        $this->db->where('contact_id', $contact_id);
        $existing = $this->db->get($this->table_votes)->row();

        if ($existing) {
            return false;
        }

        $data = [
            'election_id' => $election_id,
            'candidate_id' => $candidate_id,
            'contact_id' => $contact_id,
            'voted_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table_votes, $data);
        return $this->db->insert_id();
    }

    public function has_voted($election_id, $contact_id)
    {
        $this->db->where('election_id', $election_id);
        $this->db->where('contact_id', $contact_id);
        return $this->db->count_all_results($this->table_votes) > 0;
    }

    public function get_election_results($election_id)
    {
        // Votes store nomination IDs as candidate_id, so join nominations
        $this->db->select('mn.id as candidate_id, tm.contact_id, tc.firstname, tc.lastname, mn.position, COUNT(mv.id) as vote_count');
        $this->db->from($this->table_nominations . ' mn');
        $this->db->join($this->table . ' tm', 'tm.id = mn.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id', 'left');
        $this->db->join($this->table_votes . ' mv', 'mv.candidate_id = mn.id', 'left');
        $this->db->where('mn.election_id', $election_id);
        $this->db->where('mn.status', 'approved');
        $this->db->group_by('mn.id');
        $this->db->order_by('vote_count', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_total_votes($election_id)
    {
        $this->db->where('election_id', $election_id);
        return $this->db->count_all_results($this->table_votes);
    }

    public function get_payments($member_id = null, $status = null)
    {
        if (!$this->db->table_exists($this->table_payments)) {
            return [];
        }
        if ($member_id) {
            $this->db->where('member_id', $member_id);
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->order_by('payment_date', 'DESC')
            ->get($this->table_payments)
            ->result_array();
    }

    public function create_payment($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_payments, $data);
        return $this->db->insert_id();
    }

    public function update_payment($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_payments, $data);
    }

    public function check_membership_expiration()
    {
        $this->db->where('status', 'active');
        $this->db->where('membership_end <', date('Y-m-d'));
        $expired = $this->db->get($this->table)->result_array();

        foreach ($expired as $member) {
            $this->update_member($member['id'], ['status' => 'suspended']);
        }
    }

    public function send_event_reminders()
    {
        $reminder_time = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->db->where('event_date >=', $reminder_time);
        $this->db->where('event_date <', date('Y-m-d H:i:s', strtotime('+25 hours')));
        $events = $this->db->get($this->table_events)->result_array();

        foreach ($events as $event) {
            $this->db->where('status', 'active');
            $members = $this->db->get($this->table)->result_array();

            foreach ($members as $member) {
                $this->db->where('id', $member['contact_id']);
                $contact = $this->db->get(db_prefix() . 'contacts')->row();

                if ($contact && $contact->email) {
                    send_mail_template(
                        'Membership_event_reminder',
                        'membership',
                        $contact->email,
                        $contact->id,
                        $event['title'],
                        $event['event_date'],
                        $event['location'] ?? ''
                    );
                }
            }
        }
    }

    // ── Announcements ─────────────────────────────────────────────────
    public function get_announcements()
    {
        if (!$this->db->table_exists($this->table_announcements)) return [];
        return $this->db->order_by('created_at', 'DESC')->get($this->table_announcements)->result_array();
    }

    public function get_announcement($id)
    {
        return $this->db->where('id', $id)->get($this->table_announcements)->row_array();
    }

    public function create_announcement($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_announcements, $data);
        return $this->db->insert_id();
    }

    public function update_announcement($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table_announcements, $data);
    }

    public function delete_announcement($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_announcements);
    }

    // ── Recent members ────────────────────────────────────────────────
    public function get_recent_members($limit = 5)
    {
        if (!$this->db->table_exists($this->table)) return [];
        $this->db->select('tm.*, tc.firstname, tc.lastname, tc.email');
        $this->db->from($this->table . ' tm');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id');
        $this->db->where('tm.status', 'active');
        $this->db->order_by('tm.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    // ── Active elections for feed ─────────────────────────────────────
    public function get_active_elections_for_feed()
    {
        if (!$this->db->table_exists($this->table_elections)) return [];
        return $this->db->where('status', 'active')->order_by('start_date', 'DESC')->get($this->table_elections)->result_array();
    }

    public function get_activity_feed($limit = 20)
    {
        $activities = [];

        // Jobs
        foreach ($this->get_jobs('approved') as $job) {
            $activities[] = ['type' => 'job', 'data' => $job, 'date' => $job['created_at']];
        }

        // Stories
        foreach ($this->get_stories('approved') as $story) {
            $activities[] = ['type' => 'story', 'data' => $story, 'date' => $story['created_at']];
        }

        // Upcoming events
        foreach ($this->get_events(true) as $event) {
            $activities[] = ['type' => 'event', 'data' => $event, 'date' => $event['event_date']];
        }

        // New members (recently joined active members)
        foreach ($this->get_recent_members(10) as $m) {
            $m['title'] = $m['firstname'] . ' ' . $m['lastname'];
            $activities[] = ['type' => 'new_member', 'data' => $m, 'date' => $m['created_at']];
        }

        // Active elections
        foreach ($this->get_active_elections_for_feed() as $election) {
            $election['title'] = $election['title'];
            $activities[] = ['type' => 'election', 'data' => $election, 'date' => $election['start_date']];
        }

        // Announcements
        foreach ($this->get_announcements() as $ann) {
            $ann['title'] = $ann['title'];
            $activities[] = ['type' => 'announcement', 'data' => $ann, 'date' => $ann['created_at']];
        }

        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, $limit);
    }

    public function get_directory_members()
    {
        $this->db->select('tm.*, tc.firstname, tc.lastname, tc.email, tc.company');
        $this->db->from($this->table . ' tm');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id');
        $this->db->where('tm.status', 'active');
        $this->db->where('tm.show_in_directory', 1);
        $this->db->order_by('tc.lastname', 'ASC');
        return $this->db->get()->result_array();
    }

    public function search_members($search)
    {
        $this->db->select('tm.*, tc.firstname, tc.lastname, tc.email, tc.company');
        $this->db->from($this->table . ' tm');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id');
        $this->db->where('tm.status', 'active');
        $this->db->where('tm.show_in_directory', 1);
        $this->db->group_start();
        $this->db->like('tc.firstname', $search);
        $this->db->or_like('tc.lastname', $search);
        $this->db->or_like('tc.company', $search);
        $this->db->or_like('tm.profession', $search);
        $this->db->group_end();
        return $this->db->get()->result_array();
    }

    public function get_moderator_roles($id = null)
    {
        if ($id) {
            return $this->db->where('id', $id)->get($this->table_moderator_roles)->row_array();
        }
        return $this->db->order_by('name', 'ASC')->get($this->table_moderator_roles)->result_array();
    }

    public function create_moderator_role($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_moderator_roles, $data);
        return $this->db->insert_id();
    }

    public function update_moderator_role($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_moderator_roles, $data);
    }

    public function delete_moderator_role($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_moderator_roles);
    }

    public function get_moderators($status = null)
    {
        if ($status) {
            $this->db->where('mmo.status', $status);
        }
        $this->db->select('mmo.*, ms.firstname as staff_firstname, ms.lastname as staff_lastname, mmr.name as role_name');
        $this->db->from($this->table_moderators . ' mmo');
        $this->db->join(db_prefix() . 'staff ms', 'ms.staffid = mmo.staff_id');
        $this->db->join($this->table_moderator_roles . ' mmr', 'mmr.id = mmo.role_id');
        return $this->db->get()->result_array();
    }

    public function get_moderator($id)
    {
        return $this->db->where('id', $id)->get($this->table_moderators)->row_array();
    }

    public function create_moderator($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_moderators, $data);
        return $this->db->insert_id();
    }

    public function update_moderator($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_moderators, $data);
    }

    public function delete_moderator($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_moderators);
    }

    public function get_all_staff()
    {
        return $this->db->select('staffid, firstname, lastname, email')
            ->where('active', 1)
            ->order_by('firstname', 'ASC')
            ->get(db_prefix() . 'staff')
            ->result_array();
    }

    public function get_event_transactions($status = null, $event_id = null)
    {
        if ($status) {
            $this->db->where('met.status', $status);
        }
        if ($event_id) {
            $this->db->where('met.event_id', $event_id);
        }
        $this->db->select('met.*, me.title as event_title, tm.id as member_id, tc.firstname, tc.lastname');
        $this->db->from($this->table_event_transactions . ' met');
        $this->db->join($this->table_events . ' me', 'me.id = met.event_id', 'left');
        $this->db->join($this->table . ' tm', 'tm.id = met.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id', 'left');
        $this->db->order_by('met.transaction_date', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create_event_transaction($data)
    {
        $this->db->insert($this->table_event_transactions, $data);
        return $this->db->insert_id();
    }

    public function update_event_transaction($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_event_transactions, $data);
    }

    public function get_subscription_transactions($status = null)
    {
        if ($status) {
            $this->db->where('mst.status', $status);
        }
        $this->db->select('mst.*, tm.id as member_id, tc.firstname, tc.lastname, tc.email');
        $this->db->from($this->table_subscription_transactions . ' mst');
        $this->db->join($this->table . ' tm', 'tm.id = mst.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id', 'left');
        $this->db->order_by('mst.transaction_date', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create_subscription_transaction($data)
    {
        $this->db->insert($this->table_subscription_transactions, $data);
        return $this->db->insert_id();
    }

    public function update_subscription_transaction($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_subscription_transactions, $data);
    }

    public function get_all_transactions()
    {
        $membership_txns = $this->db->select('mp.id, mp.amount, mp.payment_date as transaction_date, mp.status, mp.payment_method, tm.id as member_id, tc.firstname, tc.lastname')
            ->from($this->table_payments . ' mp')
            ->join($this->table . ' tm', 'tm.id = mp.member_id')
            ->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id')
            ->get()->result_array();

        foreach ($membership_txns as &$txn) {
            $txn['type'] = 'membership';
            $txn['description'] = 'Membership Payment';
        }

        $event_txns = $this->db->select('met.id, met.amount, met.transaction_date, met.status, met.payment_method, tm.id as member_id, tc.firstname, tc.lastname, me.title as event_title')
            ->from($this->table_event_transactions . ' met')
            ->join($this->table . ' tm', 'tm.id = met.member_id')
            ->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id')
            ->join($this->table_events . ' me', 'me.id = met.event_id')
            ->get()->result_array();

        foreach ($event_txns as &$txn) {
            $txn['type'] = 'event';
            $txn['description'] = 'Event: ' . $txn['event_title'];
        }

        $sub_txns = $this->db->select('mst.id, mst.amount, mst.transaction_date, mst.status, mst.payment_method, tm.id as member_id, tc.firstname, tc.lastname, mst.plan_name')
            ->from($this->table_subscription_transactions . ' mst')
            ->join($this->table . ' tm', 'tm.id = mst.member_id')
            ->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id')
            ->get()->result_array();

        foreach ($sub_txns as &$txn) {
            $txn['type'] = 'subscription';
            $txn['description'] = 'Subscription: ' . $txn['plan_name'];
        }

        $all_transactions = array_merge($membership_txns, $event_txns, $sub_txns);

        usort($all_transactions, function($a, $b) {
            return strtotime($b['transaction_date']) - strtotime($a['transaction_date']);
        });

        return $all_transactions;
    }

    public function get_committee_categories($id = null)
    {
        if ($id) {
            return $this->db->where('id', $id)->get($this->table_committee_categories)->row_array();
        }
        return $this->db->order_by('name', 'ASC')->get($this->table_committee_categories)->result_array();
    }

    public function create_committee_category($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_committee_categories, $data);
        return $this->db->insert_id();
    }

    public function update_committee_category($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_committee_categories, $data);
    }

    public function delete_committee_category($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_committee_categories);
    }

    public function get_committee_designations($id = null)
    {
        if ($id) {
            return $this->db->where('id', $id)->get($this->table_committee_designations)->row_array();
        }
        return $this->db->order_by('name', 'ASC')->get($this->table_committee_designations)->result_array();
    }

    public function create_committee_designation($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_committee_designations, $data);
        return $this->db->insert_id();
    }

    public function update_committee_designation($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_committee_designations, $data);
    }

    public function delete_committee_designation($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_committee_designations);
    }

    public function get_committees($status = null)
    {
        if (!$this->db->table_exists($this->table_committees)) {
            return [];
        }
        if ($status) {
            $this->db->where('mc.status', $status);
        }
        $this->db->select('mc.*, mcc.name as category_name');
        $this->db->from($this->table_committees . ' mc');
        $this->db->join($this->table_committee_categories . ' mcc', 'mcc.id = mc.category_id', 'left');
        $this->db->order_by('mc.name', 'ASC');
        return $this->db->get()->result_array();
    }
    public function get_committee($id)
    {
        return $this->db->where('id', $id)->get($this->table_committees)->row_array();
    }

    public function create_committee($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();
        $this->db->insert($this->table_committees, $data);
        return $this->db->insert_id();
    }

    public function update_committee($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_committees, $data);
    }

    public function delete_committee($id)
    {
        $this->db->where('committee_id', $id);
        $this->db->delete($this->table_committee_members);
        $this->db->where('id', $id);
        return $this->db->delete($this->table_committees);
    }

    public function get_committee_members($committee_id = null)
    {
        if ($committee_id) {
            $this->db->where('mcm.committee_id', $committee_id);
        }
        $this->db->select('mcm.*, mc.name as committee_name, mcd.name as designation_name, tc.firstname, tc.lastname, tc.email');
        $this->db->from($this->table_committee_members . ' mcm');
        $this->db->join($this->table_committees . ' mc', 'mc.id = mcm.committee_id');
        $this->db->join($this->table_committee_designations . ' mcd', 'mcd.id = mcm.designation_id', 'left');
        $this->db->join($this->table . ' tm', 'tm.id = mcm.member_id');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id');
        $this->db->order_by('mcm.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_committee_member($id)
    {
        return $this->db->where('id', $id)->get($this->table_committee_members)->row_array();
    }

    public function add_committee_member($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_committee_members, $data);
        return $this->db->insert_id();
    }

    public function update_committee_member($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_committee_members, $data);
    }

    public function delete_committee_member($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_committee_members);
    }

    public function get_election_symbols($id = null)
    {
        if (!$this->db->table_exists($this->table_election_symbols)) {
            return $id ? null : [];
        }
        if ($id) {
            return $this->db->where('id', $id)->get($this->table_election_symbols)->row_array();
        }
        return $this->db->order_by('name', 'ASC')->get($this->table_election_symbols)->result_array();
    }

    public function create_election_symbol($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_election_symbols, $data);
        return $this->db->insert_id();
    }

    public function update_election_symbol($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_election_symbols, $data);
    }

    public function delete_election_symbol($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_election_symbols);
    }

    public function get_nominations($status = null, $election_id = null, $member_id = null, $nominator_contact_id = null)
    {
        if ($status) {
            $this->db->where('mn.status', $status);
        }
        if ($election_id) {
            $this->db->where('mn.election_id', $election_id);
        }
        if ($member_id) {
            $this->db->where('mn.member_id', $member_id);
        }
        if ($nominator_contact_id) {
            $this->db->where('mn.nominated_by_contact_id', $nominator_contact_id);
        }
        $this->db->select('mn.*, me.title as election_title, mes.name as symbol_name, tc.firstname, tc.lastname, tc.email');
        $this->db->from($this->table_nominations . ' mn');
        $this->db->join($this->table_elections . ' me', 'me.id = mn.election_id', 'left');
        $this->db->join($this->table_election_symbols . ' mes', 'mes.id = mn.symbol_id', 'left');
        $this->db->join($this->table . ' tm', 'tm.id = mn.member_id');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id');
        $this->db->order_by('mn.nominated_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_nomination($id)
    {
        return $this->db->where('id', $id)->get($this->table_nominations)->row_array();
    }

    /**
     * Get a nomination with full contact details for both the nominee and the nominator.
     * Returns: nominee_email, nominee_contact_id, nominee_firstname, nominee_lastname,
     *          nominator_email, nominator_contact_id, nominator_firstname, nominator_lastname,
     *          election_title
     */
    public function get_nomination_full($id)
    {
        $this->db->select('mn.*,
            me.title as election_title,
            nominee_c.id    as nominee_contact_id,
            nominee_c.email as nominee_email,
            nominee_c.firstname as nominee_firstname,
            nominee_c.lastname  as nominee_lastname,
            nominator_c.id    as nominator_contact_id,
            nominator_c.email as nominator_email,
            nominator_c.firstname as nominator_firstname,
            nominator_c.lastname  as nominator_lastname
        ');
        $this->db->from($this->table_nominations . ' mn');
        $this->db->join($this->table_elections . ' me',         'me.id = mn.election_id',                  'left');
        $this->db->join($this->table . ' nominee_m',            'nominee_m.id = mn.member_id',              'left');
        $this->db->join(db_prefix() . 'contacts nominee_c',    'nominee_c.id = nominee_m.contact_id',      'left');
        $this->db->join(db_prefix() . 'contacts nominator_c',  'nominator_c.id = mn.nominated_by_contact_id', 'left');
        $this->db->where('mn.id', (int)$id);
        return $this->db->get()->row_array();
    }

    public function get_nomination_by_member_and_election($member_id, $election_id)
    {
        $this->db->where('member_id', $member_id);
        $this->db->where('election_id', $election_id);
        return $this->db->get($this->table_nominations)->row();
    }

    public function create_nomination($data)
    {
        $data['nominated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_nominations, $data);
        return $this->db->insert_id();
    }

    public function update_nomination($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_nominations, $data);
    }

    public function approve_nomination($id)
    {
        $data = [
            'status' => 'approved',
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => get_staff_user_id()
        ];
        $this->db->where('id', $id);
        return $this->db->update($this->table_nominations, $data);
    }

    public function reject_nomination($id)
    {
        $data = [
            'status' => 'rejected',
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => get_staff_user_id()
        ];
        $this->db->where('id', $id);
        return $this->db->update($this->table_nominations, $data);
    }

    public function delete_nomination($id)
    {
        $this->db->where('nomination_id', $id);
        $this->db->delete($this->table_nomination_transactions);
        $this->db->where('id', $id);
        return $this->db->delete($this->table_nominations);
    }

    public function get_nomination_transactions($status = null)
    {
        if ($status) {
            $this->db->where('mnt.status', $status);
        }
        $this->db->select('mnt.*, mn.position, tc.firstname, tc.lastname');
        $this->db->from($this->table_nomination_transactions . ' mnt');
        $this->db->join($this->table_nominations . ' mn', 'mn.id = mnt.nomination_id', 'left');
        $this->db->join($this->table . ' tm', 'tm.id = mnt.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id', 'left');
        $this->db->order_by('mnt.transaction_date', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create_nomination_transaction($data)
    {
        $data['transaction_date'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_nomination_transactions, $data);
        return $this->db->insert_id();
    }

    public function get_board_members($status = null)
    {
        if (!$this->db->table_exists($this->table_board_members)) {
            return [];
        }
        if ($status) {
            $this->db->where('mbm.status', $status);
        }
        $this->db->select('mbm.*, me.title as election_title, tc.firstname, tc.lastname, tc.email, tc.phonenumber, tc.id as contact_id');
        $this->db->from($this->table_board_members . ' mbm');
        $this->db->join($this->table_elections . ' me', 'me.id = mbm.election_id', 'left');
        $this->db->join($this->table . ' tm', 'tm.id = mbm.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id', 'left');
        $this->db->order_by('mbm.position', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_board_member($id)
    {
        return $this->db->where('id', $id)->get($this->table_board_members)->row_array();
    }

    public function create_board_member($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_board_members, $data);
        return $this->db->insert_id();
    }

    public function update_board_member($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table_board_members, $data);
    }

    public function delete_board_member($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_board_members);
    }

    public function get_vote_comments($election_id = null, $candidate_id = null)
    {
        if ($election_id) {
            $this->db->where('mvc.election_id', $election_id);
        }
        if ($candidate_id) {
            $this->db->where('mvc.candidate_id', $candidate_id);
        }
        $this->db->select('mvc.*, tc.firstname, tc.lastname');
        $this->db->from($this->table_vote_comments . ' mvc');
        $this->db->join($this->table . ' tm', 'tm.id = mvc.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = tm.contact_id', 'left');
        $this->db->order_by('mvc.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function add_vote_comment($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table_vote_comments, $data);
        return $this->db->insert_id();
    }

    public function delete_vote_comment($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_vote_comments);
    }

    public function get_vote_list($election_id = null)
    {
        if ($election_id) {
            $this->db->where('mv.election_id', $election_id);
        }
        $this->db->select('mv.*, me.title as election_title, mn.position as candidate_position, tc.firstname as voter_firstname, tc.lastname as voter_lastname, cand.firstname as candidate_firstname, cand.lastname as candidate_lastname, cand.email as candidate_email');
        $this->db->from($this->table_votes . ' mv');
        $this->db->join($this->table_elections . ' me', 'me.id = mv.election_id', 'left');
        $this->db->join($this->table_nominations . ' mn', 'mn.id = mv.candidate_id', 'left');
        $this->db->join($this->table . ' tm', 'tm.id = mn.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts tc', 'tc.id = mv.contact_id', 'left');
        $this->db->join(db_prefix() . 'contacts cand', 'cand.id = tm.contact_id', 'left');
        $this->db->order_by('mv.voted_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_vote_history_by_member($member_id)
    {
        $this->db->where('mv.contact_id', $member_id);
        $this->db->select('mv.*, me.title as election_title, mn.position as candidate_position, cand.firstname as candidate_firstname, cand.lastname as candidate_lastname');
        $this->db->from($this->table_votes . ' mv');
        $this->db->join($this->table_elections . ' me', 'me.id = mv.election_id', 'left');
        $this->db->join($this->table_nominations . ' mn', 'mn.id = mv.candidate_id', 'left');
        $this->db->join($this->table . ' tm', 'tm.id = mn.member_id', 'left');
        $this->db->join(db_prefix() . 'contacts cand', 'cand.id = tm.contact_id', 'left');
        $this->db->order_by('mv.voted_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_nomination_positions()
    {
        $positions = get_option('membership_nomination_positions');
        if (!$positions) {
            return ['President', 'Vice President', 'Secretary', 'Treasurer', 'Public Relations Officer'];
        }
        return array_filter(array_map('trim', explode("\n", $positions)));
    }

    public function get_position($id)
    {
        if (!$this->db->table_exists($this->table_positions)) {
            return null;
        }
        if ($id) {
            return $this->db->where('id', $id)->get($this->table_positions)->row_array();
        }
        return $this->db->order_by('name', 'ASC')->get($this->table_positions)->result_array();
    }

    public function get_positions($election_id = null)
    {
        if (!$this->db->table_exists($this->table_positions)) {
            return [];
        }
        if ($election_id) {
            $this->db->where('election_id', (int)$election_id);
        }
        return $this->db->order_by('name', 'ASC')->get($this->table_positions)->result_array();
    }

    public function create_position($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        if (empty($data['election_id'])) {
            $data['election_id'] = null;
        }
        $this->db->insert($this->table_positions, $data);
        return $this->db->insert_id();
    }

    public function update_position($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (empty($data['election_id'])) {
            $data['election_id'] = null;
        }
        $this->db->where('id', $id);
        return $this->db->update($this->table_positions, $data);
    }

    public function delete_position($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_positions);
    }
}