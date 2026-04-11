<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Membership extends AdminController
{
    protected $table;
    protected $permission_resource = 'membership';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('membership/membership_model');
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied();
        }

        $data['title'] = _l('membership');
        $data['members_count'] = count($this->membership_model->get_all_members());
        $data['pending_count'] = count($this->membership_model->get_all_members('pending'));
        $data['active_count'] = count($this->membership_model->get_all_members('active'));
        $data['upcoming_events'] = $this->membership_model->get_events(true);

        $this->load->view('admin/dashboard', $data);
    }


    public function members($id = '')
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $member_id = $this->input->post('member_id');

            if ($member_id) {
                // UPDATE existing member
                $data = [
                    'status'            => $this->input->post('status'),
                    'membership_type'   => $this->input->post('membership_type'),
                    'profession'        => $this->input->post('profession'),
                    'join_date'         => $this->input->post('join_date') ?: null,
                    'show_in_directory' => $this->input->post('show_in_directory') ? 1 : 0,
                ];
                $this->db->where('id', $member_id);
                $this->db->update('tblmembership_members', $data);
                set_alert('success', _l('membership_member_updated'));
            } else {
                // INSERT new member
                $contact_id = $this->input->post('contact_id');
                $this->load->model('clients_model');
                $contact = $this->clients_model->get_contact($contact_id);

                if ($contact) {
                    $data = [
                        'user_id'           => $contact->userid,
                        'contact_id'        => $contact_id,
                        'status'            => $this->input->post('status') ?: 'pending',
                        'membership_type'   => $this->input->post('membership_type'),
                        'profession'        => $this->input->post('profession'),
                        'join_date'         => $this->input->post('join_date') ?: null,
                        'show_in_directory' => $this->input->post('show_in_directory') ? 1 : 0,
                    ];
                    $this->db->insert('tblmembership_members', $data);
                    set_alert('success', _l('membership_member_created'));
                } else {
                    set_alert('danger', _l('membership_contact_not_found'));
                }
            }
            redirect(admin_url('membership/members'));
        }

        $data['title']    = _l('membership_members');
        $data['members']  = $this->membership_model->get_all_members();
        $data['contacts'] = $this->membership_model->get_available_contacts();

        $this->load->view('admin/members', $data);
    }

    public function add_member()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $contact_id = $this->input->post('contact_id');
            $this->load->model('clients_model');
            $contact = $this->clients_model->get_contact($contact_id);

            if ($contact) {
                $data = [
                    'user_id'          => $contact->userid,
                    'contact_id'       => $contact_id,
                    'status'           => $this->input->post('status') ?: 'pending',
                    'membership_type'  => $this->input->post('membership_type'),
                    'profession'       => $this->input->post('profession'),
                    'join_date'        => $this->input->post('join_date') ?: null,
                    'show_in_directory' => $this->input->post('show_in_directory') ? 1 : 0,
                ];

                $this->db->insert('tblmembership_members', $data);
                $member_id = $this->db->insert_id();

                if ($member_id) {
                    set_alert('success', _l('membership_member_created'));
                }
            }
            redirect(admin_url('membership/members'));
        }

        $data['title']    = _l('membership_add_member');
        $data['contacts'] = $this->membership_model->get_available_contacts();
        $this->load->view('admin/add_member', $data);
    }

    public function member_status($member_id, $status)
    {
        if (!is_admin()) {
            access_denied();
        }

        if (!in_array($status, ['pending', 'active', 'suspended'])) {
            redirect(admin_url('membership/members'));
        }

        $this->membership_model->update_member_status($member_id, $status);

        $member = $this->membership_model->get_member($member_id);
        if ($member) {
            $this->load->model('clients_model');
            $contact = $this->clients_model->get_contact($member['contact_id']);

            if ($contact && $status == 'active') {
                send_mail_template('membership-approved', $contact->email, $contact->firstname);
            }
        }

        set_alert('success', _l('membership_status_updated'));
        redirect(admin_url('membership/members'));
    }

    public function delete_member($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_member($id);
        set_alert('success', _l('membership_member_deleted'));
        redirect(admin_url('membership/members'));
    }

    public function jobs($id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            if ($id == '') {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                $data = $this->input->post();
                $data['contact_id'] = get_staff_user_id();
                $data['status'] = 'approved';
                $job_id = $this->membership_model->create_job($data);
                if ($job_id) {
                    set_alert('success', _l('membership_job_created'));
                }
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = $this->input->post();
                $this->membership_model->update_job($id, $data);
                set_alert('success', _l('membership_job_updated'));
            }
            redirect(admin_url('membership/jobs'));
        }

        if ($id != '') {
            $data['job'] = $this->membership_model->get_job($id);
        }

        $data['title'] = _l('membership_jobs');
        $data['jobs'] = $this->membership_model->get_jobs(null);
        $data['pending_jobs'] = $this->membership_model->get_pending_jobs();
        $this->load->view('admin/jobs', $data);
    }

    public function approve_job($id)
    {
        if (!staff_can('edit', 'membership')) {
            access_denied();
        }

        $this->membership_model->approve_job($id);
        set_alert('success', _l('membership_job_approved'));
        redirect(admin_url('membership/jobs'));
    }

    public function reject_job($id)
    {
        if (!staff_can('edit', 'membership')) {
            access_denied();
        }

        $this->membership_model->reject_job($id);
        set_alert('success', _l('membership_job_rejected'));
        redirect(admin_url('membership/jobs'));
    }

    public function delete_job($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_job($id);
        set_alert('success', _l('membership_job_deleted'));
        redirect(admin_url('membership/jobs'));
    }

    public function stories($id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            if ($id == '') {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                $data = $this->input->post();
                $data['contact_id'] = get_staff_user_id();
                $data['status'] = 'approved';
                $story_id = $this->membership_model->create_story($data);
                if ($story_id) {
                    set_alert('success', _l('membership_story_created'));
                }
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = $this->input->post();
                $this->membership_model->update_story($id, $data);
                set_alert('success', _l('membership_story_updated'));
            }
            redirect(admin_url('membership/stories'));
        }

        if ($id != '') {
            $data['story'] = $this->membership_model->get_story($id);
        }

        $data['title'] = _l('membership_stories');
        $data['stories'] = $this->membership_model->get_stories(null);
        $data['pending_stories'] = $this->membership_model->get_pending_stories();
        $this->load->view('admin/stories', $data);
    }

    public function approve_story($id)
    {
        if (!staff_can('edit', 'membership')) {
            access_denied();
        }

        $this->membership_model->approve_story($id);
        set_alert('success', _l('membership_story_approved'));
        redirect(admin_url('membership/stories'));
    }

    public function reject_story($id)
    {
        if (!staff_can('edit', 'membership')) {
            access_denied();
        }

        $this->membership_model->reject_story($id);
        set_alert('success', _l('membership_story_rejected'));
        redirect(admin_url('membership/stories'));
    }

    public function delete_story($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_story($id);
        set_alert('success', _l('membership_story_deleted'));
        redirect(admin_url('membership/stories'));
    }

    public function events($id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            if ($id == '') {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                $data = $this->input->post();
                $event_id = $this->membership_model->create_event($data);
                if ($event_id) {
                    set_alert('success', _l('membership_event_created'));
                }
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = $this->input->post();
                $this->membership_model->update_event($id, $data);
                set_alert('success', _l('membership_event_updated'));
            }
            redirect(admin_url('membership/events'));
        }

        if ($id != '') {
            $data['event'] = $this->membership_model->get_event($id);
            $data['registrations'] = $this->membership_model->get_event_registrations($id);
        }

        $data['title'] = _l('membership_events');
        $data['events'] = $this->membership_model->get_events();
        $this->load->view('admin/events', $data);
    }

    public function delete_event($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_event($id);
        set_alert('success', _l('membership_event_deleted'));
        redirect(admin_url('membership/events'));
    }

    public function add_election()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $election_id = $this->membership_model->create_election($data);
            if ($election_id) {
                set_alert('success', _l('membership_election_created'));
            }
            redirect(admin_url('membership/elections'));
        }

        $data['title'] = _l('membership_add_election');
        $data['active_members'] = $this->membership_model->get_all_members('active');
        $this->load->view('admin/add_election', $data);
    }

    public function elections($id = '')
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            if ($id == '') {
                $data = $this->input->post();
                $election_id = $this->membership_model->create_election($data);
                if ($election_id) {
                    set_alert('success', _l('membership_election_created'));
                }
            } else {
                $data = $this->input->post();
                $this->membership_model->update_election($id, $data);
                set_alert('success', _l('membership_election_updated'));
            }
            redirect(admin_url('membership/elections'));
        }

        if ($id != '') {
            $data['election'] = $this->membership_model->get_election($id);
            $data['candidates'] = $this->membership_model->get_candidates($id);
            $data['results'] = $this->membership_model->get_election_results($id);
            $data['total_votes'] = $this->membership_model->get_total_votes($id);
        }

        $data['title'] = _l('membership_elections');
        $data['elections'] = $this->membership_model->get_elections();
        $data['active_members'] = $this->membership_model->get_all_members('active');
        $this->load->view('admin/elections', $data);
    }

    public function add_candidate($election_id)
    {
        if (!staff_can('manage_elections', 'membership')) {
            access_denied();
        }

        $contact_id = $this->input->post('contact_id');
        $bio = $this->input->post('bio');

        $this->membership_model->add_candidate($election_id, $contact_id, $bio);
        set_alert('success', _l('membership_candidate_added'));
        redirect(admin_url('membership/elections/' . $election_id));
    }

    public function remove_candidate($election_id, $candidate_id)
    {
        if (!staff_can('manage_elections', 'membership')) {
            access_denied();
        }

        $this->membership_model->remove_candidate($candidate_id);
        set_alert('success', _l('membership_candidate_removed'));
        redirect(admin_url('membership/elections/' . $election_id));
    }

    public function delete_election($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_election($id);
        set_alert('success', _l('membership_election_deleted'));
        redirect(admin_url('membership/elections'));
    }


    public function settings()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            foreach ($data as $key => $value) {
                update_option('membership_' . $key, $value);
            }
            set_alert('success', _l('membership_settings_updated'));
            redirect(admin_url('membership/settings'));
        }

        $data['title']        = _l('membership_settings');
        $data['positions']    = $this->membership_model->get_positions();
        $data['symbols']      = $this->membership_model->get_election_symbols();
        $data['designations'] = $this->membership_model->get_committee_designations();
        $data['categories']   = $this->membership_model->get_committee_categories();
        $this->load->view('admin/settings', $data);
    }

    public function moderator_roles($id = '')
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $permissions = $this->input->post('permissions');
            $data = [
                'name' => $this->input->post('name'),
                'permissions' => json_encode($permissions ?: []),
            ];

            if ($id == '') {
                $this->membership_model->create_moderator_role($data);
                set_alert('success', _l('membership_moderator_role_created'));
            } else {
                $this->membership_model->update_moderator_role($id, $data);
                set_alert('success', _l('membership_moderator_role_updated'));
            }
            redirect(admin_url('membership/moderator_roles'));
        }

        if ($id != '') {
            $data['role'] = $this->membership_model->get_moderator_roles($id);
        }

        $data['title'] = _l('membership_moderator_roles');
        $data['roles'] = $this->membership_model->get_moderator_roles();
        $this->load->view('admin/moderator_roles', $data);
    }

    public function delete_moderator_role($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->membership_model->delete_moderator_role($id);
        set_alert('success', _l('membership_moderator_role_deleted'));
        redirect(admin_url('membership/moderator_roles'));
    }

    public function moderators($id = '')
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            if ($id == '') {
                $data = [
                    'staff_id' => $this->input->post('staff_id'),
                    'role_id' => $this->input->post('role_id'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->create_moderator($data);
                set_alert('success', _l('membership_moderator_created'));
            } else {
                $data = [
                    'staff_id' => $this->input->post('staff_id'),
                    'role_id' => $this->input->post('role_id'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->update_moderator($id, $data);
                set_alert('success', _l('membership_moderator_updated'));
            }
            redirect(admin_url('membership/moderators'));
        }

        if ($id != '') {
            $data['moderator'] = $this->membership_model->get_moderator($id);
        }

        $data['title'] = _l('membership_moderators');
        $data['moderators'] = $this->membership_model->get_moderators();
        $data['roles'] = $this->membership_model->get_moderator_roles();
        $data['staff'] = $this->membership_model->get_all_staff();
        $this->load->view('admin/moderators', $data);
    }

    public function delete_moderator($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->membership_model->delete_moderator($id);
        set_alert('success', _l('membership_moderator_deleted'));
        redirect(admin_url('membership/moderators'));
    }


    public function committee_categories()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'status' => $this->input->post('status'),
            ];

            if (empty($id)) {
                $this->membership_model->create_committee_category($data);
                set_alert('success', _l('membership_committee_category_created'));
            } else {
                $this->membership_model->update_committee_category($id, $data);
                set_alert('success', _l('membership_committee_category_updated'));
            }
            redirect(admin_url('membership/committee_categories'));
        }

        $data['title'] = _l('membership_committee_categories');
        $data['categories'] = $this->membership_model->get_committee_categories();
        $this->load->view('admin/committee_categories', $data);
    }

    public function delete_committee_category($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->membership_model->delete_committee_category($id);
        set_alert('success', _l('membership_committee_category_deleted'));
        redirect(admin_url('membership/committee_categories'));
    }

    public function committee_designations()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
            ];

            if (empty($id)) {
                $this->membership_model->create_committee_designation($data);
                set_alert('success', _l('membership_committee_designation_created'));
            } else {
                $this->membership_model->update_committee_designation($id, $data);
                set_alert('success', _l('membership_committee_designation_updated'));
            }
            redirect(admin_url('membership/committee_designations'));
        }

        $data['title'] = _l('membership_committee_designations');
        $data['designations'] = $this->membership_model->get_committee_designations();
        $this->load->view('admin/committee_designations', $data);
    }

    public function delete_committee_designation($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->membership_model->delete_committee_designation($id);
        set_alert('success', _l('membership_committee_designation_deleted'));
        redirect(admin_url('membership/committee_designations'));
    }

    public function committees()
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            
            if (empty($id)) {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                $data = [
                    'category_id' => $this->input->post('category_id'),
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->create_committee($data);
                set_alert('success', _l('membership_committee_created'));
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = [
                    'category_id' => $this->input->post('category_id'),
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->update_committee($id, $data);
                set_alert('success', _l('membership_committee_updated'));
            }
            redirect(admin_url('membership/committees'));
        }

        $data['title'] = _l('membership_committees');
        $data['committees'] = $this->membership_model->get_committees();
        $data['categories'] = $this->membership_model->get_committee_categories();
        $this->load->view('admin/committees', $data);
    }

    public function delete_committee($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_committee($id);
        set_alert('success', _l('membership_committee_deleted'));
        redirect(admin_url('membership/committees'));
    }

    public function committee_members()
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            
            if (empty($id)) {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                $data = [
                    'committee_id' => $this->input->post('committee_id'),
                    'member_id' => $this->input->post('member_id'),
                    'designation_id' => $this->input->post('designation_id'),
                    'term_start' => $this->input->post('term_start'),
                    'term_end' => $this->input->post('term_end'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->add_committee_member($data);
                set_alert('success', _l('membership_committee_member_created'));
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = [
                    'committee_id' => $this->input->post('committee_id'),
                    'member_id' => $this->input->post('member_id'),
                    'designation_id' => $this->input->post('designation_id'),
                    'term_start' => $this->input->post('term_start'),
                    'term_end' => $this->input->post('term_end'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->update_committee_member($id, $data);
                set_alert('success', _l('membership_committee_member_updated'));
            }
            redirect(admin_url('membership/committee_members'));
        }

        $data['title'] = _l('membership_committee_members');
        $data['committee_members'] = $this->membership_model->get_committee_members();
        $data['committees'] = $this->membership_model->get_committees();
        $data['designations'] = $this->membership_model->get_committee_designations();
        $data['members'] = $this->membership_model->get_all_members('active');
        $this->load->view('admin/committee_members', $data);
    }

    public function delete_committee_member($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_committee_member($id);
        set_alert('success', _l('membership_committee_member_deleted'));
        redirect(admin_url('membership/committee_members'));
    }

    public function election_symbols()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $data = [
                'name' => $this->input->post('name'),
                'symbol_image' => $this->input->post('symbol_image'),
                'description' => $this->input->post('description'),
            ];

            if (empty($id)) {
                $this->membership_model->create_election_symbol($data);
                set_alert('success', _l('membership_election_symbol_created'));
            } else {
                $this->membership_model->update_election_symbol($id, $data);
                set_alert('success', _l('membership_election_symbol_updated'));
            }
            redirect(admin_url('membership/election_symbols'));
        }

        $data['title'] = _l('membership_election_symbols');
        $data['symbols'] = $this->membership_model->get_election_symbols();
        $this->load->view('admin/election_symbols', $data);
    }

    public function delete_election_symbol($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->membership_model->delete_election_symbol($id);
        set_alert('success', _l('membership_election_symbol_deleted'));
        redirect(admin_url('membership/election_symbols'));
    }

    public function nominations($status = '', $election_id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            
            if (empty($id)) {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                
                $this->load->model('clients_model');
                $contact_id = $this->input->post('member_id');
                $contact = $this->clients_model->get_contact($contact_id);
                
                if ($contact) {
                    $this->db->where('contact_id', $contact_id);
                    $member = $this->db->get('tblmembership_members')->row();
                    
                    if (!$member) {
                        $member_data = [
                            'user_id' => $contact->userid,
                            'contact_id' => $contact_id,
                            'status' => 'active',
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('tblmembership_members', $member_data);
                        $member_id = $this->db->insert_id();
                    } else {
                        $member_id = $member->id;
                    }
                    
                    $data = [
                        'election_id' => $this->input->post('election_id'),
                        'member_id' => $member_id,
                        'position' => $this->input->post('position'),
                        'manifesto' => $this->input->post('manifesto'),
                        'symbol_id' => $this->input->post('symbol_id'),
                        'status' => $this->input->post('status') ?: 'pending',
                    ];
                    
                    $this->membership_model->create_nomination($data);
                    set_alert('success', _l('membership_nomination_created'));
                }
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = [
                    'election_id' => $this->input->post('election_id'),
                    'position' => $this->input->post('position'),
                    'manifesto' => $this->input->post('manifesto'),
                    'symbol_id' => $this->input->post('symbol_id'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->update_nomination($id, $data);
                set_alert('success', _l('membership_nomination_updated'));
            }
            redirect(admin_url('membership/nominations'));
        }

        $data['title'] = _l('membership_nominations');
        $data['nominations'] = $this->membership_model->get_nominations($status ?: null, $election_id ?: null);
        $data['elections'] = $this->membership_model->get_elections();
        $data['status_filter'] = $status;
        $this->load->view('admin/nominations', $data);
    }

    public function approve_nomination($id)
    {
        if (!staff_can('edit', 'membership')) {
            access_denied();
        }

        $this->membership_model->approve_nomination($id);
        set_alert('success', _l('membership_nomination_approved'));
        redirect(admin_url('membership/nominations'));
    }

    public function reject_nomination($id)
    {
        if (!staff_can('edit', 'membership')) {
            access_denied();
        }

        $this->membership_model->reject_nomination($id);
        set_alert('success', _l('membership_nomination_rejected'));
        redirect(admin_url('membership/nominations'));
    }

    public function delete_nomination($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_nomination($id);
        set_alert('success', _l('membership_nomination_deleted'));
        redirect(admin_url('membership/nominations'));
    }

    public function nomination_transactions($status = '')
    {
        if (!is_admin()) {
            access_denied();
        }

        $data['title'] = _l('membership_nomination_transactions');
        $data['transactions'] = $this->membership_model->get_nomination_transactions($status ?: null);
        $data['status_filter'] = $status;
        $this->load->view('admin/nomination_transactions', $data);
    }

    public function board_members()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            
            if (empty($id)) {
                $data = [
                    'member_id' => $this->input->post('member_id'),
                    'position' => $this->input->post('position'),
                    'election_id' => $this->input->post('election_id'),
                    'term_start' => $this->input->post('term_start'),
                    'term_end' => $this->input->post('term_end'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->create_board_member($data);
                set_alert('success', _l('membership_board_member_created'));
            } else {
                $data = [
                    'member_id' => $this->input->post('member_id'),
                    'position' => $this->input->post('position'),
                    'election_id' => $this->input->post('election_id'),
                    'term_start' => $this->input->post('term_start'),
                    'term_end' => $this->input->post('term_end'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->update_board_member($id, $data);
                set_alert('success', _l('membership_board_member_updated'));
            }
            redirect(admin_url('membership/board_members'));
        }

        $data['title'] = _l('membership_board_members');
        $data['board_members'] = $this->membership_model->get_board_members();
        $data['elections'] = $this->membership_model->get_elections();
        $data['members'] = $this->membership_model->get_all_members('active');
        $this->load->view('admin/board_members', $data);
    }

    public function delete_board_member($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->membership_model->delete_board_member($id);
        set_alert('success', _l('membership_board_member_deleted'));
        redirect(admin_url('membership/board_members'));
    }

    public function pending_candidates($election_id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        $data['title'] = _l('membership_pending_candidates');
        $data['candidates'] = $this->membership_model->get_nominations('pending', $election_id ?: null);
        $data['elections'] = $this->membership_model->get_elections();
        $this->load->view('admin/pending_candidates', $data);
    }

    public function final_candidates($election_id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        $data['title'] = _l('membership_final_candidates');
        $data['candidates'] = $this->membership_model->get_nominations('approved', $election_id ?: null);
        $data['elections'] = $this->membership_model->get_elections();
        $this->load->view('admin/final_candidates', $data);
    }

    public function candidate_comments($election_id = '', $candidate_id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            $data = [
                'election_id' => $this->input->post('election_id'),
                'candidate_id' => $this->input->post('candidate_id'),
                'member_id' => $this->input->post('member_id'),
                'comment' => $this->input->post('comment'),
            ];
            $this->membership_model->add_vote_comment($data);
            set_alert('success', _l('membership_comment_added'));
            redirect(admin_url('membership/candidate_comments'));
        }

        $data['title'] = _l('membership_candidate_comments');
        $data['comments'] = $this->membership_model->get_vote_comments($election_id ?: null, $candidate_id ?: null);
        $data['elections'] = $this->membership_model->get_elections();
        $data['members'] = $this->membership_model->get_all_members('active');
        $this->load->view('admin/candidate_comments', $data);
    }

    public function delete_comment($id)
    {
        if (!staff_can('delete', 'membership')) {
            access_denied();
        }

        $this->membership_model->delete_vote_comment($id);
        set_alert('success', _l('membership_comment_deleted'));
        redirect(admin_url('membership/candidate_comments'));
    }

    public function vote_list($election_id = '')
    {
        if (!is_admin()) {
            access_denied();
        }

        $data['title']            = _l('membership_vote_list');
        $data['votes']            = $this->membership_model->get_vote_list($election_id ?: null);
        $data['elections']        = $this->membership_model->get_elections();
        $data['selected_election']= $election_id;
        $this->load->view('admin/vote_list', $data);
    }

    public function election_results($election_id = '')
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        $data['title'] = _l('membership_election_results');
        $data['elections'] = $this->membership_model->get_elections();
        
        if ($election_id) {
            $data['results'] = $this->membership_model->get_election_results($election_id);
            $data['total_votes'] = $this->membership_model->get_total_votes($election_id);
            $data['selected_election'] = $election_id;
        }

        $this->load->view('admin/election_results', $data);
    }

    public function vote_elections()
    {
        if (!staff_can('view', 'membership')) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            
            if (empty($id)) {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                $data = [
                    'title' => $this->input->post('title'),
                    'description' => $this->input->post('description'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->create_election($data);
                set_alert('success', _l('membership_election_created'));
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = [
                    'title' => $this->input->post('title'),
                    'description' => $this->input->post('description'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'status' => $this->input->post('status'),
                ];
                $this->membership_model->update_election($id, $data);
                set_alert('success', _l('membership_election_updated'));
            }
            redirect(admin_url('membership/vote_elections'));
        }

        $data['title'] = _l('membership_elections');
        $data['elections'] = $this->membership_model->get_elections();
        $this->load->view('admin/vote_elections', $data);
    }

    public function vote_nominations($status = '')
    {
        $this->nominations($status);
    }

    public function cast_vote()
    {
        if (!is_client_logged_in()) {
                redirect(site_url('clients/login'));
        }

        $contact_id = get_contact_user_id();
        $member = $this->membership_model->get_member_by_contact_id($contact_id);

        if (!$member || $member['status'] != 'active') {
            set_alert('warning', _l('membership_access_denied'));
            redirect(site_url('membership'));
        }

        if ($this->input->post()) {
            $election_id = $this->input->post('election_id');
            $candidate_id = $this->input->post('candidate_id');

            if ($this->membership_model->has_voted($election_id, $contact_id)) {
                set_alert('warning', _l('membership_already_voted'));
                redirect(site_url('membership/vote'));
            }

            $this->membership_model->cast_vote($election_id, $candidate_id, $contact_id);
            set_alert('success', _l('membership_vote_submitted'));
            redirect(site_url('membership/vote'));
        }

        $data['title'] = _l('membership_cast_vote');
        $data['active_election'] = $this->membership_model->get_active_election();
        if ($data['active_election']) {
            $data['candidates'] = $this->membership_model->get_candidates($data['active_election']['id']);
        }
        $this->load->view('public/vote', $data);
    }

    public function positions()
    {
        if (!is_admin()) {
            access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');

            if (empty($id)) {
                if (!staff_can('create', 'membership')) {
                    access_denied();
                }
                $data = [
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                ];
                $this->membership_model->create_position($data);
                set_alert('success', _l('membership_position_created'));
            } else {
                if (!staff_can('edit', 'membership')) {
                    access_denied();
                }
                $data = [
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                ];
                $this->membership_model->update_position($id, $data);
                set_alert('success', _l('membership_position_updated'));
            }
            redirect(admin_url('membership/positions'));
        }

        $data['title'] = _l('membership_positions');
        $data['positions'] = $this->membership_model->get_positions();
        $this->load->view('admin/positions', $data);
    }

    public function delete_position($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $this->membership_model->delete_position($id);
        set_alert('success', _l('membership_position_deleted'));
        redirect(admin_url('membership/positions'));
    }

    public function get_board_member_details($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $member = $this->membership_model->get_board_member($id);

        if ($member) {
            // Also get election title if available
            if (!empty($member['election_id'])) {
                $election = $this->membership_model->get_election($member['election_id']);
                $member['election_title'] = $election ? $election['title'] : '';
            }

            // Get manifesto from nomination if available
            if (!empty($member['member_id'])) {
                $nomination = $this->membership_model->get_nomination_by_member_and_election($member['member_id'], $member['election_id'] ?? null);
                $member['manifesto'] = $nomination ? $nomination['manifesto'] : '';
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'member' => $member]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => _l('membership_board_member_not_found')]));
        }
    }

    public function ajax_get_member($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $member = $this->membership_model->get_member($id);

        if ($member) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'member' => $member]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => _l('membership_member_not_found')]));
        }
    }

    public function ajax_get_position($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $position = $this->membership_model->get_position($id);

        if ($position) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'position' => $position]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => _l('membership_position_not_found')]));
        }
    }

    public function ajax_get_board_member($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $member = $this->membership_model->get_board_member($id);

        if ($member) {
            // Also get election title if available
            if (!empty($member['election_id'])) {
                $election = $this->membership_model->get_election($member['election_id']);
                $member['election_title'] = $election ? $election['title'] : '';
            }

            // Get manifesto from nomination if available
            if (!empty($member['member_id'])) {
                $nomination = $this->membership_model->get_nomination_by_member_and_election($member['member_id'], $member['election_id'] ?? null);
                $member['manifesto'] = $nomination ? $nomination['manifesto'] : '';
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'member' => $member]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => _l('membership_board_member_not_found')]));
        }
    }

    public function ajax_get_committee($id)
    {
        if (!is_admin()) {
            access_denied();
        }

        $committee = $this->membership_model->get_committee($id);

        if ($committee) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'committee' => $committee]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => _l('membership_committee_not_found')]));
        }
    }

    public function ajax_get_election_results($election_id)
    {
        if (!is_admin()) {
            access_denied();
        }

        if (!$election_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => _l('membership_select_election')]));
            return;
        }

        $results = $this->membership_model->get_election_results($election_id);
        $total_votes = $this->membership_model->get_total_votes($election_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'results' => $results,
                'total_votes' => $total_votes
            ]));
    }

    public function ajax_get_votes($election_id)
    {
        if (!is_admin()) {
            access_denied();
        }

        if (!$election_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => _l('membership_select_election')]));
            return;
        }

        $votes = $this->membership_model->get_vote_list($election_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'votes' => $votes
            ]));
    }
}