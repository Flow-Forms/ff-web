---
title: Triggers
order: 5
---

# Triggers

Triggers are automations that execute at specific points in a workflow. They can send notifications, create users, share submissions, and more.

## Execution Points

Triggers can execute at different workflow events:

| Execute Value | When It Runs |
|---------------|--------------|
| `on_submit` | When a form is submitted |
| `on_flow` | When a specific flow step is reached |
| `on_approve` | When a step is approved |
| `on_deny` | When a submission is denied |
| `on_complete` | When the entire workflow completes |

## Trigger Types

### Send Notifications

Sends custom notifications to specified users or groups.

**Use Case:** Alert stakeholders when a submission reaches a certain stage.

### Share Submission

Automatically shares the submission with specified users or groups.

**Use Case:** Give read access to auditors when a submission is approved.

### Create User

Creates new user accounts based on form data.

**Use Case:** Onboard new employees when their hiring form is approved.

### Create Reminder

Sets up deadline reminders for the submission.

**Use Case:** Send follow-up notifications if approval takes too long.

### Combine PDF

Merges multiple PDF documents attached to the submission.

**Use Case:** Create a single document package for final review.

## Conditional Triggers

Conditional steps use the trigger system to evaluate form values and branch the workflow. See [Conditional Flow Steps](/workflows/conditional-flow-steps) for details.

### Operators

| Operator | Description |
|----------|-------------|
| `is equal to` | Exact match |
| `is not equal to` | Does not match |
| `is empty` | Field has no value |
| `is not empty` | Field has a value |
| `contains` | Value contains substring |
| `does not contain` | Value does not contain substring |
| `is greater than` | Numeric comparison |
| `is less than` | Numeric comparison |

### Actions

When conditions are evaluated, one of two actions executes:

**True Action** (conditions pass):
- `approve` - Auto-approve and advance
- `continue` - Move to next step normally
- `deny` - Deny the submission
- `jump_to` - Skip to a specific flow step
- `jump_to_approver` - Skip and assign specific approver
- `step` - Create a new approval step
- `send-back` - Return to a previous step
- `trigger` - Execute another trigger

**False Action** (conditions fail):
Same options as true action.

### Multi-Condition Logic

When multiple conditions exist, they're combined with connectives:

```
Condition 1: field_a = "Yes"
Connective: AND
Condition 2: field_b > 1000
```

The system evaluates from first to last, applying connectives between each pair.

## Protected References

When a conditional step references another flow step (via `jump_to`), the target step is marked as protected. This prevents accidental deletion of steps that are part of conditional logic.
