---
title: Collective Steps
order: 4
---

# Collective Steps

Collective steps add users to a collective group without creating approval requirements. They're used to gather stakeholders across multiple forms and submissions for later reference or action.

## Overview

Unlike dynamic steps which create approval notifications, collective steps simply add users to a shared pool called a "collective." This is useful when you need to track related parties across multiple submissions without requiring their immediate approval.

## Key Difference from Dynamic Steps

| Aspect | Dynamic Step | Collective Step |
|--------|-------------|-----------------|
| Creates approval notifications | Yes | No |
| Adds users to a group | Optional | Yes (to collective) |
| Blocks workflow progression | Yes (until approved) | No |
| Purpose | Get approval from form-derived users | Track stakeholders for later |

## Collective Types

### Initial (`initial`)

Adds the original submitter to the collective.

**Behavior:**
- Retrieves the user who created the submission
- Adds them to the specified collective
- Records the source submission for tracking

**Use Case:** "Track all employees who submitted requests this quarter."

### Form (`form`)

Extracts user data from form fields and adds them to the collective.

**Behavior:**
- Reads email, name, and optionally phone from specified form elements
- Creates or updates users with the extracted data
- Adds users to the collective with source tracking
- Does NOT create approval notifications

**Configuration:**
- Email field (required) - form element containing email
- Name field (required) - form element containing name
- Phone field (optional) - form element containing phone
- Role - role to assign to created users
- Group - optional group membership

**Use Case:** "Gather all referenced clients from project intake forms into a client collective."

## Collectives

A collective is a named group that aggregates users across multiple forms and submissions.

### Use Cases for Collectives

1. **Cross-form reporting:** Track all stakeholders mentioned across different form types
2. **Batch notifications:** Send updates to everyone in a collective at once
3. **Audit trails:** Know which submission added which user
4. **Committee assembly:** Gather reviewers from multiple intake forms

## Step Sequencing

Collective steps follow specific sequencing rules:

- Can be preceded by: Any step type
- Can only be followed by: `all`, `dynamic`, `conditional`, or other `collective` steps

This prevents collective steps from immediately preceding basic approval steps (`one`, `multiple`, `group_select`).

## Workflow Behavior

When a submission reaches a collective step:

1. Users are added to the collective
2. The workflow immediately advances to the next step (no approval wait)
3. Users are tracked in the collective for future reference
