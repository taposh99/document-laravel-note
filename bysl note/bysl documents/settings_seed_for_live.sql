-- ============================================================================
-- Settings tables seed script (truncate + 5 meaningful rows each)
-- ============================================================================
-- IMPORTANT before running on a server:
--   1) BACKUP the database first (mysqldump). This TRUNCATEs 21 tables — irreversible.
--   2) This script auto-detects an existing company_id, user_id, warehouse ids,
--      location ids and item ids from the target DB (via subqueries below), so the
--      inserted rows link to whatever real data already exists on that server.
--   3) Pre-requirements on the target DB for this to work without errors:
--        - at least 1 row in `companies`
--        - at least 1 row in `users`
--        - at least 1 row in `wh_warehouses_warehouses` (ideally 3, else all
--          warehouse_id fields fall back to the same single warehouse)
--        - at least 1 row in `wh_warehouses_locations` (ideally 5, else it
--          reuses whatever locations exist, repeating if necessary)
--        - at least 1 row in `wh_items_items` (ideally 3, else it reuses items)
--      If any of these tables are completely EMPTY on the target server, the
--      corresponding NOT NULL foreign columns below will fail to insert —
--      check the "Pre-flight check" SELECT at the very top of this script first.
--   4) `wh_settings_transaction_types` is special: TO / PO / WR are mandatory
--      system-generated rows hardcoded in ItemCheckOutService (short_code==='WR')
--      and re-created by TransactionTypeSeeder. This script inserts ONLY those
--      3 rows (not 5) so it never conflicts with that hardcoded logic.
-- ============================================================================

-- Pre-flight check — run this SELECT first and make sure none of these are 0/NULL
-- before running the rest of the script:
-- SELECT
--   (SELECT COUNT(*) FROM companies) AS companies_count,
--   (SELECT COUNT(*) FROM users) AS users_count,
--   (SELECT COUNT(*) FROM wh_warehouses_warehouses WHERE deleted_at IS NULL) AS warehouses_count,
--   (SELECT COUNT(*) FROM wh_warehouses_locations WHERE deleted_at IS NULL) AS locations_count,
--   (SELECT COUNT(*) FROM wh_items_items WHERE deleted_at IS NULL) AS items_count;

SET FOREIGN_KEY_CHECKS = 0;

-- Resolve real reference ids that already exist on THIS server
SET @company_id = (SELECT id FROM companies ORDER BY created_at LIMIT 1);
SET @user_id     = (SELECT id FROM users ORDER BY created_at LIMIT 1);

SET @wh_a = (SELECT id FROM wh_warehouses_warehouses WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 0);
SET @wh_b = (SELECT COALESCE(
                (SELECT id FROM wh_warehouses_warehouses WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 1),
                @wh_a));
SET @wh_c = (SELECT COALESCE(
                (SELECT id FROM wh_warehouses_warehouses WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 2),
                @wh_a));

SET @loc_rack01  = (SELECT id FROM wh_warehouses_locations WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 0);
SET @loc_shelf01 = (SELECT COALESCE((SELECT id FROM wh_warehouses_locations WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 1), @loc_rack01));
SET @loc_dock01  = (SELECT COALESCE((SELECT id FROM wh_warehouses_locations WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 2), @loc_rack01));
SET @loc_zone01  = (SELECT COALESCE((SELECT id FROM wh_warehouses_locations WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 3), @loc_rack01));
SET @loc_wh_a    = (SELECT COALESCE((SELECT id FROM wh_warehouses_locations WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 4), @loc_rack01));

SET @item1 = (SELECT id FROM wh_items_items WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 0);
SET @item2 = (SELECT COALESCE((SELECT id FROM wh_items_items WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 1), @item1));
SET @item3 = (SELECT COALESCE((SELECT id FROM wh_items_items WHERE deleted_at IS NULL ORDER BY created_at LIMIT 1 OFFSET 2), @item1));

SET @now = NOW();

