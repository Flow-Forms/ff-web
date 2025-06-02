---
title: Zapier Integration
order: 2
---

# Zapier Integration

Connect Flow Forms to over 8,000 apps with Zapier integration. Automate workflows without writing any code.

## Getting Started with Zapier

### What You Can Do
With Flow Forms + Zapier, you can:
- Send submissions to Google Sheets
- Create tasks in project management tools
- Add contacts to your CRM
- Post to Slack channels
- Send data to 8,000+ other apps

### Setting Up Your First Zap
1. Log in to your Zapier account
2. Click "Create Zap"
3. Search for "Flow Forms" as your trigger app
4. Connect your Flow Forms account
5. Choose your action app
6. Map form fields to your destination

## Available Triggers

### New Form Submission
Triggers when a new form submission is received:
- Includes all form field data
- Submission metadata
- Real-time updates

### Updated Submission
Triggers when a submission status changes:
- Status updates
- Assignment changes
- Completion events

## Connecting Flow Forms to Zapier

### Authentication
1. In Zapier, select Flow Forms
2. Click "Connect Account"
3. Enter your API token from Flow Forms
4. Test the connection

### Generating API Tokens
1. Go to Admin → Account in Flow Forms
2. Click "API Tokens"
3. Create a new token for Zapier
4. Copy and save securely

## Common Zapier Workflows

### Form to Google Sheets
Automatically log submissions in a spreadsheet:
1. Trigger: New Form Submission
2. Action: Create Spreadsheet Row
3. Map each form field to a column

### Form to Slack
Get instant notifications in Slack:
1. Trigger: New Form Submission
2. Action: Send Channel Message
3. Customize message with form data

### Form to CRM
Add leads automatically:
1. Trigger: New Form Submission
2. Action: Create Contact (Salesforce, HubSpot, etc.)
3. Map form fields to CRM fields

### Form to Email
Send custom emails:
1. Trigger: New Form Submission
2. Action: Send Email
3. Use form data in email content

## Field Mapping

### Available Fields
All form fields are available in Zapier:
- Text fields
- Email addresses
- Dates and times
- File upload URLs
- Calculated values
- All custom fields

### Mapping Best Practices
- Use descriptive field names
- Test with sample data
- Handle optional fields
- Format dates consistently

## Advanced Features

### Filters
Add conditions to your Zaps:
- Only process certain submissions
- Filter by form values
- Route based on conditions

### Multi-Step Zaps
Create complex workflows:
1. Receive form submission
2. Look up existing records
3. Update or create as needed
4. Send notifications

## Troubleshooting

### Common Issues
- **Missing fields**: Ensure fields have data
- **Connection errors**: Refresh API token
- **Delayed triggers**: Check Zapier plan limits

### Testing Your Zaps
1. Submit a test form
2. Check Zap history
3. Verify data mapping
4. Monitor for errors

## Best Practices

- Name your Zaps clearly
- Document field mappings
- Test thoroughly before activating
- Monitor Zap performance
- Use Zapier's built-in error handling
- Keep API tokens secure