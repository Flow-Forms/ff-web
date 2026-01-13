---
title: Flow Steps
order: 1
---

# Flow Steps

Flow steps are the basic building blocks of approval workflows. They define who needs to approve a submission and how approvals are collected.

## Flow Types

### One (`one`)

Single-person approval where only one member from the assigned group needs to approve.

**Behavior:**
- Creates a notification for the first available member of the group
- Submission advances when that single person approves
- The `is_ignored` option is automatically disabled for this type

**Use Case:** When you need approval from any one person in a department (e.g., "any manager can approve this expense").

### Multiple (`multiple`)

Multiple specific people from a group must approve.

**Behavior:**
- Creates notifications for designated members
- Can be configured to require unanimous approval or just a subset
- Supports the `is_ignored` option to skip this step under certain conditions

**Use Case:** When specific individuals need to review (e.g., "both the department head and finance lead must approve").

### All (`all`)

Every member of the assigned group must approve.

**Behavior:**
- Creates notifications for all members of the group
- Submission only advances when every member has approved
- Supports the `is_ignored` option to skip this step

**Use Case:** When consensus from an entire team is required (e.g., "all board members must approve this resolution").

### Group Select (`group_select`)

Allows the submitter to choose which group should handle their submission.

**Behavior:**
- Presents the user with a selection of pre-configured groups
- The selected group then receives the submission for approval
- Does not use a single `group_id` - instead uses a many-to-many relationship with selectable groups
- Can optionally auto-share with the selected group via `autoshare_selected_group` property

**Use Case:** When submissions can be routed to different departments based on user choice (e.g., "select your regional office for processing").

## Configuration Options

| Option | Type | Description |
|--------|------|-------------|
| `name` | string | Optional custom display name for the step |
| `group_id` | integer | Required group (except for `group_select`) |
| `unanimous` | boolean | If true, all assigned members must approve before advancing |
| `sms` | boolean | Enable SMS notifications for this step |
| `is_muted` | boolean | Don't send notifications (manual follow-up) |
| `is_ignored` | boolean | Skip approval (only for `multiple`, `all`) |
| `can_edit_form` | boolean | Allow editing the form during this step |
| `is_escalatable` | boolean | Allow escalation to another approver |
| `autoshare_group_id` | integer | Automatically share with another group when this step completes |

## Step Sequencing

Flow steps are ordered by their `sort` column. When a step completes:

1. The system finds the next step in sequence for the same form
2. The next step must have group members (unless it's a special type)

**Step Type Constraints:**
- `one` type steps can precede any other step type
- `multiple` type steps can precede any other step type
- `group_select` type steps can precede any other step type
- `conditional` and `collective` steps can only be followed by `all`, `dynamic`, `conditional`, or `collective` types