-- =========================================================
-- 1) Item Categories
-- =========================================================
TRUNCATE TABLE wh_settings_item_categories;
SET @cat1 = UUID(); SET @cat2 = UUID(); SET @cat3 = UUID(); SET @cat4 = UUID(); SET @cat5 = UUID();
INSERT INTO wh_settings_item_categories (id, company_id, user_id, name, category_code, description, parent_category_id, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@cat1, @company_id, @user_id, 'Electronics', 'ELEC-CAT', 'Electronic devices and accessories', NULL, 'electronics', 1, 1, @user_id, @user_id, @now, @now),
(@cat2, @company_id, @user_id, 'Apparel', 'APRL-CAT', 'Clothing and wearable items', NULL, 'apparel', 1, 1, @user_id, @user_id, @now, @now),
(@cat3, @company_id, @user_id, 'Food & Beverage', 'FNB-CAT', 'Consumable food and drink items', NULL, 'food-beverage', 1, 1, @user_id, @user_id, @now, @now),
(@cat4, @company_id, @user_id, 'Furniture', 'FURN-CAT', 'Office and home furniture', NULL, 'furniture', 1, 1, @user_id, @user_id, @now, @now),
(@cat5, @company_id, @user_id, 'Stationery', 'STAT-CAT', 'Office and school stationery supplies', NULL, 'stationery', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 2) Item Types
-- =========================================================
TRUNCATE TABLE wh_settings_item_types;
SET @itype1 = UUID(); SET @itype2 = UUID(); SET @itype3 = UUID(); SET @itype4 = UUID(); SET @itype5 = UUID();
INSERT INTO wh_settings_item_types (id, company_id, user_id, name, description, stock_adjustment, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@itype1, @company_id, @user_id, 'Raw Material', 'Unprocessed material used in production', 'no', 'raw-material', 1, 1, @user_id, @user_id, @now, @now),
(@itype2, @company_id, @user_id, 'Finished Goods', 'Completed products ready for sale', 'yes', 'finished-goods', 1, 1, @user_id, @user_id, @now, @now),
(@itype3, @company_id, @user_id, 'Consumable', 'Items consumed during operations', 'no', 'consumable', 1, 1, @user_id, @user_id, @now, @now),
(@itype4, @company_id, @user_id, 'Packaging Material', 'Material used for packing goods', 'no', 'packaging-material', 1, 1, @user_id, @user_id, @now, @now),
(@itype5, @company_id, @user_id, 'Spare Parts', 'Replacement parts for machinery', 'yes', 'spare-parts', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 3) Item Brands
-- =========================================================
TRUNCATE TABLE wh_settings_item_brands;
SET @brand1 = UUID(); SET @brand2 = UUID(); SET @brand3 = UUID(); SET @brand4 = UUID(); SET @brand5 = UUID();
INSERT INTO wh_settings_item_brands (id, company_id, user_id, name, short_code, description, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@brand1, @company_id, @user_id, 'Samsung', 'SNG', 'Electronics and appliances brand', 'samsung', 1, 1, @user_id, @user_id, @now, @now),
(@brand2, @company_id, @user_id, 'Sony', 'SNY', 'Consumer electronics brand', 'sony', 1, 1, @user_id, @user_id, @now, @now),
(@brand3, @company_id, @user_id, 'LG', 'LG', 'Home appliances and electronics brand', 'lg', 1, 1, @user_id, @user_id, @now, @now),
(@brand4, @company_id, @user_id, 'Philips', 'PHL', 'Lighting and electronics brand', 'philips', 1, 1, @user_id, @user_id, @now, @now),
(@brand5, @company_id, @user_id, 'Panasonic', 'PNS', 'Electronics and appliances brand', 'panasonic', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 4) Item Tags
-- =========================================================
TRUNCATE TABLE wh_setting_item_tags;
INSERT INTO wh_setting_item_tags (id, company_id, name, description, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, 'Fragile', 'Handle with care, breakable items', 'fragile', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Perishable', 'Items with limited shelf life', 'perishable', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Hazardous', 'Items requiring special handling', 'hazardous', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Fast-Moving', 'High turnover items', 'fast-moving', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Seasonal', 'Items with seasonal demand', 'seasonal', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 5) Item Attributes + Attribute Details
-- =========================================================
TRUNCATE TABLE wh_settings_item_attribute_details;
TRUNCATE TABLE wh_settings_item_attributes;
SET @attr1 = UUID(); SET @attr2 = UUID(); SET @attr3 = UUID(); SET @attr4 = UUID(); SET @attr5 = UUID();
INSERT INTO wh_settings_item_attributes (id, company_id, user_id, name, attribute_short_code, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@attr1, @company_id, @user_id, 'Color', 'COL', 'color', 1, 1, @user_id, @user_id, @now, @now),
(@attr2, @company_id, @user_id, 'Size', 'SIZ', 'size', 1, 1, @user_id, @user_id, @now, @now),
(@attr3, @company_id, @user_id, 'Material', 'MAT', 'material', 1, 1, @user_id, @user_id, @now, @now),
(@attr4, @company_id, @user_id, 'Weight', 'WGT', 'weight', 1, 1, @user_id, @user_id, @now, @now),
(@attr5, @company_id, @user_id, 'Style', 'STY', 'style', 1, 1, @user_id, @user_id, @now, @now);

