---
title: Validation
order: 5
---

# Form Validation

Ensure data quality with Flow Forms' comprehensive validation system. Set rules, provide helpful error messages, and guide users to submit accurate information.

## Built-in Validation

### Field Type Validation
Each field type includes automatic validation:
- **Email** - Validates email format
- **Date** - Ensures valid dates
- **File Upload** - Checks file types and sizes
- **Numeric Fields** - Validates number formats

### Required Fields
Mark any field as required:
- Toggle the "Required" switch in field settings
- Users cannot submit until all required fields are complete
- Clear indicators show which fields are mandatory

## Input Rules & Masking

### Input Masking
Format data automatically as users type:
- Phone numbers: `(xxx) xxx-xxxx`
- Social Security: `xxx-xx-xxxx`
- Postal codes: `xxxxx-xxxx`
- Custom patterns for your specific needs

### Validation Rules
Validation rules ensure data meets your requirements before submission. Common rules include:
- Minimum/maximum length
- Specific formats or patterns
- Numeric ranges
- Date ranges

## Custom Validation

### Pattern Matching
Use regular expressions for complex validation:
- Custom ID formats
- Specific text patterns
- Advanced email validation

### Cross-Field Validation
Validate fields in relation to each other:
- End date must be after start date
- Confirmation fields must match
- Sum of fields must equal total

## Error Messages

### Default Messages
Flow Forms provides clear, user-friendly error messages for all standard validations.

### Custom Messages
Customize error messages to:
- Provide specific instructions
- Match your organization's tone
- Guide users to correct input

## Best Practices

- Validate on the server side for security
- Provide immediate feedback when possible
- Use clear, helpful error messages
- Don't over-validate - only require what's necessary
- Test validation with edge cases