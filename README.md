# PeopleOps - Employee Management HRMS

hey! so this is my employee management system. built it while learning PHP and MySQL. it's got some AI features that make it more interesting than a basic CRUD app.

## what it does

- employee database with personal, employment, and bank details
- department management
- attendance tracking (daily check-in/check-out with overtime)
- leave management (apply, approve/reject, leave types)
- payroll processing with Indian statutory deductions (PF, ESI, PT, TDS)
- employee feedback system with sentiment analysis
- AI HR chatbot for common queries
- attrition risk analysis

### AI Features (the interesting part)

1. **HR Chatbot** - answers common employee questions about leave, salary, attendance, policies. uses keyword matching (not a real ML model, but works for basic queries)

2. **Sentiment Analysis** - analyzes employee feedback for positive/negative/neutral sentiment. uses keyword matching against a predefined list of positive and negative words

3. **Attrition Risk Scoring** - calculates a 0-100 risk score for each employee based on:
   - attendance patterns (absent more = higher risk)
   - leave frequency (too many leaves = flag)
   - feedback sentiment (negative feedback = concern)
   - tenure (new employees or very long tenure might be at risk)

4. **Payroll Calculator** - automatically calculates Indian statutory deductions:
   - PF (12% of basic, max ₹1,800)
   - ESI (0.75% if gross ≤ ₹21,000)
   - Professional Tax (₹200/month if basic > ₹25,000)
   - TDS (based on old tax regime)

## tech stack

- PHP 8 (vanilla)
- MySQL database
- HTML/CSS/JS
- some inline CSS (working on moving it to the stylesheet)

## setup

1. install XAMPP
2. start Apache and MySQL
3. open phpMyAdmin, import `database.sql`
4. edit `config.php` if needed
5. put folder in htdocs
6. go to `http://localhost/employee-management`
7. login: admin / admin123

## database tables

- `users` - admin/hr login
- `departments` - company departments
- `employees` - employee master data
- `attendance` - daily attendance records
- `leave_types` - types of leaves (CL, SL, EL, etc.)
- `leaves` - leave applications
- `payroll` - monthly salary processing
- `feedback` - employee feedback with sentiment
- `chatbot_logs` - chatbot conversation history

## known issues / TODO

- [ ] chatbot is too basic - should integrate with a proper NLP API
- [ ] sentiment analysis misses context (sarcasm, mixed sentiments)
- [ ] attrition risk scoring needs more data points (performance, manager feedback)
- [ ] payroll TDS calculation is simplified - should use exact tax slabs
- [ ] no email notifications for leave approval/rejection
- [ ] should add employee self-service portal
- [ ] the CSS needs cleanup
- [ ] no export to PDF for payslips

## author

Aditya Shirsatrao
built for learning - not production ready but shows the concepts!
