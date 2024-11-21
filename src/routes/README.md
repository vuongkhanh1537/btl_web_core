GET http://localhost/btl_web_core/api/products  // Get all records
GET http://localhost/btl_web_core/api/products/{id} // Get record by ID
POST http://localhost/btl_web_core/api/products
    {
        "name": "Product Name",       // string, required
        "price": 99.99,              // number, required
        "color": "Red",              // string, required  
        "brand": "Brand Name",       // string, required
        "description": "Product description text", // string, required
        "weight": 500,               // number, required (presumably in grams)
        "size": 42,                  // number, required
        "quantity": 100,             // number, required (stock amount)
        "category": "Shoes"          // enum: "Shoes", "Stocks", or "Sneaker", required
    }
PUT http://localhost/btl_web_core/api/products/{id} // Update record by ID
    {
        "{field_name}" : {value},
        "{field_name}" : {value}
    }

DELETE http://localhost/btl_web_core/api/products/{id} // Delete by ID