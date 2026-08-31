-- PeopleOps - Employee Management HRMS
-- PostgreSQL schema
-- last updated: aug 2026

CREATE TABLE hrms.users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'hr' CHECK (role IN ('admin', 'hr', 'manager')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hrms.departments (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    manager_id INTEGER DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hrms.employees (
    id SERIAL PRIMARY KEY,
    emp_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender VARCHAR(10) CHECK (gender IN ('male', 'female', 'other')),
    address TEXT,
    city VARCHAR(50),
    department_id INTEGER REFERENCES hrms.departments(id),
    designation VARCHAR(100),
    date_of_joining DATE,
    employment_type VARCHAR(20) DEFAULT 'full_time' CHECK (employment_type IN ('full_time', 'part_time', 'contract', 'intern')),
    basic_salary DECIMAL(10,2) DEFAULT 0,
    pan_number VARCHAR(10),
    aadhar_number VARCHAR(12),
    bank_account VARCHAR(20),
    bank_name VARCHAR(50),
    emergency_contact VARCHAR(20),
    emergency_contact_name VARCHAR(100),
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'terminated')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hrms.attendance (
    id SERIAL PRIMARY KEY,
    employee_id INTEGER NOT NULL REFERENCES hrms.employees(id),
    attendance_date DATE NOT NULL,
    check_in TIME,
    check_out TIME,
    status VARCHAR(20) DEFAULT 'present' CHECK (status IN ('present', 'absent', 'half_day', 'leave', 'holiday')),
    overtime_hours DECIMAL(4,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (employee_id, attendance_date)
);

CREATE TABLE hrms.leave_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    days_allowed INTEGER DEFAULT 0,
    description TEXT
);

CREATE TABLE hrms.leaves (
    id SERIAL PRIMARY KEY,
    employee_id INTEGER NOT NULL REFERENCES hrms.employees(id),
    leave_type_id INTEGER NOT NULL REFERENCES hrms.leave_types(id),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INTEGER NOT NULL,
    reason TEXT,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    approved_by INTEGER DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hrms.payroll (
    id SERIAL PRIMARY KEY,
    employee_id INTEGER NOT NULL REFERENCES hrms.employees(id),
    month INTEGER NOT NULL,
    year INTEGER NOT NULL,
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
    status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'processed', 'paid')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hrms.feedback (
    id SERIAL PRIMARY KEY,
    employee_id INTEGER NOT NULL REFERENCES hrms.employees(id),
    feedback_text TEXT NOT NULL,
    rating INTEGER CHECK (rating >= 1 AND rating <= 5),
    category VARCHAR(20) DEFAULT 'work' CHECK (category IN ('work', 'management', 'culture', 'compensation', 'growth')),
    sentiment VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hrms.chatbot_logs (
    id SERIAL PRIMARY KEY,
    user_query VARCHAR(500) NOT NULL,
    bot_response TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin (password: admin123)
INSERT INTO hrms.users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Priya HR', 'priya.peopleops@gmail.com', 'admin'),
('rahul.mgr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rahul Manager', 'rahul.peopleops@gmail.com', 'manager');

INSERT INTO hrms.departments (name, description) VALUES
('Engineering', 'Software development and technical teams'),
('Product', 'Product management and design'),
('Marketing', 'Digital marketing and brand management'),
('Sales', 'Business development and client relations'),
('HR', 'Human resources and people operations'),
('Finance', 'Accounting and financial operations'),
('Operations', 'Office operations and admin');

INSERT INTO hrms.leave_types (name, days_allowed, description) VALUES
('Casual Leave', 12, 'For personal work or emergencies'),
('Sick Leave', 6, 'For medical reasons'),
('Earned Leave', 15, 'Accumulated leave from previous years'),
('Maternity Leave', 182, 'As per Maternity Benefit Act'),
('Paternity Leave', 5, 'For new fathers'),
('Compensatory Off', 0, 'Against working on holidays');

INSERT INTO hrms.employees (emp_id, first_name, last_name, email, phone, date_of_birth, gender, address, city, department_id, designation, date_of_joining, employment_type, basic_salary, pan_number, aadhar_number, bank_account, bank_name, emergency_contact, emergency_contact_name, status) VALUES
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

INSERT INTO hrms.attendance (employee_id, attendance_date, check_in, check_out, status, overtime_hours) VALUES
(1, CURRENT_DATE - INTERVAL '30 days', '09:05:00', '18:30:00', 'present', 0.5),
(1, CURRENT_DATE - INTERVAL '29 days', '09:15:00', '19:00:00', 'present', 1.0),
(1, CURRENT_DATE - INTERVAL '28 days', '09:00:00', '18:00:00', 'present', 0),
(1, CURRENT_DATE - INTERVAL '27 days', NULL, NULL, 'absent', 0),
(1, CURRENT_DATE - INTERVAL '26 days', '09:10:00', '18:45:00', 'present', 0.75),
(2, CURRENT_DATE - INTERVAL '30 days', '09:20:00', '18:15:00', 'present', 0),
(2, CURRENT_DATE - INTERVAL '29 days', '09:00:00', '18:30:00', 'present', 0.5),
(2, CURRENT_DATE - INTERVAL '28 days', '09:30:00', '17:30:00', 'half_day', 0),
(2, CURRENT_DATE - INTERVAL '27 days', '09:05:00', '18:00:00', 'present', 0),
(2, CURRENT_DATE - INTERVAL '26 days', '09:10:00', '18:45:00', 'present', 0.75),
(3, CURRENT_DATE - INTERVAL '30 days', '08:55:00', '19:15:00', 'present', 1.25),
(3, CURRENT_DATE - INTERVAL '29 days', '09:00:00', '18:30:00', 'present', 0.5),
(3, CURRENT_DATE - INTERVAL '28 days', '09:05:00', '18:00:00', 'present', 0),
(3, CURRENT_DATE - INTERVAL '27 days', '09:10:00', '18:45:00', 'present', 0.75),
(3, CURRENT_DATE - INTERVAL '26 days', NULL, NULL, 'leave', 0),
(4, CURRENT_DATE - INTERVAL '30 days', '09:30:00', '18:00:00', 'present', 0),
(4, CURRENT_DATE - INTERVAL '29 days', '09:45:00', '17:30:00', 'half_day', 0),
(4, CURRENT_DATE - INTERVAL '28 days', '09:00:00', '18:30:00', 'present', 0.5),
(4, CURRENT_DATE - INTERVAL '27 days', '09:15:00', '19:00:00', 'present', 1.0),
(4, CURRENT_DATE - INTERVAL '26 days', '09:00:00', '18:00:00', 'present', 0),
(5, CURRENT_DATE - INTERVAL '30 days', '09:00:00', '18:30:00', 'present', 0.5),
(5, CURRENT_DATE - INTERVAL '29 days', '09:10:00', '18:00:00', 'present', 0),
(5, CURRENT_DATE - INTERVAL '28 days', NULL, NULL, 'absent', 0),
(5, CURRENT_DATE - INTERVAL '27 days', '09:05:00', '19:30:00', 'present', 1.5),
(5, CURRENT_DATE - INTERVAL '26 days', '09:15:00', '18:45:00', 'present', 0.75);

INSERT INTO hrms.leaves (employee_id, leave_type_id, start_date, end_date, days, reason, status) VALUES
(1, 1, CURRENT_DATE - INTERVAL '15 days', CURRENT_DATE - INTERVAL '14 days', 2, 'Family function in native place', 'approved'),
(2, 2, CURRENT_DATE - INTERVAL '10 days', CURRENT_DATE - INTERVAL '10 days', 1, 'Fever and cold', 'approved'),
(3, 1, CURRENT_DATE - INTERVAL '5 days', CURRENT_DATE - INTERVAL '3 days', 3, 'Personal work - bank registration', 'approved'),
(4, 3, CURRENT_DATE - INTERVAL '20 days', CURRENT_DATE - INTERVAL '18 days', 3, 'Vacation trip to Goa', 'approved'),
(5, 1, CURRENT_DATE - INTERVAL '2 days', CURRENT_DATE - INTERVAL '2 days', 1, 'childs school function', 'pending'),
(8, 2, CURRENT_DATE - INTERVAL '7 days', CURRENT_DATE - INTERVAL '6 days', 2, 'dentist appointment', 'approved');

INSERT INTO hrms.payroll (employee_id, month, year, basic_salary, hra, conveyance, medical_allowance, special_allowance, gross_salary, pf_deduction, esi_deduction, professional_tax, tds, total_deductions, net_salary, status) VALUES
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

INSERT INTO hrms.feedback (employee_id, feedback_text, rating, category) VALUES
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

INSERT INTO hrms.chatbot_logs (user_query, bot_response, created_at) VALUES
('what is the leave policy?', 'Leave policy: Apply at least 2 days in advance for casual leave. Sick leave can be applied on the day itself with medical certificate if >2 days.', '2026-08-25 10:15:00'),
('how much is pf deduction?', 'PF deduction is 12% of basic salary. You can check your PF balance on the EPFO portal.', '2026-08-26 11:30:00'),
('can i work from home tomorrow?', 'WFH policy: Maximum 2 days per week with manager approval. Submit request at least 1 day in advance.', '2026-08-27 09:45:00'),
('when is salary credited?', 'Salary is credited by the 7th of every month. For any salary-related queries, contact the Finance team.', '2026-08-28 14:20:00'),
('what are the office timings?', 'Working hours: 9:00 AM to 6:00 PM, Monday to Friday. Saturday is half-day (9 AM - 1 PM) if required.', '2026-08-29 08:50:00'),
('hello', 'Hello! I am the PeopleOps HR assistant. How can I help you today?', '2026-08-30 10:00:00');
