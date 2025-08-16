# Tenant Management System

## Overview
The Tenant Management System allows Super Admins to create, manage, and generate tenant codes for the Lead Management application.

## Features

### 1. Add New Tenants
- Super Admins can create new tenants by providing a tenant name
- Tenant codes are automatically generated in the format: `TEN-XXXXXX` (where XXXXXX is a random 6-character string)
- Duplicate tenant names are not allowed

### 2. View Tenant List
- Display all tenants in a table format
- Shows tenant name, tenant code, and creation date
- Sorted by creation date (newest first)

### 3. Edit Tenants
- Update tenant names
- Maintains unique tenant name validation
- Modal-based editing interface

### 4. Delete Tenants
- Remove tenants from the system
- Confirmation dialog before deletion

### 5. Regenerate Tenant Codes
- Generate new tenant codes for existing tenants
- Useful for security purposes or code rotation
- Confirmation dialog before regeneration

## Access Control
- Only users with `role_id = 3` (Super Admin) can access tenant management
- Uses `SuperAdminMiddleware` for proper authorization
- Middleware applied at route level (Laravel 12 compatible)
- Unauthorized access attempts will result in a 403 error
- JSON responses for API requests, regular error pages for web requests

## Routes

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/tenant` | Tenant management page |
| GET | `/tenant/fetch` | Fetch all tenants (JSON) |
| POST | `/tenant/store` | Create new tenant |
| PUT | `/tenant/{id}` | Update tenant |
| DELETE | `/tenant/{id}` | Delete tenant |
| POST | `/tenant/{id}/regenerate-code` | Regenerate tenant code |

## Database Schema

### Tenants Table
- `id` - Primary key
- `tenant_name` - Unique tenant name
- `tenant_code` - Auto-generated unique code
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## Usage

### For Super Admins:
1. Navigate to the Super Admin dashboard
2. Click on "Super Admin" in the sidebar
3. Select "Tenant Management"
4. Use the form to add new tenants
5. Use the table actions to edit, delete, or regenerate codes

### API Usage:
All tenant operations are available via AJAX calls for seamless user experience.

## Security Features
- CSRF protection on all forms
- Input validation and sanitization
- Role-based access control
- Unique tenant name enforcement
- Secure tenant code generation

## Sample Tenant Codes
- `TEN-ABC123`
- `TEN-XYZ789`
- `TEN-DEF456`

## Error Handling
- Validation errors are displayed inline
- Success/error messages are shown as alerts
- Network errors are handled gracefully
- Confirmation dialogs for destructive actions
