# MOE Chart Database - Usage Examples

This document provides examples of how to use the organizational structure database.

## Database Structure

The system uses three main layers:
1. **Organizational Units** - The structure (Ministry, Divisions, Sections, etc.)
2. **Positions** - Authority & reporting relationships
3. **Position Assignments** - People assigned to positions over time

## Basic Usage Examples

### Creating Organizational Units

```php
use App\Models\OrganizationUnit;

// Create the Ministry (root level)
$ministry = OrganizationUnit::create([
    'name' => 'Ministry of Education',
    'code' => 'MOE',
    'unit_type' => 'MINISTRY',
    'parent_id' => null,
    'level' => 1,
    'status' => 'ACTIVE',
]);

// Create Permanent Secretary Office
$psOffice = OrganizationUnit::create([
    'name' => 'Permanent Secretary Office',
    'code' => 'PS-OFFICE',
    'unit_type' => 'DIRECTORATE',
    'parent_id' => $ministry->id,
    'level' => 2,
    'status' => 'ACTIVE',
]);

// Create a Division
$basicEducation = OrganizationUnit::create([
    'name' => 'Basic Education Division',
    'code' => 'BED',
    'unit_type' => 'DIVISION',
    'parent_id' => $psOffice->id,
    'level' => 3,
    'status' => 'ACTIVE',
]);
```

### Creating Positions

```php
use App\Models\Position;

// Create Minister position
$ministerPosition = Position::create([
    'title' => 'Minister',
    'unit_id' => $ministry->id,
    'reports_to_position_id' => null, // Top level
    'is_head' => true,
    'status' => 'ACTIVE',
]);

// Create Permanent Secretary position
$psPosition = Position::create([
    'title' => 'Permanent Secretary',
    'unit_id' => $psOffice->id,
    'reports_to_position_id' => $ministerPosition->id,
    'is_head' => true,
    'status' => 'ACTIVE',
]);

// Create Director position
$directorPosition = Position::create([
    'title' => 'Director',
    'unit_id' => $basicEducation->id,
    'reports_to_position_id' => $psPosition->id,
    'is_head' => true,
    'status' => 'ACTIVE',
]);
```

### Creating Users/Employees

```php
use App\Models\User;

$user = User::create([
    'full_name' => 'John Doe',
    'name' => 'John Doe', // For Laravel auth compatibility
    'email' => 'john.doe@moe.go.tz',
    'phone' => '+255123456789',
    'employee_number' => 'MOE001',
    'password' => bcrypt('password'),
    'status' => 'ACTIVE',
]);
```

### Assigning Users to Positions

```php
use App\Models\PositionAssignment;

$assignment = PositionAssignment::create([
    'user_id' => $user->id,
    'position_id' => $directorPosition->id,
    'start_date' => now(),
    'end_date' => null, // Ongoing assignment
    'is_active' => true,
]);
```

### Querying Unit Head (For Approval Workflow)

```php
use App\Services\OrganizationService;

$orgService = new OrganizationService();

// Get the head of a unit
$unitHead = $orgService->getUnitHead($basicEducation->id);

if ($unitHead) {
    // This user can approve requests for this unit
    echo "Unit head: " . $unitHead->full_name;
}

// Check if a user is the head of a unit
$isHead = $orgService->isUnitHead($user->id, $basicEducation->id);
```

### Using Eloquent Relationships

```php
// Get all positions in a unit
$positions = $basicEducation->positions;

// Get the unit for a position
$unit = $directorPosition->unit;

// Get who a position reports to
$supervisor = $directorPosition->reportsTo;

// Get all subordinates of a position
$subordinates = $psPosition->subordinates;

// Get active assignments for a position
$activeAssignments = $directorPosition->activeAssignments;

// Get all position assignments for a user
$userAssignments = $user->positionAssignments;

// Get active position assignments for a user
$activeUserAssignments = $user->activePositionAssignments;

// Get organizational hierarchy
$hierarchy = $orgService->getUnitHierarchy($basicEducation->id);
// Returns: [Ministry, PS Office, Basic Education Division]
```

### Creating Advisory Bodies

```php
use App\Models\AdvisoryBody;

$advisoryCouncil = AdvisoryBody::create([
    'name' => 'National Education Advisory Council',
    'reports_to_position_id' => $ministerPosition->id,
]);
```

## Running Migrations

Before using the system, make sure to:

1. Create the database:
```sql
CREATE DATABASE moe_chart;
```

2. Update your `.env` file (already configured):
```
DB_CONNECTION=mysql
DB_DATABASE=moe_chart
DB_USERNAME=root
DB_PASSWORD=
```

3. Run migrations:
```bash
php artisan migrate
```

## Key Features

- **Hierarchical Structure**: Units can have parent-child relationships
- **Position Authority**: Positions define reporting relationships
- **Historical Tracking**: Position assignments track start/end dates
- **Flexible Head Queries**: Use `OrganizationService` to find unit heads dynamically
- **Status Management**: All entities support ACTIVE/INACTIVE status
