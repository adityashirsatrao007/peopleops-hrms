-- PeopleOps - Employee Management HRMS
-- built by Aditya for learning PHP + MySQL
-- includes AI features for HR analytics
-- last updated: aug 2026

CREATE DATABASE IF NOT EXISTS peopleops_db;
USE peopleops_db;

-- Users table (admin/hr staff)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'hr', 'manager') DEFAULT 'hr',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments table
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    manager_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Employees table
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    city VARCHAR(50),
    department_id INT,
    designation VARCHAR(100),
    date_of_joining DATE,
    employment_type ENUM('full_time', 'part_time', 'contract', 'intern') DEFAULT 'full_time',
    basic_salary DECIMAL(10,2) DEFAULT 0,
    pan_number VARCHAR(10),
    aadhar_number VARCHAR(12),
    bank_account VARCHAR(20),
    bank_name VARCHAR(50),
    emergency_contact VARCHAR(20),
    emergency_contact_name VARCHAR(100),
    status ENUM('active', 'inactive', 'terminated') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Attendance table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    check_in TIME,
    check_out TIME,
    status ENUM('present', 'absent', 'half_day', 'leave', 'holiday') DEFAULT 'present',
    overtime_hours DECIMAL(4,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    UNIQUE KEY unique_attendance (employee_id, attendance_date)
);

-- Leave types table
CREATE TABLE leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    days_allowed INT DEFAULT 0,
    description TEXT
);

-- Leave applications table
CREATE TABLE leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INT NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
);

-- Payroll table
CREATE TABLE payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    hra DECIMAL(10,2) DEFAULT 0,
    conveyance DECIMAL(10,2) DEFAULT 0,
    medical_allowance DECIMAL(10,2) DEFAULT 0,
    special_allowance DECIMAL(10,2) DEFAULT 0,
    gross_salary DECIMAL(10,2) NOT NULL,
    pf_deduction DECIMAL(10,2) DEFAULT 0,
    esi_deduction DECIMAL(10,2) DEFAULT 0,
    professional_tax DECIMAL(10,2) DEFAULT 0,
    tds DECIMAL(10,2) DEFAULT 0,
    other_deductions DECIMAL(10,2) DEFAULT 0,
    total_deductions DECIMAL(10,2) DEFAULT 0,
    net_salary DECIMAL(10,2) NOT NULL,
    status ENUM('draft', 'processed', 'paid') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- Employee feedback table (for sentiment analysis)
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    feedback_text TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    category ENUM('work', 'management', 'culture', 'compensation', 'growth') DEFAULT 'work',
    sentiment VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- HR chatbot conversations table
