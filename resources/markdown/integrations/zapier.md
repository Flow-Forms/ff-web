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
- Trigger actions at any point in your approval workflow
- Send data to 8,000+ other apps

### How It Works
Zapier integrates with Flow Forms through **triggers** in your approval workflow. You add a Zapier trigger to your form's workflow, and whenever that trigger fires, the data is sent to your connected Zap.

This gives you complete control over when data is sent to Zapier—on submission, after approval, when specific conditions are met, or any other point in your workflow.

## Managing Integrations

### Integrations Tab
All Zapier connections for a form are managed in the dedicated **Integrations** tab within your form's admin panel. This provides a clear overview of:
- All connected Zaps
- Webhook health status
- Which fields are being sent to each Zap
- When each webhook was last triggered

### Viewing Connected Webhooks
Each connected webhook displays:
- **Status badge**: Health indicator showing if the connection is working
- **Trigger name**: The workflow trigger that fires this webhook
- **Created date**: When the connection was established
- **Last triggered**: When the webhook was last invoked
- **Error count**: Number of failed delivery attempts
- **Fields**: Which form fields are being sent to Zapier

### Webhook Actions
For each connected webhook, you can:
- **Open in Zapier**: Direct link to edit your Zap in Zapier's editor
- **Enable/Disable**: Temporarily pause a webhook without disconnecting
- **Reset Errors**: Clear the error count after fixing issues
- **Disconnect**: Remove the webhook connection entirely

## API Tokens

### Generating API Tokens
1. Go to **Admin → Account** in Flow Forms
2. Click "API Tokens"
3. Create a new token for Zapier
4. Copy and save securely

## Webhook Health Status

Flow Forms tracks the health of each Zapier connection to help you identify and fix issues quickly.

### Status Indicators
- **Healthy** (green): Webhook is working correctly with no recent errors
- **Degraded** (yellow): Some delivery failures detected (5-9 failed attempts)
- **Failing** (red): Multiple delivery failures (10+ failed attempts)
- **Disabled** (gray): Webhook has been manually paused

### Automatic Error Handling
- Failed webhook deliveries are automatically retried
- Error counts track consecutive failures
- After 10+ consecutive failures, webhooks may be automatically pruned
- Use "Reset Errors" after fixing issues to restore healthy status

## Troubleshooting

### Common Issues

**Webhook showing "Degraded" or "Failing" status**
- Verify your Zap is active in Zapier
- Click "Reset Errors" in Flow Forms after resolving the issue

**Zapier not firing**
- Verify the trigger is properly configured in your workflow
- Check that the workflow conditions allow the trigger to fire
- Test with a new submission

### Getting Help
If you continue to experience issues:
- Review the webhook status in your form's Integrations tab
- Contact support with your form ID and webhook details