# PDF Attachments for Email Notifications

You can now attach a PDF of the submission to your custom email notifications. This makes it easy to share a complete record of the submission with recipients without requiring them to log in.

## How It Works

When you enable PDF attachments on a notification trigger, recipients will receive your custom email with a PDF file attached. The PDF contains the same submission data shown in the print view.

## Setting It Up

1. Go to your form's **Triggers** section
2. Create or edit a **Notify** trigger
3. Enable **Use custom email** and select your email template
4. Toggle on **Attach submission PDF**
5. Save your trigger

The PDF attachment option only appears when using a custom email template. Standard notifications don't include this option.

## What's Included in the PDF

The attached PDF is generated from the submission's print view. It includes:

- All submitted form data
- Field labels and values
- The same layout you see when printing a submission manually

## Things to Know

- **File size**: PDFs are generated on-demand when the notification is sent. Complex forms with many fields may produce larger files.
- **Custom templates required**: You must select a custom email template to use this feature. The toggle won't appear for standard notifications.
- **Error handling**: If PDF generation fails for any reason, the email will still be sent without the attachment, and the error will be logged.
