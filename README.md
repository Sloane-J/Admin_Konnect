# Departmental Management Platform - Streamlined Project Scope

## 🎯 Project Overview

### The Problem

Modern organizations struggle with fragmented departmental workflows, particularly around document management, visitor coordination, and incident tracking. Current solutions often involve:

- Physical filing cabinets and paper-based document routing
- Manual visitor logbooks and phone-based coordination
- Email-only incident reporting with no tracking
- Disconnected systems that don't communicate
- Poor audit trails and accountability gaps

### The Solution

A streamlined digital platform that modernizes departmental operations with simple, practical workflows. The system replaces physical processes with straightforward digital alternatives while leveraging existing organizational infrastructure (email, authentication systems).

### Core Value Proposition

- **Digitizes** manual departmental processes with simple workflows
- **Centralizes** document routing, visitor management, and incident tracking
- **Integrates** with existing organizational infrastructure
- **Provides** clear audit trails and accountability
- **Scales** across multiple departments within an organization

---

## 🏢 Target Users & Use Cases

### Primary Users

1. **Department Staff** - Create, route, and manage documents and incidents
2. **Department Heads** - Manage team access, view department activity
3. **Administrative Staff** - Handle visitor management, cross-department coordination
4. **System Administrators** - Configure departments, users, and system settings
5. **Super Admin** - configures all,departments, users and everything else

### Key Use Cases - more may be added and most departments would have the same use cases for the app except stated otherwise

- **Library Department** just communication with staff and other departments), vendor communications, incident reports, dcoument storage, memos and letters
- **HR Department** managing employee documents, visitor appointments, workplace incidents
- **Finance Department**
- **IT Department** managing incident reports
- **Administration , dept heads, admins and super admins**  broadcasting announcements and all others

---

## 🚀 Core Features & Modules

### 1. Document Management & Routing System

**Purpose**: Replace physical filing with simple digital document workflows

**Features**:

- Digital document upload and storage
- Simple A→B routing (send document to one person)
- Recipients can manually forward to others as needed
- Document search by title, category, and metadata
- Read receipts showing who opened documents and when
- Automatic audit trail tracking the full routing chain - using spactie activity logs package
- Password-protected access for sensitive documents
- Users can upload any document under categories and metadata

**Simplified Workflow**:

```
User A → User B
1. User A uploads document to platform
2. User A selects User B as recipient
3. System stores document and sends email notification to User B
4. User B receives in-app notification + email
5. User B opens document (password required if sensitive)
6. Read receipt logged: "User B opened at [timestamp]"
7. User B can forward to User C if needed
8. Full routing chain logged automatically
```


### 2. Visitor Management System

**Purpose**: Digitize visitor registration and track visit logs

**Features**:

- Laravel Zap integration for appointment scheduling
- Simple check-in/check-out logging
- Visitor history tracking (basic list/table view)
- Host notifications for visitor arrivals

**Simplified Workflow**:

```
External Visitor to IT Department
1. Visitor books appointment via Laravel Zap
2. IT staff receives booking notification
3. Day of visit: Manual check-in recorded in platform
4. Host notified of arrival via email + in-app
5. Manual check-out when visit ends
6. Visit logged with date, time, purpose, duration
```

### 3. Incident Reporting & Management

**Purpose**: Streamline incident documentation and departmental routing

**Features**:

- Text-based incident reporting (no file attachments)
- Department selection via dropdown
- Automatic routing to department head
- Incident status tracking
- Simple incident history view

**Incident Types**: - user would determine this in the incident report

- Security incidents (unauthorized access, theft)
- Maintenance issues (equipment failure, facility problems)
- Safety incidents (accidents, hazards)
- IT incidents (system outages, security concerns)
- Policy violations

**Simplified Workflow**:

```
Water Leak in Library
1. Staff reports incident via web form (text only)
2. Selects "Maintenance" department from dropdown
3. System automatically routes to Maintenance Department Head
4. Department head receives email + in-app notification
5. Department head assigns to staff member or responds
6. Status updated to "In Progress" → "Resolved"
7. Incident closed
```

### 4. Communication & Notification System

**Purpose**: Coordinate departmental communications and broadcasts

**Features**:

- Email + in-app notifications only
- Automated workflow notifications
- Department-wide announcements
- Broadcast to one department or all departments
- Document read receipts
- Notification history

**Notification Types**:
- Document routed to you
- Document opened (read receipt)
- Visitor checked in
- Department announcements
- System alerts

**Key Simplifications**:
- Email + in-app notifications only (no push, no SMS)
- Simple announcement system (dropdown + message)

### 5. Read Receipts

