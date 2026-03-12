---
title: Bulk Actions Toolbar
order: 6

---
# Bulk Actions Toolbar

On the **{{icon:paper-clip size-4 inline-block align-text-center}} Submissions** page, the **Bulk Actions Toolbar** is situated at the bottom of the Control Panel, below the search bar and **{{icon:document-duplicate size-4 inline-block align-text-center}} Forms** quick-filter. On the left end is a checkbox that allows you to select all submissions displayed on your page. If no submissions are selected, the right end will say *select submission(s) to access bulk actions*. 

When at least one submission's checkbox is selected, the right end changes to **{{icon:bolt size-4 inline-block align-text-center}} Actions**. If you have multiple submissions pending your action, and would like to have the same action apply to all of them, click **{{icon:bolt size-4 inline-block align-text-center}} Actions**. A **Bulk Actions** modal appears with a **Select Action** dropdown below it. Depending on the submissions you select, the common corresponding actions available for those submissions will appear in the dropdown. If the action you are looking for is not displaying there, it is because at least one of the submissions you have selected is not eligible for that action. 

Once you have chosen the action that you want, it will appear in the dropdown. The number of submissions the action will apply to is reflected in the blue button you select to proceed (e.g. "**Apply to # Submissions**"). Click **Cancel** or the "X" to exit the modal without taking the action.


## Bulk PDF Print Feature

## Overview

The Bulk PDF Print feature allows users to select multiple submissions and generate a single combined PDF document. This feature includes real-time progress tracking, email notifications, and a streamlined user interface.

## User Guide

### Selecting Submissions

1. Navigate to the Submissions list page
2. Use the checkboxes next to each submission to select individual items
3. Use the "Select All" checkbox to select all submissions on the current page
4. The selection count is displayed in a badge (e.g., "5 selected")

### Printing Selected Submissions

