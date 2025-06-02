---
title: Field Types
order: 2
---

# Field Types

Flow Forms provides a comprehensive set of field types to handle any data collection need. Each field type is optimized for its specific use case with built-in validation and user-friendly interfaces.

## Basic Input Fields

### Text
Single-line text input for names, titles, and short responses.
- Supports input masks for formatting (phone numbers, postal codes, etc.)
- Custom placeholder text
- Character limit validation

### Textarea
Multi-line text area for longer responses, comments, and detailed feedback.
- Adjustable height
- Character count display
- Rich text formatting options

### Email
Email input with automatic validation.
- Built-in email format validation
- Prevents invalid submissions
- Can be used for receipt delivery with payment fields

## Selection Fields

### Select (Dropdown)
Dropdown menu for choosing from a list of options.
- Single or multiple selection modes
- "Allow Other" option for custom responses
- Can use shared lists for consistent options across forms

### Radio Buttons
Single-choice selection from a list of options.
- Clean, accessible design
- "Allow Other" option available
- Ideal for yes/no questions or limited choices

### Checkboxes
Multiple-choice selection with optional quantities.
- Allow multiple selections
- "Allow Other" option for unlisted items
- Quantity selection for each option (e.g., for product orders)

## Date & Time Fields

### Date
Date picker with calendar interface.
- Customizable date format
- Mobile-friendly date selection
- Past/future date restrictions available

### Time
Time selection field.
- 12 or 24-hour format options
- Minute increment settings
- Time range validation

## Specialized Fields

### File Upload
Secure file upload with type and size restrictions.
- Multiple file upload support
- File type restrictions (images, documents, etc.)
- Maximum file size limits
- Progress indicators

### Signature
Digital signature capture field.
- Touch and mouse support
- Mobile-optimized drawing area
- Signature image saved with submission

### Payment
Integrated payment collection via Stripe.
- Fixed amount or calculated total options
- Secure payment processing
- Automatic receipt generation
- Can reference calculated fields for dynamic pricing

## Advanced Fields

### Calculated
Performs calculations on numeric fields.
- Sum, average, min, max operations
- Real-time calculation updates
- Can be used with payment fields for dynamic pricing

### Composite
Combines multiple fields into a single value.
- Merge first and last name into full name
- Create formatted addresses
- Custom templates with field placeholders

### Group
Container for organizing related fields.
- Visual grouping with borders/backgrounds
- Conditional display logic
- Helps organize complex forms

### Content
Display static content within your form.
- HTML content support
- Instructions, terms, or explanations
- Images and formatted text

## Pre-built Field Sets

### User Information
Quickly add a complete user information section:
- First Name
- Last Name
- Full Name (automatically combined)
- Email Address

### Address
Complete address collection with:
- Street Address
- City
- State/Province (with dropdown for US states)
- ZIP/Postal Code

## Common Field Properties

All fields support these standard options:

### Validation
- **Required** - Make any field mandatory
- **Custom Rules** - Laravel validation syntax for advanced rules
- **Custom Error Messages** - Helpful, specific error feedback

### Display Options
- **Label** - Field name displayed to users
- **Placeholder** - Example text shown in empty fields
- **Help Text** - Additional instructions below the field
- **Conditional Logic** - Show/hide fields based on values from another field

### Advanced Options
- **Input Masks** - Format input automatically (e.g. US telephone numbers, (xxx) xxx-xxxx)
- **Default Values** - Pre-fill fields with default data

