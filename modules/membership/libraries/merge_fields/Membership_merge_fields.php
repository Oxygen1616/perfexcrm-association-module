<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Membership merge fields — defines every {placeholder} available
 * across all membership email templates so they appear in the
 * "Available Merge Fields" sidebar of the email template editor.
 *
 * The class name determines the group name shown in the sidebar:
 *   strtolower(strbefore('Membership_merge_fields', '_merge_fields')) → 'membership'
 * This matches the template type='membership' stored in the DB, so
 * common fields (available=['membership']) appear on every template.
 *
 * Template-specific fields use available=['_'] (a dummy value that
 * never matches any type) plus a `templates` array with exact slugs,
 * so the view's OR-condition shows them only on the right templates.
 */
class Membership_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [

            // ── Fields available on ALL membership templates ─────────

            [
                'name'      => 'Contact First Name',
                'key'       => '{contact_firstname}',
                'available' => ['membership'],
            ],
            [
                'name'      => 'Contact Last Name',
                'key'       => '{contact_lastname}',
                'available' => ['membership'],
            ],
            [
                'name'      => 'Member Dashboard Link',
                'key'       => '{dashboard_link}',
                'available' => ['membership'],
            ],

            // ── Event Reminder template ──────────────────────────────

            [
                'name'      => 'Event Name',
                'key'       => '{event_name}',
                'available' => ['_'],   // never matches by type; shown via `templates`
                'templates' => ['membership-event-reminder'],
            ],
            [
                'name'      => 'Event Date',
                'key'       => '{event_date}',
                'available' => ['_'],
                'templates' => ['membership-event-reminder'],
            ],
            [
                'name'      => 'Event Location',
                'key'       => '{event_location}',
                'available' => ['_'],
                'templates' => ['membership-event-reminder'],
            ],

            // ── Nomination templates (all four) ──────────────────────

            [
                'name'      => 'Election Title',
                'key'       => '{election_title}',
                'available' => ['_'],
                'templates' => [
                    'membership-nomination-submitted-nominee',
                    'membership-nomination-submitted-nominator',
                    'membership-nomination-approved',
                    'membership-nomination-rejected',
                ],
            ],

            // ── Nomination submitted — to nominee only ───────────────

            [
                'name'      => 'Nominator Name',
                'key'       => '{nominator_name}',
                'available' => ['_'],
                'templates' => ['membership-nomination-submitted-nominee'],
            ],

            // ── Nomination submitted — to nominator only ─────────────

            [
                'name'      => 'Nominee Name',
                'key'       => '{nominee_name}',
                'available' => ['_'],
                'templates' => ['membership-nomination-submitted-nominator'],
            ],

        ];
    }

    /**
     * format() is provided for completeness.
     * Actual value replacement is handled inside each mail class's
     * build() method via set_merge_fields(), so this method is not
     * invoked during normal email sending for this module.
     *
     * @param  int $contact_id
     * @return array
     */
    public function format($contact_id = 0)
    {
        $fields = [
            '{contact_firstname}' => '',
            '{contact_lastname}'  => '',
            '{dashboard_link}'    => site_url('membership/client'),
            '{event_name}'        => '',
            '{event_date}'        => '',
            '{event_location}'    => '',
            '{election_title}'    => '',
            '{nominator_name}'    => '',
            '{nominee_name}'      => '',
        ];

        if ($contact_id) {
            $contact = get_instance()->db
                ->where('id', (int) $contact_id)
                ->get(db_prefix() . 'contacts')
                ->row();

            if ($contact) {
                $fields['{contact_firstname}'] = $contact->firstname;
                $fields['{contact_lastname}']  = $contact->lastname;
            }
        }

        return $fields;
    }
}
