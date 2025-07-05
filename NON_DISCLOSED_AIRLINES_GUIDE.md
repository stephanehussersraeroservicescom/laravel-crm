# 🔒 Non-Disclosed Airlines Implementation Guide

## 📋 Overview

Your aircraft interior CRM now supports **confidential airline projects** where the airline identity must remain undisclosed until authorization is given. This is critical in the aviation industry where:

- Early-stage projects require confidentiality
- Competitive bidding situations exist
- NDAs restrict airline identity disclosure
- Projects are in preliminary discussion phases

## 🗂️ **Updated Database Relationships with Confidential Support**

### **Enhanced Project → Airline Relationship**

```
Projects
├── airline_id (nullable) ────── Airlines
├── airline_disclosed (boolean)
├── airline_code_placeholder (string, nullable)
├── confidentiality_notes (text, nullable)
├── airline_disclosed_at (timestamp, nullable)
└── disclosed_by (user_id, nullable)
```

**Business Logic:**
- `airline_disclosed = false` → Project uses confidential airline
- `airline_disclosed = true` → Project has real airline assigned
- When disclosed, audit trail captures who disclosed and when

### **Special "Non-Disclosed" Airline Entity**

```sql
-- Default confidential airline entry
INSERT INTO airlines (name, code, region, comment) VALUES (
    'Non-Disclosed Airline',
    'CONFIDENTIAL', 
    'North America',
    'Default entry for projects where airline identity is confidential'
);
```

## 🔄 **Project Lifecycle with Confidential Airlines**

### **Phase 1: Confidential Project Creation**
```php
// Create confidential project
$project = Project::create([
    'name' => 'Premium Cabin Interior Project',
    'airline_id' => $confidentialAirline->id,
    'airline_disclosed' => false,
    'airline_code_placeholder' => 'MAJOR-US-001',
    'confidentiality_notes' => 'Large US carrier, NDA expires Dec 2025',
]);
```

### **Phase 2: Project Management (Confidential)**
- Project appears as "MAJOR-US-001 (Confidential)" in lists
- Only authorized users can see disclosure option
- All related opportunities inherit confidential status
- Audit trail tracks all access

### **Phase 3: Airline Disclosure**
```php
// When authorized to disclose
$project->discloseAirline($realAirlineId, $userId);

// Automatic audit trail entry created
Action::create([
    'actionable_id' => $project->id,
    'actionable_type' => Project::class,
    'title' => 'Airline Disclosed',
    'description' => 'Airline disclosed as Delta Air Lines (DAL)',
    'created_by' => $userId,
]);
```

## 💻 **Updated Model Methods**

### **Project Model Enhancements**

```php
// Check if airline is disclosed
$project->isAirlineDisclosed(); // returns boolean

// Get display name for UI
$project->display_airline; // "Delta Air Lines" or "MAJOR-US-001 (Confidential)"

// Get display code for UI  
$project->display_airline_code; // "DAL" or "CONF-001"

// Disclose airline (with audit trail)
$project->discloseAirline($airlineId, $userId);

// Mark as confidential
$project->markAsConfidential($placeholderCode, $notes);

// Query scopes
Project::disclosed()->get(); // Only disclosed projects
Project::nonDisclosed()->get(); // Only confidential projects
Project::confidential()->get(); // Projects using CONFIDENTIAL airline
```

### **Enhanced Relationships**

```php
// Projects can filter by disclosure status
Project::with(['airline', 'disclosedByUser'])
    ->disclosed()
    ->where('created_at', '>=', $date)
    ->get();

// Opportunities inherit project confidentiality
$opportunity->project->isAirlineDisclosed();
$opportunity->project->display_airline;
```

## 🎨 **UI/UX Implementation**

### **Project List View**
```blade
<!-- Display airline with confidentiality indicator -->
<td class="px-6 py-4">
    @if($project->isAirlineDisclosed())
        <span class="text-green-600 font-medium">{{ $project->airline->name }}</span>
        <span class="text-xs text-gray-500">({{ $project->airline->code }})</span>
    @else
        <span class="text-orange-600 font-medium">{{ $project->display_airline }}</span>
        <span class="text-xs text-orange-500">({{ $project->display_airline_code }})</span>
        @can('update', $project)
            <button wire:click="openDisclosureModal({{ $project->id }})" 
                    class="ml-2 text-blue-500 hover:text-blue-700 text-xs">
                Disclose
            </button>
        @endcan
    @endif
</td>
```

### **Project Creation Form**
```blade
<!-- Confidentiality toggle -->
<div class="mb-4">
    <label class="flex items-center">
        <input type="checkbox" wire:model="isConfidential" class="mr-2">
        <span class="text-sm font-medium">This is a confidential project (airline not disclosed)</span>
    </label>
</div>

@if($isConfidential)
    <!-- Confidential project fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Airline Code Placeholder
            </label>
            <input wire:model="airlineCodePlaceholder" type="text" 
                   placeholder="e.g., MAJOR-EU-001"
                   class="block w-full px-3 py-2 border border-gray-300 rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Confidentiality Notes
            </label>
            <textarea wire:model="confidentialityNotes" rows="3"
                      placeholder="Internal notes about confidentiality requirements"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
        </div>
    </div>
@else
    <!-- Regular airline selection -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Airline</label>
        <select wire:model="selectedAirline" class="block w-full px-3 py-2 border border-gray-300 rounded-md">
            <option value="">Select Airline</option>
            @foreach($airlines as $airline)
                <option value="{{ $airline->id }}">{{ $airline->name }} ({{ $airline->code }})</option>
            @endforeach
        </select>
    </div>
@endif
```

