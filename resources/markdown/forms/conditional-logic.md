---
title: Conditional Logic
order: 4
---

# Conditional Logic

Create dynamic forms that adapt [adapt](api/getting-started#Token%20Security) based on user responses. Show or hide fields, create branching logic, and personalize the form experience.

## Understanding Conditional Logic

Conditional logic allows you to:
- Show fields only when specific conditions are met
- Hide irrelevant questions
- Create different paths through your form
- Reduce form complexity for users

## Setting Up Conditions

### Basic Conditions
1. Select the field you want to show/hide
2. Click "Add Condition" in the field settings
3. Choose the trigger field and value
4. Save your condition

### Condition Types
- **Is Equal To** - Exact match
- **Is Not Equal To** - Exclude specific values
- **Contains** - Partial text match
- **Is Empty** - No value entered
- **Is Not Empty** - Any value entered

## Common Use Cases

### Progressive Disclosure
Show additional fields only when needed:
- If "Do you have allergies?" = "Yes", show allergy details field
- If payment type = "Check", show check number field

### Branching Forms
Create different paths based on initial choices:
- Different questions for different user types
- Skip sections that don't apply

### Dynamic Requirements
Make fields required only in certain situations:
- Require phone number only for urgent requests
- Require additional documentation based on amount

## Best Practices

- Keep conditions simple and logical
- Test all possible paths through your form
- Provide clear instructions when fields appear
- Don't hide critical information behind complex conditions