INSERT INTO wh_settings_item_attribute_details (id, item_attribute_id, attribute_value, short_code, color, photo, status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @attr1, 'Red', 'RED', '#FF0000', '', 1, @user_id, @user_id, @now, @now),
(UUID(), @attr1, 'Blue', 'BLU', '#0000FF', '', 1, @user_id, @user_id, @now, @now),
(UUID(), @attr2, 'Small', 'SM', '', '', 1, @user_id, @user_id, @now, @now),
(UUID(), @attr2, 'Large', 'LG', '', '', 1, @user_id, @user_id, @now, @now),
(UUID(), @attr3, 'Cotton', 'COT', '', '', 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 6) Item MTD (Material Technical Details)
-- =========================================================
TRUNCATE TABLE wh_settings_item_mtd;
INSERT INTO wh_settings_item_mtd (id, company_id, user_id, name, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, 'GSM', 'gsm', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Composition', 'composition', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Style Number', 'style-number', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Color Fastness', 'color-fastness', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Shrinkage %', 'shrinkage-percent', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 7) Currencies
-- =========================================================
TRUNCATE TABLE wh_settings_currencies;
INSERT INTO wh_settings_currencies (id, company_id, user_id, currency_name, default_currency, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, '["USD"]', 'USD', 'usd', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, '["EUR"]', 'EUR', 'eur', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, '["GBP"]', 'GBP', 'gbp', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, '["BDT"]', 'BDT', 'bdt', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, '["INR"]', 'INR', 'inr', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 8) Location Types
-- =========================================================
TRUNCATE TABLE wh_setting_location_type;
INSERT INTO wh_setting_location_type (id, company_id, user_id, name, description, warehouse_id, slug, is_default, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, 'Warehouse', 'Top level warehouse location', NULL, 'warehouse-lt', 1, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Zone', 'Zone within a warehouse', NULL, 'zone-lt', 0, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Rack', 'Storage rack location', NULL, 'rack-lt', 0, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Shelf', 'Shelf within a rack', NULL, 'shelf-lt', 0, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Dock', 'Loading/unloading dock', NULL, 'dock-lt', 0, 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 9) Measurement Types
-- =========================================================
TRUNCATE TABLE wh_setting_measurement_types;
SET @mt_weight = UUID(); SET @mt_volume = UUID(); SET @mt_length = UUID(); SET @mt_count = UUID(); SET @mt_area = UUID();
INSERT INTO wh_setting_measurement_types (id, company_id, user_id, name, description, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@mt_weight, @company_id, @user_id, 'Weight', 'Measurement of mass', 'weight', 1, 1, @user_id, @user_id, @now, @now),
(@mt_volume, @company_id, @user_id, 'Volume', 'Measurement of volume', 'volume', 1, 1, @user_id, @user_id, @now, @now),
(@mt_length, @company_id, @user_id, 'Length', 'Measurement of length', 'length', 1, 1, @user_id, @user_id, @now, @now),
(@mt_count, @company_id, @user_id, 'Count', 'Measurement by piece/unit count', 'count', 1, 1, @user_id, @user_id, @now, @now),
(@mt_area, @company_id, @user_id, 'Area', 'Measurement of surface area', 'area', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 10) Measurement Units
-- =========================================================
TRUNCATE TABLE wh_settings_measurement_unit;
SET @unit_kg = UUID(); SET @unit_g = UUID(); SET @unit_l = UUID(); SET @unit_m = UUID(); SET @unit_cm = UUID();
INSERT INTO wh_settings_measurement_unit (id, name, measurement_type_id, company_id, description, short_code, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@unit_kg, 'Kilogram', @mt_weight, @company_id, 'Unit of mass', 'kg', 'kilogram', 1, 1, @user_id, @user_id, @now, @now),
(@unit_g, 'Gram', @mt_weight, @company_id, 'Smaller unit of mass', 'g', 'gram', 1, 1, @user_id, @user_id, @now, @now),
(@unit_l, 'Liter', @mt_volume, @company_id, 'Unit of volume', 'L', 'liter', 1, 1, @user_id, @user_id, @now, @now),
(@unit_m, 'Meter', @mt_length, @company_id, 'Unit of length', 'm', 'meter', 1, 1, @user_id, @user_id, @now, @now),
(@unit_cm, 'Centimeter', @mt_length, @company_id, 'Smaller unit of length', 'cm', 'centimeter', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 11) Unit of Measurement Conversions
-- =========================================================
TRUNCATE TABLE wh_settings_unit_of_measurement_conver;
INSERT INTO wh_settings_unit_of_measurement_conver (id, company_id, user_id, measurement_type_id, from_measurement_unit_id, from_ratio, to_measurement_unit_id, to_ratio, operator, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, @mt_weight, @unit_kg, 1, @unit_g, 1000, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @mt_weight, @unit_g, 1000, @unit_kg, 1, 2, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @mt_length, @unit_m, 1, @unit_cm, 100, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @mt_length, @unit_cm, 100, @unit_m, 1, 2, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @mt_volume, @unit_l, 1, @unit_l, 1, 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 12) SKU Configs
-- =========================================================
TRUNCATE TABLE wh_settings_sku_configs;
INSERT INTO wh_settings_sku_configs (id, company_id, warehouse_id, user_id, prefix, length, show_leading_zero, formula, sku_start, increment_by, is_default, formula_chips, item_categories, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @wh_a, @user_id, 'EL', 20, 1, 'EL-{Item Name}-1', 1, 1, 1, '[{"id":"button-itemName-1","type":"button","value":"Item Name"}]', JSON_ARRAY(@cat1), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @wh_a, @user_id, 'AP', 20, 1, 'AP-{Item Name}-1', 1, 1, 0, '[{"id":"button-itemName-2","type":"button","value":"Item Name"}]', JSON_ARRAY(@cat2), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @wh_b, @user_id, 'FB', 20, 0, 'FB-{Item Category}-1', 1, 1, 0, '[{"id":"button-itemCategory-3","type":"button","value":"Item Category"}]', JSON_ARRAY(@cat3), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @wh_b, @user_id, 'FN', 20, 1, 'FN-{Item Code}-1', 1, 1, 0, '[{"id":"button-itemCode-4","type":"button","value":"Item Code"}]', JSON_ARRAY(@cat4), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @wh_c, @user_id, 'ST', 20, 0, 'ST-{Item Name}-1', 1, 1, 0, '[{"id":"button-itemName-5","type":"button","value":"Item Name"}]', JSON_ARRAY(@cat5), 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 13) Batch Configuration
-- =========================================================
TRUNCATE TABLE wh_settings_batch_configur;
INSERT INTO wh_settings_batch_configur (id, company_id, user_id, warehouse_id, mode, starting_batch_number, last_batch_no, increment_digit, is_enabled, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, @wh_a, 'automatic', 'BATCH1000', NULL, 4, 1, 'batch-config-wh-a', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_b, 'automatic', 'BATCH2000', NULL, 4, 1, 'batch-config-wh-b', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_c, 'manual', '1', NULL, 3, 1, 'batch-config-wh-c', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_a, 'automatic', 'LOT100', NULL, 5, 0, 'batch-config-lot-a', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_b, 'manual', '1000', NULL, 4, 1, 'batch-config-manual-b', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 14) Transaction Types
-- =========================================================
-- NOTE: TO / PO / WR are mandatory system-generated types — ItemCheckOutService hardcodes
-- short_code === 'WR', and TransactionTypeSeeder always (re)creates exactly these 3. They
-- MUST exist, and ONLY these 3 are inserted here (not 5) to match production behaviour.
TRUNCATE TABLE wh_settings_transaction_types;
INSERT INTO wh_settings_transaction_types (id, company_id, user_id, name, short_code, description, slug, transaction_type, status, is_system_generated, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, 'Transfer Order', 'TO', 'This type will be used to track transfer order', 'transfer-order', 1, 1, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Purchase Order', 'PO', 'This type is used for purchase process', 'purchase-order', 2, 1, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Without Reference', 'WR', '--', 'without-reference', 3, 1, 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 15) Vendor Types
-- =========================================================
TRUNCATE TABLE wh_settings_vendor_types;
SET @vt1 = UUID(); SET @vt2 = UUID(); SET @vt3 = UUID(); SET @vt4 = UUID(); SET @vt5 = UUID();
INSERT INTO wh_settings_vendor_types (id, company_id, user_id, name, description, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@vt1, @company_id, @user_id, 'Manufacturer', 'Manufactures goods directly', 'manufacturer', 1, 1, @user_id, @user_id, @now, @now),
(@vt2, @company_id, @user_id, 'Wholesaler', 'Sells goods in bulk quantity', 'wholesaler', 1, 1, @user_id, @user_id, @now, @now),
(@vt3, @company_id, @user_id, 'Distributor', 'Distributes goods to retailers', 'distributor', 1, 1, @user_id, @user_id, @now, @now),
(@vt4, @company_id, @user_id, 'Importer', 'Imports goods from abroad', 'importer', 1, 1, @user_id, @user_id, @now, @now),
(@vt5, @company_id, @user_id, 'Service Provider', 'Provides maintenance and services', 'service-provider', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 16) Vendors
-- =========================================================
TRUNCATE TABLE wh_settings_vendors;
INSERT INTO wh_settings_vendors (id, company_id, user_id, vendor_type_id, name, slug, contact_person, contact_number, email_address, physical_address, additional_notes, photo, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, @vt1, 'Bengal Textile Mills', 'bengal-textile-mills', 'Karim Rahman', '+8801711000001', 'karim@bengaltextile.com', 'Gazipur, Dhaka, Bangladesh', 'Reliable long-term fabric supplier', NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @vt2, 'Dhaka Wholesale House', 'dhaka-wholesale-house', 'Rafiqul Islam', '+8801711000002', 'rafiq@dhakawholesale.com', 'Old Dhaka, Bangladesh', 'Bulk grocery and FMCG supplier', NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @vt3, 'Chittagong Distributors Ltd', 'chittagong-distributors-ltd', 'Nasrin Akter', '+8801711000003', 'nasrin@ctgdist.com', 'Chattogram, Bangladesh', 'Regional distribution partner', NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @vt4, 'Global Import Traders', 'global-import-traders', 'Shafiq Ahmed', '+8801711000004', 'shafiq@globalimport.com', 'Motijheel, Dhaka, Bangladesh', 'Imports electronics from China', NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @vt5, 'ProCare Maintenance Services', 'procare-maintenance-services', 'Farhana Yasmin', '+8801711000005', 'farhana@procare.com', 'Uttara, Dhaka, Bangladesh', 'Warehouse equipment maintenance', NULL, 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 17) Customer Types
-- =========================================================
TRUNCATE TABLE wh_settings_customers_types;
SET @ct1 = UUID(); SET @ct2 = UUID(); SET @ct3 = UUID(); SET @ct4 = UUID(); SET @ct5 = UUID();
INSERT INTO wh_settings_customers_types (id, company_id, user_id, warehouse_id, name, description, slug, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(@ct1, @company_id, @user_id, @wh_a, 'Retail Customer', 'Individual retail buyers', 'retail-customer', 1, 1, @user_id, @user_id, @now, @now),
(@ct2, @company_id, @user_id, @wh_a, 'Wholesale Customer', 'Bulk buying business customers', 'wholesale-customer', 1, 1, @user_id, @user_id, @now, @now),
(@ct3, @company_id, @user_id, @wh_b, 'Corporate Customer', 'Corporate/institutional clients', 'corporate-customer', 1, 1, @user_id, @user_id, @now, @now),
(@ct4, @company_id, @user_id, @wh_b, 'Online Customer', 'E-commerce channel customers', 'online-customer', 1, 1, @user_id, @user_id, @now, @now),
(@ct5, @company_id, @user_id, @wh_c, 'Government Customer', 'Government/public sector buyers', 'government-customer', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 18) Customers
-- =========================================================
TRUNCATE TABLE wh_settings_customers;
INSERT INTO wh_settings_customers (id, company_id, user_id, name, slug, customer_type_id, contact_person, contact_number, contact_numberCountry, email_address, physical_address, additional_notes, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, 'Apex Retail Store', 'apex-retail-store', @ct1, 'Jamal Uddin', '+8801911000001', 'BD', 'jamal@apexretail.com', 'Dhanmondi, Dhaka, Bangladesh', 'Frequent repeat customer', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Nasrin Trading Co', 'nasrin-trading-co', @ct2, 'Nasrin Sultana', '+8801911000002', 'BD', 'nasrin@nasrintrading.com', 'Khulna, Bangladesh', 'Bulk order every month', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Skyline Corporate Ltd', 'skyline-corporate-ltd', @ct3, 'Tanvir Hasan', '+8801911000003', 'BD', 'tanvir@skylinecorp.com', 'Gulshan, Dhaka, Bangladesh', 'Annual supply contract', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'ShopEase Online', 'shopease-online', @ct4, 'Mitu Rahman', '+8801911000004', 'BD', 'mitu@shopease.com', 'Uttara, Dhaka, Bangladesh', 'E-commerce partner', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'District Supply Office', 'district-supply-office', @ct5, 'Abdul Kader', '+8801911000005', 'BD', 'kader@dso.gov.bd', 'Rajshahi, Bangladesh', 'Government procurement office', 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 19) Item Priority Rules
-- =========================================================
TRUNCATE TABLE wh_settings_item_priority_rules;
INSERT INTO wh_settings_item_priority_rules (id, company_id, user_id, warehouse_id, rule, location_ids, category_ids, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, @wh_a, 1, JSON_ARRAY(@loc_rack01, @loc_shelf01), JSON_ARRAY(@cat1), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_a, 2, JSON_ARRAY(@loc_zone01), JSON_ARRAY(@cat2), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_b, 1, JSON_ARRAY(@loc_dock01), JSON_ARRAY(@cat3), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_b, 2, JSON_ARRAY(@loc_rack01, @loc_dock01), JSON_ARRAY(@cat4), 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, @wh_c, 1, JSON_ARRAY(@loc_shelf01), JSON_ARRAY(@cat5), 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 20) Inventory Audit Schedules
-- =========================================================
TRUNCATE TABLE wh_settings_inventory_audit_schedules;
INSERT INTO wh_settings_inventory_audit_schedules (id, company_id, user_id, audit_name, slug, location_ids, category_ids, item_ids, brand_ids, description, schedule_start_date, schedule_end_date, repeat_every, repeat_on, monthly_days, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, @user_id, 'Monthly Electronics Audit', 'monthly-electronics-audit', JSON_ARRAY(@loc_rack01), JSON_ARRAY(@cat1), JSON_ARRAY(@item1), JSON_ARRAY(@brand1), 'Monthly stock audit for electronics section', '2026-01-01', '2026-12-31', 1, 1, NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Quarterly Apparel Audit', 'quarterly-apparel-audit', JSON_ARRAY(@loc_shelf01), JSON_ARRAY(@cat2), JSON_ARRAY(@item2), JSON_ARRAY(@brand2), 'Quarterly stock check for apparel items', '2026-01-01', '2026-12-31', 3, 1, NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Weekly Food Audit', 'weekly-food-audit', JSON_ARRAY(@loc_dock01), JSON_ARRAY(@cat3), JSON_ARRAY(@item3), JSON_ARRAY(@brand3), 'Weekly perishable food stock audit', '2026-01-01', '2026-12-31', 7, 1, NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Annual Furniture Audit', 'annual-furniture-audit', JSON_ARRAY(@loc_zone01), JSON_ARRAY(@cat4), JSON_ARRAY(@item1), JSON_ARRAY(@brand4), 'Yearly audit for furniture inventory', '2026-01-01', '2026-12-31', 12, 1, NULL, 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, @user_id, 'Monthly Stationery Audit', 'monthly-stationery-audit', JSON_ARRAY(@loc_rack01, @loc_shelf01), JSON_ARRAY(@cat5), JSON_ARRAY(@item2), JSON_ARRAY(@brand5), 'Monthly stationery stock audit', '2026-01-01', '2026-12-31', 1, 15, 15, 1, 1, @user_id, @user_id, @now, @now);

-- =========================================================
-- 21) Barcode Configurations
-- =========================================================
TRUNCATE TABLE wh_settings_barcode_configurations;
INSERT INTO wh_settings_barcode_configurations (id, company_id, name, category, description, barcode_type, starting_value, increment_pattern, prefix, postfix, leading_zero, status, row_status, created_by, updated_by, created_at, updated_at) VALUES
(UUID(), @company_id, 'Item Barcode Standard', 1, 'Default barcode format for items', 'Code-128', '00000001', 1, 'P-', '', 'no', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Location Barcode Standard', 2, 'Default barcode format for locations', 'Code-128', '00000001', 1, 'L-', '', 'no', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Transaction Barcode Standard', 3, 'Default barcode format for transactions', 'Code-128', '00000001', 1, 'T-', '', 'no', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Item EAN Barcode', 1, 'EAN-13 barcode format for retail items', 'EAN-13', '00000001', 1, 'E-', '', 'yes', 1, 1, @user_id, @user_id, @now, @now),
(UUID(), @company_id, 'Location Code-39 Barcode', 2, 'Code-39 barcode format for rack labels', 'Code-39', '00000001', 1, 'LX-', '', 'no', 1, 1, @user_id, @user_id, @now, @now);

SET FOREIGN_KEY_CHECKS = 1;

-- Verification counts
SELECT 'wh_settings_item_categories' t, COUNT(*) c FROM wh_settings_item_categories
UNION ALL SELECT 'wh_settings_item_types', COUNT(*) FROM wh_settings_item_types
UNION ALL SELECT 'wh_settings_item_brands', COUNT(*) FROM wh_settings_item_brands
UNION ALL SELECT 'wh_setting_item_tags', COUNT(*) FROM wh_setting_item_tags
UNION ALL SELECT 'wh_settings_item_attributes', COUNT(*) FROM wh_settings_item_attributes
UNION ALL SELECT 'wh_settings_item_attribute_details', COUNT(*) FROM wh_settings_item_attribute_details
UNION ALL SELECT 'wh_settings_item_mtd', COUNT(*) FROM wh_settings_item_mtd
UNION ALL SELECT 'wh_settings_currencies', COUNT(*) FROM wh_settings_currencies
UNION ALL SELECT 'wh_setting_location_type', COUNT(*) FROM wh_setting_location_type
UNION ALL SELECT 'wh_setting_measurement_types', COUNT(*) FROM wh_setting_measurement_types
UNION ALL SELECT 'wh_settings_measurement_unit', COUNT(*) FROM wh_settings_measurement_unit
UNION ALL SELECT 'wh_settings_unit_of_measurement_conver', COUNT(*) FROM wh_settings_unit_of_measurement_conver
UNION ALL SELECT 'wh_settings_sku_configs', COUNT(*) FROM wh_settings_sku_configs
UNION ALL SELECT 'wh_settings_batch_configur', COUNT(*) FROM wh_settings_batch_configur
UNION ALL SELECT 'wh_settings_transaction_types', COUNT(*) FROM wh_settings_transaction_types
UNION ALL SELECT 'wh_settings_vendor_types', COUNT(*) FROM wh_settings_vendor_types
UNION ALL SELECT 'wh_settings_vendors', COUNT(*) FROM wh_settings_vendors
UNION ALL SELECT 'wh_settings_customers_types', COUNT(*) FROM wh_settings_customers_types
UNION ALL SELECT 'wh_settings_customers', COUNT(*) FROM wh_settings_customers
UNION ALL SELECT 'wh_settings_item_priority_rules', COUNT(*) FROM wh_settings_item_priority_rules
UNION ALL SELECT 'wh_settings_inventory_audit_schedules', COUNT(*) FROM wh_settings_inventory_audit_schedules
UNION ALL SELECT 'wh_settings_barcode_configurations', COUNT(*) FROM wh_settings_barcode_configurations;
