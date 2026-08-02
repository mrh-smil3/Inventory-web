classDiagram
    direction TB

    class User {
        - bigint id
        - string name
        - string email
        - string password
        - datetime created_at
        - datetime updated_at
        + authenticate() bool
        + updateProfile() void
    }

    class Category {
        - bigint id
        - string name
        - string description
        - datetime created_at
        - datetime updated_at
        + getCategoryDetails()
        + updateCategory()
    }

    class Satuan {
        - bigint id
        - string name
        - datetime created_at
        - datetime updated_at
        + getSatuan()
    }

    class Product {
        - bigint id
        - bigint category_id
        - string sku
        - string name
        - bigint unit_id
        - int stock
        - int min_stock
        - decimal purchase_price
        - decimal selling_price
        - datetime created_at
        - datetime updated_at
        + checkStock() int
        + updateStock(int qty, string type)
        + calculateMargin() decimal
    }

    class Supplier {
        - bigint id
        - string name
        - string phone
        - string address
        - datetime created_at
        - datetime updated_at
        + getSupplierInfo()
        + updateSupplier()
    }

    class StockIn {
        - bigint id
        - bigint supplier_id
        - string invoice_number
        - decimal total_price
        - datetime transaction_date
        - text note
        - datetime created_at
        - datetime updated_at
        + createInvoice()
        + getTotalQty() int
    }

    class StockInItem {
        - bigint id
        - bigint stock_in_id
        - bigint product_id
        - int qty
        - decimal unit_price
        - decimal subtotal
        - datetime created_at
        - datetime updated_at
        + addItem()
        + removeItem()
    }

    class StockOut {
        - bigint id
        - string invoice_number
        - decimal total_price
        - datetime transaction_date
        - text note
        - enum status
        - datetime created_at
        - datetime updated_at
        + createInvoice()
        + getTotalQty() int
    }

    class StockOutItem {
        - bigint id
        - bigint product_id
        - bigint stock_out_id
        - int qty
        - decimal unit_price
        - decimal subtotal
        - datetime created_at
        - datetime updated_at
        + addItem()
        + removeItem()
    }

    class StockMutation {
        - bigint id
        - bigint product_id
        - enum type
        - int qty
        - bigint reference_id
        - datetime transaction_date
        - text note
        - datetime created_at
        - datetime updated_at
        + recordMutation()
        + getHistoryByProduct()
    }

    %% Relasi Antar Class
    Category "1" --> "*" Product : has
    Satuan "1" --> "*" Product : uses
    
    Supplier "1" --> "*" StockIn : supplies
    
    StockIn "1" *-- "*" StockInItem : contains (Composition)
    Product "1" --> "*" StockInItem : listed in
    
    StockOut "1" *-- "*" StockOutItem : contains (Composition)
    Product "1" --> "*" StockOutItem : listed in
    
    Product "1" --> "*" StockMutation : tracks history