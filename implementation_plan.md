# Goal Description
The objective is to create a robust, role-based "Membership Dashboard" system tailored for community organizations (like the Association of Ndigbo in Estonia) using a custom **Perfex CRM Module**. This replaces the WordPress + JetEngine architecture with Perfex CRM's CodeIgniter HMVC framework, leveraging its native Customer (Client Portal) and Staff (Admin) capabilities to deliver member directories, event management, job boards, elections, and transaction tracking.

## User Review Required
> [!IMPORTANT]
> - **Authentication Strategy:** The plan assumes we map "Members" to Perfex **Contacts** (Client Portal users) and "Executives/Admins" to Perfex **Staff**. This natively provides secure login, password resets, and session management.
> - **Payments Integration:** Now relying entirely on the native Perfex **Sales -> Invoices/Payments** menus. The custom payments submodule will be completely removed from the Membership module. Is that fully aligned with your tracking needs?

---

## Proposed Changes

The module will be created under `modules/membership/`. 

### 1. User Roles & Access Control
Instead of WordPress Roles, we will utilize Perfex CRM's Permission system and Contact authentication:
- **Pending Member / Active Member:** Handled via native Perfex **Contacts** (associated with a Membership Client). We will use native **Custom Fields** to store `Membership Status` (Pending/Active/Suspended), `Profession`, `Graduation Year`, and `Directory Visibility`.
- **Executive:** Perfex **Staff** member with specific `membership_executive` permissions. They will use a dedicated **Member Management Tab** inside the module to one-click Approve, Reject, or Suspend contacts.
- **Super Admin:** Perfex **Staff** with native administrator privileges.

### 2. Database Structure (Activation Hook)
On module activation (`register_activation_hook`), the following custom CodeIgniter tables will be generated:
- `tblmembership_jobs` (Job board submissions)
- `tblmembership_stories` (Member articles/news)
- `tblmembership_events` & `tblmembership_event_registrations`
- `tblmembership_elections`, `tblmembership_candidates`, & `tblmembership_votes`
- `tblmembership_positions` (for roles in nominations and elections) [NEW]

### 3. Member Dashboard (Client Portal)
The Member Dashboard will be accessible strictly to logged-in Contacts with an "Active" Membership Status.
**Modules (Views in `views/public/`):**
1. **Dashboard Home:**
   - **Profile Removal:** Remove the "My Profile" section from the membership dashboard (fallback to native).
   - **Payment Table:** Add a payment table showing client's invoices directly on the dashboard, pulled from the native invoices page (or embedding the invoice page content).
2. **Activity Feed:** Aggregated timeline of recent jobs, events, and stories.
3. **Member Directory:** Searchable grid of Active members (Contacts).
4. **Events & Registrations:** View upcoming events, RSVP, and view QR tickets.
5. **Job Posts & Stories:** Submit via CodeIgniter forms (CSRF protected); view approved entries.
6. **Elections & Voting:** 
   - Rename button/link from "Apply for Nominatiom" to **"Add Nomination"**.
   - Available Elections should be clickable (name/title) and reveal a popup displaying the detailed election information.
   - **Cast Vote:** The vote form must dynamically populate Candidates based on the selected Election. Candidates should be fetched exclusively from the "Final Candidate" table in the admin area.
   - Captured votes must be mapped directly to the admin Vote List (categorized per election per candidate) while tracking the casting user.
   - When Election is selected it should pull the final candidates for that election from the final candidate table in the admin vote menu.
7. **Board Members & Committees:**
   - **Board Members:** Listing items (names) must be clickable, opening a popup modal that displays their details.
   - **Committees:** Listing items (titles/names) must be clickable, opening a popup modal that displays committee details.

### 4. Executive Dashboard (Admin Area)
Accessible to Staff via Perfex Admin sidemenu. Handled via `Admin_membership.php`.
**Modules (Views in `views/admin/`):**
1. **Positions Submenu [NEW]:**
   - Add a "Positions" Submenu under Membership to create/manage organizational positions. 
   - The associated table will supply the available positions to the Client-Side forms for Nominations and Voting.
2. **Payments & Billing:** 
   - **[DELETE]** Remove the custom "Payments and Transactions" submenu from Membership. All billing will exclusively use Perfex CRM native Sales -> Payments/Invoices features.
3. **Admin Tables UX Standardization:**
   - ALL tables in the membership admin pages must mimic the native CRM Customers/Contacts tables.
   - Main entity names (e.g., job titles, events, candidates) should be clickable to open an editable form containing their details in a popup/slideout or new page.
   - Quick actions (`Edit | Delete`) must appear directly underneath the main entity name when hovering or listed inline.
4. **Member Management Roster:** A dedicated table listing all enrolled contacts with quick-actions to Approve/Suspend.
5. **Event & Content Management:** Full CRUD interface for Events, Jobs, and Stories.
6. **Elections Admin:** Set up elections, link candidates manually to the Final Candidates table, and maintain the aggregate Vote List.

### 5. Forms & Data Input
Perfex does not have a visual form builder for the Client Portal out-of-the-box in the same way JetEngine does, so we will build native HTML/CodeIgniter forms:
- **Client Facing:** Job Submission (`post_job`), Story Submission (`post_story`), Add Nomination, Cast Vote.
- **Form Handling:** Submissions POST to `Clients_membership` controller -> Validated with `$CI->form_validation` -> Saved via `Membership_model` -> Returns alert `set_alert()`.

### 6. Additional Infrastructure
- **Email Notifications:** Register custom email templates in Perfex (`hooks()->add_action('after_email_templates_init')`). Triggers: *New Registration, Payment Verified, Story Approved, Event Reminder.*
- **Cron Jobs:** Hook into Perfex's core cron via `after_cron_run`. Tasks: *Membership expiration checks, Event reminders (24h before).*
- **Menus:** Implement `hooks()->add_action('admin_init')` to inject the Admin sidebar menu, and `hooks()->add_action('customers_navigation_end')` to inject the Client Portal top-bar menu.

---

## Verification Plan

### Automated Tests
*Note: Perfex CRM standard development relies heavily on manual verification, but we can structure some modular testing if configured.*
- Validate that SQL tables are generated correctly upon Module Activation (`Setup -> Modules -> Activate`).
- Verify Database constraints (e.g., uniqueness of a vote per user in `tblmembership_votes`).

### Manual Verification
1. **Role Verification:** Log in as testing Contact. Verify they cannot access `/admin/membership` routes.
2. **Forms/CSRF:** Submit a dummy Job Post from the Client Portal. Ensure XSS filtering and CSRF checks pass.
3. **Admin Queue:** Log in as Admin Staff. Navigate to `Membership -> Content Moderation`. Validate the dummy Job Post appears and can be approved.
4. **Directory:** Verify the approved Job Post and Active Contacts show up in the Client Portal public directory grids.
5. **Uninstall:** Deactivate and uninstall the module to verify DB cleanup occurs (if programmed to do so) without breaking core CRM functionality.
