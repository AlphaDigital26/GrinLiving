import json
import re
import os

js_path = 'products.js'
sql_path = 'database.sql'

with open(js_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Extract the JSON array from products.js
match = re.search(r'const products = (\[.*?\]);', content, re.DOTALL)
if match:
    json_str = match.group(1)
    products = json.loads(json_str)
    
    with open(sql_path, 'w', encoding='utf-8') as f:
        f.write("CREATE DATABASE IF NOT EXISTS `grin_living_db`;\n")
        f.write("USE `grin_living_db`;\n\n")
        f.write("CREATE TABLE IF NOT EXISTS `products` (\n")
        f.write("  `id` INT AUTO_INCREMENT PRIMARY KEY,\n")
        f.write("  `title` VARCHAR(255) NOT NULL,\n")
        f.write("  `category` VARCHAR(100) NOT NULL,\n")
        f.write("  `image` VARCHAR(255) NOT NULL\n")
        f.write(") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n")
        
        if products:
            f.write("INSERT INTO `products` (`title`, `category`, `image`) VALUES\n")
            values = []
            for p in products:
                title = p.get('title', '').replace("'", "''")
                category = p.get('category', '').replace("'", "''")
                image = p.get('image', '').replace("'", "''")
                values.append(f"('{title}', '{category}', '{image}')")
            f.write(",\n".join(values) + ";\n")
    print(f"Successfully generated {sql_path} with {len(products)} products.")
else:
    print("Could not find products array in products.js")
