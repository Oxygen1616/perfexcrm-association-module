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

        $now            = date('Y-m-d H:i:s');
        $seven_days     = date('Y-m-d 23:59:59', strtotime('+6 days'));
        $fourteen_days  = date('Y-m-d 23:59:59', strtotime('+13 days'));

        $data['member']           = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        $data['activity_feed']    = $this->membership_model->get_activity_feed(5) ?: [];
        $data['this_week_events'] = $this->membership_model->get_events_by_week_range($now, $seven_days) ?: [];
        $data['next_week_events'] = $this->membership_model->get_events_by_week_range($seven_days, $fourteen_days) ?: [];
        $data['later_events']     = $this->membership_model->get_events_by_week_range($fourteen_days, '9999-12-31 23:59:59') ?: [];

        $data['my_registrations'] = [];
        if ($data['member']) {
            $registrations = $this->membership_model->get_my_registrations($this->contact_user_id);
            foreach ($registrations as $reg) {
                $data['my_registrations'][$reg['event_id']] = $reg;
            }
        }

        // ── Pending actions ──────────────────────────────────────────
        $data['pending_actions'] = [];

        // 1. Unregistered upcoming events
        $all_upcoming = array_merge($data['this_week_events'], $data['next_week_events']);
        foreach ($all_upcoming as $ev) {
            if (empty($data['my_registrations'][$ev['id']])) {
                $data['pending_actions'][] = [
                    'icon'  => 'fa-calendar-check',
                    'color' => 'tw-text-blue-500',
                    'text'  => _l('membership_action_register_event') . ': <strong>' . e($ev['title']) . '</strong>',
                    'url'   => site_url('membership/client/events'),
                    'label' => _l('membership_register'),
                    'btn'   => 'btn-primary',
                ];
            }
        }

        // 2. Unpaid invoices
        // Perfex status constants: 1=Unpaid, 2=Paid, 3=Partially Paid, 4=Overdue, 5=Cancelled, 6=Draft
        $this->load->model('invoices_model');
        $all_invoices = $this->invoices_model->get('', ['clientid' => get_client_user_id()]) ?: [];
        foreach ($all_invoices as $inv) {
            if (in_array((int)$inv['status'], [
                Invoices_model::STATUS_UNPAID,    // 1
                Invoices_model::STATUS_PARTIALLY, // 3
                Invoices_model::STATUS_OVERDUE,   // 4
            ])) {
                $data['pending_actions'][] = [
                    'icon'  => 'fa-credit-card',
                    'color' => 'tw-text-red-500',
                    'text'  => _l('membership_action_unpaid_invoice') . ': <strong>' . format_invoice_number($inv['id']) . '</strong> — ' . app_format_money($inv['total'], get_base_currency()),
                    'url'   => site_url('invoice/' . $inv['id'] . '/' . $inv['hash']),
                    'label' => _l('membership_pay_now'),
                    'btn'   => 'btn-danger',
                ];
            }
        }

        // 3. Active election — vote reminder
        if ($data['member']) {
            $active_election = $this->membership_model->get_active_election();
            if ($active_election && !$this->membership_model->has_voted($active_election['id'], $this->contact_user_id)) {
                $data['pending_actions'][] = [
                    'icon'  => 'fa-check-square',
                    'color' => 'tw-text-purple-500',
                    'text'  => _l('membership_action_vote_reminder') . ': <strong>' . e($active_election['title']) . '</strong>',
                    'url'   => site_url('membership/client/cast_vote'),
                    'label' => _l('membership_cast_vote'),
                    'btn'   => 'btn-info',
                ];
            }
        }

        // 4. Profile incomplete — link to the standard Perfex client profile page
        if ($data['member']) {
            $missing = [];
            $contact_info = $this->db->where('id', $this->contact_user_id)->get(db_prefix() . 'contacts')->row_array();
            if ($contact_info && empty($contact_info['phonenumber'])) $missing[] = _l('membership_phone');
            if ($contact_info && empty($contact_info['firstname']))   $missing[] = _l('clients_firstname');
            if (!empty($missing)) {
                $data['pending_actions'][] = [
                    'icon'  => 'fa-user',
                    'color' => 'tw-text-amber-500',
                    'text'  => _l('membership_action_profile_incomplete') . ': <strong>' . implode(', ', $missing) . '</strong>',
                    'url'   => site_url('clients/profile'),
                    'label' => _l('membership_complete_profile'),
                    'btn'   => 'btn-warning',
                ];
            }
        }

        $data['reg_open_map'] = $this->membership_model->get_event_registration_open_map();

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
        // Events are public — only require login, not active membership
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $now           = date('Y-m-d H:i:s');
        $seven_days    = date('Y-m-d 23:59:59', strtotime('+6 days'));
        $fourteen_days = date('Y-m-d 23:59:59', strtotime('+13 days'));

        $data['this_week_events'] = $this->membership_model->get_events_by_week_range($now, $seven_days) ?: [];
        $data['next_week_events'] = $this->membership_model->get_events_by_week_range($seven_days, $fourteen_days) ?: [];
        $data['later_events']     = $this->membership_model->get_events_by_week_range($fourteen_days, '9999-12-31 23:59:59') ?: [];
        $data['member']           = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        $data['my_registrations'] = [];
        $regs = $this->membership_model->get_my_registrations($this->contact_user_id);
        foreach ($regs as $r) {
            $data['my_registrations'][$r['event_id']] = $r;
        }

        // Per-event registration settings (event_id => 1/0; missing key = open by default)
        $data['reg_open_map'] = $this->membership_model->get_event_registration_open_map();

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

        // Per-event registration toggle
        if (!$this->membership_model->is_event_registration_open($event_id)) {
            set_alert('warning', _l('membership_event_registration_closed_msg'));
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

        $data['jobs']    = $this->membership_model->get_jobs('approved');
        // Member's own submissions (all statuses)
        $all_jobs = $this->membership_model->get_jobs(null);
        $data['my_jobs'] = array_filter($all_jobs, function($j) {
            return (isset($j['contact_id']) && $j['contact_id'] == $this->contact_user_id);
        });

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
                    'contact_id'    => $this->contact_user_id,
                    'title'         => $this->input->post('title'),
                    'company'       => $this->input->post('company'),
                    'description'   => $this->input->post('description'),
                    'location'      => $this->input->post('location'),
                    'salary_range'  => $this->input->post('salary_range'),
                    'external_url'  => $this->input->post('external_url'),
                    'posted_by_type'=> 'member',
                    'status'        => 'pending',
                ];

                $job_id = $this->membership_model->create_job($jobData);

                if ($job_id) {
                    set_alert('success', _l('membership_job_submitted'));
                    redirect(site_url('membership/client/jobs'));
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
                    redirect(site_url('membership/client/stories'));
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
            redirect(site_url('membership/client/elections'));
        }

        if ($this->membership_model->has_voted($election_id, $this->contact_user_id)) {
            set_alert('warning', _l('membership_already_voted'));
            redirect(site_url('membership/client/elections'));
        }

        $candidate_id = $this->input->post('candidate_id');
        if (!$candidate_id) {
            set_alert('danger', _l('membership_select_candidate'));
            redirect(site_url('membership/client/elections'));
        }

        $result = $this->membership_model->cast_vote($election_id, $candidate_id, $this->contact_user_id);

        if ($result) {
            set_alert('success', _l('membership_vote_submitted'));
        } else {
            set_alert('danger', _l('membership_vote_failed'));
        }

        redirect(site_url('membership/client/elections'));
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
        // Use the standard Perfex CRM client profile page — it already handles
        // all contact fields, profile image, email notifications, and password change.
        redirect(site_url('clients/profile'));
    }

    public function nominations()
    {
        $this->check_member_access();

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);

        // Split elections: current (active + not expired) vs past (expired or closed)
        $now = date('Y-m-d H:i:s');
        $all_elections = $this->membership_model->get_elections();
        $data['elections'] = array_values(array_filter($all_elections, function($el) use ($now) {
            return $el['status'] === 'active' && $el['end_date'] >= $now;
        }));
        $data['past_elections'] = array_values(array_filter($all_elections, function($el) use ($now) {
            return $el['status'] !== 'draft' && ($el['status'] !== 'active' || $el['end_date'] < $now);
        }));
        $data['symbols']            = $this->membership_model->get_election_symbols();
        $data['my_nominations']     = $this->membership_model->get_nominations(null, null, null, $this->contact_user_id);
        $data['members']            = $this->membership_model->get_all_members('active');
        $data['nomination_fee']     = get_option('membership_nomination_fee') ?: '0.00';
        $data['nomination_currency']= get_option('membership_nomination_currency') ?: 'USD';
        $data['nomination_rules']   = get_option('membership_nomination_rules') ?: '';

        // Pre-build positions map keyed by election_id so the view needs no AJAX
        $positions_map = [];
        foreach (array_merge($data['elections'], $data['past_elections']) as $el) {
            $db_pos = array_column($this->membership_model->get_positions($el['id']), 'name');
            $inline = [];
            if (!empty($el['positions'])) {
                $inline = array_filter(array_map('trim', explode(',', $el['positions'])));
            }
            $all = array_values(array_unique(array_merge($db_pos, array_values($inline))));
            sort($all);
            $positions_map[$el['id']] = $all;
        }
        $data['positions_by_election'] = $positions_map;

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

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $allowed_mimes      = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $finfo    = new finfo(FILEINFO_MIME_TYPE);
                $mime     = $finfo->file($_FILES['photo']['tmp_name']);

                if (!in_array($file_ext, $allowed_extensions, true) || !in_array($mime, $allowed_mimes, true) || !getimagesize($_FILES['photo']['tmp_name'])) {
                    set_alert('danger', _l('membership_invalid_file_type'));
                    redirect(site_url('membership/client/nominations'));
                    return;
                }

                $photo_path = 'uploads/membership/nominations/';
                if (!is_dir($photo_path)) {
                    mkdir($photo_path, 0755, true);
                }
                // Safe filename — never use original filename, only whitelisted extension
                $photo_name = uniqid('nom_', true) . '.' . $file_ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path . $photo_name);
            }

            $nominationData = [
                'election_id'             => $election_id,
                'member_id'               => $nominated_member_id,
                'nominated_by_contact_id' => $this->contact_user_id,
                'position'                => $this->input->post('position'),
                'manifesto'               => $this->input->post('manifesto'),
                'photo'                   => $photo_name,
                'status'                  => 'pending',
            ];

            $nomination_id = $this->membership_model->create_nomination($nominationData);

            if ($nomination_id) {
                // Load full nomination data for email sending
                $nom = $this->membership_model->get_nomination_full($nomination_id);

                if ($nom) {
                    $nominator_name = trim($member['firstname'] . ' ' . $member['lastname']);
                    $nominee_name   = trim(($nom['nominee_firstname'] ?? '') . ' ' . ($nom['nominee_lastname'] ?? ''));
                    $election_title = $nom['election_title'] ?? '';

                    // Email → the person who was nominated
                    if (!empty($nom['nominee_email'])) {
                        send_mail_template('Membership_nomination_to_nominee', 'membership',
                            $nom['nominee_email'],
                            $nom['nominee_contact_id'],
                            $election_title,
                            $nominator_name
                        );
                    }

                    // Email → the person who submitted the nomination
                    $this->load->model('clients_model');
                    $nominator_contact = $this->clients_model->get_contact($this->contact_user_id);
                    if ($nominator_contact && !empty($nominator_contact->email)) {
                        send_mail_template('Membership_nomination_to_nominator', 'membership',
                            $nominator_contact->email,
                            $this->contact_user_id,
                            $nominee_name,
                            $election_title
                        );
                    }
                }

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

        // Pre-build positions map keyed by election_id — no AJAX needed in view
        $positions_map = [];
        foreach ($data['elections'] as $el) {
            $db_pos = array_column($this->membership_model->get_positions($el['id']), 'name');
            $inline = [];
            if (!empty($el['positions'])) {
                $inline = array_filter(array_map('trim', explode(',', $el['positions'])));
            }
            $all = array_values(array_unique(array_merge($db_pos, array_values($inline))));
            sort($all);
            $positions_map[$el['id']] = $all;
        }
        $data['positions_by_election'] = $positions_map;

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
                redirect(site_url('membership/client/cast_vote'));
            }

            $result = $this->membership_model->cast_vote($election_id, $candidate_id, $this->contact_user_id);

            if ($result) {
                set_alert('success', _l('membership_vote_submitted'));

                // Notify all admins that a vote was cast
                $election = $this->membership_model->get_election($election_id);
                $election_title = $election ? $election['title'] : '#' . $election_id;
                $admins = $this->db->where('admin', 1)->get(db_prefix() . 'staff')->result_array();
                $notifiedUsers = [];
                foreach ($admins as $admin) {
                    $notified = add_notification([
                        'description' => _l('membership_notification_vote_cast', $election_title),
                        'touserid'    => $admin['staffid'],
                        'link'        => 'membership/vote_list',
                    ]);
                    if ($notified) {
                        $notifiedUsers[] = $admin['staffid'];
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            } else {
                set_alert('danger', _l('membership_vote_failed'));
            }

            redirect(site_url('membership/client/cast_vote'));
        }

        $data['elections']         = $this->membership_model->get_elections('active');
        $data['selected_election'] = (int)$this->input->get('election_id') ?: null;

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

        $data['members']       = $this->membership_model->get_all_members('active');
        $data['board_members'] = $this->membership_model->get_board_members('active');

        $this->data($data)
            ->title(_l('membership_members'))
            ->view('public/board_members')
            ->layout();
    }

    public function get_election_details()
    {
        $this->output->set_content_type('application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        if (!$member || $member['status'] != 'active') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // Accept ID from URI segment (.../get_election_details/5) or GET param (?election_id=5)
        $election_id = (int)$this->uri->segment(5) ?: (int)$this->input->get('election_id') ?: (int)$this->input->post('election_id');
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
        // This is an AJAX endpoint — never redirect, always return HTML
        if (!is_client_logged_in()) {
            echo '<div class="alert alert-danger">' . _l('membership_access_denied') . '</div>';
            return;
        }

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        if (!$member || $member['status'] != 'active') {
            echo '<div class="alert alert-danger">' . _l('membership_access_denied') . '</div>';
            return;
        }

        $election_id = (int)$this->input->get('election_id');
        if (!$election_id) {
            echo '<div class="alert alert-warning">' . _l('membership_select_election') . '</div>';
            return;
        }

        $election = $this->membership_model->get_election($election_id);
        if (!$election || $election['status'] != 'active') {
            echo '<div class="alert alert-warning">' . _l('membership_election_not_available') . '</div>';
            return;
        }

        $has_voted  = $this->membership_model->has_voted($election_id, $this->contact_user_id);
        $candidates = $this->membership_model->get_final_candidates($election_id);

        // Build available positions: merge election's inline positions + DB positions for this election
        $election   = $this->membership_model->get_election($election_id);
        $positions  = [];
        if ($election && !empty($election['positions'])) {
            $positions = array_filter(array_map('trim', explode(',', $election['positions'])));
        }
        $db_positions = array_column($this->membership_model->get_positions($election_id), 'name');
        $positions = array_values(array_unique(array_merge($positions, $db_positions)));
        sort($positions);

        $this->load->view('public/partials/_candidates_list', [
            'candidates'  => $candidates ?: [],
            'election_id' => $election_id,
            'has_voted'   => $has_voted,
            'positions'   => $positions,
        ]);
    }

    public function get_vote_history()
    {
        $this->output->set_content_type('application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        if (!$member || $member['status'] != 'active') {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
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

    public function ajax_get_positions_by_election($election_id = 0)
    {
        $this->output->set_content_type('application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false]);
            return;
        }

        $member = $this->membership_model->get_member_by_contact_id($this->contact_user_id);
        if (!$member || $member['status'] != 'active') {
            echo json_encode(['success' => false]);
            return;
        }

        $election_id = (int)$election_id;
        $election    = $this->membership_model->get_election($election_id);

        if (!$election) {
            echo json_encode(['success' => false, 'positions' => []]);
            return;
        }

        // Positions from the positions table linked to this election
        $db_positions = array_column(
            $this->membership_model->get_positions($election_id),
            'name'
        );

        // Positions from the election's own comma-separated field
        $inline = [];
        if (!empty($election['positions'])) {
            $inline = array_filter(array_map('trim', explode(',', $election['positions'])));
        }

        // Merge, deduplicate, sort
        $all = array_values(array_unique(array_merge($db_positions, $inline)));
        sort($all);

        echo json_encode(['success' => true, 'positions' => $all]);
    }
}
