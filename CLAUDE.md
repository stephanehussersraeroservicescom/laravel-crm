## Development Memories

- good commit , push

## August 20, 2025 - Database Cleanup (Contract Pricing & Products)

### IMPORTANT: Contract Pricing Data Structure
- **ACTIVE TABLE:** `contract_prices` - This is the only contract pricing table in use
- **REMOVED:** `contract_pricing` table was obsolete and has been dropped
- **Entry Point:** Database Manager → Contract Prices (`/database-manager/contract-prices`)
- **Model:** `App\Models\ContractPrice`
- **Component:** `Livewire\DatabaseManager\ContractPriceTable`

### IMPORTANT: Products Data Structure  
- **ACTIVE TABLE:** `products` - This is the only products table in use
- **REMOVED:** `part_numbers` table was obsolete and has been dropped
- **REMOVED:** `product_templates` table was obsolete and has been dropped
- **Entry Point:** Database Manager → Products (`/database-manager/products`)
- **Model:** `App\Models\Product`
- **Component:** `Livewire\DatabaseManager\ProductTable`

### Why These Cleanups Were Done
1. **Contract Pricing:** Found duplicate tables (`contract_prices` vs `contract_pricing`)
   - `contract_pricing` was created but never implemented (no model, no UI)
   - `contract_prices` is fully implemented and actively used

2. **Products:** Found duplicate tables (`products` vs `part_numbers`)
   - `part_numbers` had no model, no UI, but had a foreign key from quote_lines
   - `products` is fully implemented with model, UI, and active usage
   - Removed unused `part_number_id` column from quote_lines table
   - Quote lines use `part_number` string field instead of foreign key

### Active Structure Summary
- **Products Management:** `products` table → Product model → Database Manager UI
- **Contract Pricing:** `contract_prices` table → ContractPrice model → Database Manager UI
- **Quote Lines:** Use string `part_number` field, not foreign key relationships

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