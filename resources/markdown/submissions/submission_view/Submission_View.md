---
title: Submission View
order: 1
---


# Submission View

From the **{{icon:paper-clip size-4 inline-block align-text-center}} Submissions** page, navigate to any of the entries that have populated below the Control Panel to view individual form submissions. Submissions are listed with up to 20 per page. There is a forward/backward button at the bottom of the page for access to any additional pages of filtered results/submissions.

The **header** of each submission contains all of the controls, status indicators, and options available for you to understand and directly interact with a submission.

Some of the more basic elements of the submission's header are outlined here, but those that require more detail have been divided into their own sub-categories.

Each submission displays the **Form Name,** or title, which is listed in the top left corner of the submission's header. This is also a clickable link to start a new submission of that form type.

Just below that, is the **submitted by** section that lists the name of the user that submitted the form, along with a color-coded box (auto-assigned) containing their initials.

## Form Status
The color-coded bar across the top of each submission's header indicates the status of that particular submission as it progresses through its workflow toward completion. Not every status is utilized in every form, but the exhaustive list is as follows:

- Blue - **Approved** (submission is complete)
- Yellow- **Pending** (waiting on another's action)
- Red - **Pending on Me** (waiting on your action)
- Orange - **Sent Back** (submission has been sent back to the form's originator)
- Magenta - **Info Requested** (sent back to any flow-step for additional information)
- Gray- **Denied** (submission has been rejected and cannot be continued)
- Teal - **Escalated** (skipped flow-step(s) to seek input/approval)


## {{icon:funnel size-7 inline-block align-text-center}} (Filter By)
The {{icon:funnel size-4 inline-block align-text-center}} (funnel) icon can be found beside every form element on a submission entry. This is a quick method to filter your submission results by that specific element on a form.

*For example:* If you had a form named **Expenses,** one of the elements on that form could be to select the *Category* for an expense, such as "Software," or "Marketing," or "Travel." From the **{{icon:paper-clip size-4 inline-block align-text-center}} Submissions** page, if you are focusing on an **Expenses** submission, and you select the {{icon:funnel size-4 inline-block align-text-center}} icon beside *Category,* and that submission has "Marketing" as the input for *Category,* your submission results will filter to display only those **Expenses** form submissions that have "Marketing" as the input under *Category.*

When a {{icon:funnel size-4 inline-block align-text-center}}  filter is selected, the Control Panel above the listed submissions will load with the new active filters displayed. The name of the form you are filtering by will be listed beside **{{icon:document-duplicate size-4 inline-block align-text-center}} Forms,** and can be unselected as a filter by clicking the "X" beside it. Whatever form element was selected with the {{icon:funnel size-4 inline-block align-text-center}} filter, will be listed below that with "**Filtering By**," beside the form element, along with the input that you are filtering by. This also will be listed with a "X" beside it in order to cease filtering by that input.

*As in the example above:* **Expenses** would be listed beside **{{icon:document-duplicate size-4 inline-block align-text-center}} Forms,** and **"Filtering By**  |  Category:  **Marketing**" would be displayed as the rest of the filter's parameters as they appear in the Control Panel.


## {{icon:pencil-square size-7 inline-block align-text-center}} (Edit)
If a form's permissions have been set to allow editing, you may alter the inputs and add data/comments to the input areas of a submission that has already been submitted.

When a submission has been edited, a note is added in that submission's "Notes" log that lists the editor's user name and the time it was edited, along with the message, *"This submission has been edited."* This is accessible from the submission's **{{icon:document-text size-4 inline-block align-text-center}} View All Notes** button or while fully viewing a single submission via the **{{icon:arrow-top-right-on-square size-4 inline-block align-text-center}} (Pop-out)** button or a direct, emailed link.

If desired, all of a form's contributors can receive a notification when a form has been edited. This is initiated by a user with administrator permissions, and is set up in the form's flow steps.

## History -
View every action that has been taken on that submission so far. When selected, a modal will display the **Process History.**
- **Action By**- lists the user's name
- **Action**- displays the kind of action they took on the form
- **Date**- specifies the date and timestamp of that action

*You may also choose to **show conditional steps** for a more in-depth insight into the flow's parameters (not interesting to most of us).*

## Actions / Continue

The [Actions and Continue](Submissions/Submission_View/Actions) buttons allow you to take workflow actions on submissions pending your input. When a submission is **Pending On Me**, you will see either a **Continue** button (if additional data input is required) or an **Actions** button (if only approval/review is needed).

Available actions include **Approve, Request Info, Send Back,** and **Deny.**

*See the full [Actions](Submissions/Submission_View/Actions) page for detailed information on each action type.*

### Final Status or Pending on -
If not listed as **Approved** or **Denied,** a color-coded box, or boxes, with the initials of the user/s on whom the submission is pending will appear.

If it is pending on a single user, their full name will be presented alongside.

If there are multiple user actions pending, you can click the boxes, containing their initials, to view a modal window that lists the full names of the users that have actions still required for the submission to complete the required steps in its flow and be finalized.

### Time stamp(s) -
While still pending, only the date and time the submission was created is listed. When the submission has reached finality, whether through approval or denial, the date and time of its completion, or when it was last edited, is listed as well.

## {{icon:cog-6-tooth size-4 inline-block align-text-center}} (Options)

Clicking the gear icon in a submission's header presents you with a side-bar titled **Submission Options** that lists the available options for interacting with that submission. These vary greatly by the user's permissions and the submission selected.

*See the full [Options](Submissions/Submission_View/Options) page for detailed information on each available option.*

### {{icon:arrow-top-right-on-square size-4 inline-block align-text-center}} (Pop-out) -
Fully view a single submission on a reloaded page.  From the reloaded page, you can click the "{{icon:arrow-uturn-left size-4 inline-block align-text-center}} Previous Page" link, just above the submission's header on its left end, to return to your submissions page as you left it.

### {{icon:arrows-pointing-out size-5 inline-block align-text-center}} Show More -
Fully view all data/entries on the form submission while remaining on your "Submissions" page. Also referred to as *Expanded View.*
- Most attached documents (ex: .png, .jpeg) will also expand in view. PDFs will display a "view PDF" link in order to view the attachment in a modal window. Alternatively, you can click the attachment's link, just below it, to download the attached document or photo.

### {{icon:arrows-pointing-in size-5 inline-block align-text-center}} Show Less -
Return to the overview display of the submission's data. The elements displayed in this view are selected at the time of the form's creation. This view option displays attachments as thumbnails and/or viewing links. This button only appears when in the expanded, or, "show more" view, and is also referred to as *Collapsed View.*

## + Add Note

Add additional information, comments, or attach files for the record. Notes are visible to anyone with access to the submission, but can be set to "protected" when you need only a group of users to be able to view them.

*See the full [Add Note](Submissions/Submission_View/Add_Note) page for detailed instructions on creating, viewing, and managing notes.*


## Admin Options

Administrators have access to additional submission options including the ability to undo approved or denied decisions, transfer submissions, edit forms and flows, and configure form options.

*See the full [Admin Options](Submissions/Submission_View/Admin_Options) page for details on administrator-only features.*


### Form-Specific Views
Navigate to any form to see its entries/data:
- Submission count
- Recent entries
- Status overview



## Submission Actions

### Individual Actions
For each submission, you can:
- View full details
- Edit submission data (if permissions allow)
- Add notes or comments
- Change submission status
- Assign to team members

### Bulk Actions
Select multiple submissions to:
- Export to CSV
- Update status in bulk
- Assign to users
- Apply tags or categories

## Grouping & Permissions

### Group Access
Administrators can assign groups to view specific form submissions:
1. Edit the form
2. Go to "Additional Form Options"
3. Assign groups who should have access
4. Save changes

### Permission Levels
- **View Only** - See submission data
- **Edit** - Modify submission content
- **Manage** - Full control including deletion

## Export Options

### CSV Export
Export submissions to CSV format:
1. Apply any filters needed
2. Click "Export to Excel" button
3. Choose fields to include
4. Download your file

### Data Format
Exported data includes:
- All form fields
- Submission metadata
- Timestamps
- Status information

## Best Practices

- Use filters to create custom workflows
- Save commonly used filters
- Regularly export data for backup
- Set up group permissions appropriately
- Use bulk actions to save time
