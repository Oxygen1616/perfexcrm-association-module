# Perfex CRM Membership Dashboard Module

## Overview
A robust, role-based membership system for community organizations built as a custom Perfex CRM module. Replaces complex WordPress + JetEngine architecture with Perfex CRM's native capabilities.

## Features

### For Members (Client Portal)
- **Activity Feed**: Timeline of recent jobs, events, and stories
- **Member Directory**: Searchable directory of active members
- **Event Management**: View events, register with QR code tickets
- **Job Board**: Post and view job opportunities (approval workflow)
- **Story Sharing**: Share member articles/news (approval workflow)
- **Elections**: Participate in votes with one-vote-per-member enforcement
- **Billing & Dues**: View and pay membership invoices
- **Profile Management**: Update personal information and preferences

### For Administrators
- **Member Management**: Approve, suspend, delete members; view stats
- **Content Moderation**: Approve/reject jobs, stories, events
- **Event Management**: Full CRUD for events and registrations
- **Election Administration**: Set up elections, manage candidates, view results
- **Payment Monitoring**: Track membership payments and dues
- **Module Settings**: Configure pricing, membership types, automation

## Technical Implementation

### Architecture
- Built as Perfex CRM module following HMVC pattern
- Utilizes native Perfex authentication (Contacts for members, Staff for admins)
- Custom database tables for membership-specific entities
- Integrated with Perfex's menu, cron, email template, and permissions systems

### Database Tables
- `tblmembership_members` - Member status and metadata
- `tblmembership_jobs` - Job board submissions
- `tblmembership_stories` - Member articles and stories
- `tblmembership_events` & `tblmembership_event_registrations` - Events and RSVPs
- `tblmembership_elections`, `tblmembership_candidates`, `tblmembership_votes` - Election system
- `tblmembership_payments` - Membership dues tracking

### Key Integrations
- **Authentication**: Uses Perfex's native login/password reset/session management
- **Menus**: Admin sidebar menu and client portal navigation
- **Cron**: Automated membership expiration checks and event reminders
- **Email**: Custom templates for welcomes, approvals, and reminders
- **Invoices**: Leverages native Perfex invoicing for dues tracking
- **Permissions**: Granular staff capabilities for different module functions

## Installation & Activation
See `MODULE_ACTIVATION_INSTRUCTIONS.md` for detailed setup instructions.

## Usage
1. Activate module via Setup → Modules
2. Configure settings (membership types, dues amounts, etc.)
3. Add members via Admin → Membership → Members
4. Approve member applications to activate accounts
5. Members access dashboard via client portal navigation
6. Administrators manage content via Admin → Membership

## Customization
- Modify views in `/views/admin/` and `/views/public/`
- Adjust logic in `/models/Membership_model.php`
- Update routes in `/config/routes.php`
- Add new email templates via hooks in `membership.php`
- Extend settings in `/controllers/Admin_membership.php` settings method

## Support
This module is designed for Perfex CRM v2.3+. For issues or enhancements, please refer to the Perfex CRM documentation or community resources.