---
title: Options
order: 1
---

# {{icon:cog-6-tooth size-15 inline-block align-text-center}} Options 

Clicking the gear icon in a submission's header opens a sidebar from the right side of your screen titled **Submission Options**. Just below the title, you'll see the form name and its Submission ID (which starts with "#sub_" followed by a unique string of letters and numbers). The menu below displays the available options for interacting with that submission. These options vary based on your permissions and the selected submission.

Available options may include:

## + Add Note

Add collaboration notes and comments to a submission. Selecting **+ Add Note** from the Options menu opens directly to the notes creation modal.
*See the [Add Note](Submissions/Submission_View/Add_Note/Add_Note) page for more information on adding notes.*

## {{icon:share size-6 inline-block align-text-center}} Share

Unless a form is set as "private," you can share submissions with other Flow Forms users if you're the owner or submitter of a form, or an administrator. Sharing grants viewing permission only—shared users cannot take actions on submissions but can add notes.

Choosing **{{icon:share size-4 inline-block align-text-center}} Share** under the **{{icon:cog-6-tooth size-4 inline-block align-text-center}} (Options)** menu opens a modal with a searchable dropdown labeled **Select User to Share Submissions With**. Select a user's name and a checkmark will appear beside their information, with their name and email address displayed in the dropdown.

Below this, you can choose between two sharing options:

- **Only this submission** - Allows the selected user to view only the current submission
- **All submissions on this form** - Allows the selected user to view every submission you've made to this form

If sharing is already active, those details appear at the bottom of the modal, differentiated as **This submission only** or **All submissions on this form**. If no sharing exists, it displays _"Not shared with anyone."_

After clicking the blue **Share** button, your new sharing selection appears in this section. You can add additional users one at a time. To remove access, select the **{{icon:trash size-4 inline-block align-text-center}}** (trash) icon beside the user's information. A confirmation prompt will ask _"Are you sure you want to remove sharing with this user?"_ Click **Cancel** to exit or **OK** to remove their access."


- ### Advanced Sharing:
Clicking the blue **Advanced sharing** link under the sharing options select-box opens additional sharing options.

From the **Sharing** modal, you can toggle **All Submissions** on if you would like to designate a user to be allowed to view all of your submissions across all of your forms. When enabled, the remaining selection is a searchable dropdown box labeled **Share with Selected Users.** Select a user's name and a checkmark will appear beside it. Their name will also appear in the dropdown box. Click the blue **Save** button to apply the sharing or **X** to exit without saving.

When the **All Submissions** toggle switch is **off,** you'll see two searchable dropdowns:

- **Submissions From Selected Forms -** lists all forms you're included in. Select the form you'd like to share, a checkmark will appear beside it, and its name will appear in the dropdown.
- **Share with Selected Users -** Select the user you'd like to grant access and their name will appear in the box. This allows the user viewing access to all your submissions, but only on the specified form.

Click the blue **Save** button to activate the sharing or **X** to exit without saving.

Clicking the blue **Advanced sharing** link under the sharing options select-box takes you to additional sharing options for you as a user.

From the **Sharing** modal, you may select to turn on a toggle switch labeled, **All Submissions.** Do this if you would like to designate a user to be allowed to view *all* of your submissions to *all* of your forms. When this is switched **on,** the only remaining selection is a drop-down box, with search capability, labeled, **Share with Selected Users.** From this, you may select the user's name with whom you would like to share your viewing permissions. A checkmark will appear beside their name once selected, and will also appear in the drop-down box. Click the blue, **Save** button to submit this new share or "X" out of the modal to exit without saving.

When the **All Submissions** toggle switch is set to **off,** you are left with two, searchable, drop-down boxes.
	- **Submissions From Selected Forms -** lists all the forms you are included in. Select the form you'd like to share, a checkmark will appear beside it, and the form name will appear in the drop-down box.
	- **Share with Selected Users -** Select the user you'd like to grant access to that form, and their name will appear in the box. This allows the user viewing access to all your submissions, but only on the specified form.

Click the blue, **Save** button to activate the new share, or the **X** to exit without saving.

Any existing shares appear at the bottom under **Individual Shared Submissions** or **Submissions Shared by Form**, showing the user's name and number of shared forms. Click a user's name to expand the list of shared forms, each with a **{{icon:trash size-4 inline-block align-text-center}}** (trash) icon. Clicking the trash icon prompts _"Are you sure you want to delete this share?"_ Click **Cancel** to exit or **OK** to remove sharing for that form.

When no sharing is configured, the modal displays _"No submissions individually"_ under **Individual Shared Submissions** and _"No submissions shared by form"_ under **Submissions Shared by Form**.



## {{icon:calendar size-6 inline-block align-text-center}} Reminders
***Use Case:** I need to be reminded to follow up on this next week*

