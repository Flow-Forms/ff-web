---
title: Dynamic Steps
order: 2
---

# Dynamic Steps

Dynamic steps create approvers on-the-fly based on form data or submission context. They're used when the approvers aren't known in advance and need to be determined from the submission itself.

## Overview

Dynamic steps use a trigger mechanism to extract user information and create approval notifications.

## Dynamic Types

### Initial (`initial`)

Assigns the original submitter as the approver for this step.

**Behavior:**
- Retrieves the user who created the submission
- Creates a notification for that user to approve
- Useful for confirmation or acknowledgment workflows

**Use Case:** "The employee who submitted the request must confirm the final details."

### Form (`form`)

Extracts user data from form fields to create new approvers.

**Behavior:**
- Reads email, name, and optionally phone from specified form elements
- Creates or updates users with the extracted data
- Assigns a role (typically `external` for outside collaborators)
- Optionally adds the user to a specific group
- Can handle multiple rows from repeating fields

**Configuration:**
- Email field (required) - form element containing email
- Name field (required) - form element containing name
- Phone field (optional) - form element containing phone
- Role - role to assign to created users
- Group - optional group membership

**Use Case:** "A manager enters their supervisor's email in the form, and that supervisor becomes an approver."

### Approvers (`approvers`)

Uses approvers from a previous flow step.

**Behavior:**
- References another flow step by ID
- Collects all users who approved that previous step
- Creates notifications for those same users at this step

**Use Case:** "The same committee members who approved Phase 1 must also approve Phase 2."

## Features

### Graduation Support

Dynamic steps can connect to another form for related workflows (e.g., student graduation linking to a different approval form).

### Multi-Row Extraction

When the form type is set to `form`, the system can extract users from repeating field groups:

1. Identifies rows based on the email field element
2. For each row with an email value, extracts corresponding name/phone data
3. Creates/updates users for each row
4. Creates notifications for all extracted users

### User Creation

For `form` type dynamic steps:

1. Checks if a user with the email already exists in the account
2. If not, creates a new user with:
   - Email address from form
   - Name from form
   - Phone (if provided)
   - Assigned role
   - Optional group membership
3. Sends appropriate notifications to new users

## Step Sequencing

Dynamic steps follow specific sequencing rules:

- Can be preceded by: `conditional`, `collective`, `all`, `dynamic`
- Cannot be directly preceded by: `one`, `multiple`, `group_select`

This ensures proper workflow branching when using conditional logic.