CREATE TABLE chatbot_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_query VARCHAR(500) NOT NULL,
    bot_response TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin (password: admin123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Priya HR', 'priya.peopleops@gmail.com', 'admin'),
('rahul.mgr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rahul Manager', 'rahul.peopleops@gmail.com', 'manager');

-- Departments
INSERT INTO departments (name, description) VALUES
('Engineering', 'Software development and technical teams'),
('Product', 'Product management and design'),
('Marketing', 'Digital marketing and brand management'),
('Sales', 'Business development and client relations'),
('HR', 'Human resources and people operations'),
('Finance', 'Accounting and financial operations'),
('Operations', 'Office operations and admin');

-- Leave types (Indian standard)
INSERT INTO leave_types (name, days_allowed, description) VALUES
('Casual Leave', 12, 'For personal work or emergencies'),
('Sick Leave', 6, 'For medical reasons'),
('Earned Leave', 15, 'Accumulated leave from previous years'),
('Maternity Leave', 182, 'As per Maternity Benefit Act'),
('Paternity Leave', 5, 'For new fathers'),
('Compensatory Off', 0, 'Against working on holidays');

-- Employees (realistic Indian names and data)
INSERT INTO employees (emp_id, first_name, last_name, email, phone, date_of_birth, gender, address, city, department_id, designation, date_of_joining, employment_type, basic_salary, pan_number, aadhar_number, bank_account, bank_name, emergency_contact, emergency_contact_name, status) VALUES
('EMP001', 'Amit', 'Kumar', 'amit.kumar@peopleops.in', '9876543210', '1992-05-15', 'male', '123 MG Road, Near City Hospital', 'Pune', 1, 'Senior Software Engineer', '2022-03-15', 'full_time', 85000.00, 'ABCPK1234A', '123456789012', '50100123456789', 'SBI', '9876543211', 'Sunita Kumar', 'active'),
('EMP002', 'Sneha', 'Patil', 'sneha.patil@peopleops.in', '9876543212', '1994-08-22', 'female', '456 Shivaji Nagar, Apte Road', 'Pune', 1, 'Software Engineer', '2023-01-10', 'full_time', 65000.00, 'SNEPP5678B', '234567890123', '50100234567890', 'HDFC', '9876543213', 'Rajesh Patil', 'active'),
('EMP003', 'Rajesh', 'Gupta', 'rajesh.gupta@peopleops.in', '9876543214', '1990-11-30', 'male', '789 FC Road, Near MIT College', 'Pune', 2, 'Product Manager', '2021-06-20', 'full_time', 95000.00, 'RAJPG9012C', '345678901234', '50100345678901', 'ICICI', '9876543215', 'Priya Gupta', 'active'),
('EMP004', 'Priya', 'Sharma', 'priya.sharma@peopleops.in', '9876543216', '1993-03-18', 'female', '234 Deccan Gymkhana, Near Garware College', 'Pune', 3, 'Marketing Lead', '2022-09-05', 'full_time', 72000.00, 'PRISH3456D', '456789012345', '50100456789012', 'Kotak', '9876543217', 'Vikram Sharma', 'active'),
('EMP005', 'Vikram', 'Joshi', 'vikram.joshi@peopleops.in', '9876543218', '1988-07-25', 'male', '56 Koregaon Park, Near Boat Club', 'Pune', 4, 'Business Development Manager', '2021-02-14', 'full_time', 78000.00, 'VIKJJ7890E', '567890123456', '50100567890123', 'Axis', '9876543219', 'Anita Joshi', 'active'),
('EMP006', 'Neha', 'Khaparde', 'neha.khaparde@peopleops.in', '9876543220', '1995-12-10', 'female', '89 Sadashiv Peth, Near Laxmi Road', 'Pune', 5, 'HR Executive', '2023-04-01', 'full_time', 55000.00, 'NEHKH5678F', '678901234567', '50100678901234', 'SBI', '9876543221', 'Sanjay Khaparde', 'active'),
('EMP007', 'Suresh', 'Deshmukh', 'suresh.deshmukh@peopleops.in', '9876543222', '1991-04-05', 'male', '321 Swargate, Near Katraj', 'Pune', 6, 'Accountant', '2022-11-20', 'full_time', 52000.00, 'SURES1234G', '789012345678', '50100789012345', 'BOB', '9876543223', 'Kavita Deshmukh', 'active'),
('EMP008', 'Kavita', 'Bhatt', 'kavita.bhatt@peopleops.in', '9876543224', '1996-09-14', 'female', '67 Kothrud, Near PMT Bus Stop', 'Pune', 1, 'Junior Developer', '2024-01-15', 'full_time', 42000.00, 'KAVIB9012H', '890123456789', '50100890123456', 'PNB', '9876543225', 'Ramesh Bhatt', 'active'),
('EMP009', 'Aditya', 'Shirsatrao', 'aditya.s@peopleops.in', '9876543226', '1998-02-28', 'male', '45 Solapur Road, Near Railway Station', 'Solapur', 1, 'Full Stack Developer', '2024-06-01', 'full_time', 48000.00, 'ADITS0123I', '901234567890', '50100901234567', 'IDBI', '9876543227', 'Shirish Shirsatrao', 'active'),
('EMP010', 'Pooja', 'Thakur', 'pooja.thakur@peopleops.in', '9876543228', '1997-06-20', 'female', '78 Hadapsar, Near Magarpatta', 'Pune', 3, 'Content Writer', '2023-08-10', 'full_time', 45000.00, 'POOTH6789J', '012345678901', '50101012345678', 'Union Bank', '9876543229', 'Manoj Thakur', 'active');

-- Attendance records (last 30 days)
INSERT INTO attendance (employee_id, attendance_date, check_in, check_out, status, overtime_hours) VALUES
(1, CURDATE() - INTERVAL 30 DAY, '09:05:00', '18:30:00', 'present', 0.5),
(1, CURDATE() - INTERVAL 29 DAY, '09:15:00', '19:00:00', 'present', 1.0),
(1, CURDATE() - INTERVAL 28 DAY, '09:00:00', '18:00:00', 'present', 0),
(1, CURDATE() - INTERVAL 27 DAY, NULL, NULL, 'absent', 0),
(1, CURDATE() - INTERVAL 26 DAY, '09:10:00', '18:45:00', 'present', 0.75),
(2, CURDATE() - INTERVAL 30 DAY, '09:20:00', '18:15:00', 'present', 0),
(2, CURDATE() - INTERVAL 29 DAY, '09:00:00', '18:30:00', 'present', 0.5),
(2, CURDATE() - INTERVAL 28 DAY, '09:30:00', '17:30:00', 'half_day', 0),
(2, CURDATE() - INTERVAL 27 DAY, '09:05:00', '18:00:00', 'present', 0),
(2, CURDATE() - INTERVAL 26 DAY, '09:10:00', '18:45:00', 'present', 0.75),
(3, CURDATE() - INTERVAL 30 DAY, '08:55:00', '19:15:00', 'present', 1.25),
(3, CURDATE() - INTERVAL 29 DAY, '09:00:00', '18:30:00', 'present', 0.5),
(3, CURDATE() - INTERVAL 28 DAY, '09:05:00', '18:00:00', 'present', 0),
(3, CURDATE() - INTERVAL 27 DAY, '09:10:00', '18:45:00', 'present', 0.75),
(3, CURDATE() - INTERVAL 26 DAY, NULL, NULL, 'leave', 0),
(4, CURDATE() - INTERVAL 30 DAY, '09:30:00', '18:00:00', 'present', 0),
(4, CURDATE() - INTERVAL 29 DAY, '09:45:00', '17:30:00', 'half_day', 0),
(4, CURDATE() - INTERVAL 28 DAY, '09:00:00', '18:30:00', 'present', 0.5),
(4, CURDATE() - INTERVAL 27 DAY, '09:15:00', '19:00:00', 'present', 1.0),
(4, CURDATE() - INTERVAL 26 DAY, '09:00:00', '18:00:00', 'present', 0),
(5, CURDATE() - INTERVAL 30 DAY, '09:00:00', '18:30:00', 'present', 0.5),
(5, CURDATE() - INTERVAL 29 DAY, '09:10:00', '18:00:00', 'present', 0),
(5, CURDATE() - INTERVAL 28 DAY, NULL, NULL, 'absent', 0),
(5, CURDATE() - INTERVAL 27 DAY, '09:05:00', '19:30:00', 'present', 1.5),
(5, CURDATE() - INTERVAL 26 DAY, '09:15:00', '18:45:00', 'present', 0.75);

-- Leave applications
INSERT INTO leaves (employee_id, leave_type_id, start_date, end_date, days, reason, status) VALUES
(1, 1, DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 14 DAY), 2, 'Family function in native place', 'approved'),
(2, 2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), 1, 'Fever and cold', 'approved'),
(3, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 3, 'Personal work - bank registration', 'approved'),
(4, 3, DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 18 DAY), 3, 'Vacation trip to Goa', 'approved'),
(5, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_SUB(CURDATE(), INTERVAL 2 DAY), 1, ' childs school function', 'pending'),
(8, 2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 2, ' dentist appointment', 'approved');

