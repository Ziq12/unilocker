# 🔒 UniLocker 
### Secure Campus Delivery & Item Exchange System

## 📖 Overview
**UniLocker** is a web-based prototype designed to solve common campus delivery and item exchange issues. Students frequently face problems with food deliveries being stolen, packages being damaged by animals (like monkeys or cats), or friends being unable to return borrowed items when the owner is unavailable. 

UniLocker simulates a secure, smart-locker network where deliveries and items can be deposited and collected safely using a PIN-based transaction system, without the need for physical IoT hardware.

## 👥 User Roles & Features

### 1. Student (Receiver)
* Register and log in securely.
* View pending deliveries and locker assignments.
* Collect items by entering the Locker Number and a 6-digit PIN.
* View transaction history.

### 2. Depositor (Public User)
* **No login required.**
* Deposit items (Food, Parcels, Documents).
* Assign to a specific student via Student ID, or create an anonymous deposit.
* Upload a proof-of-deposit photo.
* Receive a generated PIN to share with the receiver.

### 3. Administrator
* Manage locker inventory (Add, update status).
* View all system transactions and delivery logs.
* Monitor user activity and audit logs.

## 🧩 Core System Modules

### 1. 🔐 Authentication Module
Handles all user identity verification, access control, and session management.
* **Core Functions:** Student/Admin Registration, Secure Login, Logout, and Session Management.
* **Security Controls Implemented:**
  * **Cryptographic Storage:** Passwords are never stored in plaintext; they are securely hashed using **bcrypt** (`password_hash()`).
  * **Session Security:** Implements session regeneration upon login, strict 15-minute inactivity timeouts, and `HttpOnly`/`Secure` cookie flags to prevent Session Hijacking and Fixation.
  * **Input Validation:** Strict server-side validation for Student ID formats and email uniqueness to prevent fake accounts and account enumeration.
  * **Threats Mitigated:** Brute-force attacks, credential stuffing, session fixation, and plaintext password exposure.

### 2. 📦 Transaction & Locker Modules
The core business logic handling the secure handover of items between Public Depositors and Students.

#### A. Deposit Transaction Workflow
* **Known Receiver Deposit:** The depositor enters the receiver's Student ID, item category, and uploads a proof photo. The system automatically assigns an available locker and generates a secure 6-digit PIN.
* **Anonymous Deposit:** The depositor uploads item information without a Student ID. The system assigns a locker and generates a PIN for the depositor to manually share with the receiver.
* **Security Controls Implemented:**
  * **Secure File Uploads:** Strict MIME-type validation, file size limits, and randomized filenames (`uniqid()`) to prevent Remote Code Execution (RCE) via malicious scripts.
  * **PIN Security:** PINs are generated randomly and stored as **bcrypt hashes** in the database (never in plaintext).
  * **Database Security:** All transaction queries use **PDO Prepared Statements** to completely eliminate SQL Injection risks.
  * **Threats Mitigated:** Malicious file uploads, PIN guessing, and SQL Injection.

#### B. Collection Workflow
* **Process:** The student logs in, views their pending deliveries, selects a locker, and enters the 6-digit PIN to confirm collection.
* **Security Controls Implemented:**
  * **Strict Ownership Validation (RBAC):** The system explicitly verifies that the logged-in user's ID matches the `receiver_id` of the delivery in the database. This completely prevents **Insecure Direct Object Reference (IDOR)** / Horizontal Privilege Escalation (Student A cannot collect Student B's item).
  * **Secure PIN Verification:** Uses `password_verify()` to check the user's input against the stored hash securely without exposing the PIN.
  * **Threats Mitigated:** Unauthorized collection, IDOR attacks, and brute-force PIN guessing.

## 🛠️ Tech Stack
* **Backend:** PHP (Native)
* **Database:** MySQL
* **Server:** Apache (via XAMPP)
* **Frontend:** HTML5, CSS3, Bootstrap 5

## 🚀 Installation & Setup Guide

### Prerequisites
* Download and install [XAMPP](https://www.apachefriends.org/).

### Step 1: Project Setup
1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Clone or download this repository into your XAMPP `htdocs` directory:
   ```bash
   C:\xampp\htdocs\unilocker\
   ```

### Step 2: Database Configuration
1. Open your browser and navigate to phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `unilocker_db`.
3. Go to the **SQL** tab, copy the contents of `schema.sql`, and click **Go** to import the tables and dummy data.

### Step 3: Run the Application
1. Open your browser and go to: 
   ```text
   http://localhost/unilocker/public/
   ```
2. You can now register a new student account or use the default credentials below.

## 🔑 Default Test Credentials
* **Admin Account:**
  * Email: `admin@uni.edu` | Password: `password123`
* **Student Account:**
  * Email: `john@uni.edu` | Password: `password123`

## 📂 Project Structure
```text
unilocker/
├── config/          # Database connection scripts
├── includes/        # Reusable UI components (Header, Footer, Auth checks)
├── public/          # Web-accessible pages (Login, Dashboard, Deposit)
│   └── admin/       # Admin-only dashboard and management pages
├── uploads/         # Directory for deposited item proof photos
├── assets/          # CSS and JS files
└── schema.sql       # Database schema and dummy data
