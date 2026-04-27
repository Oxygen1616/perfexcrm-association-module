<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Mail class: sent to a contact when they are added as a new member (status = pending).
 *
 * Usage:
 *   send_mail_template('Membership_welcome', 'membership', $contact->email, $contact->id);
 */
class Membership_welcome extends App_mail_template
{
    protected $for = 'customer';

    public $slug = 'membership-welcome';

    protected $contact_email;
    protected $contact_id;

    public function __construct($contact_email, $contact_id)
    {
        parent::__construct();
        $this->contact_email = $contact_email;
        $this->contact_id    = (int) $contact_id;
    }

    public function build()
    {
        $contact = $this->ci->db
            ->where('id', $this->contact_id)
            ->get(db_prefix() . 'contacts')
            ->row();

        $merge_fields = [
            '{contact_firstname}' => $contact ? $contact->firstname            : '',
            '{contact_lastname}'  => $contact ? $contact->lastname             : '',
            '{contact_email}'     => $contact ? $contact->email                : $this->contact_email,
            '{dashboard_link}'    => site_url('membership/client'),
        ];

        $this->to($this->contact_email)
             ->set_merge_fields($merge_fields);
    }
}