### **Disclosure Modal**
```blade
@if($showDisclosureModal && $selectedProject)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Modal content -->
        <div class="bg-white rounded-lg p-6">
            <h3 class="text-lg font-medium mb-4">Disclose Airline for Project</h3>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    Current: <strong>{{ $selectedProject->display_airline }}</strong>
                </p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Select Actual Airline
                </label>
                <select wire:model="newAirlineId" class="block w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Choose airline...</option>
                    @foreach($disclosableAirlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }} ({{ $airline->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Disclosure Reason
                </label>
                <textarea wire:model="disclosureReason" rows="3"
                          placeholder="Why is the airline being disclosed now?"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button wire:click="closeDisclosureModal()" class="px-4 py-2 border border-gray-300 rounded-md">
                    Cancel
                </button>
                <button wire:click="discloseAirline()" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                    Disclose Airline
                </button>
            </div>
        </div>
    </div>
@endif
```

## 🔍 **Search and Filtering**

### **Enhanced Search Capabilities**
```php
// Search includes placeholder codes
Project::where(function ($query) use ($search) {
    $query->where('name', 'like', "%{$search}%")
          ->orWhere('airline_code_placeholder', 'like', "%{$search}%")
          ->orWhereHas('airline', function ($q) use ($search) {
              $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
          });
});
```

### **Disclosure Status Filters**
```blade
<!-- Filter dropdown -->
<select wire:model="filterAirlineDisclosed" class="block w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">All Projects</option>
    <option value="disclosed">Disclosed Airlines</option>
    <option value="confidential">Confidential Projects</option>
</select>
```

## 🔒 **Security and Compliance**

### **Access Control**
- Only users with `update_projects` permission can disclose airlines
- Disclosure actions are logged with user ID and timestamp
- Confidential projects have visual indicators in all views
- API responses respect disclosure status

### **Audit Trail**
```php
// Automatic audit entries for disclosure
Action::create([
    'actionable_id' => $project->id,
    'actionable_type' => Project::class,
    'title' => 'Airline Disclosed',
    'description' => "Airline disclosed as {$airline->name} ({$airline->code}). Reason: {$reason}",
    'created_by' => $userId,
    'completed_at' => now(),
]);
```

### **Data Protection**
- Placeholder codes are searchable but don't reveal identity
- Confidentiality notes are encrypted if sensitive
- Export functions respect disclosure status
- API responses filter based on user permissions

## 📊 **Reporting and Analytics**

### **Disclosure Metrics**
```php
// Project disclosure statistics
$stats = [
    'total_projects' => Project::count(),
    'disclosed_projects' => Project::disclosed()->count(),
    'confidential_projects' => Project::nonDisclosed()->count(),
    'avg_disclosure_time' => Project::whereNotNull('airline_disclosed_at')
        ->selectRaw('AVG(DATEDIFF(airline_disclosed_at, created_at)) as avg_days')
        ->value('avg_days'),
];
```

### **Pipeline Reports**
- Separate confidential and disclosed project pipelines
- Opportunity tracking respects project confidentiality
- Revenue projections can include/exclude confidential projects

## 🚀 **Implementation Steps**

### **1. Database Setup**
```bash
# Run the migration
php artisan migrate

# Verify the confidential airline exists
php artisan tinker
>>> App\Models\Airline::where('code', 'CONFIDENTIAL')->first()
```

### **2. Update Existing Projects (if needed)**
```php
// Mark existing projects as disclosed (one-time script)
Project::whereNotNull('airline_id')
    ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('airlines')
              ->whereRaw('airlines.id = projects.airline_id')
              ->where('airlines.code', 'CONFIDENTIAL');
    })
    ->update(['airline_disclosed' => true]);
```

### **3. UI Implementation**
- Update project table views to show disclosure status
- Add confidentiality toggle to project creation forms
- Implement disclosure modal for authorized users
- Update search to include placeholder codes

### **4. Testing**
```php
// Test confidential project creation
$project = Project::create([
    'name' => 'Test Confidential Project',
    'airline_id' => Airline::where('code', 'CONFIDENTIAL')->first()->id,
    'airline_disclosed' => false,
    'airline_code_placeholder' => 'TEST-001',
]);

// Test disclosure
$project->discloseAirline($realAirlineId, $userId);
assert($project->fresh()->isAirlineDisclosed());
```

## 🎯 **Benefits**

1. **Industry Compliance**: Meets aviation industry confidentiality requirements
2. **Flexible Workflow**: Projects can start confidential and be disclosed later
3. **Audit Trail**: Complete tracking of disclosure decisions and timing
4. **Search Capability**: Can find projects by placeholder codes
5. **Security**: Role-based access to disclosure functionality
6. **Transparency**: Clear visual indicators of confidential status

This implementation allows your CRM to handle the complex confidentiality requirements common in aircraft interior projects while maintaining full audit trails and security compliance.