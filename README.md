ChicThreads - Modern E-commerce Platform

ChicThreads is a fully functional, responsive e-commerce web application built with PHP, MySQL, and modern front-end utilities. The project features a curated shopping experience with dynamic filtering, a robust cart system, and an interactive user interface.

🚀 Current Project Status: Core Shopping Engine Complete

The project has transitioned from a static prototype to a database-driven application. All primary shopping flows—from browsing to checkout—are currently operational.

✨ Key Features

1. Dynamic Homepage

Vibrant Category Navigation: 12 high-quality, clickable category cards (Dresses, Tops, Jeans, Electronics, etc.).

Responsive Header: Sticky navigation with search integration and real-time cart count indicators.

Newsletter Integration: Styled subscription section for user engagement.

2. Advanced Shop & Filtering System

Dual-Layer Filtering:

Shop For (Audience): Filter products by Men, Women, Kids, and Unisex.

Sub-Category Groups: Collapsible sidebar filters (Apparel, Accessories, Electronics).

Price Range Filter: Interactive range slider (0 - $500) to narrow down choices.

Modern Grid Layout: Product cards utilize a clean square aspect ratio for a premium look.

3. Complete Shopping Cart Flow

Product Details: Individual pages for every item fetching real-time data from MySQL.

Session-Based Cart: Add, view, and remove items without requiring a complex database sync for guest/temporary sessions.

Checkout Process: Functional order summary, shipping information collection, and a localized "Thank You" confirmation page.

4. Contact & Support

Interactive Map: Embedded Google Maps integration for physical store location.

Database-Stored Inquiries: A full backend handler (handle_contact.php) that validates and saves customer messages to a contact_messages table.

🛠️ Technology Stack

Backend: PHP 8.x

Database: MySQL

Styling: Tailwind CSS (via CDN)

Interactivity: Alpine.js (for dropdowns, cart logic, and notifications)

Icons: Lucide/Heroicons

📂 Project Structure

index.php: Main landing page and category hub.

shop.php: Catalog page with query-based filtering logic.

product-detail.php: Dynamic template for single product viewing.

view_cart.php: UI for managing current selections.

add_to_cart.php / remove_from_cart.php: Logic handlers for session data.

checkout.php: Shipping and order summary form.

contact.php: Customer support page with embedded map.

connection.php: Database configuration (Required for operation).

🗄️ Database Requirements

To run this project, ensure your ecommerce database includes:

products table: Must include id, name, category, price, image_url, and audience.

contact_messages table: For storing support inquiries.

Refer to update_product_audience.sql and create_contact_table.sql for the latest schema and seed data.

Note: This README reflects the work completed as of the current development phase. Future updates may include user account management, payment gateway integration, and an admin dashboard.