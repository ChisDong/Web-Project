CREATE DATABASE web_project
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE TABLE categories (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    slug VARCHAR(150) UNIQUE,
    description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE TABLE collections (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    slug VARCHAR(150) UNIQUE,
    description TEXT,
    banner VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE TABLE products (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT,
    collection_id BIGINT NULL,
    name VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    base_price INT,
    discount_percent INT DEFAULT 0,
    gender ENUM('nam','nu','unisex') DEFAULT 'unisex',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (collection_id) REFERENCES collections(id)
);



CREATE TABLE product_colors (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT,
    color_name VARCHAR(100),
    color_code VARCHAR(20),       -- mã màu hex #ffffff
    main_image VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE product_images (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT,
    image_url VARCHAR(255),
    role ENUM('main','secondary'),
    created_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE sizes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    size_name VARCHAR(10),  -- S, M, L, XL
    sort_order INT
);
CREATE TABLE product_variants (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT,
    color_id BIGINT,
    size_id BIGINT,
    sku VARCHAR(100) UNIQUE,
    price INT,
    stock INT DEFAULT 0,
    weight FLOAT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (color_id) REFERENCES product_colors(id),
    FOREIGN KEY (size_id) REFERENCES sizes(id)
);
CREATE TABLE CustomerAddresses (
    address_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT NOT NULL,
    address_line VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    ward VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    district VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    city VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    country VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Vietnam',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_customer
        FOREIGN KEY (customer_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    total_price INT,
    status ENUM('pending','paid','shipping','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id)
    FOREIGN KEY (address) REFERENCES customer_addresses(address_id)
);
CREATE TABLE order_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT,
    variant_id BIGINT,
    quantity INT,
    price INT,      -- lưu giá tại thời điểm mua
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (variant_id) REFERENCES product_variants(id)
);
CREATE TABLE discounts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE,
    percent INT,
    max_value INT,
    expire_at DATE,
    created_at TIMESTAMP NULL
);
CREATE TABLE product_reviews (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT,
    user_id BIGINT,
    rating TINYINT,
    comment TEXT,
    created_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE product_faqs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    question TEXT NOT NULL,
    answer   TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE product_highlights (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    title VARCHAR(255),       -- VAI BIẾT THỚ CHIỀU CƠ THỂ
    description TEXT,         -- Chất vải co giãn 4 chiều...
    image_url VARCHAR(255),   -- ảnh nhỏ bên trái
    sort_order INT DEFAULT 0, -- để sắp xếp trên giao diện
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE notifications (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    url VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


