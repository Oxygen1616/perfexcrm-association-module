<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Mail class: sent to registered members 24 hours before an event.
 *
 * Usage:
 *   send_mail_template('Membership_event_reminder', 'membership',
 *       $contact->email, $contact->id, $event['title'], $event['event_date'], $event['location']);
 */
class Membership_event_reminder extends App_mail_template
{
    protected $for = 'customer';

    public $slug = 'membership-event-reminder';

    protected $contact_email;
    protected $contact_id;
    protected $event_name;
    protected $event_date;
    protected $event_location;

    public function __construct($contact_email, $contact_id, $event_name, $event_date, $event_location = '')
    {
        parent::__construct();
        $this->contact_email  = $contact_email;
        $this->contact_id     = (int) $contact_id;
        $this->event_name     = $event_name;
        $this->event_date     = $event_date;
        $this->event_location = $event_location;
    }

    public function build()
    {
        $contact = $this->ci->db
            ->where('id', $this->contact_id)
            ->get(db_prefix() . 'contacts')
            ->row();

        $merge_fields = [
            '{contact_firstname}' => $contact ? $contact->firstname : '',
            '{contact_lastname}'  => $contact ? $contact->lastname  : '',
            '{event_name}'        => $this->event_name,
            '{event_date}'        => _dt($this->event_date),
            '{event_location}'    => $this->event_location ?: 'TBD',
            '{dashboard_link}'    => site_url('membership/client'),
        ];

        $this->to($this->contact_email)
             ->set_merge_fields($merge_fields);
    }
}
