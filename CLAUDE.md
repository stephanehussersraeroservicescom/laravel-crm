## Development Memories

- good commit , push

## August 20, 2025 - Contract Pricing Cleanup

### IMPORTANT: Contract Pricing Data Structure
- **ACTIVE TABLE:** `contract_prices` - This is the only contract pricing table in use
- **REMOVED:** `contract_pricing` table was obsolete and has been dropped
- **Entry Point:** Database Manager → Contract Prices (`/database-manager/contract-prices`)
- **Model:** `App\Models\ContractPrice`
- **Component:** `Livewire\DatabaseManager\ContractPriceTable`

### Why This Cleanup Was Done
- Found two tables: `contract_prices` and `contract_pricing`
- `contract_pricing` was created via migration but never implemented (no model, no UI, no references)
- `contract_prices` is fully implemented with model, UI, and active usage in quotes
- Removed confusion by dropping the unused table

### Contract Prices Structure (Active)
- Uses `customer_identifier` (string) instead of foreign key for flexibility
- Supports pricing by: part number, root code, airline, or combinations
- Prices stored in cents (integer) for precision
- Date range validation with `valid_from` and `valid_to`
- Used by quote forms for automatic pricing lookups

## August 18, 2025 - PDF and Database Enhancements

### PDF Table Improvements
- Added UOM (Unit of Measure) column to PDF table with separate display from quantity
- Removed product family header rows for cleaner table layout
- Reduced table font sizes from 10pt to 9pt and padding from 8px to 6px
- Adjusted column widths to accommodate new UOM column

### Payment Terms Migration
- Moved payment terms from quote level to customer level
- Set "Pro Forma" as default payment terms instead of "Net 30"
- Updated PDF to display customer payment terms

### Product Descriptions Restructuring
- Grouped product descriptions by certification requirements instead of individual products
- Products with same certification now display together with product names above shared description
- Reduced font sizes and spacing throughout PDF for more compact layout

### Salesperson Code Implementation
- Added salesperson_code field to both users and quotes tables
- Created proper salesperson records: Stephane (SFH), Dominic (DD), Jason (JE)
- Updated quote seeder to assign salesperson codes directly to quotes
- Modified PDF template to show "Quotation SFH0001" format with proper salesperson codes
- Implemented fallback system for salesperson code display

### Database Schema Updates
- Added UOM column to product_classes table (enum: 'LY', 'UNIT', default 'LY')
- Added salesperson_code column to users table
- Added salesperson_code column to quotes table for data integrity
- Updated all models with new fillable fields

### Seeder Improvements
- Updated ProductCatalogSeeder to include UOM values
- Enhanced RealQuoteSeeder with proper salesperson assignments
- Fixed airline seeder to match current table schema (removed code field, added region)
- Created realistic customer payment terms assignments

### PDF Layout Optimization
- Reduced spacing between header elements
- Optimized font sizes: product descriptions (9pt), terms (9pt), footer (8pt)
- Maintained readability while achieving more compact layout
- Integrated quotation title with salesperson code for professional appearance

All changes tested with fresh migration and seeding. System now properly displays different salesperson codes across quotes and maintains data integrity at the quote level.