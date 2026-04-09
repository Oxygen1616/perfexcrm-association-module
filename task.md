# Task Checklist: Ndigbo in Estonia - Perfex CRM Membership Module

## Phase 1: Foundation & Setup
- [ ] Create base module structure (`modules/membership/`)
- [ ] Create `membership.php` init file with headers and hooks
- [ ] Implement `register_activation_hook` to create custom database tables
- [ ] Create staff permissions (Executive, Membership Admin) in init file
- [ ] Add `membership_status`, `profession`, `graduation_year` to `tblcontacts` via activation hook (or custom member table)
- [ ] Setup module language files (`language/english/membership_lang.php`)

## Phase 2: Core Models & Controllers
- [ ] Create `Membership_model.php` for database interactions (jobs, events, stories, etc.)
- [ ] Create `Admin_membership.php` (Staff Admin Controller)
- [ ] Create `Clients_membership.php` (Client Portal Controller)
- [ ] Set up routing and sidebar menu hooks for Admin and Client portal

## Phase 3: Member Dashboard (Client Portal)
- [ ] Build Activity Feed view & controller logic
- [ ] Build Member Directory with filters
- [ ] Build My Events & Event Registration Views
- [ ] Build Job Posts board & submission forms
- [ ] Build Stories/Articles listing & submission
- [ ] Implement Membership Plans & Transaction display (integration with Perfex core payments)
- [ ] Build Elections & Voting interface
- [ ] Centralized Notifications Hub

## Phase 4: Executive Dashboard (Admin Area)
- [ ] Build Member Management DataTables (Approve/Suspend members)
- [ ] Build Payment Verification & Transaction logs
- [ ] Build Event Management & Ticketing Admin
- [ ] Build Content Moderation Queue (Jobs, Stories)
- [ ] Build Elections Admin (Create elections, manage candidates)
- [ ] System Settings & Integration Options

## Phase 5: Infrastructure & Polish
- [ ] Register new email templates for module notifications
- [ ] Implement module Cron job hook for membership expiry and event reminders
- [ ] Mobile responsiveness review and CSS/JS asset organization
- [ ] Security audit (CSRF, XSS, Permission checks)
