<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sent to the nominee when their nomination is approved by admin.
 *
 * Usage:
 *   send_mail_template('Membership_nomination_approved', 'membership',
 *       $nominee_email, $nominee_contact_id, $election_title);
 */
class Membership_nomination_approved extends App_mail_template
{
    protected $for = 'customer';

    public $slug = 'membership-nomination-approved';

    protected $nominee_email;
    protected $nominee_contact_id;
    protected $election_title;

    public function __construct($nominee_email, $nominee_contact_id, $election_title)
    {
        parent::__construct();
        $this->nominee_email      = $nominee_email;
        $this->nominee_contact_id = (int) $nominee_contact_id;
        $this->election_title     = $election_title;
    }

    public function build()
    {
        $contact = $this->ci->db
            ->where('id', $this->nominee_contact_id)
            ->get(db_prefix() . 'contacts')
            ->row();

        $merge_fields = [
            '{contact_firstname}' => $contact ? $contact->firstname : '',
            '{contact_lastname}'  => $contact ? $contact->lastname  : '',
            '{election_title}'    => $this->election_title,
            '{dashboard_link}'    => site_url('membership/client/nominations'),
        ];

        $this->to($this->nominee_email)
             ->set_merge_fields($merge_fields);
    }
}
