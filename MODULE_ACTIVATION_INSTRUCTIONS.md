# Membership Module Activation Instructions

## Overview
You have successfully created the Membership Dashboard module for Perfex CRM. The module is located at:
`/modules/membership/`

## Activation Steps

### 1. Verify Module Files
Ensure all required files are present in `/modules/membership/`:
- `membership.php` (main module file)
- `install.php` (database table creation)
- `uninstall.php` (cleanup on removal)
- `/controllers/Admin_membership.php` (admin interface)
- `/controllers/Client.php` (member dashboard)
- `/models/Membership_model.php` (data logic)
- `/views/admin/` (admin interface views)
- `/views/public/` (member dashboard views)
- `/language/english/membership_lang.php` (language strings)
- `/config/routes.php` (module routes)

### 2. Activate the Module
1. Log in to Perfex CRM as an Administrator
2. Navigate to: **Setup → Modules**
3. Find "Membership" in the module list
4. Click the **Activate** button
5. The module will automatically:
   - Create all required database tables
   - Register email templates
   - Set up cron jobs
   - Add menu items

### 3. Access the Module

#### Admin Interface
After activation, access the admin dashboard at:
**URL:** `/admin/membership`
**Menu:** Look for "Membership" in the left admin sidebar (positioned near the top)

#### Member Dashboard
Active members can access their dashboard at:
**URL:** `/membership`
**Menu:** Logged-in members with active status will see "Member Dashboard" in the client portal navigation

## Module Features

### Admin Capabilities
- Member management (approve/suspend/delete)
- Content moderation (jobs, stories, events)
- Election management (setup, candidates, results)
- Payment monitoring
- Module settings configuration

### Member Features
- Activity feed (jobs, events, stories)
- Searchable member directory
- Event registration with QR codes
- Job/story submission (approval workflow)
- Election voting (one-vote-per-member)
- Billing & dues tracking
- Profile management

## Troubleshooting

### 404 Errors
If you encounter 404 errors after activation:
1. Clear your browser cache
2. Ensure you're logged in as an administrator for admin routes
3. Ensure you're logged in as an active member for client routes
4. Check that the module is actually activated in Setup → Modules
5. Verify PHP error logs for any syntax issues

### Database Issues
If tables aren't being created:
1. Check that the `install.php` file has proper permissions
2. Verify the database user has CREATE TABLE privileges
3. Check PHP error logs for installation errors

### Menu Items Not Showing
1. Ensure you have the correct user role (admin for admin menu, active member for client menu)
2. Try refreshing the page or clearing browser cache
3. Check that the hook functions in `membership.php` are executing properly

## Technical Notes

### Authentication
- Members map to Perfex **Contacts** (Client Portal users)
- Executives/Admins map to Perfex **Staff** with specific permissions
- Uses Perfex's native authentication, password reset, and session management

### Database Tables Created
- `tblmembership_members` (member status & metadata)
- `tblmembership_jobs` (job board)
- `tblmembership_stories` (member articles)
- `tblmembership_events` & `tblmembership_event_registrations`
- `tblmembership_elections`, `tblmembership_candidates`, `tblmembership_votes`
- `tblmembership_payments` (membership dues tracking)

### Hooks Implemented
- Admin menu initialization
- Client navigation menu
- Cron jobs (expiration checks, event reminders)
- Email template registration
- Migration safety (for description/content fields)
- Staff permissions (view, create, edit, delete, manage_members, manage_elections)

## Next Steps
1. Activate the module following the instructions above
2. Configure module settings via Admin → Membership → Settings
3. Add membership types and set monthly dues amount
4. Begin adding members via Admin → Membership → Members
5. Test member registration and approval workflow