1. Click the [**Print**](Submissions/Export_Print#Print) button on the right end, above the Submissions Toolbar
2. If you have submissions selected, a modal window will appear that lists the number of submissions you have selected to print with a "Cancel" or "{{icon:printer size-4 inline-block align-text-center}} Start Print Job" button. "Recent Print Jobs" (if any) are also listed here.
3. If no submissions are selected, the modal will just show your recent print jobs. 

### Progress Tracking

When a print job starts:
- A modal displays real-time progress
- You'll see a progress bar showing X of Y submissions processed
- The modal can be closed to work in the background
- The job continues processing on the server

### Download Options

Once complete:
- You'll receive an email notification with a download link
- The notification appears in your notification center
- Download links are valid for 7 days
- Click "Download PDF" in the progress modal

### Print Job States

- **Pending**: Job created but not yet started
- **Processing**: PDFs are being generated and combined
- **Completed**: PDF is ready for download
- **Failed**: An error occurred during processing

### Bulk Actions
Select multiple submissions to:
- Export to CSV
- Update status in bulk
- Assign to users
- Apply tags or categories

## Best Practices

- Use filters to create custom workflows
- Save commonly used filters
- Regularly export data for backup
- Set up group permissions appropriately
- Use bulk actions to save time



## Technical Implementation

### Architecture

The feature follows a queue-based architecture:

```
User Selection → Print Job → Queue → Individual PDFs → Merge → Final PDF
                    ↓                                              ↓
                Database                                    Notification
```

### Components

#### Frontend Components

1. **SubmissionsList** (`app/Livewire/SubmissionsList.php`)
   - Manages submission selection state
   - Handles bulk actions UI

2. **PrintProgress** (`app/Livewire/PrintProgress.php`)
   - Displays progress modal
   - Polls for status updates every 5 seconds
   - Handles job creation and monitoring

3. **submission-card** (`resources/views/components/submission-card.blade.php`)
   - Individual submission cards with checkboxes
   - Integrated with Flux UI checkbox groups

#### Backend Components

1. **PrintJob Model** (`app/Models/PrintJob.php`)
   - Tracks print job status and progress
   - Multi-tenant aware
   - Progress calculation methods

2. **PrintSubmissionsJob** (`app/Jobs/PrintSubmissionsJob.php`)
   - Processes submissions in chunks of 25
   - Generates individual PDFs using `SubmissionPrintTrait`
   - Handles errors gracefully

3. **CombinePrintedPDFsJob** (`app/Jobs/CombinePrintedPDFsJob.php`)
   - Merges individual PDFs using `libmergepdf`
   - Cleans up temporary files
   - Sends completion notification

4. **PrintCompleteNotification** (`app/Notifications/PrintCompleteNotification.php`)
   - Email notification with download link
   - Database notification for tracking

### Database Schema

```sql
CREATE TABLE print_jobs (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    status VARCHAR(255) DEFAULT 'pending',
    total_submissions INTEGER DEFAULT 0,
    processed_submissions INTEGER DEFAULT 0,
    file_path VARCHAR(255) NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status)
);
```

### Queue Configuration

Jobs run on the `exports` queue:
- PrintSubmissionsJob
- CombinePrintedPDFsJob
- PrintCompleteNotification

Ensure your queue workers are running:
```bash
php artisan queue:work --queue=exports
```

### File Storage

- Temporary PDFs: `storage/app/exports/temp/print/`
- Final PDFs: `storage/app/exports/pdf/`
- Files use ULID naming for uniqueness
- Temporary files are cleaned up after merging

## Configuration

### Memory Limits

The feature processes PDFs in chunks to manage memory:
- Default chunk size: 25 submissions
- Configurable in `PrintSubmissionsJob::$chunkSize`

### Storage Disk

Uses the `exports` disk defined in `config/filesystems.php`:
```php
'exports' => [
    'driver' => 'local',
    'root' => storage_path('app/exports'),
    'url' => env('APP_URL').'/storage/exports',
    'visibility' => 'private',
],
```

### PDF Generation

Uses existing `SubmissionPrintTrait` which supports:
- Blueprint-based PDF generation
- Blade template fallback
- Anonymous blueprint support

## Error Handling

### Individual PDF Failures
- Logged but don't stop the entire job
- Other submissions continue processing
- Failed submissions are skipped

### Complete Job Failure
- Status set to 'failed'
- Temporary files cleaned up
- User can retry with new selection

### Queue Failures
- Standard Laravel queue retry logic applies
- Failed jobs logged to `failed_jobs` table

## Security

- User can only access their own print jobs
- Temporary download URLs expire after 7 days
- Multi-tenant isolation enforced
- File paths use ULIDs to prevent guessing

## Performance Considerations

1. **Chunked Processing**: Prevents memory exhaustion
2. **Queue-based**: Non-blocking UI experience
3. **Progress Updates**: Database writes throttled per chunk
4. **Temporary Files**: Cleaned up immediately after merge

## Testing

The feature includes comprehensive test coverage:

### Unit Tests
- `tests/Feature/BulkPrintTest.php` - Selection and job creation
- `tests/Feature/Livewire/PrintProgressTest.php` - Progress tracking

### Test Scenarios
- Submission selection/deselection
- Select all functionality
- Print job creation
- Progress modal display
- User isolation
- Empty selection handling

Run tests:
```bash
php artisan test tests/Feature/BulkPrintTest.php
php artisan test tests/Feature/Livewire/PrintProgressTest.php
```

## Troubleshooting

### Print job stuck in "processing"
1. Check queue workers are running
2. Check Laravel logs for errors
3. Verify exports disk has write permissions

### PDFs not generating
1. Check SubmissionPrintTrait compatibility
2. Verify blueprint templates exist
3. Check memory limits in PHP configuration

### Download links not working
1. Verify exports disk configuration
2. Check S3/storage permissions
3. Confirm temporary URL generation

## Future Enhancements

1. **Batch Templates**: Apply different templates per submission type
2. **Cover Pages**: Add summary/index pages
3. **Compression**: ZIP option for very large jobs
4. **Scheduling**: Queue priority based on job size
5. **Partial Downloads**: Stream large files in chunks