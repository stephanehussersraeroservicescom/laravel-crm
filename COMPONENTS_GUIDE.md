# Livewire 3 Consistent Components Guide

## Overview
This guide shows how to use our new consistent Livewire components for table layouts. These components ensure consistent styling and behavior across all tables in the CRM.

## Available Components

### 1. TableContainer
Main wrapper for table pages
```blade
<x-table-container title="Airlines" max-width="max-w-7xl">
    <!-- Content -->
</x-table-container>
```

### 2. ManagementPanel
Consistent styling for add/edit forms
```blade
<x-management-panel :editing="$editing" entity-name="Airline">
    <form wire:submit.prevent="save">
        <!-- Form content -->
    </form>
</x-management-panel>
```

### 3. FormGrid
Responsive grid for form fields
```blade
<x-form-grid :cols="3">
    <!-- Form fields -->
</x-form-grid>
```

### 4. FormField
Consistent field styling with labels and help text
```blade
<x-form-field label="Airline Name" required help="Enter the full airline name">
    <input type="text" wire:model.live="name" 
           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
</x-form-field>
```

### 5. TableControls
Search and filter controls
```blade
<x-table-controls search-placeholder="Search airlines...">
    <!-- Additional controls -->
</x-table-controls>
```

### 6. TableBox
Wrapper for the actual table with consistent styling
```blade
<x-table-box>
    <table class="min-w-full divide-y divide-gray-200">
        <!-- Table content -->
    </table>
</x-table-box>
```

## Component Properties

### TableContainer
- `title`: Page title (string, optional)
- `responsive`: Enable responsive behavior (bool, default: true)
- `max-width`: Container max width (string, default: 'max-w-7xl')

### ManagementPanel
- `title`: Custom title (string, optional)
- `editing`: Whether in edit mode (bool, default: false)
- `entity-name`: Entity name for auto-generated titles (string, default: 'Item')

### FormGrid
- `cols`: Number of columns (int, default: 3)
- `gap`: Grid gap spacing (string, default: 'gap-4')
- `responsive`: Custom responsive classes (string, auto-generated from cols)

### FormField
- `label`: Field label (string, optional)
- `required`: Show required asterisk (bool, default: false)
- `help`: Help text below field (string, optional)

### TableControls
- `show-search`: Show search input (bool, default: true)
- `show-deleted`: Show "Show deleted" checkbox (bool, default: true)
- `search-placeholder`: Placeholder text (string, default: 'Search...')

### TableBox
- `responsive`: Enable responsive scrolling (bool, default: true)
- `shadow`: Shadow class (string, default: 'shadow-sm')

## Complete Example

```blade
<div>
    <x-table-container title="Airlines">
        <!-- Management Panel -->
        <x-management-panel :editing="$editing" entity-name="Airline">
            <form wire:submit.prevent="save">
                <x-form-grid :cols="4">
                    <x-form-field label="Airline Name" required>
                        <input type="text" wire:model.live="name" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </x-form-field>
                    
                    <x-form-field label="Region" required>
                        <select wire:model.live="region" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Region...</option>
                            <!-- Options -->
                        </select>
                    </x-form-field>
                    
                    <div class="flex flex-col justify-end">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2">
                            {{ $editing ? 'Update' : 'Add Airline' }}
                        </button>
                    </div>
                </x-form-grid>
            </form>
        </x-management-panel>
        
        <!-- Table Controls -->
        <x-table-controls search-placeholder="Search airlines...">
            <!-- Additional controls -->
        </x-table-controls>
        
        <!-- Table -->
        <x-table-box>
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Table content -->
            </table>
        </x-table-box>
    </x-table-container>
</div>
```

## Benefits

1. **Consistency**: All tables have the same look and feel
2. **Maintainability**: Changes to components affect all tables
3. **Responsive**: Built-in responsive behavior
4. **Accessibility**: Consistent form labels and structure
5. **DRY**: Don't repeat yourself - reuse components
6. **Flexibility**: Components accept slots and can be customized

## Migration Strategy

1. Keep existing tables working
2. Create new tables using components
3. Gradually refactor existing tables
4. Test thoroughly
5. Remove old styling once migrated

## Files Created

- `app/View/Components/TableContainer.php` & view
- `app/View/Components/ManagementPanel.php` & view  
- `app/View/Components/TableControls.php` & view
- `app/View/Components/TableBox.php` & view
- `app/View/Components/FormGrid.php` & view
- `app/View/Components/FormField.php` & view
- `resources/views/livewire/airlines-table-refactored.blade.php` (example)
