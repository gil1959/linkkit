# LinkKit - Ultimate Biolink & Marketplace Platform

LinkKit is an advanced, all-in-one Biolink and E-Commerce platform built with PHP. It allows creators, influencers, and businesses to build powerful biolink pages while seamlessly integrating a robust marketplace. 

## 🌟 Key Features

### 🛍️ Comprehensive Marketplace
- **Digital Products**: Sell downloadable files, unique random codes, or automated webhook-triggered events.
- **Physical Products**: Full support for physical product fulfillment with automated shipping cost calculation using the **RajaOngkir API**.
- **Manual & Custom Services**: Manage offline services and custom client requests.

### 💳 Modern Payment & Checkout System
- **Tripay Integration**: Seamless integration with Tripay Payment Gateway for automated Virtual Account, e-Wallet, and convenience store payments.
- **Dynamic Cart Calculations**: Real-time tax, platform service fees (5%), shipping costs, and discount voucher handling.
- **Manual Payments**: Supports offline transfers with automated proof-of-payment upload and admin verification workflows.

### 🛡️ Security & Seller Verification
- **Identity Verification System**: Strict KTP and Selfie verification process for sellers before they can withdraw funds.
- **Secure Withdrawals**: Automated fund settlement system where sellers can request withdrawals to their registered bank accounts securely.
- **Fraud Protection**: Detailed logging of transactions and robust CSRF protection on sensitive forms.

### 📊 Dashboard & Analytics
- Complete financial reporting (Pending Funds, Withdrawable Funds, Total Spent).
- Clean, modern, glassmorphism-inspired UI for both merchants and buyers.
- Comprehensive Order Management: Track statuses from "Pending" to "Paid", manage shipping receipts, and automatically fulfill digital orders upon payment success.

### 🔧 Extensibility
- Dynamic webhooks and custom post-purchase instructions.
- Fully translatable UI using standard locale structures.
- Rating and Review system for verified buyers.

## 🚀 Technology Stack
- **Backend**: Core PHP (Custom MVC architecture)
- **Database**: MySQL / MariaDB
- **Frontend**: Vanilla HTML/CSS, JavaScript, FontAwesome
- **Integrations**: Tripay API (Payments), RajaOngkir API (Logistics)

## 📦 Setup & Installation

> **Note:** The `config.php`, `vendor/` directory, and sensitive keys are excluded from this repository for security purposes.

1. Clone the repository: `git clone https://github.com/gil1959/linkkit.git`
2. Run `composer install` to install required PHP dependencies.
3. Import the database schema from the SQL dumps provided in your environment.
4. Duplicate `config.example.php` (if available) to `config.php` and configure your database and base URL.
5. Setup the proper writable permissions for the `uploads/` directory.

## 🔒 Security Policy
Please do not commit `config.php` or any files containing live API keys (like Tripay Private Key or RajaOngkir API Key) to this repository.