If you would like an email and/or text message to be sent to you, at a specific time, that is linked to a submission, select **{{icon:calendar size-4 inline-block align-text-center}} Reminders** from the Options menu of that submission. A modal will appear with these options:

- **When** - set the date for the reminder, or for when the reminders are to begin
	- **Frequency** -
		- *One Time* - for one reminder, sent one time (on date set above)
		- *Recurring* - Select for reminders to be sent at an interval of your choosing
			- **Interval** - number of units between reminders
			- **Frequency** - unit of time (day, week, month, year)
			- **Ends** -
				- *Never* - Select to have reminders continue in perpetuity
				- *On* - Select to end reminders on a specified date (date selection box appears when "On" is selected)
				- *After* - Select for reminders to end after a certain number have been sent. When selected, an "Occurrences" box appears to allow the number of reminders to be selected.
-  **Trigger** - A trigger is an action that is auto-deployed at the time of a set occurrence in a form's workflow. If, at the time of the form's flow design, a trigger has been created, it will appear here as an option to deploy that trigger at the time specified by your reminder. Both the trigger's action and your reminder communication will occur when this is selected from reminders.
-  **Message** - Type your desired communication for the reminder in this section. It will be sent as your reminder message via email, and SMS if selected, along with abbreviated submission info and a link to the submission.
-  **Send SMS** - Toggle button is off by default. Click to turn on if a text message reminder is desired in addition to the default email notification.
- **Current Reminders** - If any reminders are already set on the submission, they will appear here, listed with their **Type** and **Message.** If there are none, *"No reminders set"* will appear. You can set multiple reminders on a submission. Reminders can be deleted (stopped), before they are set to expire, by clicking the {{icon:trash size-4 inline-block align-text-center}} (trash) icon that appears beside their listing in current reminders.
- Click **Cancel** to exit reminders or click **Add Reminder** to set your reminder with the parameters you have selected.

## {{icon:arrow-path-rounded-square size-4 inline-block align-text-center}} Reassign

## {{icon:arrows-right-left size-4 inline-block align-text-center}} Reassign to Me

## Connected Submissions
connected to another form. In the form's build, a connected element is set. conn subs show up when the data on the submission you're looking at is pulled from or contributing to other submissions. only shows those "downstream" and, from Options, it's just filtering to show your results that are connected submissions
## Connected Forms
some elements are used from the viewed form on other forms. clicking takes you to the connected form

## {{icon:printer size-6 inline-block align-text-center}} Print
If you select to print from the Options menu, the submission will be reloaded on its own page, from which you see the full, individual submission for printing.

## Custom View

## {{icon:document-text size-6 inline-block align-text-center}} Download as PDF
Instantly downloads the submission to your device as a PDF.

## {{icon:document-duplicate size-6 inline-block align-text-center}} Copy Submission

***Use Case**: I want to create a similar submission without re-entering all of the data.*

Selecting **{{icon:document-duplicate size-6 inline-block align-text-center}} Copy Submission** creates a new submission, on a reloaded page, with the current submission's data pre-filled. You can then modify the information as needed before clicking **Submit** to create a new, unique submission. This is useful when creating multiple similar submissions without re-entering common information.

This option is available to any user with viewing permissions for the form or submission, not just its owner or submitter.


## {{icon:arrow-path size-6 inline-block align-text-center}} Restart Submission

***Use Case:** This submission needs to start completely over.*  

If you are the owner or submitter of a form, or an administrator, you have the option to restart a submission. This takes you to a page with the selected form submission reloaded, but not yet submitted. If you have any modifications to make, you can do so before clicking the blue "Submit" button at the bottom.

**Important:** This restarts your submission at the beginning of its workflow, and any actions that had already been taken will need to be completed again for it to reach approval. This action cannot be undone.


## {{icon:trash size-6 inline-block align-text-center}} Delete Submission

As the owner or submitter of a form, or an administrator, you can delete a submission by selecting **{{icon:trash size-4 inline-block align-text-center}} Delete Submission** from the **{{icon:cog-6-tooth size-4 inline-block align-text-center}} (Options)** menu of that submission.

A confirmation modal will appear asking _"Are you sure you want to delete this submission? This action cannot be undone."_ The modal displays the form name, submission ID, and creation time for verification.

Select **Cancel** to exit without deleting, or the red **Delete Submission** button to permanently delete the submission. Once deleted, the submission ID and data cannot be recovered.

## {{icon:envelope size-6 inline-block align-text-center}} Resend Email
***Use Case**- The assigned users didn't receive the email or it was lost or deleted.*

Select this to resend a notification email to the currently assigned user or users. This sends to all users that have active notifies on this submission, but has a rate limit to prevent redundancy or abuse.