**Purpose**: Track document access for accountability

**Features**:

- Automatic logging when documents are opened or downloaded
- Shows: User name, email, timestamp, IP address
- Visible to document sender only
- Simple list view of all opens
- Cannot be disabled by recipients

**Read Receipt Display**:

```
Document: "Q4 Budget Proposal"
Sent by: John Doe (Finance)
Sent to: Jane Smith (Administration)

Read Receipts:
- Jane Smith opened on Oct 3, 2025 at 2:45 PM
- Jane Smith forwarded to Mike Johnson
- Mike Johnson opened on Oct 3, 2025 at 4:12 PM
```

---

### System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Client Layer                         │
├─────────────────────────────────────────────────────────┤
│  React Components (Inertia.js powered)                 │
│  • Document Viewer    • Visitor Logs                   │
│  • Incident Reporter  • Announcement Form              │
│  • Notification Bell  • Read Receipt Display           │
│  • Search Interface   • Simple Tables                  │
└─────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────┐
│                  Application Layer                      │
├─────────────────────────────────────────────────────────┤
│  Laravel Controllers & Services                         │
│  • DocumentController     • VisitorController          │
│  • IncidentController     • AnnouncementController     │
│  • NotificationService    • MailService (SMTP)         │
│  • FileStorageService     • AuditService               │
│  • ReadReceiptService     • ZapSchedulingService       │
└─────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────┐
│                   Data Layer                           │
├─────────────────────────────────────────────────────────┤
│  PostgreSQL Database                                    │
│  • Users & Departments    • Documents & Routing        │
│  • Visitors & Visits      • Incidents                  │
│  • Audit Logs            • Notifications               │
│  • Read Receipts         • Announcements               │
│                                                         │
│  Laravel file Storage                                       │
│  • Document Files        • User Uploads                │
│  • System Backups                                      │
└─────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────┐
│                 Integration Layer                       │
├─────────────────────────────────────────────────────────┤
│  External Services                                      │
│  • SMTP Server (Google)  • Laravel Zap               │
│  • ILovePDF (external links to the website only)                      │
└─────────────────────────────────────────────────────────┘
```

### Simplified Permission Matrix

| Feature            | Super Admin   | Dept Head                                                                                                                                                 | Staff                                         |
| ------------------ | ------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------- |
| Send Documents     | ✅ (anyone)    | ✅ (anyone)                                                                                                                                                | ✅ (anyone)                                    |
| View Documents     | ✅ (all)       | ✅ (routed to them)                                                                                                                                        | ✅ (routed to them)                            |
| View Read Receipts | ✅ (all)       | ✅ (dept docs routed to them)                                                                                                                              | ✅ (own docs)                                  |
| Manage Visitors    | ✅             | ✅ (dept)                                                                                                                                                  | ✅ (assigned)                                  |
| Create Incidents   | ✅             | ✅                                                                                                                                                         | ✅                                             |
| View Incidents     | ✅ (all)       | ✅ (dept only)                                                                                                                                             | ✅ (created by them)                           |
| Send Announcements | ✅ (all depts) | ✅ (all depts)                                                                                                                                             | ✅ (own dept)                                  |
| Manage Users       | ✅             | ✅ (dept)                                                                                                                                                  | ❌                                             |
| View Audit Logs    | ✅             | ✅ (dept but only some actions, because of users privacy they can only see their logs for documents, they can only those which have been routed to them  ) | ❌ but can view some of their own acivity logs |

### Cross-Department Permissions

- **Documents**: Anyone can send to anyone in any department
- **Incidents**: Anyone creates → selects department from dropdown → goes to that department head only
- **Announcements**: Staff can only broadcast to own department; Heads can broadcast to any/all departments
- **Visitors**: Visible only to host and their department

---

### Intangible Benefits

- **Improved Accountability**: Read receipts provide proof of document delivery and viewing
- **Enhanced Security**: Better document control and access tracking
- **Staff Satisfaction**: Reduced manual work and clearer communication
- **Environmental Impact**: Significant reduction in paper usage
- **Scalability**: Platform grows with organizational needs



## Final Schema Summary

**Tables you have:**

1. ✅ users
2. ✅ departments
3. ✅ documents (with `status` and `document_category`)
4. ✅ document_routing
5. ✅ read_receipts
6. ✅ visitor_visits (merged from 2 tables)
7. ✅ incidents (no severity)
8. ✅ notifications (handles broadcasts + regular notifications)

---

This platform is positioned to become an essential, easy-to-use tool for modern organizational operations—delivering the digital infrastructure needed for efficient, secure, and accountable departmental management without unnecessary complexity.
