
-- Schema for Restaurant POS (SQLite)
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL CHECK(role IN ('admin','cashier','waiter','kitchen')),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tables (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  label TEXT NOT NULL,
  capacity INTEGER DEFAULT 4,
  active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS menu_categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS menu_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  category_id INTEGER,
  name TEXT NOT NULL,
  price_cents INTEGER NOT NULL,
  tax_rate REAL DEFAULT 0.0,
  sku TEXT,
  inventory_deduct INTEGER DEFAULT 0,
  FOREIGN KEY(category_id) REFERENCES menu_categories(id)
);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  table_id INTEGER,
  user_id INTEGER,
  status TEXT NOT NULL DEFAULT 'open',
  discount_cents INTEGER DEFAULT 0,
  note TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  closed_at DATETIME,
  FOREIGN KEY(table_id) REFERENCES tables(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL,
  item_id INTEGER NOT NULL,
  qty INTEGER NOT NULL DEFAULT 1,
  price_cents INTEGER NOT NULL,
  tax_rate REAL NOT NULL DEFAULT 0.0,
  note TEXT,
  kds_status TEXT NOT NULL DEFAULT 'queued',
  FOREIGN KEY(order_id) REFERENCES orders(id),
  FOREIGN KEY(item_id) REFERENCES menu_items(id)
);

CREATE TABLE IF NOT EXISTS payments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL,
  method TEXT NOT NULL,
  amount_cents INTEGER NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(order_id) REFERENCES orders(id)
);

CREATE TABLE IF NOT EXISTS payment_meta (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  payment_id INTEGER NOT NULL,
  meta_key TEXT NOT NULL,
  meta_value TEXT,
  FOREIGN KEY(payment_id) REFERENCES payments(id)
);

CREATE TABLE IF NOT EXISTS settings (
  key TEXT PRIMARY KEY,
  value TEXT
);

INSERT OR IGNORE INTO users (name,email,password_hash,role) VALUES
('Admin','admin@example.com','$2y$10$3mvVukl1mU2Xyq7s4O6qs.P7jFZg4mVO9GkqgS4rQbS4yQyG7tY8C','admin'),
('Cashier','cashier@example.com','$2y$10$eFf5pU2oWm7Z2t.YZ8o2Q.0XWj8Di2bQq9k3j3b3n1m3l1e2r2yN.','cashier'),
('Waiter','waiter@example.com','$2y$10$3mvVukl1mU2Xyq7s4O6qs.P7jFZg4mVO9GkqgS4rQbS4yQyG7tY8C','waiter'),
('Kitchen','kitchen@example.com','$2y$10$3mvVukl1mU2Xyq7s4O6qs.P7jFZg4mVO9GkqgS4rQbS4yQyG7tY8C','kitchen');

INSERT OR IGNORE INTO tables (label,capacity) VALUES ('T1',4),('T2',4),('T3',2),('T4',6);

INSERT OR IGNORE INTO menu_categories (name) VALUES ('Drinks'),('Mains'),('Sides');

INSERT OR IGNORE INTO menu_items (category_id,name,price_cents,tax_rate,sku,inventory_deduct) VALUES
(1,'Water 500ml',100,0.00,'DRK001',0),
(1,'Cola 330ml',250,0.05,'DRK002',1),
(2,'Beef Burger',800,0.05,'MAI001',1),
(2,'Chicken Shawarma',700,0.05,'MAI002',1),
(3,'Fries',300,0.05,'SID001',1),
(3,'Salad',350,0.05,'SID002',1);

INSERT OR IGNORE INTO settings (key, value) VALUES
('restaurant_name','Restaurant Demo'),
('restaurant_address','123 Main St, Hargeisa, Somaliland'),
('restaurant_phone','+252 63 0000000'),
('currency_symbol','$'),
('vat_rate','0.05'),
('tax_mode','per_item'),
('payment_methods','["cash","card","zaad","edahab"]'),
('receipt_footer','Mahadsanid! — Thank you for your visit.');