-- Payroll (last 2 months)
INSERT INTO payroll (employee_id, month, year, basic_salary, hra, conveyance, medical_allowance, special_allowance, gross_salary, pf_deduction, esi_deduction, professional_tax, tds, total_deductions, net_salary, status) VALUES
(1, 7, 2026, 85000.00, 34000.00, 1600.00, 1250.00, 8150.00, 130000.00, 1800.00, 0.00, 200.00, 12800.00, 14800.00, 115200.00, 'paid'),
(2, 7, 2026, 65000.00, 26000.00, 1600.00, 1250.00, 6150.00, 100000.00, 1800.00, 0.00, 200.00, 8200.00, 10200.00, 89800.00, 'paid'),
(3, 7, 2026, 95000.00, 38000.00, 1600.00, 1250.00, 9150.00, 145000.00, 1800.00, 0.00, 200.00, 16500.00, 18500.00, 126500.00, 'paid'),
(4, 7, 2026, 72000.00, 28800.00, 1600.00, 1250.00, 6350.00, 110000.00, 1800.00, 0.00, 200.00, 10500.00, 12500.00, 97500.00, 'paid'),
(5, 7, 2026, 78000.00, 31200.00, 1600.00, 1250.00, 7950.00, 120000.00, 1800.00, 0.00, 200.00, 11800.00, 13800.00, 106200.00, 'paid'),
(1, 8, 2026, 85000.00, 34000.00, 1600.00, 1250.00, 8150.00, 130000.00, 1800.00, 0.00, 200.00, 12800.00, 14800.00, 115200.00, 'processed'),
(2, 8, 2026, 65000.00, 26000.00, 1600.00, 1250.00, 6150.00, 100000.00, 1800.00, 0.00, 200.00, 8200.00, 10200.00, 89800.00, 'processed'),
(3, 8, 2026, 95000.00, 38000.00, 1600.00, 1250.00, 9150.00, 145000.00, 1800.00, 0.00, 200.00, 16500.00, 18500.00, 126500.00, 'draft'),
(4, 8, 2026, 72000.00, 28800.00, 1600.00, 1250.00, 6350.00, 110000.00, 1800.00, 0.00, 200.00, 10500.00, 12500.00, 97500.00, 'draft'),
(5, 8, 2026, 78000.00, 31200.00, 1600.00, 1250.00, 7950.00, 120000.00, 1800.00, 0.00, 200.00, 11800.00, 13800.00, 106200.00, 'draft');

