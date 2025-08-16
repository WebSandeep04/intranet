# Worklog System Documentation

## Overview

The Worklog System is a comprehensive time tracking solution that allows users to log their work hours against specific customers, projects, and modules. The system includes entry types (Full Day, Half Day, Leave) and ensures accurate time tracking with validation rules.

## Key Features

### 1. Entry Types
- **Full Day**: 8 working hours
- **Half Day**: 4 working hours  
- **Leave**: 0 working hours

### 2. Session Management
- Users can add multiple entries to a session before submitting
- Real-time validation of total time against selected entry type
- Prevents duplicate entries
- Session data persists until submission or manual clearing

### 3. Time Validation
- Total logged time must be at least equal to the selected entry type
- Users cannot submit if time is less than the entry type hours
- Users can exceed the entry type hours as much as needed
- Real-time feedback on time status (Incomplete/Complete)

### 4. Duplicate Prevention
- Checks for duplicate entries based on date, entry type, customer, project, module, user, and description
- Prevents both session duplicates and database duplicates

## System Architecture

### Database Tables

1. **entry_types** - Defines working hour types
   - `name` (Full Day, Half Day, Leave)
   - `working_hours` (8, 4, 0)
   - `description`
   - `tenant_id`

2. **worklogs** - Stores time tracking entries
   - `work_date` - Date of work
   - `entry_type_id` - Reference to entry type
   - `customer_id` - Reference to customer
   - `project_id` - Reference to project
   - `module_id` - Reference to module
   - `hours` - Hours worked
   - `minutes` - Minutes worked
   - `description` - Work description
   - `user_id` - User who logged the time
   - `tenant_id` - Multi-tenant support

### Unique Constraint
Prevents duplicate entries:
```sql
UNIQUE(work_date, entry_type_id, customer_id, project_id, module_id, user_id, description)
```

## User Workflow

### Step 1: Select Date and Entry Type
1. **Date**: Defaults to current date, user can change
2. **Entry Type**: Choose from Full Day (8h), Half Day (4h), or Leave (0h)

### Step 2: Select Customer and Project
1. **Customer**: Choose from available customers
2. **Project**: Automatically loads projects assigned to the selected customer
3. **Module**: Automatically loads modules based on selected project

### Step 3: Enter Time and Description
1. **Hours**: 0-24 hours
2. **Minutes**: 0-59 minutes
3. **Description**: Detailed work description (required)

### Step 4: Add to Session
- Entry is added to session (not saved to database yet)
- Form resets for next entry
- Session shows running total and status

### Step 5: Submit Worklog
- Only enabled when total time is at least equal to entry type
- All session entries saved to database in transaction
- Session cleared after successful submission

## Validation Rules

### Time Validation
- **Individual Entry**: Cannot be less than selected entry type hours
- **Total Session**: Must be at least equal to entry type hours (can exceed)
- **Submit Button**: Only enabled when time is at least equal to entry type

### Duplicate Prevention
- **Session Level**: Prevents adding same entry to session
- **Database Level**: Prevents saving duplicate entries
- **Check Fields**: Date, Entry Type, Customer, Project, Module, User, Description

### Required Fields
- Date
- Entry Type
- Customer
- Project
- Module
- Hours
- Minutes
- Description

## API Endpoints

### GET Routes
- `/worklog` - Main worklog page
- `/worklog/fetch` - Get user's worklog history
- `/worklog/entry-types` - Get available entry types
- `/worklog/customers` - Get available customers
- `/worklog/projects` - Get available projects
- `/worklog/modules/{projectId}` - Get modules for specific project
- `/worklog/session-entries` - Get current session entries

### POST Routes
- `/worklog/add-to-session` - Add entry to session
- `/worklog/remove-from-session` - Remove entry from session
- `/worklog/clear-session` - Clear all session entries
- `/worklog/submit` - Submit all session entries to database

### DELETE Routes
- `/worklog/{id}` - Delete specific worklog entry

## Session Management

### Session Key
```php
$sessionKey = 'worklog_entries_' . Auth::user()->id;
```

### Session Structure
```php
[
    'id' => 'unique_id',
    'work_date' => '2025-08-15',
    'entry_type_id' => 1,
    'entry_type_name' => 'Full Day',
    'customer_id' => 1,
    'customer_name' => 'Rajesh Kumar',
    'project_id' => 1,
    'project_name' => 'Web Development',
    'module_id' => 1,
    'module_name' => 'Frontend Development',
    'hours' => 4,
    'minutes' => 30,
    'description' => 'Worked on responsive design',
    'total_minutes' => 270
]
```

## Error Handling

### Common Error Messages
1. **Time Less Than Entry Type**: "Total time (3h 30m) cannot be less than Full Day working hours (8h)"
2. **Duplicate Entry**: "This entry already exists in the database."
3. **Incomplete Time**: "Total logged time (3h 0m) is less than Full Day working hours (8h). Please add more entries."

### Transaction Safety
- All session entries saved in single database transaction
- Rollback on any error
- Session cleared only after successful save

## User Interface Features

### Real-time Updates
- Session entries update immediately
- Time calculations update automatically
- Submit button state changes based on validation

### Visual Feedback
- **Green Badge**: Complete (time is at least equal to required)
- **Yellow Badge**: Incomplete (time less than required)

### Responsive Design
- Works on desktop and mobile devices
- Bootstrap-based responsive layout
- Clean, intuitive interface

## Security Features

### Authentication
- All routes protected with authentication middleware
- User can only access their own worklog entries

### Multi-tenancy
- All data scoped to user's tenant
- Tenant isolation maintained throughout

### CSRF Protection
- All forms include CSRF tokens
- AJAX requests include CSRF protection

## Usage Examples

### Example 1: Full Day Worklog
1. Select Date: 2025-08-15
2. Select Entry Type: Full Day (8h)
3. Add Entries:
   - Customer: Rajesh Kumar, Project: Web Development, Module: Frontend, Time: 4h 0m
   - Customer: Rajesh Kumar, Project: Web Development, Module: Backend, Time: 4h 30m
4. Total: 8h 30m (at least 8h, can exceed)
5. Submit: Success

### Example 2: Half Day Worklog
1. Select Date: 2025-08-16
2. Select Entry Type: Half Day (4h)
3. Add Entries:
   - Customer: Priya Sharma, Project: Digital Marketing, Module: SEO, Time: 2h 30m
   - Customer: Priya Sharma, Project: Digital Marketing, Module: Content, Time: 2h 0m
4. Total: 4h 30m (at least 4h, can exceed)
5. Submit: Success

### Example 3: Leave Day
1. Select Date: 2025-08-17
2. Select Entry Type: Leave (0h)
3. No entries needed
4. Submit: Success

## Benefits

1. **Accurate Time Tracking** - Ensures logged time matches expected hours
2. **Duplicate Prevention** - Prevents accidental duplicate entries
3. **Session Management** - Allows multiple entries before submission
4. **Real-time Validation** - Immediate feedback on time status
5. **User-friendly Interface** - Intuitive design with clear feedback
6. **Multi-tenant Support** - Isolated data per tenant
7. **Transaction Safety** - All-or-nothing submission process

## Future Enhancements

1. **Time Tracking** - Real-time timer functionality
2. **Reporting** - Generate time reports by date range, customer, project
3. **Approval Workflow** - Manager approval for submitted worklogs
4. **Export Features** - Export to Excel, PDF
5. **Bulk Operations** - Bulk edit, delete, approve
6. **Notifications** - Email notifications for approvals, reminders
7. **Mobile App** - Native mobile application
8. **Integration** - Integration with project management tools
