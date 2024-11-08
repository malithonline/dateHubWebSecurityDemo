# 💻 DateHub - NIBM Software Security AAA Assessment

A secure PHP-based application demonstrating Authentication, Authorization, and Accounting (AAA) capabilities without using any frameworks. Developed for NIBM Software Security Assessment 2024.

## 🎯 Assessment Focus
Implementation of secure AAA functionality using raw PHP, demonstrating security best practices and secure coding principles.

## ✨ Key Requirements Met
* ⚡ Zero Framework Implementation
* 🔐 Custom AAA System
* 📊 Database Integration
* 🚫 No External Security Frameworks
* 📝 Complete Activity Logging
* 👥 Role-Based Access Control
* 🔄 Version Control (GitHub)

## 🛠️ Technologies Used
* Raw PHP (No Frameworks)
* MySQL Database
* HTML5
* TailwindCSS
* JavaScript (Vanilla)
* Flowbite Components
* Python (for data population)

## 📁 Project Structure
```
datehub-security/
├── data population/     # Python scripts for database population
├── sql/                 # Database structure and schemas
├── uploads/             # File upload directory
├── dashboard.php        # User dashboard
├── database.php         # Database connection handler
├── index.html           # Landing page
├── index.php            # Main application entry
├── login.html           # Login interface
├── login.php            # Login handler
├── logout.php           # Session termination
├── register.html        # Registration interface
├── register.php         # Registration handler
├── styles.css           # Custom styling
├── LICENSE              # MIT License
└── README.md            # Project documentation
```

## 🔒 Security Features
* Custom Authentication System
* Session Management
* Password Hashing & Salting
* CSRF Protection
* XSS Prevention
* SQL Injection Protection
* Input Validation
* Rate Limiting
* Secure File Handling
* Comprehensive Activity Logging

## 💻 Installation

1. Clone the repository
```bash
git clone https://github.com/malithonline/DateHub_WebSecurityDemo.git
```

2. Database setup
```sql
CREATE DATABASE aaa_system;
```

3. Import database structure
```bash
mysql -u root -p dating_app < sql/schema.sql
```

4. Configure database
   * Copy database.example.php to database.php
   * Update database credentials

5. (Optional) Populate test data
```bash
cd "data population"
python populate_data.py
```

6. Start local server
```bash
php -S localhost:8000
```

## 📝 Usage
1. Register account
2. Complete profile
3. Browse matches
4. Like profiles
5. Admin panel access

## 🔑 Test Credentials
```
Admin:
Username: admin
Password: admin123

User:
Username: user
Password: user123
```

## 🎓 Assessment Details
* **Course**: BSc (Hons) Computing 2024
* **Module**: Software Security
* **Assessment**: AAA Implementation
* **Weight**: 50%
* **Lecturer**: Mr. Niranga Dharmaratna
* **Due Date**: October 26, 2024

## 🌐 Deployment
Live Demo: https://malith.eu.org

## 👨‍💻 Student Details
* Name: [Malith Madhuwanthe]
* Student ID: [233f-025]
* Batch: HNDSE 23.3F
* GitHub: @malithonline

## ⚠️ Important Notes
* No frameworks were used as per assessment requirements
* All security implementations are custom-built
* Complete logging system for AAA implementation
* Source code available for lecturer review
* Test data population script uses fictional data
* Uploaded files are securely stored in uploads directory

## 📄 License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---
Made with 💡 for NIBM Software Security Assessment 2024
