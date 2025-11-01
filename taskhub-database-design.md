# TaskHub - Database Design Documentation

## Table of Contents
1. [Database Overview](#database-overview)
2. [ERD (Entity Relationship Diagram)](#erd-entity-relationship-diagram)
3. [Complete Table Structures](#complete-table-structures)
4. [Relationships Explained](#relationships-explained)
5. [Indexes Strategy](#indexes-strategy)
6. [Migration Files](#migration-files)

---

## 1. Database Overview

**Database Type:** MySQL 8.0+ or PostgreSQL 15+  
**Character Set:** utf8mb4 (for emoji support)  
**Collation:** utf8mb4_unicode_ci  
**Engine:** InnoDB (transactions, foreign keys)

**Total Tables:** 13 main tables

```
Core Tables (7):
├── users
├── organizations
├── projects
├── tasks
├── comments
├── attachments
└── tags

Pivot Tables (3):
├── organization_user
├── project_user
└── task_tag

Support Tables (3):
├── invitations
├── notifications
└── activity_logs
```

---

## 2. ERD (Entity Relationship Diagram)

```
┌──────────────────────────────────────────────────────────────────┐
│                    DATABASE RELATIONSHIPS                         │
└──────────────────────────────────────────────────────────────────┘

                        ┌─────────────┐
                        │    users    │
                        └──────┬──────┘
                               │
              ┌────────────────┼────────────────┐
              │                │                │
              │ (M:M)          │ (1:M)          │ (1:M)
              ↓                ↓                ↓
    ┌──────────────────┐  ┌──────────┐   ┌──────────────┐
    │ organization_user│  │comments  │   │activity_logs │
    └────────┬─────────┘  └──────────┘   └──────────────┘
             │
             │ (M:M)
             ↓
    ┌─────────────────┐
    │ organizations   │
    └────────┬────────┘
             │
             │ (1:M)
             ↓
    ┌─────────────────┐
    │    projects     │
    └────────┬────────┘
             │
    ┌────────┼────────┐
    │ (1:M)  │        │ (M:M)
    ↓        │        ↓
┌────────┐   │   ┌─────────────┐
│ tasks  │   │   │project_user │
└───┬────┘   │   └─────────────┘
    │        │
    │ (M:M)  │ (1:M)
    ↓        ↓
┌─────────┐ ┌────────────┐
│task_tag │ │ comments   │ (Polymorphic)
└─────────┘ │attachments │
    ↑       └────────────┘
    │
    │ (M:M)
    ↓
┌────────┐
│  tags  │
└────────┘

Relationship Key:
- (1:M) = One-to-Many
- (M:M) = Many-to-Many (requires pivot table)
- (Polymorphic) = Can belong to multiple models
```

---

## 3. Complete Table Structures

### Table 1: `users`

**Purpose:** Store user accounts

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    bio TEXT NULL,
    timezone VARCHAR(50) DEFAULT 'UTC',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `name`: User's full name
- `email`: Unique email address
- `email_verified_at`: NULL until user verifies email
- `password`: Bcrypt hashed password
- `avatar`: Path to profile picture
- `bio`: User biography (optional)
- `timezone`: User's timezone for date display
- `remember_token`: For "Remember Me" functionality
- `created_at`: Account creation timestamp
- `updated_at`: Last update timestamp

**Sample Data:**
```
id | name        | email              | email_verified_at   | timezone
1  | John Doe    | john@example.com   | 2024-01-15 10:30:00 | America/New_York
2  | Alice Smith | alice@example.com  | 2024-01-16 09:15:00 | Asia/Dhaka
3  | Bob Johnson | bob@example.com    | 2024-01-17 14:20:00 | Europe/London
```

---

### Table 2: `organizations`

**Purpose:** Store organizations (companies/teams)

```sql
CREATE TABLE organizations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) NULL,
    description TEXT NULL,
    settings JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `name`: Organization name (e.g., "Acme Corp")
- `slug`: URL-friendly identifier (e.g., "acme-corp")
- `logo`: Path to organization logo
- `description`: About the organization
- `settings`: JSON field for custom settings:
  ```json
  {
    "working_hours": "09:00-17:00",
    "timezone": "America/New_York",
    "allow_public_projects": false
  }
  ```
- `created_at`: When organization was created
- `updated_at`: Last update

**Sample Data:**
```
id | name         | slug         | description
1  | Acme Corp    | acme-corp    | Software development company
2  | Tech Startup | tech-startup | Innovative tech solutions
```

---

### Table 3: `organization_user` (Pivot)

**Purpose:** Many-to-Many relationship between users and organizations

```sql
CREATE TABLE organization_user (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role ENUM('owner', 'manager', 'member') NOT NULL DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_org_user (organization_id, user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_organization_id (organization_id),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `organization_id`: Reference to organization
- `user_id`: Reference to user
- `role`: User's role in this organization
  - `owner`: Full control, can delete org
  - `manager`: Can create projects, invite members
  - `member`: Can be added to projects
- `joined_at`: When user joined this organization

**Sample Data:**
```
id | organization_id | user_id | role    | joined_at
1  | 1               | 1       | owner   | 2024-01-15 10:30:00
2  | 1               | 2       | manager | 2024-01-16 09:15:00
3  | 1               | 3       | member  | 2024-01-17 14:20:00
4  | 2               | 2       | owner   | 2024-01-18 11:00:00
```

**Interpretation:**
- John (user 1) is owner of Acme Corp (org 1)
- Alice (user 2) is manager in Acme Corp and owner of Tech Startup (org 2)
- Bob (user 3) is member in Acme Corp

---

### Table 4: `projects`

**Purpose:** Store projects within organizations

```sql
CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    description TEXT NULL,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'archived') 
           NOT NULL DEFAULT 'planning',
    color VARCHAR(7) DEFAULT '#3B82F6',
    start_date DATE NULL,
    end_date DATE NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_created_at (created_at),
    UNIQUE KEY unique_org_slug (organization_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `organization_id`: Which organization owns this project
- `name`: Project name
- `slug`: URL-friendly name
- `description`: Project details
- `status`: Current project status
- `color`: Hex color for visual identification
- `start_date`: Project start date
- `end_date`: Project deadline
- `created_by`: User who created the project
- `created_at`, `updated_at`: Timestamps

**Sample Data:**
```
id | organization_id | name              | status  | start_date | end_date   | created_by
1  | 1               | Website Redesign  | active  | 2024-01-20 | 2024-03-20 | 1
2  | 1               | Mobile App Dev    | planning| 2024-02-01 | 2024-05-01 | 2
3  | 2               | Marketing Campaign| active  | 2024-01-25 | 2024-02-25 | 2
```

---

### Table 5: `project_user` (Pivot)

**Purpose:** Many-to-Many relationship between projects and users

```sql
CREATE TABLE project_user (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role ENUM('manager', 'member') NOT NULL DEFAULT 'member',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_project_user (project_id, user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_project_id (project_id),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `project_id`: Reference to project
- `user_id`: Reference to user
- `role`: User's role in this project
  - `manager`: Can manage project, all tasks
  - `member`: Can create/view tasks
- `added_at`: When user was added to project
- `added_by`: Who added this user (NULL if project creator)

**Sample Data:**
```
id | project_id | user_id | role    | added_at            | added_by
1  | 1          | 1       | manager | 2024-01-20 10:00:00 | NULL
2  | 1          | 2       | member  | 2024-01-20 10:05:00 | 1
3  | 1          | 3       | member  | 2024-01-20 10:06:00 | 1
4  | 2          | 2       | manager | 2024-02-01 09:00:00 | NULL
5  | 2          | 3       | member  | 2024-02-01 09:10:00 | 2
```

**Interpretation:**
- Project 1 (Website Redesign): John is manager, Alice and Bob are members
- Project 2 (Mobile App): Alice is manager, Bob is member

---

### Table 6: `tasks`

**Purpose:** Store tasks within projects

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED NOT NULL,
    parent_task_id BIGINT UNSIGNED NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    status ENUM('to_do', 'in_progress', 'in_review', 'completed', 'on_hold') 
           NOT NULL DEFAULT 'to_do',
    assigned_to BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    due_date DATETIME NULL,
    estimated_hours DECIMAL(5,2) NULL,
    actual_hours DECIMAL(5,2) NULL DEFAULT 0,
    completed_at TIMESTAMP NULL,
    position INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_project_id (project_id),
    INDEX idx_parent_task_id (parent_task_id),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_created_by (created_by),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_due_date (due_date),
    INDEX idx_created_at (created_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_composite_filter (project_id, status, assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `organization_id`: For multi-tenancy filtering
- `project_id`: Which project this task belongs to
- `parent_task_id`: NULL for main tasks, task ID for subtasks
- `title`: Task title/summary
- `description`: Detailed description (supports markdown/HTML)
- `priority`: Task priority level
- `status`: Current task status
- `assigned_to`: User assigned to this task (NULL if unassigned)
- `created_by`: User who created the task
- `due_date`: When task should be completed
- `estimated_hours`: Estimated time to complete
- `actual_hours`: Actual time spent (tracked)
- `completed_at`: Timestamp when marked as completed
- `position`: For custom ordering/sorting
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: For soft deletes

**Sample Data:**
```
id | project_id | title                | priority | status      | assigned_to | due_date            | parent_task_id
1  | 1          | Design homepage      | high     | in_progress | 2           | 2024-02-01 17:00:00 | NULL
2  | 1          | Create logo          | medium   | to_do       | 2           | 2024-02-05 17:00:00 | NULL
3  | 1          | Header section       | medium   | to_do       | 2           | 2024-02-02 17:00:00 | 1
4  | 1          | Footer section       | low      | to_do       | 3           | 2024-02-03 17:00:00 | 1
5  | 2          | Setup repository     | high     | completed   | 2           | 2024-02-01 17:00:00 | NULL
```

**Interpretation:**
- Task 1 is main task assigned to Alice, in progress
- Tasks 3 and 4 are subtasks of task 1 (parent_task_id = 1)

---

### Table 7: `comments` (Polymorphic)

**Purpose:** Comments on tasks, projects, or other entities

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commentable_type VARCHAR(255) NOT NULL,
    commentable_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_commentable (commentable_type, commentable_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `commentable_type`: Model name (e.g., 'App\Models\Task', 'App\Models\Project')
- `commentable_id`: ID of the entity being commented on
- `user_id`: Who wrote the comment
- `content`: Comment text (supports markdown)
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

**Sample Data:**
```
id | commentable_type    | commentable_id | user_id | content
1  | App\Models\Task     | 1              | 2       | Started working on the homepage design
2  | App\Models\Task     | 1              | 1       | @alice Looks great! Can you add the hero section?
3  | App\Models\Project  | 1              | 1       | Project kickoff meeting completed
```

**Interpretation:**
- Comment 1: Alice commented on Task 1
- Comment 2: John commented on Task 1, mentioning Alice
- Comment 3: John commented on Project 1

---

### Table 8: `attachments` (Polymorphic)

**Purpose:** File attachments for tasks, projects, comments

```sql
CREATE TABLE attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attachable_type VARCHAR(255) NOT NULL,
    attachable_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_attachable (attachable_type, attachable_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `attachable_type`: Model name
- `attachable_id`: ID of the entity
- `user_id`: Who uploaded the file
- `file_name`: Original filename
- `file_path`: Storage path (relative or full URL)
- `file_size`: File size in bytes
- `mime_type`: File type (image/jpeg, application/pdf, etc.)
- `created_at`: Upload timestamp

**Sample Data:**
```
id | attachable_type | attachable_id | user_id | file_name         | file_path                    | file_size | mime_type
1  | App\Models\Task | 1             | 2       | mockup-v1.png     | attachments/tasks/1/abc.png  | 524288    | image/png
2  | App\Models\Task | 1             | 2       | requirements.pdf  | attachments/tasks/1/def.pdf  | 1048576   | application/pdf
```

---

### Table 9: `tags`

**Purpose:** Tags for categorizing tasks

```sql
CREATE TABLE tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#6B7280',
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_org_slug (organization_id, slug),
    INDEX idx_organization_id (organization_id),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `organization_id`: Tags are organization-specific
- `name`: Tag name (e.g., "Bug", "Feature")
- `slug`: URL-friendly version
- `color`: Hex color for display
- `description`: What this tag means
- `created_at`, `updated_at`: Timestamps

**Sample Data:**
```
id | organization_id | name      | slug      | color   
1  | 1               | Bug       | bug       | #EF4444
2  | 1               | Feature   | feature   | #10B981
3  | 1               | Design    | design    | #8B5CF6
4  | 1               | Urgent    | urgent    | #F59E0B
```

---

### Table 10: `task_tag` (Pivot)

**Purpose:** Many-to-Many relationship between tasks and tags

```sql
CREATE TABLE task_tag (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_task_tag (task_id, tag_id),
    INDEX idx_task_id (task_id),
    INDEX idx_tag_id (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Sample Data:**
```
id | task_id | tag_id | created_at
1  | 1       | 3      | 2024-01-20 10:00:00
2  | 1       | 4      | 2024-01-20 10:00:00
3  | 2       | 3      | 2024-01-20 10:05:00
```

**Interpretation:**
- Task 1 has tags: Design (3) and Urgent (4)
- Task 2 has tag: Design (3)

---

### Table 11: `notifications`

**Purpose:** Store user notifications

```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data JSON NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_notifiable (notifiable_type, notifiable_id),
    INDEX idx_read_at (read_at),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: UUID primary key
- `type`: Notification class name
- `notifiable_type`: Model type (usually 'App\Models\User')
- `notifiable_id`: User ID
- `data`: JSON payload with notification details:
  ```json
  {
    "message": "You've been assigned to task: Design homepage",
    "task_id": 1,
    "project_name": "Website Redesign",
    "assigned_by": "John Doe"
  }
  ```
- `read_at`: NULL if unread, timestamp if read
- `created_at`: When notification was created

**Sample Data:**
```
id                                  | type                      | notifiable_id | data                                          | read_at
550e8400-e29b-41d4-a716-446655440000| TaskAssignedNotification  | 2             | {"message":"Assigned to...","task_id":1}     | NULL
550e8400-e29b-41d4-a716-446655440001| CommentAddedNotification  | 2             | {"message":"John commented...","task_id":1}  | 2024-01-21 10:00:00
```

---

### Table 12: `activity_logs` (Polymorphic)

**Purpose:** Audit trail for all activities

```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    loggable_type VARCHAR(255) NOT NULL,
    loggable_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_loggable (loggable_type, loggable_id),
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `organization_id`: For filtering
- `loggable_type`: Model being logged
- `loggable_id`: ID of the entity
- `user_id`: Who performed the action (NULL for system)
- `action`: What happened (created, updated, deleted, etc.)
- `description`: Human-readable description
- `old_values`: Previous values (JSON)
- `new_values`: New values (JSON)
- `ip_address`: User's IP
- `user_agent`: Browser info
- `created_at`: When it happened

**Sample Data:**
```json
id | loggable_type    | loggable_id | user_id | action  | description                    | old_values           | new_values
1  | App\Models\Task  | 1           | 2       | updated | Changed status to In Progress  | {"status":"to_do"}   | {"status":"in_progress"}
2  | App\Models\Task  | 1           | 1       | created | Created task                   | null                 | {"title":"Design..."}
```

---

### Table 13: `invitations`

**Purpose:** Track organization invitations

```sql
CREATE TABLE invitations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    email VARCHAR(255) NOT NULL,
    role ENUM('manager', 'member') NOT NULL DEFAULT 'member',
    token VARCHAR(100) NOT NULL UNIQUE,
    invited_by BIGINT UNSIGNED NOT NULL,
    accepted_at TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at),
    INDEX idx_accepted_at (accepted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns Explained:**
- `id`: Primary key
- `organization_id`: Which organization
- `email`: Invitee's email
- `role`: Role they'll have when they join
- `token`: Unique invitation token (32 random chars)
- `invited_by`: Who sent the invitation
- `accepted_at`: NULL until accepted
- `expires_at`: Invitation expiry (usually 7 days)
- `created_at`: When invitation was sent

**Sample Data:**
```
id | organization_id | email             | role   | token        | invited_by | accepted_at | expires_at
1  | 1               | bob@example.com   | member | abc123xyz... | 1          | 2024-01-17  | 2024-01-24
2  | 1               | carol@example.com | manager| def456uvw... | 1          | NULL        | 2024-01-27
```

---

## 4. Relationships Explained

### One-to-Many Relationships

```
1. organizations → projects
   - One organization has many projects
   - One project belongs to one organization

2. projects → tasks
   - One project has many tasks
   - One task belongs to one project

3. users → tasks (as creator)
   - One user creates many tasks
   - One task is created by one user

4. tasks → tasks (self-referencing)
   - One task (parent) has many subtasks
   - One subtask belongs to one parent task

5. users → comments
   - One user writes many comments
   - One comment is written by one user

6. users → attachments
   - One user uploads many attachments
   - One attachment is uploaded by one user

7. organizations → tags
   - One organization has many tags
   - One tag belongs to one organization

8. organizations → activity_logs
   - One organization has many activity logs
   - One activity log belongs to one organization
```

### Many-to-Many Relationships

```
1. organizations ↔ users (through organization_user)
   - One organization has many users
   - One user can belong to many organizations
   - Pivot stores: role, joined_at

2. projects ↔ users (through project_user)
   - One project has many members
   - One user can be in many projects
   - Pivot stores: role, added_at, added_by

3. tasks ↔ tags (through task_tag)
   - One task can have many tags
   - One tag can be on many tasks
   - Pivot stores: created_at
```

### Polymorphic Relationships

```
1. comments (polymorphic)
   - Can comment on: tasks, projects
   - Uses: commentable_type, commentable_id
   
   Example:
   Task → has many comments (commentable_type = 'App\Models\Task')
   Project → has many comments (commentable_type = 'App\Models\Project')

2. attachments (polymorphic)
   - Can attach files to: tasks, projects, comments
   - Uses: attachable_type, attachable_id
   
   Example:
   Task → has many attachments (attachable_type = 'App\Models\Task')
   Comment → has many attachments (attachable_type = 'App\Models\Comment')

3. activity_logs (polymorphic)
   - Can log activity for: tasks, projects, users, organizations
   - Uses: loggable_type, loggable_id
   
   Example:
   Task → has many activity_logs (loggable_type = 'App\Models\Task')
   Project → has many activity_logs (loggable_type = 'App\Models\Project')
```

---

## 5. Indexes Strategy

### Why Indexes Matter
- Speed up queries by 10-100x
- Essential for foreign keys
- Critical for filtering and sorting
- Multi-column indexes for complex queries

### Primary Indexes (Already Defined)

```sql
-- Foreign Key Indexes
All foreign key columns have indexes:
- organization_user.organization_id
- organization_user.user_id
- projects.organization_id
- projects.created_by
- project_user.project_id
- project_user.user_id
- tasks.organization_id
- tasks.project_id
- tasks.assigned_to
- tasks.created_by
- And so on...

-- Unique Indexes
- users.email
- organizations.slug
- organization_user(organization_id, user_id)
- project_user(project_id, user_id)
- task_tag(task_id, tag_id)
- tags(organization_id, slug)
- invitations.token

-- Filtering Indexes
- tasks.status (frequently filtered)
- tasks.priority (frequently filtered)
- tasks.due_date (for sorting by deadline)
- projects.status (filter by project status)
```

### Composite Indexes (Multi-Column)

```sql
-- Most Important Composite Index
CREATE INDEX idx_composite_filter ON tasks 
(project_id, status, assigned_to);

-- Why? Common query pattern:
SELECT * FROM tasks 
WHERE project_id = 1 
  AND status = 'in_progress' 
  AND assigned_to = 2;

-- Other useful composite indexes:
CREATE INDEX idx_task_dates ON tasks (project_id, due_date);
CREATE INDEX idx_notification_status ON notifications (notifiable_id, read_at);
CREATE INDEX idx_activity_date ON activity_logs (organization_id, created_at);
```

### Full-Text Search Indexes (Optional)

```sql
-- For searching task titles and descriptions
ALTER TABLE tasks ADD FULLTEXT INDEX idx_task_search (title, description);

-- Usage:
SELECT * FROM tasks 
WHERE MATCH(title, description) AGAINST('design homepage' IN NATURAL LANGUAGE MODE);
```

### Index Maintenance Tips

```
1. Monitor Slow Queries
   - Use Laravel Telescope or query logs
   - Identify queries taking > 100ms
   - Add indexes for those patterns

2. Avoid Over-Indexing
   - Each index slows down INSERT/UPDATE
   - Don't index rarely-used columns
   - Max 5-7 indexes per table

3. Regular Analysis
   - Run ANALYZE TABLE monthly
   - Check index usage with EXPLAIN
   - Remove unused indexes
```

---

## 6. Migration Files

### Migration Order (Important!)

```
Migrations must be run in this order due to foreign key dependencies:

1. create_users_table
2. create_organizations_table
3. create_organization_user_table
4. create_invitations_table
5. create_projects_table
6. create_project_user_table
7. create_tasks_table
8. create_tags_table
9. create_task_tag_table
10. create_comments_table
11. create_attachments_table
12. create_notifications_table
13. create_activity_logs_table
```

### Sample Migration Files

#### Migration 1: Users Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('timezone', 50)->default('UTC');
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

#### Migration 2: Organizations Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
```

#### Migration 3: Organization_User Pivot Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['owner', 'manager', 'member'])->default('member');
            $table->timestamp('joined_at')->useCurrent();
            
            $table->unique(['organization_id', 'user_id']);
            $table->index('user_id');
            $table->index('organization_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
```

#### Migration 4: Projects Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name', 200);
            $table->string('slug', 200);
            $table->text('description')->nullable();
            $table->enum('status', [
                'planning', 
                'active', 
                'on_hold', 
                'completed', 
                'archived'
            ])->default('planning');
            $table->string('color', 7)->default('#3B82F6');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->unique(['organization_id', 'slug']);
            $table->index('organization_id');
            $table->index('status');
            $table->index('created_by');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
```

#### Migration 5: Project_User Pivot Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['manager', 'member'])->default('member');
            $table->timestamp('added_at')->useCurrent();
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->unique(['project_id', 'user_id']);
            $table->index('user_id');
            $table->index('project_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
```

#### Migration 6: Tasks Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', [
                'to_do', 
                'in_progress', 
                'in_review', 
                'completed', 
                'on_hold'
            ])->default('to_do');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('due_date')->nullable();
            $table->decimal('estimated_hours', 5, 2)->nullable();
            $table->decimal('actual_hours', 5, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('organization_id');
            $table->index('project_id');
            $table->index('parent_task_id');
            $table->index('assigned_to');
            $table->index('created_by');
            $table->index('status');
            $table->index('priority');
            $table->index('due_date');
            $table->index('created_at');
            $table->index('deleted_at');
            
            // Composite index for common query patterns
            $table->index(['project_id', 'status', 'assigned_to'], 'idx_composite_filter');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

#### Migration 7: Tags Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name', 50);
            $table->string('slug', 50);
            $table->string('color', 7)->default('#6B7280');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['organization_id', 'slug']);
            $table->index('organization_id');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
```

#### Migration 8: Task_Tag Pivot Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['task_id', 'tag_id']);
            $table->index('task_id');
            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_tag');
    }
};
```

#### Migration 9: Comments Table (Polymorphic)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('commentable_type');
            $table->unsignedBigInteger('commentable_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['commentable_type', 'commentable_id']);
            $table->index('user_id');
            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
```

#### Migration 10: Attachments Table (Polymorphic)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type');
            $table->unsignedBigInteger('attachable_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path', 500);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['attachable_type', 'attachable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
```

#### Migration 11: Notifications Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
```

#### Migration 12: Activity Logs Table (Polymorphic)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('loggable_type');
            $table->unsignedBigInteger('loggable_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action', 100);
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('organization_id');
            $table->index(['loggable_type', 'loggable_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
```

#### Migration 13: Invitations Table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->enum('role', ['manager', 'member'])->default('member');
            $table->string('token', 100)->unique();
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('organization_id');
            $table->index('email');
            $table->index('token');
            $table->index('expires_at');
            $table->index('accepted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
```

---

## 7. Database Seeders

### Sample Seeder Structure

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            OrganizationSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            TagSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
```

### User Seeder

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'America/New_York',
        ]);

        // Create additional users
        User::create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'Asia/Dhaka',
        ]);

        User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'Europe/London',
        ]);

        // Create 10 more random users
        User::factory(10)->create();
    }
}
```

---

## 8. Query Examples

### Common Query Patterns

#### Get User's Tasks Across All Projects

```php
// Get all tasks assigned to user #2 in organization #1
$tasks = Task::where('organization_id', 1)
    ->where('assigned_to', 2)
    ->with(['project', 'creator', 'tags'])
    ->orderBy('due_date', 'asc')
    ->get();
```

#### Get Project Dashboard Data

```php
// Get project with statistics
$project = Project::with([
    'tasks' => function($query) {
        $query->withCount('comments', 'attachments');
    },
    'members',
    'tasks.assignee'
])
->withCount([
    'tasks',
    'tasks as completed_tasks_count' => function($query) {
        $query->where('status', 'completed');
    },
    'tasks as overdue_tasks_count' => function($query) {
        $query->where('due_date', '<', now())
              ->where('status', '!=', 'completed');
    }
])
->findOrFail(1);
```

#### Search Tasks with Filters

```php
// Complex filter query
$tasks = Task::query()
    ->where('project_id', 1)
    ->when($status, function($query, $status) {
        return $query->where('status', $status);
    })
    ->when($priority, function($query, $priority) {
        return $query->where('priority', $priority);
    })
    ->when($assignee, function($query, $assignee) {
        return $query->where('assigned_to', $assignee);
    })
    ->when($tags, function($query, $tags) {
        return $query->whereHas('tags', function($q) use ($tags) {
            $q->whereIn('tags.id', $tags);
        });
    })
    ->with(['assignee', 'tags', 'project'])
    ->orderBy('priority', 'desc')
    ->orderBy('due_date', 'asc')
    ->paginate(20);
```

#### Get User's Organizations and Projects

```php
// Get all organizations user belongs to with their projects
$user = User::with([
    'organizations' => function($query) {
        $query->withPivot('role');
    },
    'organizations.projects' => function($query) use ($user) {
        // Only projects where user is a member
        $query->whereHas('members', function($q) use ($user) {
            $q->where('users.id', $user->id);
        });
    }
])->findOrFail(1);
```

#### Activity Timeline for a Task

```php
// Get complete activity history
$task = Task::with([
    'activityLogs' => function($query) {
        $query->with('user')->orderBy('created_at', 'desc');
    },
    'comments' => function($query) {
        $query->with('user')->orderBy('created_at', 'asc');
    },
    'attachments' => function($query) {
        $query->with('user')->orderBy('created_at', 'desc');
    }
])->findOrFail(1);
```

---

## 9. Performance Optimization Tips

### 1. Eager Loading (Prevent N+1)

```php
// BAD - N+1 Query Problem
$tasks = Task::all();
foreach ($tasks as $task) {
    echo $task->assignee->name; // Queries for each task!
}

// GOOD - Eager Loading
$tasks = Task::with('assignee')->get();
foreach ($tasks as $task) {
    echo $task->assignee->name; // No additional queries
}
```

### 2. Select Only Needed Columns

```php
// BAD - Fetches all columns
$users = User::all();

// GOOD - Only needed columns
$users = User::select('id', 'name', 'email')->get();
```

### 3. Use Chunking for Large Datasets

```php
// Process 1000 tasks in batches of 100
Task::where('organization_id', 1)
    ->chunk(100, function($tasks) {
        foreach ($tasks as $task) {
            // Process task
        }
    });
```

### 4. Cache Frequently Accessed Data

```php
// Cache organization tags for 1 hour
$tags = Cache::remember("org_{$orgId}_tags", 3600, function() use ($orgId) {
    return Tag::where('organization_id', $orgId)->get();
});
```

### 5. Use Database Transactions

```php
DB::transaction(function () {
    $project = Project::create([...]);
    $project->members()->attach($userId, ['role' => 'manager']);
    ActivityLog::create([...]);
});
```

---

This completes the Database Design Documentation! 