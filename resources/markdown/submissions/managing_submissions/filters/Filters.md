---
title: Filters
order: 4
---

# Filters
Flow Forms provides comprehensive filtering options to find exactly what you need. Using filters to limit your displayed submissions allows you to handle common tasks efficiently, locate data quickly, run reports, and much more.

To access the complete filter menu, start from the **{{icon:paper-clip size-4 inline-block align-text-center}} Submissions** page and click the blue **{{icon:adjustments-horizontal size-4 inline-block align-text-center}} Filters** button in the Control Panel. A slide-out menu will appear from the right side of the screen. If any filters are currently active, a **Clear All** button will be available at the top. Clear the filters when starting a new search to ensure previous filtering choices aren't limiting your results. Your Submission Count is also displayed at the top of this menu beside **Filters** and will adjust based on your filtering selections. If your submission count shows "0," no submissions match your search criteria.

Apply multiple filters simultaneously to narrow your displayed results. You can filter by any of these criteria:
- [Forms](#Forms)
- [Terms](#Terms)
- [Flow Step](#Flow Step)
- [Date Range](#Start date / End date)
- [Status](#Submission Status)
- [Submitted By](#Submitted by)
- [Pending On](#Pending on)
- [Participants](Participated in Submission)

Once all filtering selections are complete, click the blue **{{icon:funnel size-4 inline-block align-text-center}} Apply Filters** button at the bottom of the menu. To exit the Filters menu and view your results, either click on the submissions page in the background or click the "X" in the menu's top right corner.

### Forms

Display and search only submissions from selected forms. From the **{{icon:adjustments-horizontal size-4 inline-block align-text-center}} Filters** menu, select forms to include in your search from the dropdown labeled ***Select forms to filter*** or type the form's title into the search bar within the dropdown and select from the results. A checkmark will appear beside any selected form. To deselect a form, click it again and the checkmark will disappear.

After selecting your desired forms, the number of selected forms displays in the dropdown box.

*The **Forms** and **Status** filters are both accessible through [Quick-Filters](#quick-filters). When a form is selected as a filter, that form's title will also appear on the Control Panel beside **{{icon:document-duplicate size-4 inline-block align-text-center}} Forms**, located just below the left end of the search bar. From the Control Panel, you can click the "X" next to a form's title to stop filtering by that form.*


### Terms

Choosing to filter by **Terms** allows you to display submissions that have a particular response to a specific form element.

To do so, you must first select to filter by that [form](#forms). When at least one form is selected, **Terms** will appear as a filtering option just below the **Forms** dropdown. When you click **Add Term**, a new dropdown presents every form element included in your selected form.

When a form element is selected from the dropdown, you will only search that selected form element for your specified term. Your search will not include data from any other form elements.

If the form element is a text input, a box will appear for you to type the term you wish to filter for, labeled _Enter search value._

If it is a radio, select, or checkbox style form element, the box will be a dropdown (with search option) containing the input options available for that specific form element.

Once the term or terms are specified, click the blue **+ Add Term** button and your criteria will be displayed, listing both the form element you selected and the term you entered. These can be removed by clicking the "X" beside the term or by clearing the filters.

_For example:_ I have selected a form used in my organization called "Client List" from the **Forms** filter. When I click **Add Term**, the dropdown displays every form element on the "Client List" form, such as: Business name, Contact name, Phone number, Address, etc. If I select the form element **Address** and then type "Billings, MT" in the text box that appears, my search results will display only clients that have "Billings, MT" in the address field.



### Flow Step
If you would like to filter to display only submissions from a form that are on a specific step in their workflow, choose to filter by Flow Step.

First, you must select to filter by [form](#forms). When at least one form is selected **Flow Step** will appear below the **Terms** filter option within the **Filters** menu. Clicking the dropdown will present a numbered list of every flow step from the selected form in the order they occur. Click on a flow step to select it for filtering, and a checkmark will appear on its left. Click again to deselect. Multiple flow steps can be selected simultaneously. Once selected, the flow step's title will display in the dropdown, or if two or more are selected, the number of chosen flow steps will display.



### Start date / End date
To see submissions from a certain period of time, choose to filter by date. From the filter menu, click in the box below "Start Date," where the current date is displayed in gray, and either type the 8-digit date (2-digit month, 2-digit day, 4-digit year) or select the date from the calendar that marks the beginning of your search range.

Repeat the same process in the "End Date" box to mark the end of your desired search range.

The submissions displayed will be based on the date they were submitted.

*If you have a form that has a date as one of its form elements, such as "Invoice date," and would like to filter for that, you will need to use the [Terms](#terms) filter instead.*



### Submission Status 
Display and search submissions that share the status you select.

As a submission moves through its workflow toward completion, its status reflects that progress. Each status is represented by a color. Not every status is used in every form, but the complete list is as follows:

- Blue - **Approved** (submission is complete)
- Yellow- **Pending** (waiting on another user's action)
- Red - **Pending on Me** (waiting on your action)
- Orange - **Sent Back** (submission has been sent back to a prior step or to the initial submitter)
- Magenta - **Info Requested** (pending on a user in the form's workflow for additional information)
- Gray- **Denied** (submission has been rejected and cannot be continued)
- Teal - **Escalated** (skipped flowstep(s) to seek input or approval)

Select by clicking in the box beside any status. A checkmark will appear when a status is selected, and multiple may be selected. Click the box again to deselect. With this filter active, your search results will only display submissions that match the selected statuses.

**Status** and **Forms** filters are both accessible through [Quick-Filters](#quick-filters). When **Status** is selected as a filter, the status name (along with a corresponding color dot) will appear on the Control Panel beside **{{icon:funnel size-4 inline-block align-text-center}} Status**, located just below the right end of the search bar. From the Control Panel, you can click the "X" next to that status to stop filtering by that status.


### Submitted By

To display only  submissions submitted by a specified user or users, utilize the **Submitted By** filter option.

From the blue **Filters** button, scroll down to **Submitted By** and click its dropdown that says *Search by name or email.* A list of users accessible to you within your organization will load below with a search bar at the top. Begin typing the name or email of a desired user into the search bar and click their info line to select them as they appear. A checkmark will appear beside their email address when selected, which you can click again to deselect. All selected user names will appear listed above, along with an "X" that you can click to remove their submissions from your filtered search results.

By default, the list of users that populates in the dropdown will only include those with Flow Forms user accounts within your organization. However, there is a toggle switch just below the dropdown labeled **show external** that, when selected, allows you to also search and select names and emails of users who have submitted to your Flow Forms dataset from an _external_ form link. Activating this switch would be useful if you need to search for data submitted by a user who filled out a public-facing form for your organization."


### Pending On

To display the submissions that are pending on a specific user or users, click the blue, "Filters" button, and scroll down to "Pending On." Click the drop down that says, "Search by name or email," and a list of users accessible to you within your organization will load below with a search bar at the top. Begin to type the name or email of a desired user into the search bar and click their info line to select them as they appear. A green checkmark will appear beside their email address, if selected, which you may click again to unselect. All selected user names will appear listed just above, along with a red "X" that you can click if you want to remove their submissions from your filtered search results.

By default, the list of users that populates in the drop-down will only include those with Flow Forms user accounts within your organization. There is, however, a toggle switch just below the drop down box, with "show external" beside it, that, when selected, allows you to also search and select names and emails of users that have submitted into your Flow Forms data set from an *external* form link. This search addition would come in handy if you need to search for data submitted by a user that filled out a public-facing form for your organization.

### Participated In Submission

To only show submissions to forms that include a designated user or users, at any point in the workflow, you can specify this with the "Participated in Submission" filter. 

From the blue, "Filters" button slide-out menu, scroll down to "Participated in Submission."  Click the drop down that says, "Search by name or email,"  and a list of users accessible to you within your organization will load below with a search bar at the top. Begin to type the name or email of a desired user into the search bar and click their info line to select them as they appear. A green checkmark will appear beside their email address, if selected, which you may click again to unselect. All selected user names will appear listed just above, along with a red "X" that you can click if you want to remove their submissions from your filtered search results.

By default, the list of users that populates in the drop-down will only include those with Flow Forms user accounts within your organization. There is, however, a toggle switch just below the drop down box, with "show external" beside it, that, when selected, allows you to also search and select names and emails of users that have submitted into your Flow Forms data set from an *external* form link. This search addition can be used to search for data submitted by a user that filled out a public-facing form for your organization that does not have an internal Flow Forms account for their use.



## Saved Filters

For frequently accessed search criteria, you have the option to save a filter and name it accordingly. Follow these steps to create reusable filters:
1. **Make your filtering selections** - Utilize any or all of the above mentioned filters.
2. **Click "{{icon:bookmark size-4 inline-block align-text-center}} Save Current Filters"** - This option displays at the top of the "Filters" slide-out menu after a filter is selected.
3. **Name your filter**- Enter your choice for the filter's name in the text box that appears next.
4. **Click "Save"** (or cancel to exit without saving) - Refresh your page, after saving, for the new filter to appear.
5. **Access saved filters from the sidebar under "{{icon:funnel size-4 inline-block align-text-center}} Filters"** - The list of your saved filters is presented alphabetically, is scrollable for users with more than four filters saved, and also contains a search bar. You can dispose of unwanted filters by clicking the red trash can ({{icon:trash size-4 inline-block align-text-center}}) that is to the right of your filter's name. A pop-up window will ask, "Are you sure you want to delete this filter?" Click "OK" to proceed with deletion, or "Cancel" to exit.


## Clear Filters 
Any time a filter of any type is active, the option to *"Clear Filters"* will appear on the Submissions Control Panel, to the right of the *"Filters"* button, beside the search bar. Clicking this removes **all** active filters (including in your search bar) and restores the presentation of the full list of submissions available to you. 

Since Flow Forms saves and displays your last filter selections on your "Submissions" page, the option to "Clear filters" is also available from the main sidebar, beside "{{icon:paper-clip size-4 inline-block align-text-center}} Submissions," if you wish to clear your filters at the onset of loading your submission page.


## Quick-Filters
Two of the most commonly used filters, *"Forms"* and *"Status,"*  are easily accessible from their links on the Submissions Control Panel. 

"{{icon:document-duplicate size-4 inline-block align-text-center}} Forms" is located just below the search bar on its left side. To filter by form, click to show a drop down menu with search option. All your forms are listed in alphabetical order and can be scrolled through or found by searching for the form title. Click to select the form or forms you would like to include in your search. A checkmark will appear to the left of any form you have selected. If no form is selected as a filter, submissions from every form will be included in your displayed results.

"{{icon:check-badge size-4 inline-block align-text-center}} Status" is located below the search bar, but on its right side. To filter by a submission's status, click to see every status option listed (color coded) with a select box to the left of each. Click in the box of any status, and a checkmark will indicate its selection. Click the checkmark again to unselect that status from your filtered results. If no status is selected, submissions with every status will be included in your displayed results.

When "Forms" or "Status" filters are active, their name will appear listed on the Submissions Control Panel, below the search bar, on their respective sides, with the option to "X" out of any that you would like to remove from filtering.








## Filtering Submissions

### Available Filters
- **Submitted By** - Find submissions from specific users
- **Forms** - Filter by specific forms
- **Status** - View by submission status
- **Terms** - Search within field data using `field name: search term`
- **Date Range** - Filter by submission date
- **Participants** - Find submissions involving specific users
- **Pending On** - See submissions awaiting action from specific users

### Using Filters
1. Click the filter icon in your submissions view
2. Select your filter criteria
3. Apply multiple filters to narrow results
4. Save frequently used filter combinations

### Saved Filters
Create reusable filters for common searches:
1. Set up your filter criteria
2. Click "Save Filter"
3. Name your filter
4. Access saved filters from the dropdown menu

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