-- Employee feedback (for sentiment analysis)
INSERT INTO feedback (employee_id, feedback_text, rating, category) VALUES
(1, 'Great work environment, love the team. Management is supportive and work-life balance is good.', 5, 'culture'),
(2, 'The new project is exciting but deadlines are tight. Need more resources for testing.', 3, 'work'),
(3, 'Excellent growth opportunities. The company invests in employee development.', 5, 'growth'),
(4, 'Salary is competitive but could be better. Benefits are good though.', 3, 'compensation'),
(5, 'Great client interactions. The sales team is very collaborative and helpful.', 4, 'work'),
(6, 'HR processes are improving. The new leave management system is much better.', 4, 'management'),
(7, 'The finance team needs better software tools. Current systems are outdated.', 2, 'work'),
(8, 'Good learning environment for freshers. Mentors are helpful and patient.', 5, 'growth'),
(9, 'The company culture is great. Love the flexibility in work hours.', 4, 'culture'),
(10, 'Content team is small but talented. Need more content writers to handle the workload.', 3, 'work'),
(1, 'The new office space is amazing. Really enjoying the open floor plan.', 5, 'culture'),
(3, 'Product roadmap is clear. Good direction from leadership.', 4, 'management'),
(5, 'Need better CRM tools. Current system is slow and outdated.', 2, 'work'),
(2, 'Code review process is thorough. Helps maintain code quality.', 4, 'work'),
(4, 'Marketing budget has been increased. Looking forward to new campaigns.', 4, 'compensation');
