ALTER TABLE agri_erp.inventory_balances 
CHANGE COLUMN quantity quantity_on_hand DECIMAL(15,4) DEFAULT 0.0000, 
CHANGE COLUMN total_value inventory_value DECIMAL(15,4) DEFAULT 0.0000, 
ADD COLUMN average_cost DECIMAL(15,4) DEFAULT 0.0000 AFTER inventory_value, 
ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE agri_erp.stock_ledger 
CHANGE COLUMN transaction_date movement_date DATE NOT NULL, 
CHANGE COLUMN reference reference_number VARCHAR(100), 
CHANGE COLUMN source_id source_transaction_id INT UNSIGNED, 
CHANGE COLUMN qty_in quantity_in DECIMAL(15,4) DEFAULT 0.0000, 
CHANGE COLUMN qty_out quantity_out DECIMAL(15,4) DEFAULT 0.0000, 
CHANGE COLUMN total_value total_cost DECIMAL(15,4) DEFAULT 0.0000, 
CHANGE COLUMN running_qty balance_quantity DECIMAL(15,4) DEFAULT 0.0000, 
CHANGE COLUMN running_value balance_value DECIMAL(15,4) DEFAULT 0.0000, 
ADD COLUMN movement_type VARCHAR(50) AFTER reference_number, 
ADD COLUMN source_type VARCHAR(50) AFTER source_module, 
ADD COLUMN created_by INT UNSIGNED AFTER balance_value;
