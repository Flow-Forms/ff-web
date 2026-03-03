---
title: Admin Options
order: 1
---

# Admin Options

The following options are available exclusively to users with administrator permissions. These appear under **{{icon:cog-6-tooth size-4 inline-block align-text-center}} (Options)** in the submission's header when you have admin-level access.

## {{icon:arrow-uturn-left size-4 inline-block align-text-center}} Undo Approved / Denied

***Use Case**- A submission was approved/denied by mistake and needs to be reversed.*

This is an emergency function to reverse terminal state (approved/denied) decisions. The last action is undone and the submission is reverted to a previous workflow step, allowing it to be processed again.

When selected, a modal will open with **Undo Submission Approved/Denied** at the top. The **Process History** is next, which lists the **Step, Action By, Action,** and **Date** of every step in the form's workflow as it occurred. Below that is **Revert to Step**, over a drop down box labeled "*Select a step to revert to.*" The dropdown box lists every step in the selected form's workflow. Click the step you would like the submission to return to and it will appear in the box.

Next, you have the **Reason for Undo** text box that asks you to *"Please provide a reason for undoing this submission."* Once you have typed in that information, your options are to select *Cancel* to exit without undoing, or **Undo and Revert** to proceed with sending the submission back to the step you selected. 
The bottom of the modal warns you, 
> "*This action will change the submission status and notify the approvers at the selected step. This action is logged in the process history."* 

Once you select the blue, **Undo and Revert** button, notifications will be sent, the status becomes "Pending" again, on the user you selected, and any steps that come after your selection will have to be redone and approved again.  

## {{icon:user-group size-6 inline-block align-text-center}} Transfer
***Use Case** - A submission is stuck/misrouted and needs emergency admin intervention.*

If a submission needs intervention to change the assigned user on a workflow step, you can make that designation using the **Transfer** option. Unlike [Reassign](submissions/submission_view/options#reassign), which only allows you to select users in the same group type as the original, **Transfer** allows you to select any users, including external ones.

The **Transfer Submission** modal prompts you to _Select users to transfer to_ with a dropdown containing all available users. To _**include external users**_ in the dropdown, activate the toggle switch with that label. Selected users will show a checkmark beside their information and appear listed in the dropdown.

The _**Send email notification(s)**_ toggle is enabled by default. Turn it off if you prefer not to notify the selected users. Click **Cancel** to exit without transferring the submission's action, or the blue **Transfer to Users** button to initiate the transfer. If notifications are enabled, they will be sent at this time.


## {{icon:swatch size-6 inline-block align-text-center}} Form Options


##  {{icon:queue-list size-6 inline-block align-text-center}} Edit Flow


## {{icon:pencil size-6 inline-block align-text-center}} Edit Form



