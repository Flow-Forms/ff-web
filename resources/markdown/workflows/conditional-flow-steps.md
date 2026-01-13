---
title: Conditional Flow Steps
order: 3
---

# Conditional Flow Steps

Conditional flow steps allow you to create dynamic, branching workflows based on form submission data. When a submission reaches a conditional step, the system evaluates the conditions you've defined and automatically routes the submission accordingly.

## Overview

A conditional step evaluates one or more conditions against form field values and takes different actions based on whether those conditions are true or false.

**Basic structure:**
```
IF [condition(s)]
THEN [action when true]
OTHERWISE [action when false]
```

## Creating a Conditional Step

1. Navigate to your form's workflow editor
2. Click **Add Step** and select **Conditional**
3. Enter a **Name** for your conditional step
4. Configure your conditions and actions
5. Click **Save Conditional**

## Conditions

### Form Element

Select which form field to evaluate. All form elements from the form (including inherited elements from linked forms) are available.

### Operators

| Operator | Description |
|----------|-------------|
| **is equal to** | Field value exactly matches the specified value |
| **is not equal to** | Field value does not match the specified value |
| **is greater than** | Field value is numerically greater than the specified value |
| **is greater than or equal to** | Field value is numerically greater than or equal to the specified value |
| **is less than** | Field value is numerically less than the specified value |
| **is less than or equal to** | Field value is numerically less than or equal to the specified value |
| **contains** | Field value contains the specified text anywhere within it |
| **is empty** | Field has no value (no comparison value needed) |
| **is not empty** | Field has any value (no comparison value needed) |

### Value

Enter the value to compare against. For fields with predefined options (dropdowns, radio buttons, etc.), you'll see a dropdown of available values. For other fields, enter the comparison value manually.

**Note:** The "is empty" and "is not empty" operators do not require a comparison value.

## Multiple Conditions

You can add multiple conditions to create complex logic. Click **Add Condition** to add additional conditions.

### Connectives

When you have multiple conditions, you must specify how they relate to each other:

| Connective | Description |
|------------|-------------|
| **AND** | Both conditions must be true |
| **OR** | Either condition can be true |

### Evaluation Order

Conditions are evaluated from top to bottom using left-to-right precedence. This means:

```
A AND B OR C
```

Is evaluated as:

```
(A AND B) OR C
```

**Example:**
- Condition 1: Department **is equal to** "Engineering"
- AND Condition 2: Level **is greater than** 5
- OR Condition 3: Role **is equal to** "Manager"

This evaluates as: `(Engineering AND Level > 5) OR Manager`

## Actions

### True Actions (THEN)

When all conditions evaluate to true, one of these actions is performed:

| Action | Description |
|--------|-------------|
| **Continue** | Proceed to the next step in the workflow |
| **Jump to Flow** | Skip to a specific step in the workflow |
| **Jump to Approver of Flow** | Skip to a specific approval step |
| **Run a Trigger** | Execute an on-demand trigger, then perform the "And Then" action |
| **Approve** | Automatically approve the submission |
| **Deny** | Automatically deny the submission |
| **Send Back** | Return the submission to the submitter (not available for anonymous forms) |

### False Actions (OTHERWISE / AND THEN)

When conditions evaluate to false (or after a trigger runs), one of these actions is performed:

| Action | Description |
|--------|-------------|
| **Continue** | Proceed to the next step in the workflow |
| **Jump to Flow** | Skip to a specific step in the workflow |
| **Jump to Approver of Flow** | Skip to a specific approval step |
| **Approve** | Automatically approve the submission |
| **Deny** | Automatically deny the submission |

**Note:** When the true action is "Run a Trigger", the false action becomes "And Then" - specifying what happens after the trigger completes.

## Protected Elements

Form elements used in conditional logic are automatically protected from deletion. This ensures your workflow logic remains intact. The protection is removed when you delete the conditional step or change the conditions.

Similarly, flows and triggers referenced by jump actions are protected while in use by a conditional step.

## Examples

### Example 1: Route by Department

Route submissions to different approval paths based on department:

- **IF** Department **is equal to** "Finance"
- **THEN** Jump to Flow: "Finance Approval"
- **OTHERWISE** Continue

### Example 2: Auto-approve Small Requests

Automatically approve requests under a certain amount:

- **IF** Amount **is less than** 100
- **THEN** Approve
- **OTHERWISE** Continue

### Example 3: Complex Routing

Route based on multiple criteria:

- **IF** Priority **is equal to** "High"
- **AND** Category **is equal to** "Security"
- **THEN** Jump to Flow: "Security Team Review"
- **OTHERWISE** Continue

## Best Practices

1. **Name your conditionals descriptively** - Use names that describe what the condition checks (e.g., "Route High Priority", "Validate Budget")

2. **Keep conditions simple** - Complex nested logic can be hard to maintain. Consider using multiple conditional steps for clarity.

3. **Test thoroughly** - Submit test data that covers both true and false scenarios for your conditions.

4. **Consider edge cases** - Think about what happens when fields are empty or contain unexpected values.

## Troubleshooting

### Condition not evaluating as expected

- Verify the form element contains the expected data type
- Check for leading/trailing spaces in text comparisons
- For numeric comparisons, ensure the field contains numeric data

### Cannot delete a form element

- The element may be protected by a conditional step
- Check your conditional steps and remove references to the element first

### Cannot delete a workflow step

- The step may be referenced by a conditional's "Jump to" action
- Update the conditional step to use a different action first
