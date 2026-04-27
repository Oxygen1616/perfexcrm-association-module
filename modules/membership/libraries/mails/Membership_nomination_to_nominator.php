<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sent to the person who submitted the nomination (the nominator).
 *
 * Usage:
 *   send_mail_template('Membership_nomination_to_nominator', 'membership',
 *       $nominator_email, $nominator_contact_id, $nominee_name, $election_title);
 */
class Membership_nomination_to_nominator extends App_mail_template
{
    protected $for = 'customer';

    public $slug = 'membership-nomination-submitted-nominator';

    protected $nominator_email;
    protected $nominator_contact_id;
    protected $nominee_name;
    protected $election_title;

    public function __construct($nominator_email, $nominator_contact_id, $nominee_name, $election_title)
    {
        parent::__construct();
        $this->nominator_email      = $nominator_email;
        $this->nominator_contact_id = (int) $nominator_contact_id;
        $this->nominee_name         = $nominee_name;
        $this->election_title       = $election_title;
    }

    public function build()
    {
        $contact = $this->ci->db
            ->where('id', $this->nominator_contact_id)
            ->get(db_prefix() . 'contacts')
            ->row();

        $merge_fields = [
            '{contact_firstname}' => $contact ? $contact->firstname : '',
            '{contact_lastname}'  => $contact ? $contact->lastname  : '',
            '{nominee_name}'      => $this->nominee_name,
            '{election_title}'    => $this->election_title,
            '{dashboard_link}'    => site_url('membership/client/nominations'),
        ];

        $this->to($this->nominator_email)
             ->set_merge_fields($merge_fields);
    }
}
