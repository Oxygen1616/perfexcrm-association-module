<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Client extends ClientsController
{
    protected $contact_user_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('membership/membership_model');
        $this->contact_user_id = get_contact_user_id();
        $this->load->library('form_validation');
    }

    private function check_member_access()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        if (!$member || $member['status'] != 'active') {
            set_alert('warning', _l('membership_access_denied'));
            redirect(site_url('clients'));
        }

        return $member;
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $data['member'] = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        $data['activity_feed'] = $this->membership_model->get_activity_feed(10) ?: [];
        $data['upcoming_events'] = $this->membership_model->get_events(true) ?: [];

        $data['my_registrations'] = [];
        if ($data['member']) {
            $registrations = $this->membership_model->get_my_registrations($this->contact_user_id);
            foreach ($registrations as $reg) {
                $data['my_registrations'][$reg['event_id']] = $reg;
            }
        }

        $this->data($data)
            ->title(_l('membership_dashboard'))
            ->view('public/dashboard')
            ->layout();
    }

    public function directory()
    {
        $this->check_member_access();

        $search = $this->input->get('search');

        if ($search) {
            $data['members'] = $this->membership_model->search_members($search);
        } else {
            $data['members'] = $this->membership_model->get_directory_members();
        }

        $data['search'] = $search;

        $this->data($data)
            ->title(_l('membership_directory'))
            ->view('public/directory')
            ->layout();
    }

    public function events()
    {
        $this->check_member_access();

        $data['events'] = $this->membership_model->get_events(true);
        $data['my_registrations'] = $this->membership_model->get_my_registrations($this->contact_user_id);

        $this->data($data)
            ->title(_l('membership_events'))
            ->view('public/events')
            ->layout();
    }

    public function register_event($event_id)
    {
        $this->check_member_access();

        $event = $this->membership_model->get_event($event_id);
        if (!$event) {
            set_alert('danger', _l('membership_event_not_found'));
            redirect(site_url('membership/client/events'));
        }

        $existing = $this->membership_model->get_event_registration($event_id, $this->contact_user_id);
        if ($existing) {
            set_alert('warning', _l('membership_already_registered'));
            redirect(site_url('membership/client/events'));
        }

        $result = $this->membership_model->register_for_event($event_id, $this->contact_user_id);

        if ($result) {
            set_alert('success', _l('membership_registration_success'));
        } else {
            set_alert('danger', _l('membership_registration_failed'));
        }

        redirect(site_url('membership/client/events'));
    }

    public function jobs()
    {
        $this->check_member_access();

        $data['jobs'] = $this->membership_model->get_jobs('approved');

        $this->data($data)
            ->title(_l('membership_jobs'))
            ->view('public/jobs')
            ->layout();
    }

    public function post_job()
    {
        $this->check_member_access();

        if ($this->input->post()) {
            $this->form_validation->set_rules('title', _l('membership_job_title'), 'required|trim|max_length[255]');
            $this->form_validation->set_rules('company', _l('membership_job_company'), 'trim|max_length[255]');
            $this->form_validation->set_rules('description', _l('membership_job_description'), 'required|trim');
            $this->form_validation->set_rules('location', _l('membership_job_location'), 'trim|max_length[255]');
            $this->form_validation->set_rules('salary_range', _l('membership_job_salary'), 'trim|max_length[100]');

            if ($this->form_validation->run() !== false) {
                $jobData = [
                    'contact_id'  => $this->contact_user_id,
                    'title'       => $this->input->post('title'),
                    'company'     => $this->input->post('company'),
                    'description' => $this->input->post('description'),
                    'location'    => $this->input->post('location'),
                    'salary_range' => $this->input->post('salary_range'),
                    'status'      => 'pending',
                ];

                $job_id = $this->membership_model->create_job($jobData);

                if ($job_id) {
                    set_alert('success', _l('membership_job_submitted'));
                    redirect(site_url('membership/jobs'));
                }
            }
        }

        $this->data([])
            ->title(_l('membership_post_job'))
            ->view('public/post_job')
            ->layout();
    }

    public function stories()
    {
        $this->check_member_access();

        $data['stories'] = $this->membership_model->get_stories('approved');

        $this->data($data)
            ->title(_l('membership_stories'))
            ->view('public/stories')
            ->layout();
    }

    public function post_story()
    {
        $this->check_member_access();

        if ($this->input->post()) {
            $this->form_validation->set_rules('title', _l('membership_story_title'), 'required|trim|max_length[255]');
            $this->form_validation->set_rules('content', _l('membership_story_content'), 'required|trim');

            if ($this->form_validation->run() !== false) {
                $storyData = [
                    'contact_id' => $this->contact_user_id,
                    'title'      => $this->input->post('title'),
                    'content'    => $this->input->post('content'),
                    'status'     => 'pending',
                ];

                $story_id = $this->membership_model->create_story($storyData);

                if ($story_id) {
                    set_alert('success', _l('membership_story_submitted'));
                    redirect(site_url('membership/stories'));
                }
            }
        }

        $this->data([])
            ->title(_l('membership_post_story'))
            ->view('public/post_story')
            ->layout();
    }

    public function elections()
    {
        $this->check_member_access();

        $data['active_election'] = $this->membership_model->get_active_election();

        if ($data['active_election']) {
            $data['candidates'] = $this->membership_model->get_candidates($data['active_election']['id']);
            $data['has_voted'] = $this->membership_model->has_voted($data['active_election']['id'], $this->contact_user_id);
            $data['results'] = $this->membership_model->get_election_results($data['active_election']['id']);
            $data['total_votes'] = $this->membership_model->get_total_votes($data['active_election']['id']);
        }

        $this->data($data)
            ->title(_l('membership_elections'))
            ->view('public/elections')
            ->layout();
    }

    public function vote($election_id)
    {
        $this->check_member_access();

        $election = $this->membership_model->get_election($election_id);
        if (!$election || $election['status'] != 'active') {
            set_alert('danger', _l('membership_election_not_available'));
            redirect(site_url('membership/elections'));
        }

        if ($this->membership_model->has_voted($election_id, $this->contact_user_id)) {
            set_alert('warning', _l('membership_already_voted'));
            redirect(site_url('membership/elections'));
        }

        $candidate_id = $this->input->post('candidate_id');
        if (!$candidate_id) {
            set_alert('danger', _l('membership_select_candidate'));
            redirect(site_url('membership/elections'));
        }

        $result = $this->membership_model->cast_vote($election_id, $candidate_id, $this->contact_user_id);

        if ($result) {
            set_alert('success', _l('membership_vote_submitted'));
        } else {
            set_alert('danger', _l('membership_vote_failed'));
        }

        redirect(site_url('membership/elections'));
    }

    public function billing()
    {
        $this->check_member_access();

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        $this->load->model('invoices_model');
        $data['member'] = $member;
        $data['invoices'] = $this->invoices_model->get('', [
            'clientid' => get_client_user_id(),
        ]);

        $this->data($data)
            ->title(_l('membership_billing'))
            ->view('public/billing')
            ->layout();
    }

    public function profile()
    {
        $this->check_member_access();

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        if ($this->input->post()) {
            $updateData = [
                'membership_type'   => $this->input->post('membership_type'),
                'profession'        => $this->input->post('profession'),
                'join_date'         => $this->input->post('join_date') ?: null,
                'show_in_directory' => $this->input->post('show_in_directory') ? 1 : 0,
            ];

            $this->membership_model->update_member($member['id'], $updateData);
            set_alert('success', _l('membership_profile_updated'));
            redirect(site_url('membership/profile'));
        }

        $data['member'] = $member;

        $this->data($data)
            ->title(_l('membership_profile'))
            ->view('public/profile')
            ->layout();
    }

    public function nominations()
    {
        $this->check_member_access();

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        $data['elections'] = $this->membership_model->get_elections();
        $data['symbols'] = $this->membership_model->get_election_symbols();
        $data['my_nominations'] = $this->membership_model->get_nominations(null, null, $member['id']);

        $this->data($data)
            ->title(_l('membership_nominations'))
            ->view('public/nominations')
            ->layout();
    }

    public function apply_nomination()
    {
        $this->check_member_access();

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        if ($this->input->post()) {
            $election_id = $this->input->post('election_id');
            $nominated_member_id = $this->input->post('nominated_member_id');

            $existing = $this->membership_model->get_nomination_by_member_and_election($nominated_member_id, $election_id);

            if ($existing) {
                set_alert('warning', _l('membership_already_nominated'));
                redirect(site_url('membership/client/nominations'));
            }

            $photo_name = null;

            if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
                $photo_path = 'uploads/membership/nominations/';
                if (!is_dir($photo_path)) {
                    mkdir($photo_path, 0755, true);
                }
                $photo_name = time() . '_photo_' . $_FILES['photo']['name'];
                move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path . $photo_name);
            }

            $nominationData = [
                'election_id'  => $election_id,
                'member_id'    => $nominated_member_id,
                'manifesto'    => $this->input->post('manifesto'),
                'photo'        => $photo_name,
                'declaration'  => $this->input->post('declaration') ? 1 : 0,
                'status'       => 'pending',
            ];

            $nomination_id = $this->membership_model->create_nomination($nominationData);

            if ($nomination_id) {
                set_alert('success', _l('membership_nomination_submitted'));
            } else {
                set_alert('danger', _l('membership_nomination_failed'));
            }

            redirect(site_url('membership/client/nominations'));
        }

        $data['elections'] = $this->membership_model->get_elections();
        $data['positions'] = $this->membership_model->get_nomination_positions();
        $data['committees'] = $this->membership_model->get_committees('active');
        $data['members'] = $this->membership_model->get_all_members('active');
        $data['symbols'] = $this->membership_model->get_election_symbols();
        $data['nomination_fee'] = get_option('membership_nomination_fee') ?: '0.00';
        $data['nomination_currency'] = get_option('membership_nomination_currency') ?: 'USD';
        $data['nomination_rules'] = get_option('membership_nomination_rules') ?: '';

        $this->data($data)
            ->title(_l('membership_submit_nomination'))
            ->view('public/apply_nomination')
            ->layout();
    }

    public function cast_vote()
    {
        $this->check_member_access();

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        if ($this->input->post()) {
            $election_id = $this->input->post('election_id');
            $candidate_id = $this->input->post('candidate_id');

            if ($this->membership_model->has_voted($election_id, $this->contact_user_id)) {
                set_alert('warning', _l('membership_already_voted'));
                redirect(site_url('membership/cast_vote'));
            }

            $result = $this->membership_model->cast_vote($election_id, $candidate_id, $this->contact_user_id);

            if ($result) {
                set_alert('success', _l('membership_vote_submitted'));
            } else {
                set_alert('danger', _l('membership_vote_failed'));
            }

            redirect(site_url('membership/cast_vote'));
        }

        $data['elections'] = $this->membership_model->get_elections('active');
        $data['selected_election'] = $this->input->get('election_id');

        if ($data['selected_election']) {
            $data['candidates'] = $this->membership_model->get_final_candidates($data['selected_election']);
        }

        $this->data($data)
            ->title(_l('membership_cast_vote'))
            ->view('public/vote')
            ->layout();
    }

    public function committees()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $data['committees'] = $this->membership_model->get_committees('active');

        $this->data($data)
            ->title(_l('membership_committees'))
            ->view('public/committees')
            ->layout();
    }

    public function board_members()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $data['board_members'] = $this->membership_model->get_board_members('active');

        $this->data($data)
            ->title(_l('membership_board_members'))
            ->view('public/board_members')
            ->layout();
    }

    public function get_election_details()
    {
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $election_id = $this->input->post('election_id');
        if (!$election_id) {
            echo json_encode(['success' => false, 'message' => 'Election ID required']);
            return;
        }

        $election = $this->membership_model->get_election($election_id);
        if (!$election) {
            echo json_encode(['success' => false, 'message' => 'Election not found']);
            return;
        }

        echo json_encode(['success' => true, 'election' => $election]);
    }

    public function get_candidates()
    {
        $this->check_member_access();

        $election_id = $this->input->get('election_id');
        if (!$election_id) {
            echo json_encode(['success' => false, 'message' => 'Election ID required']);
            return;
        }

        $candidates = $this->membership_model->get_final_candidates($election_id);

        if ($candidates) {
            // Load the candidates view partial
            $this->load->view('public/partials/_candidates_list', ['candidates' => $candidates, 'election_id' => $election_id]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('membership_no_candidates')]);
        }
    }

    public function get_vote_history()
    {
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member not found']);
            return;
        }

        $voteHistory = $this->membership_model->get_vote_history_by_member($member['id']);
        echo json_encode(['success' => true, 'votes' => $voteHistory]);
    }

    public function vote_history()
    {
        $this->check_member_access();

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        $data['vote_history'] = $this->membership_model->get_vote_history_by_member($this->contact_user_id);

        $this->data($data)
            ->title(_l('membership_vote_history'))
            ->view('public/vote_history')
            ->layout();
    }
}
