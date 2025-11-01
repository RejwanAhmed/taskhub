# TaskHub - System Flow & Process Documentation

## Table of Contents
1. [User Journey Maps](#user-journey-maps)
2. [Detailed System Flows](#detailed-system-flows)
3. [Permission Matrix](#permission-matrix)
4. [State Transitions](#state-transitions)
5. [Use Case Scenarios](#use-case-scenarios)

---

## 1. User Journey Maps

### Journey 1: First-Time Organization Setup

```
┌─────────────────────────────────────────────────────────────────┐
│                    NEW USER REGISTRATION                         │
└─────────────────────────────────────────────────────────────────┘

Step 1: Landing Page
  ↓
  User clicks "Sign Up"
  ↓
Step 2: Registration Form
  Fields:
  - Full Name
  - Email Address
  - Password (min 8 chars)
  - Confirm Password
  ↓
  Submit Form
  ↓
Step 3: Account Creation
  System Actions:
  ✓ Validates input
  ✓ Checks email uniqueness
  ✓ Hashes password
  ✓ Creates user record (status: unverified)
  ✓ Generates verification token
  ✓ Sends verification email
  ↓
Step 4: Verification Pending Screen
  Message: "Please check your email to verify your account"
  ↓
Step 5: User Checks Email
  ↓
Step 6: Click Verification Link
  ↓
Step 7: Email Verified
  System Actions:
  ✓ Updates user status: verified
  ✓ Auto-login user
  ↓
Step 8: Organization Setup Page
  Fields:
  - Organization Name (required)
  - Organization Slug (auto-generated, editable)
  - Logo (optional)
  ↓
  Submit
  ↓
Step 9: Organization Created
  System Actions:
  ✓ Creates organization record
  ✓ Creates organization_user record (role: owner)
  ✓ Sends welcome email
  ✓ Shows onboarding tutorial (optional)
  ↓
Step 10: Organization Dashboard
  Shows:
  - Empty state with action prompts
  - "Invite team members" button
  - "Create your first project" button
  
  User Journey Complete! ✓
```

---

### Journey 2: Team Member Invitation & Onboarding

```
┌─────────────────────────────────────────────────────────────────┐
│                    INVITING TEAM MEMBERS                         │
└─────────────────────────────────────────────────────────────────┘

From Owner/Manager Perspective:

Step 1: Navigate to Team Section
  ↓
Step 2: Click "Invite Member"
  ↓
Step 3: Invitation Form
  Fields:
  - Email Address
  - Role Selection:
    ○ Manager (can create projects, invite members)
    ○ Member (can be added to projects)
  ↓
  Submit
  ↓
Step 4: Invitation Created
  System Actions:
  ✓ Creates invitation record
    - email
    - role
    - token (unique, 32 chars)
    - expires_at (7 days from now)
    - invited_by
  ✓ Sends invitation email with link
  ↓
Step 5: Invitation Sent
  UI Shows:
  - "Invitation sent to john@example.com"
  - Pending invitations list
  - Option to resend/cancel invitation

---

From Invitee Perspective:

Step 1: Receives Email
  Subject: "You've been invited to join [Org Name] on TaskHub"
  ↓
Step 2: Clicks Invitation Link
  URL: https://taskhub.com/invite/{token}
  ↓
Step 3: System Validates Token
  ↓
  ┌─────────────────────────────────────────┐
  │         Token Valid?                     │
  └─────────────────────────────────────────┘
            ↓                    ↓
          YES                   NO
            ↓                    ↓
    Check Email          Show Error:
    in Database          "Invalid or expired invitation"
            ↓                    ↓
    ┌────────────────┐          END
    │ User Exists?   │
    └────────────────┘
       ↓         ↓
      YES       NO
       ↓         ↓
       
  [YES Branch - User Exists]
  Step 4a: Show Login Form
    ↓
  Step 5a: User Logs In
    ↓
  Step 6a: System Links User to Organization
    ✓ Creates organization_user record
    ✓ Assigns role from invitation
    ✓ Marks invitation as accepted
    ✓ Sends notification to inviter
    ↓
  Step 7a: User Now Has Access to Organization
    - Can switch between organizations (if in multiple)
    - Sees new organization in dropdown
    ↓
  END
  
  [NO Branch - New User]
  Step 4b: Show Registration Form
    Email: pre-filled (from invitation)
    Name: empty
    Password: empty
    ↓
  Step 5b: User Completes Registration
    ↓
  Step 6b: System Creates Account & Links to Org
    ✓ Creates user account
    ✓ Creates organization_user record
    ✓ Assigns role from invitation
    ✓ Marks invitation as accepted
    ✓ Auto-verifies email (since invited)
    ✓ Sends notification to inviter
    ↓
  Step 7b: Welcome to Organization
    Shows onboarding tour
    ↓
  END
```

---

### Journey 3: Project Creation & Task Management

```
┌─────────────────────────────────────────────────────────────────┐
│                    PROJECT LIFECYCLE                             │
└─────────────────────────────────────────────────────────────────┘

Phase 1: PROJECT CREATION

Step 1: Owner/Manager → "Projects" Section
  ↓
Step 2: Click "New Project"
  ↓
Step 3: Project Creation Form
  Fields:
  - Project Name* (required)
  - Description (rich text editor)
  - Start Date
  - End Date
  - Status (dropdown):
    • Planning
    • Active
    • On Hold
    • Completed
    • Archived
  - Color (for visual identification)
  - Project Manager (optional, select from org members)
  ↓
Step 4: Submit
  ↓
Step 5: System Creates Project
  ✓ Creates project record
  ✓ Links to organization
  ✓ Adds creator to project_user (role: manager)
  ✓ If project manager assigned, adds them too
  ✓ Logs activity: "Project created"
  ✓ Sends notification to project manager
  ↓
Step 6: Redirect to Project Dashboard
  Shows:
  - Empty task board
  - "Add members" prompt
  - "Create first task" prompt

---

Phase 2: ADDING PROJECT MEMBERS

Step 1: Project Manager → Project Settings → Members Tab
  ↓
Step 2: See Organization Members List
  Shows all org members with checkboxes
  ↓
Step 3: Select Members to Add
  Example:
  ☑ Alice (Designer) → Role: Member
  ☑ Bob (Developer) → Role: Member
  ☑ Charlie (QA) → Role: Manager
  ☐ Diana (not selected)
  ↓
Step 4: Assign Roles
  For each selected member:
  - Member (can view/create tasks)
  - Manager (can manage project)
  ↓
Step 5: Save
  ↓
Step 6: System Updates
  ✓ Creates project_user records for each
  ✓ Sends notification: "You've been added to [Project Name]"
  ✓ Logs activity
  ↓
Step 7: Members Now See Project
  - Appears in their project list
  - Can access project dashboard
  - Can view all tasks

---

Phase 3: TASK CREATION & ASSIGNMENT

Step 1: Any Project Member → Click "New Task"
  ↓
Step 2: Task Creation Form
  Required Fields:
  - Title*
  
  Optional Fields:
  - Description (rich text: bold, italic, lists, links, code)
  - Priority:
    • Low (default)
    • Medium
    • High
    • Urgent
  - Status:
    • To Do (default)
    • In Progress
    • In Review
    • Completed
  - Assign To (dropdown of project members)
  - Due Date & Time
  - Estimated Hours
  - Tags (multi-select)
  - Parent Task (for creating subtask)
  ↓
Step 3: Submit
  ↓
Step 4: System Creates Task
  ✓ Creates task record
  ✓ Links to project and organization
  ✓ Creates activity log: "[Creator] created this task"
  ✓ If assigned, sends notification to assignee
  ✓ If due date set, schedules reminder notification
  ↓
Step 5: Task Appears in Project Board
  - Listed in appropriate status column
  - Visible to all project members

---

Phase 4: WORKING ON A TASK

Step 1: Assignee Receives Notification
  "You've been assigned to: [Task Title]"
  ↓
Step 2: Click Notification → Opens Task Detail Page
  ↓
Step 3: Task Detail View Shows:
  - Task information
  - Description
  - Priority, Status, Due date
  - Comments section
  - Attachments section
  - Activity timeline
  - Action buttons (Edit, Delete, Change Status)
  ↓
Step 4: Assignee Updates Status
  Changes: "To Do" → "In Progress"
  ↓
Step 5: System Updates
  ✓ Updates task status
  ✓ Logs activity: "[User] changed status to In Progress"
  ✓ Sends notification to:
    - Task creator
    - Project managers
  ✓ Updates task timestamp
  ↓
Step 6: Assignee Adds Comment
  "Started working on the design mockups"
  ↓
Step 7: System Processes Comment
  ✓ Creates comment record (polymorphic)
  ✓ Logs activity
  ✓ Sends notification to:
    - Task creator
    - Anyone @mentioned
  ↓
Step 8: Assignee Uploads Attachment
  Uploads: mockup-v1.png
  ↓
Step 9: System Handles File
  ✓ Validates file (type, size)
  ✓ Generates unique filename
  ✓ Stores file (local/S3)
  ✓ Creates thumbnail (if image)
  ✓ Creates attachment record (polymorphic)
  ✓ Logs activity
  ✓ Notifies task creator
  ↓
Step 10: Work Continues...
  - More comments
  - More file uploads
  - Status updates
  ↓
Step 11: Task Completion
  Assignee changes status: "In Progress" → "In Review"
  ↓
Step 12: Review Process
  Project Manager reviews work
  ↓
  ┌────────────────────┐
  │   Approved?        │
  └────────────────────┘
     ↓           ↓
    YES         NO
     ↓           ↓
  Status:     Status:
  Completed   Back to
              In Progress
     ↓           ↓
  Task Done   Needs Work
     ✓           ✓
```

---

## 2. Detailed System Flows

### Flow A: Authentication & Authorization

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER LOGIN FLOW                               │
└─────────────────────────────────────────────────────────────────┘

Step 1: User → Login Page
  ↓
Step 2: Enter Credentials
  - Email
  - Password
  - [✓] Remember Me (optional)
  ↓
Step 3: Submit
  ↓
Step 4: Backend Validation
  ↓
  Check 1: Email exists?
    NO → Return error: "Invalid credentials"
    YES → Continue
  ↓
  Check 2: Password correct?
    NO → Return error: "Invalid credentials"
    YES → Continue
  ↓
  Check 3: Email verified?
    NO → Return error: "Please verify your email first"
         Offer: "Resend verification email"
    YES → Continue
  ↓
  Check 4: Account active?
    NO → Return error: "Account suspended"
    YES → Continue
  ↓
Step 5: Create Session
  ✓ Generate session token
  ✓ Store in database (if remember me)
  ✓ Set cookie
  ✓ Log login activity
  ↓
Step 6: Load User Data
  ✓ Get user info
  ✓ Get organizations user belongs to
  ✓ Get user permissions
  ↓
Step 7: Determine Landing Page
  ↓
  Has multiple organizations?
    YES → Show organization selector
    NO → Go to organization dashboard
  ↓
Step 8: User Logged In Successfully ✓

---

┌─────────────────────────────────────────────────────────────────┐
│                    AUTHORIZATION CHECKS                          │
└─────────────────────────────────────────────────────────────────┘

Every Request Goes Through:

1. Authentication Check
   Is user logged in?
   NO → Redirect to login
   YES → Continue

2. Organization Check
   Does user belong to this organization?
   NO → 403 Forbidden
   YES → Continue

3. Resource Check (Project, Task, etc.)
   Does resource belong to user's organization?
   NO → 404 Not Found (don't reveal existence)
   YES → Continue

4. Permission Check
   Does user have permission for this action?
   NO → 403 Forbidden
   YES → Allow action

Example for "Update Task":
  ┌──────────────────────────────────────┐
  │ User wants to update Task #123       │
  └──────────────────────────────────────┘
           ↓
  Check 1: Is user authenticated?
           ↓ YES
  Check 2: Does Task #123 exist?
           ↓ YES
  Check 3: Task belongs to user's organization?
           ↓ YES
  Check 4: Is user member of task's project?
           ↓ YES
  Check 5: Permission to update?
           - If assignee: YES
           - If project manager: YES
           - If just member: NO (can only comment)
           ↓
  Action: ALLOWED / DENIED
```

---

### Flow B: Multi-Project Assignment

```
┌─────────────────────────────────────────────────────────────────┐
│          USER IN MULTIPLE PROJECTS SCENARIO                      │
└─────────────────────────────────────────────────────────────────┘

Organization: Acme Corp (ID: 1)

User: Bob (ID: 5)
Organization Role: Manager

Projects Bob is Part Of:

┌────────────────────────────────────────────────────────┐
│ Project A: "Website Redesign" (ID: 10)                │
│ Bob's Role: Member                                     │
│ Bob's Tasks:                                           │
│   - Task #1: "Design homepage" (Assigned, In Progress) │
│   - Task #5: "Create logo" (Assigned, To Do)          │
│   - Task #8: "Review mockups" (Assigned, To Do)       │
└────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────┐
│ Project B: "Mobile App Dev" (ID: 11)                  │
│ Bob's Role: Manager                                    │
│ Bob's Tasks:                                           │
│   - Task #12: "Setup project" (Created by Bob, Done)  │
│   - Task #15: "API integration" (Assigned, In Progress)│
└────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────┐
│ Project C: "Marketing Campaign" (ID: 12)              │
│ Bob's Role: Member                                     │
│ Bob's Tasks:                                           │
│   - Task #20: "Write blog post" (Assigned, To Do)     │
│   - Task #22: "Social media" (Watching, not assigned) │
└────────────────────────────────────────────────────────┘

Bob's Dashboard View:

┌─────────────────────────────────────────────────────────┐
│ MY TASKS (8 total)                                      │
├─────────────────────────────────────────────────────────┤
│ Today (Due today)                                       │
│   ☐ Design homepage (Project A) - High Priority        │
│   ☐ API integration (Project B) - Medium Priority      │
├─────────────────────────────────────────────────────────┤
│ This Week (Due in 7 days)                              │
│   ☐ Create logo (Project A)                            │
│   ☐ Write blog post (Project C)                        │
├─────────────────────────────────────────────────────────┤
│ Later                                                   │
│   ☐ Review mockups (Project A)                         │
├─────────────────────────────────────────────────────────┤
│ Completed                                               │
│   ✓ Setup project (Project B)                          │
└─────────────────────────────────────────────────────────┘

What Bob Can Do:

In Project A (as Member):
  ✓ View all tasks
  ✓ Create new tasks
  ✓ Update assigned tasks
  ✓ Comment on any task
  ✓ Upload files to tasks
  ✗ Cannot manage project settings
  ✗ Cannot add/remove members
  ✗ Cannot delete project

In Project B (as Manager):
  ✓ Everything a Member can do, PLUS:
  ✓ Manage project settings
  ✓ Add/remove project members
  ✓ Delete tasks
  ✓ Reassign any task
  ✓ Delete project
  ✓ Change project status

In Project C (as Member):
  ✓ Same as Project A
```

---

### Flow C: Task Status Workflow

```
┌─────────────────────────────────────────────────────────────────┐
│                    TASK STATUS LIFECYCLE                         │
└─────────────────────────────────────────────────────────────────┘

                    ┌──────────────┐
                    │   CREATED    │
                    │   (To Do)    │
                    └──────────────┘
                           ↓
                  Assignee starts work
                           ↓
                    ┌──────────────┐
                    │ IN PROGRESS  │
                    └──────────────┘
                      ↓         ↓
            Work continues    Blocked?
                      ↓           ↓
                    Continue   ┌──────────┐
                      ↓        │ ON HOLD  │
                    Work       └──────────┘
                    Done          ↓
                      ↓         Unblocked
                      ↓           ↓
                      └───────────┘
                           ↓
                  Submit for review
                           ↓
                    ┌──────────────┐
                    │  IN REVIEW   │
                    └──────────────┘
                           ↓
                    ┌─────────────┐
                    │  Reviewed?  │
                    └─────────────┘
                      ↓         ↓
                 APPROVED    REJECTED
                      ↓         ↓
              ┌──────────┐      ↓
              │COMPLETED │  Back to
              └──────────┘  IN PROGRESS
                    ↓
              Task Closed ✓

Status Change Rules:

To Do → In Progress
  Who: Assignee or Project Manager
  Action: Start working

In Progress → On Hold
  Who: Assignee or Project Manager
  Reason: Blocked, waiting for dependency
  Requires: Comment explaining why

On Hold → In Progress
  Who: Assignee or Project Manager
  Action: Blocker resolved

In Progress → In Review
  Who: Assignee
  Action: Work completed, needs review
  Requires: Comment with summary

In Review → Completed
  Who: Project Manager or Task Creator
  Action: Work approved

In Review → In Progress
  Who: Project Manager or Task Creator
  Action: Changes requested
  Requires: Comment with feedback

Any Status → Completed (Emergency)
  Who: Project Manager only
  Action: Mark as done without review

Completed → In Progress (Reopening)
  Who: Project Manager only
  Action: Task needs more work
  Requires: Comment explaining why
```

---

## 3. Permission Matrix

```
┌─────────────────────────────────────────────────────────────────┐
│                    ORGANIZATION LEVEL                            │
└─────────────────────────────────────────────────────────────────┘

Action                          | Owner | Manager | Member
─────────────────────────────────────────────────────────────────
View organization dashboard     |   ✓   |    ✓    |   ✓
Update organization settings    |   ✓   |    ✗    |   ✗
Upload organization logo        |   ✓   |    ✗    |   ✗
Delete organization             |   ✓   |    ✗    |   ✗
Transfer ownership              |   ✓   |    ✗    |   ✗
Invite members                  |   ✓   |    ✓    |   ✗
Remove members                  |   ✓   |    ✓    |   ✗
View all projects               |   ✓   |    ✓    |   ✗*
Create projects                 |   ✓   |    ✓    |   ✗
View organization activity      |   ✓   |    ✓    |   ✗
View organization analytics     |   ✓   |    ✓    |   ✗

* Members can only see projects they're added to

┌─────────────────────────────────────────────────────────────────┐
│                    PROJECT LEVEL                                 │
└─────────────────────────────────────────────────────────────────┘

Action                          | Org Owner | Proj Manager | Proj Member
──────────────────────────────────────────────────────────────────────
View project                    |     ✓     |      ✓       |     ✓
Update project details          |     ✓     |      ✓       |     ✗
Delete project                  |     ✓     |      ✓       |     ✗
Archive project                 |     ✓     |      ✓       |     ✗
Add project members             |     ✓     |      ✓       |     ✗
Remove project members          |     ✓     |      ✓       |     ✗
Change member roles             |     ✓     |      ✓       |     ✗
View all project tasks          |     ✓     |      ✓       |     ✓
Create tasks                    |     ✓     |      ✓       |     ✓
View project analytics          |     ✓     |      ✓       |     ✓
Export project data             |     ✓     |      ✓       |     ✗

┌─────────────────────────────────────────────────────────────────┐
│                    TASK LEVEL                                    │
└─────────────────────────────────────────────────────────────────┘

Action                    | Proj Manager | Assignee | Other Member
───────────────────────────────────────────────────────────────────
View task                 |      ✓       |    ✓     |      ✓
Create task               |      ✓       |    ✓     |      ✓
Update task details       |      ✓       |    ✓     |      ✗
Delete task               |      ✓       |    ✗     |      ✗
Change status             |      ✓       |    ✓     |      ✗
Reassign task             |      ✓       |    ✗     |      ✗
Update priority           |      ✓       |    ✓     |      ✗
Set/change due date       |      ✓       |    ✓     |      ✗
Add comments              |      ✓       |    ✓     |      ✓
Edit own comments         |      ✓       |    ✓     |      ✓
Delete any comments       |      ✓       |    ✗     |      ✗
Upload attachments        |      ✓       |    ✓     |      ✓
Delete attachments        |      ✓       | ✓ (own)  |  ✓ (own)
Add/remove tags           |      ✓       |    ✓     |      ✗
Create subtasks           |      ✓       |    ✓     |      ✓
Watch/unwatch task        |      ✓       |    ✓     |      ✓

┌─────────────────────────────────────────────────────────────────┐
│                    COMMENT LEVEL                                 │
└─────────────────────────────────────────────────────────────────┘

Action                    | Comment Owner | Proj Manager | Others
───────────────────────────────────────────────────────────────────
View comment              |      ✓        |      ✓       |   ✓
Edit comment              |   ✓ (15min)   |      ✗       |   ✗
Delete comment            |      ✓        |      ✓       |   ✗
React to comment          |      ✓        |      ✓       |   ✓
Reply to comment          |      ✓        |      ✓       |   ✓
```

---

## 4. State Transitions

### User Account States

```
[Pending] → Email Verification → [Unverified]
                                      ↓
                              Verify Email
                                      ↓
                                  [Active]
                                      ↓
                          ┌───────────┴───────────┐
                          ↓                       ↓
                    Deactivated            Suspended by Admin
                          ↓                       ↓
                    [Inactive]              [Suspended]
                          ↓                       ↓
                    Reactivate            Unsuspend by Admin
                          ↓                       ↓
                          └───────────┬───────────┘
                                      ↓
                                  [Active]
```

### Project States

```
[Created] → Status: Planning
                ↓
        Start Project
                ↓
        Status: Active
                ↓
        ┌───────┴────────┐
        ↓                ↓
    Pause          Complete All Tasks
        ↓                ↓
  Status: On Hold   Status: Completed
        ↓                ↓
    Resume         Archive/Close
        ↓                ↓
  Status: Active   Status: Archived
```

### Task States (Detailed)

```
                    [Created]
                        ↓
                Status: To Do
                        ↓
              ┌─────────┴─────────┐
              ↓                   ↓
        Assignee Starts     Delete Task
              ↓                   ↓
      Status: In Progress    [Deleted]
              ↓                (Soft Delete)
      ┌───────┴────────┐
      ↓                ↓
   Blocked        Work Complete
      ↓                ↓
Status: On Hold   Status: In Review
      ↓                ↓
   Resolved        ┌───┴───┐
      ↓            ↓       ↓
Back to In Progress  Approved  Rejected
                     ↓       ↓
             Status: Completed  Back to In Progress
                     ↓
               [Closed Task]
                     ↓
               Reopen? (by Manager)
                     ↓
             Status: In Progress
```

### Invitation States

```
[Created] → Status: Pending
                ↓
        Send Email
                ↓
        Status: Sent
                ↓
        ┌───────┴────────┐
        ↓                ↓
   User Clicks      Expires (7 days)
        ↓                ↓
  Status: Clicked   Status: Expired
        ↓                ↓
User Registers      [Can Resend]
        ↓
Status: Accepted
        ↓
    [Complete]
```

---

## 5. Use Case Scenarios

### Scenario 1: Daily Standup Meeting

```
Context:
- Team: 5 members
- Project: "Mobile App Development"
- Time: 10:00 AM Daily

Flow:
1. Project Manager opens project dashboard
2. Filters tasks by: Status = "In Progress" + "In Review"
3. Sees list of active tasks with assignees
4. Each team member updates their task status
5. If blocked, changes status to "On Hold" with comment
6. Manager reassigns tasks if needed
7. Team sees updated board in real-time
```

### Scenario 2: Sprint Planning

```
Context:
- Sprint Duration: 2 weeks
- Team Size: 8 members
- New Project: "E-commerce Platform"

Flow:
1. Manager creates project "E-commerce - Sprint 1"
2. Sets start date and end date (2 weeks)
3. Adds all team members
4. Bulk creates tasks from requirements:
   - "Setup repository"
   - "Design database schema"
   - "Create user authentication"
   - "Build product catalog"
   - (20 more tasks...)
5. Assigns priorities to each task
6. Estimates hours for each task
7. Tags tasks by feature: "auth", "catalog", "checkout"
8. Assigns tasks to team members
9. Sets due dates strategically
10. Team members receive notifications
11. Sprint starts!
```

### Scenario 3: Client Review Process

```
Context:
- Deliverable: Website homepage design
- Need: Client approval before development

Flow:
1. Designer completes mockup
2. Designer updates task: Status = "In Review"
3. Uploads mockup.png to task attachments
4. Adds comment: "@manager Ready for client review"
5. Manager receives notification
6. Manager reviews mockup internally
7. Manager shares mockup with client (external)
8. Client provides feedback via email
9. Manager adds comment: "Client requested changes: [details]"
10. Changes task status back to "In Progress"
11. Reassigns to designer with feedback
12. Designer makes changes
13. Repeat until approved
14. Manager marks as "Completed"
```

### Scenario 4: Urgent Bug Fix

```
Context:
- Production bug reported
- Needs immediate attention
- Multiple teams involved

Flow:
1. Support team member creates task:
   - Title: "🚨 URGENT: Payment gateway not working"
   - Priority: Urgent
   - Due Date: Today (ASAP)
   - Assigns to: Lead Developer
   - Tags: "bug", "production", "critical"
2. Lead developer receives immediate notification
3. Developer changes status to "In Progress"
4. Creates subtask: "Investigate issue"
5. Creates subtask: "Deploy hotfix"
6. Finds root cause, adds comment with details
7. @mentions DevOps team for deployment
8. DevOps receives notification
9. Multiple team members collaborate in comments
10. Issue resolved in 2 hours
11. Developer marks main task as "Completed"
12. Adds final comment with resolution summary
13. Activity log shows complete timeline
14. Manager sees resolution time in analytics
```

### Scenario 5: New Employee Onboarding

```
Context:
- New Developer: Sarah
- First Day: Monday
- Needs: Access, training, first tasks

Flow:
Day 1 - Morning:
1. HR Manager invites Sarah to organization
2. Sarah receives invitation email
3. Sarah clicks link, registers account
4. Sarah verifies email
5. Sarah joins organization as "Member"
6. HR adds Sarah to "Onboarding" project
7. Sarah sees welcome tasks:
   - ☐ "Complete profile setup"
   - ☐ "Read documentation"
   - ☐ "Setup development environment"
   - ☐ "Meet with mentor"

Day 1 - Afternoon:
8. Team Lead adds Sarah to "Product Team" project
9. Sarah receives notification
10. Sarah sees her first real task:
    - "Fix minor UI bug (good first issue)"
    - Priority: Low
    - Has detailed description
    - Tagged: "good-first-issue", "frontend"
11. Sarah completes tasks throughout the week
12. Mentor reviews her work via task comments
13. Sarah gets familiar with system
```

### Scenario 6: Multi-Project Coordination

```
Context:
- Designer working on 3 projects simultaneously
- Needs to manage time across projects
- Different priorities and deadlines

User: Alex (Designer)

Morning Routine:
1. Alex logs in at 9:00 AM
2. Dashboard shows "My Tasks" from all projects:
   
   TODAY (3 tasks):
   - Project A: "Design banner" (High) - Due 5 PM
   - Project B: "Create icons" (Medium) - Due today
   - Project C: "Review mockup" (Low) - Due today
   
   THIS WEEK (5 tasks):
   - Various tasks from different projects
   
3. Alex prioritizes based on:
   - Due dates
   - Priority levels
   - Project importance
   
4. Alex starts with highest priority task
5. Updates status to "In Progress"
6. Works for 2 hours
7. Uploads design to task
8. Marks as "In Review"
9. Moves to next task from different project

Throughout Day:
- Receives notifications from multiple projects
- Comments on tasks from different teams
- Switches between projects seamlessly
- All activity tracked automatically
- Managers from each project see Alex's progress

End of Day:
- Alex checks dashboard
- 2 tasks completed
- 1 task still in progress
- Dashboard shows tomorrow's priorities
- Alex logs off, confident nothing is missed
```

### Scenario 7: Project Deadline Crisis

```
Context:
- Project deadline: Friday (3 days away)
- 15 tasks still "To Do"
- Need to prioritize and redistribute

Project Manager Actions:
1. Opens project dashboard
2. Sees concerning statistics:
   - 15 tasks To Do
   - 8 tasks In Progress
   - 3 days remaining
3. Analyzes team workload:
   - Alice: 8 tasks assigned (overloaded)
   - Bob: 2 tasks assigned (available)
   - Charlie: 5 tasks assigned (balanced)
4. Manager takes action:
   - Filters tasks by priority
   - Identifies "must-have" vs "nice-to-have"
   - Moves 5 low-priority tasks to next sprint
   - Reassigns 3 tasks from Alice to Bob
   - Updates due dates on critical tasks
   - Adds comments explaining urgency
5. Team receives notifications
6. Daily standups to track progress
7. Manager monitors dashboard multiple times/day
8. Friday arrives:
   - All critical tasks completed
   - 2 tasks moved to next sprint
   - Project delivered on time
9. Manager generates report for stakeholders
```

---

## 6. Edge Cases & Exception Handling

### Edge Case 1: User Deleted from Organization

```
Scenario: User is removed from organization mid-session

What Happens:
1. Admin removes user "John" from organization
2. System triggers:
   ✓ Removes John from organization_user
   ✓ Removes John from all project_user
   ✓ Unassigns John from all tasks
   ✓ Reassigns John's tasks to project managers
   ✓ Keeps John's comments (for history)
   ✓ Keeps John's activity logs (for audit)
   ✓ Sends notification to John
3. John is currently browsing a task
4. John tries to save comment
5. System detects: User no longer in organization
6. Response: "You no longer have access to this organization"
7. John redirected to homepage
8. John's session cleared
```

### Edge Case 2: Project Deleted with Active Tasks

```
Scenario: Manager deletes project with 50 active tasks

Protection:
1. Manager clicks "Delete Project"
2. System shows confirmation dialog:
   "This project has 50 active tasks. Deleting cannot be undone.
    Type project name to confirm: Website Redesign"
3. Manager types project name
4. Manager confirms
5. System performs:
   ✓ Soft deletes project (deleted_at timestamp)
   ✓ Soft deletes all tasks
   ✓ Keeps all data for 30 days (recovery period)
   ✓ Removes from all member's views
   ✓ Logs deletion activity
   ✓ Sends notification to all project members
6. If needed, can be restored within 30 days
7. After 30 days, hard delete via scheduled job
```

### Edge Case 3: Concurrent Task Updates

```
Scenario: Two users update same task simultaneously

Timeline:
T+0s: Alice opens Task #123 (Status: To Do)
T+0s: Bob opens Task #123 (Status: To Do)
T+10s: Alice changes status to "In Progress"
T+12s: Bob changes status to "In Review"

Resolution:
1. Alice's update arrives first:
   - Task status: To Do → In Progress
   - Activity logged
   - Timestamp: 10:30:10
2. Bob's update arrives 2 seconds later:
   - System detects: Task updated since Bob loaded it
   - System applies: Last-write-wins
   - Task status: In Progress → In Review
   - Activity logged: Both changes
   - Timestamp: 10:30:12
3. Alice receives real-time notification:
   "Bob changed status to In Review"
4. Alice sees updated status
5. Both activities preserved in history
```

### Edge Case 4: File Upload Failure

```
Scenario: User uploads 50MB file (limit: 10MB)

Flow:
1. User selects file: presentation.pptx (50MB)
2. User clicks "Upload"
3. Frontend validates:
   - Size: 50MB > 10MB ❌
4. Shows error immediately:
   "File size exceeds limit of 10MB"
5. Upload cancelled before sending to server
6. User sees helpful message:
   "Please compress the file or upload to external storage"

Alternative: Network fails during upload
1. User selects file: mockup.png (5MB)
2. Upload starts
3. At 60% - network disconnects
4. System detects failure
5. Shows retry option
6. User clicks "Retry"
7. Upload resumes from 60%
8. Upload completes successfully
```

### Edge Case 5: Email Already Exists

```
Scenario: Invitation sent to existing user

Flow:
1. Manager invites: john@example.com
2. System checks: Email exists in users table
3. System checks: John already in organization?
   
   Case A: John IS in organization
   - Show error: "This user is already a member"
   - Suggest: "Change their role in Team Settings"
   
   Case B: John NOT in organization
   - Create invitation
   - Send email: "You've been invited to join [Org]"
   - John clicks link
   - System: "Already have account? Login to accept"
   - John logs in
   - System adds John to organization
   - John now sees both organizations
```

---

## 7. Notification Rules Summary

```
┌─────────────────────────────────────────────────────────────────┐
│                    NOTIFICATION TRIGGERS                         │
└─────────────────────────────────────────────────────────────────┘

Event                           | Notify Who              | Channels
────────────────────────────────────────────────────────────────────
Task assigned to user           | Assignee                | In-app + Email
Task due in 24 hours            | Assignee                | In-app + Email
Task overdue                    | Assignee + Manager      | In-app + Email
Task status changed             | Creator + Watchers      | In-app
Comment added                   | Creator + Assignee +    | In-app + Email
                                | @Mentioned users        |
@Mentioned in comment           | Mentioned user          | In-app + Email
File uploaded to task           | Creator + Assignee      | In-app
Task completed                  | Creator + Manager       | In-app
Added to project                | New member              | In-app + Email
Removed from project            | Removed member          | Email
Organization invitation         | Invitee                 | Email
Task reassigned                 | Old assignee +          | In-app + Email
                                | New assignee            |
Project deadline approaching    | All project members     | In-app + Email
(7 days before)                 |                         |
Daily digest (8 AM)             | Users with tasks due    | Email
                                | today                   |
Weekly summary (Monday 8 AM)    | All active users        | Email

Notification Preferences:
- Users can disable each notification type
- Users can set "Do Not Disturb" hours
- Users can choose email frequency:
  • Instant (immediate email)
  • Daily digest (once per day)
  • Weekly digest (once per week)
  • Never (in-app only)
```

---

## 8. Data Validation Rules

```
┌─────────────────────────────────────────────────────────────────┐
│                    VALIDATION RULES                              │
└─────────────────────────────────────────────────────────────────┘

USERS:
- Name: Required, 2-100 characters
- Email: Required, valid email format, unique in system
- Password: Required, min 8 chars, 1 uppercase, 1 number
- Avatar: Optional, image only, max 2MB

ORGANIZATIONS:
- Name: Required, 2-100 characters
- Slug: Required, unique, lowercase, alphanumeric + hyphens
- Logo: Optional, image only, max 2MB

PROJECTS:
- Name: Required, 2-200 characters
- Description: Optional, max 5000 characters
- Start Date: Optional, cannot be in past
- End Date: Optional, must be after start date
- Status: Required, must be valid enum value
- Organization: Required, must exist

TASKS:
- Title: Required, 2-500 characters
- Description: Optional, max 10,000 characters
- Priority: Required, must be valid enum
- Status: Required, must be valid enum
- Due Date: Optional, cannot be before today (warning only)
- Estimated Hours: Optional, 0.5-999.9 hours
- Project: Required, user must have access
- Assignee: Optional, must be project member
- Parent Task: Optional, must be in same project, no circular refs

COMMENTS:
- Content: Required, 1-5000 characters
- Commentable: Required, must exist and be accessible

ATTACHMENTS:
- File: Required
- Types Allowed: images (jpg, png, gif), documents (pdf, doc, docx),
                spreadsheets (xls, xlsx), archives (zip)
- Max Size: 10MB per file
- Virus Scan: Required (in production)

TAGS:
- Name: Required, 2-50 characters, unique per organization
- Color: Required, valid hex color code
```

---

This completes the System Flow & Process Documentation. Now let me create the **Database Design Documentation** with complete table structures!