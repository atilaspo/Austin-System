
# 🚀 Austin-System - Project Documentation

## 📝 1. Introduction
Austin-System is a clinic management system developed as a Minimum Viable Product (MVP) using the **Rapid Software Development** technique.  
- **📂 GitHub Repository:** [Austin-System](https://github.com/atilaspo/Austin-System)  
- **🌐 Website:** [austin-system.infinityfreeapp.com](https://austin-system.infinityfreeapp.com)  
- **🛠️ Methodology:** Rapid Software Development (RSD)  

This project was led by **Santiago Ortiz** and developed in **7 days**. The purpose is to rapidly demonstrate a functional application and its capabilities.  

> ⚠️ **Note:** Although the **MVC (Model-View-Controller)** pattern is the intended architecture, it has not been fully implemented yet. Future iterations will address this, along with improvements in architecture, security, and additional features.

---

## 💻 2. Technologies Used
- **🖥️ Backend:** PHP, MySQL  
- **🎨 Frontend:** HTML, CSS, JavaScript  
- **☁️ Server:** InfinityFree Hosting  
- **💾 Version Control:** GitHub  

## 👥 3. Users, Roles, and Credentials
The system has multiple roles, each with different access levels:

- **👨‍⚕️ Patients:** View and schedule appointments.  
- **👩‍⚕️ Doctors:** Manage availability and review appointments.  
- **🔑 Administrator:** Manage users and reports.  
- **💳 Cashier:** Handle payments and invoicing.  
- **🏥 Clinic:** View general statistics.  

### 🗝️ Example Credentials for Testing:

#### **🛡️ Administrator:**  
- Username: `admin`  
- Password: `admin`  

#### **🏥 Clinic:**  
- Username: `clinic`  
- Password: `clinic`  

#### **💳 Cashier:**  
- Username: `cashier`  
- Password: `cashier`  

#### **👨‍⚕️ Doctors:**  
- Username: `willow@austin.com`  
  Password: `willow@austin.com`  

- Username: `sulav@austin.com`  
  Password: `sulav@austin.com`  

- Username: `lunshwa@austin.com`  
  Password: `lunshwa@austin.com`  

- Username: `bianca@austin.com`  
  Password: `bianca@austin.com`  

#### **👨‍👩‍👧‍👦 Patients:**  
- Username: `jack@austin.com`  
  Password: `patient`  

- Username: `lucas@austin.com`  
  Password: `patient2@austin.com`  

- Username: `chloe@austin.com`  
  Password: `chloe@austin.com`  

---

## ⚙️ 4. Key Features (MVP)
- **🔐 User Registration and Login:** Secure authentication system.  
- **📅 Appointment Management:** Create, modify, and cancel appointments.  
- **📊 Admin Panel:** Manage users, doctors, and reports.  
- **🩺 Medical History:** View patient records.  
- **💳 Payment Processing:** Manage payments and generate receipts.  

## 🏛️ 5. Design and Architecture
- **📐 Model:** Intended MVC (Model-View-Controller) architecture *(not yet fully implemented)*.  
- **💾 Database:** Relational database using MySQL.  

## 🚀 6. Development Process (Rapid Software Development)
This project was developed using the **Rapid Software Development (RSD)** technique, focusing on:
- **⚡ Quick Iterations:** Multiple versions in short periods.  
- **🛠️ Prototyping:** Continuous testing with users.  
- **🔄 Continuous Delivery:** Frequent updates to the GitHub repository.  

## 🧪 7. How to Test the System
### ✅ Option 1: From the Website
- Visit: [🌐 austin-system.infinityfreeapp.com](https://austin-system.infinityfreeapp.com)  
- Log in using the provided credentials.  

### 💻 Option 2: Locally (using XAMPP)  
**Prerequisites:**  
- You must have **XAMPP** installed and running to support PHP and MySQL.  

1. **Clone the Repository:**  
   ```bash
   git clone https://github.com/atilaspo/Austin-System.git
   ```

2. **Move the Repository to XAMPP's `htdocs` Folder:**  
   - Navigate to your XAMPP installation folder.  
   - Copy or move the cloned repository into the `htdocs` directory (`xampp/htdocs/Austin-System`).  

3. **Set Up Local Database:**  
   - Start **XAMPP Control Panel** and ensure that **Apache** and **MySQL** are running.  
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).  
   - Create a new database named **`austin`**.  
   - Import the database from the file `db/austin.sql` located in the repository.  

4. **Access the System:**  
   Open your browser and go to:  
   ```
   http://localhost/Austin-System/
   ```

---

## 🛤️ 8. Conclusion and Next Steps
This MVP was developed in **7 days** to rapidly showcase the app's functionalities. Future iterations will focus on:
- ✅ **Implementing proper MVC architecture.**  
- 🛡️ **Enhancing security protocols.**  
- 🚀 **Optimizing system performance.**  
- 💡 **Adding advanced features and user experience improvements.**  

---

Made with ❤️ by [Santiago Ortiz](https://github.com/atilaspo)  
