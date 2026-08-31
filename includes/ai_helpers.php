<?php
// ai_helpers.php - HR AI features for PeopleOps
// these are rule-based AI functions, not actual ML
// but they work well enough for a student project

/**
 * HR Policy Chatbot
 * answers common employee questions based on keyword matching
 * TODO: integrate with a proper NLP library or API later
 */
function hrChatbot($query) {
    $query_lower = strtolower($query);
    
    // define patterns and responses
    // found this approach in a chatbot tutorial, adapted for HR context
    $patterns = [
        // leave related
        'leave|vacation|holiday|chutti' => [
            'You can apply for leave through the Leave Management section. We offer Casual Leave (12 days/year), Sick Leave (6 days), and Earned Leave (15 days).',
            'Leave policy: Apply at least 2 days in advance for casual leave. Sick leave can be applied on the day itself with medical certificate if >2 days.'
        ],
        'casual leave|CL' => [
            'Casual Leave: 12 days per year. Cannot be carried forward. Apply through the leave portal.',
            'You have 12 casual leaves per year. For more details, check the leave policy document.'
        ],
        'sick leave|SL|medical' => [
            'Sick Leave: 6 days per year. Medical certificate required if leave exceeds 2 consecutive days.',
            'For sick leave, apply through the portal. If you need more than 2 days, please attach a medical certificate.'
        ],
        'maternity|pregnant' => [
            'Maternity Leave: 182 days (26 weeks) as per Maternity Benefit Act. Available for women employees with at least 80 days of service.',
            'Maternity leave is 26 weeks. Please inform HR at least 2 months before the expected date.'
        ],
        
        // salary related
        'salary|pay|payscale' => [
            'Salary is credited by the 7th of every month. For any salary-related queries, contact the Finance team.',
            'Salary breakdown: Basic + HRA + Conveyance + Medical Allowance + Special Allowance. Deductions include PF, ESI, and Professional Tax.'
        ],
        'pf|provident fund' => [
            'PF: 12% of basic salary is deducted (employee contribution). Company also contributes 12% (13.61% including admin charges).',
            'Provident Fund deduction is 12% of basic salary. You can check your PF balance on the EPFO portal.'
        ],
        'esi|insurance' => [
            'ESI: If your gross salary is below ₹21,000/month, 0.75% is deducted. Company contributes 3.25%. Covers medical, maternity, and disability benefits.',
            'ESI is applicable for employees earning up to ₹21,000/month. It provides medical, sickness, and maternity benefits.'
        ],
        'tds|tax' => [
            'TDS is deducted based on your declared investments under Section 80C. Please submit your investment proofs to Finance by January end.',
            'Tax is deducted at source based on your estimated annual income. Submit investment proofs to reduce TDS.'
        ],
        'professional tax|PT' => [
            'Professional Tax: ₹200/month for employees earning above ₹25,000/month. Maximum ₹2,500/year.',
            'Professional Tax varies by state. In Maharashtra, it is ₹200/month for salaries above ₹25,000.'
        ],
        
        // attendance related
        'attendance|check.in|check.out|punch' => [
            'Attendance is marked through biometric/face recognition. Office hours: 9:00 AM - 6:00 PM. Grace period: 15 minutes.',
            'Check-in time: 9:00 AM. Check-out: 6:00 PM. Overtime must be approved by your manager.'
        ],
        'overtime|ot' => [
            'Overtime must be pre-approved by your manager. Overtime rate: 2x normal hourly rate for work beyond 8 hours.',
            'Overtime is compensated at double the hourly rate. Please get prior approval from your reporting manager.'
        ],
        'late|latecoming' => [
            'Late arrivals beyond the 15-minute grace period will be marked as half-day if more than 1 hour late.',
            'More than 1 hour late = half day deduction. More than 2 hours = absent. Please be on time.'
        ],
        
        // work related
        'work from home|wfh|remote' => [
            'WFH policy: Maximum 2 days per week with manager approval. Submit request at least 1 day in advance.',
            'Work from home is allowed up to 2 days/week. Apply through the HR portal with manager approval.'
        ],
        'hours|working hours| timings' => [
            'Working hours: 9:00 AM to 6:00 PM, Monday to Friday. Saturday is half-day (9 AM - 1 PM) if required.',
            'Standard working hours are 9 hours (including 1 hour lunch break). Office: 9 AM - 6 PM.'
        ],
        'holiday|company holiday' => [
            'Company holidays follow the Maharashtra state government calendar. Plus: Independence Day, Republic Day, Gandhi Jayanti, Diwali, Holi.',
            'We observe national holidays and major festivals. Check the holiday calendar in the HR portal.'
        ],
        
        // growth related
        'appraisal|promotion|increment' => [
            'Appraisal cycle: Annual (April). Performance review in March. Promotions are based on performance and role availability.',
            'Annual appraisal is in April. Performance review happens in March. Self-assessment submission is mandatory.'
        ],
        'training|learning|course' => [
            'We offer LinkedIn Learning access for all employees. Budget: ₹25,000/year per employee for external courses.',
            'Learning budget: ₹25,000/year. You can access LinkedIn Learning, Udemy, and attend external workshops with approval.'
        ],
        
        // general
        'hr|human resource' => [
            'For HR queries, contact Neha Khaparde (HR Executive) at neha.khaparde@peopleops.in or ext 101.',
            'HR team is available Monday-Friday, 9 AM - 6 PM. Email: hr@peopleops.in'
        ],
        'feedback|suggestion' => [
            'You can submit feedback through the Feedback section. All feedback is anonymous and reviewed monthly.',
            'Feedback is welcome! Submit through the portal or email hr@peopleops.in. All responses are confidential.'
        ],
        'hello|hi|hey|namaste' => [
            'Hello! I am the PeopleOps HR assistant. How can I help you today?',
            'Namaste! Ask me anything about HR policies, leave, salary, or attendance.'
        ],
        'thank|thanks|dhanyavad' => [
            'You\'re welcome! Is there anything else I can help with?',
            'Happy to help! Let me know if you have more questions.'
        ],
    ];
    
    // check each pattern
    foreach ($patterns as $keywords => $responses) {
        $keyword_list = explode('|', $keywords);
        foreach ($keyword_list as $keyword) {
            if (strpos($query_lower, trim($keyword)) !== false) {
                return $responses[array_rand($responses)];
            }
        }
    }
    
    // default response if no pattern matched
    // TODO: add more patterns as employees ask more questions
    $default_responses = [
        'I\'m not sure I understand that. Could you rephrase? You can ask about leave, salary, attendance, or HR policies.',
        'Sorry, I don\'t have information on that. Try asking about leave policy, salary, PF, or attendance rules.',
        'I\'m still learning! Please ask about HR policies, leave, salary, or contact hr@peopleops.in for specific queries.'
    ];
    
    return $default_responses[array_rand($default_responses)];
}

/**
 * Sentiment Analysis for employee feedback
 * uses keyword matching - positive vs negative words
 */
function analyzeSentiment($text) {
    $positive_words = [
        'great', 'good', 'excellent', 'amazing', 'love', 'best', 'happy',
        'supportive', 'helpful', 'fantastic', 'wonderful', 'perfect',
        'enjoy', 'excited', 'learning', 'growth', 'opportunity',
        'flexible', 'flexibility', 'balance', 'collaborative', 'talented',
        'impressive', 'recommend', 'proud', 'grateful', 'appreciate',
        // hindi/indian context
        'accha', 'badhiya', 'sahi', 'ekdum', 'zabardast', 'mast'
    ];
    
    $negative_words = [
        'bad', 'poor', 'terrible', 'worst', 'slow', 'outdated', 'hate',
        'stressed', 'overworked', 'underpaid', 'micromanage', 'toxic',
        'complaint', 'problem', 'issue', 'difficult', 'frustrated',
        'burnout', 'exhausted', 'unfair', 'politics', 'favoritism',
        // hindi/indian context
        'kharab', 'ganda', 'galat', 'dikkat', 'bekaar', 'stress'
    ];
    
    $text_lower = strtolower($text);
    $pos_count = 0;
    $neg_count = 0;
    
    foreach ($positive_words as $word) {
        if (strpos($text_lower, $word) !== false) $pos_count++;
    }
    
    foreach ($negative_words as $word) {
        if (strpos($text_lower, $word) !== false) $neg_count++;
    }
    
    if ($pos_count > $neg_count) return 'positive';
    if ($neg_count > $pos_count) return 'negative';
    return 'neutral';
}

/**
 * Attrition Risk Scoring
 * calculates risk based on attendance, leave patterns, and feedback
 */
function calculateAttritionRisk($conn, $employee_id) {
    $risk_score = 0;
    $factors = [];
    
    // factor 1: attendance pattern (absent more = higher risk)
    $attendance = $conn->query("
        SELECT 
            COUNT(*) as total_days,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
            SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_days,
            SUM(overtime_hours) as total_overtime
        FROM attendance 
        WHERE employee_id = $employee_id 
        AND attendance_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ")->fetch_assoc();
    
    if ($attendance['total_days'] > 0) {
        $absence_rate = ($attendance['absent_days'] + ($attendance['half_days'] * 0.5)) / $attendance['total_days'];
        if ($absence_rate > 0.2) { // more than 20% absence
            $risk_score += 30;
            $factors[] = 'high absence rate (' . round($absence_rate * 100) . '%)';
        } elseif ($absence_rate > 0.1) {
            $risk_score += 15;
            $factors[] = 'moderate absence rate';
        }
        
        // low overtime might indicate disengagement (or good work-life balance)
        if ($attendance['total_overtime'] < 2 && $attendance['total_days'] > 10) {
            $risk_score += 5;
            $factors[] = 'low overtime hours';
        }
    }
    
    // factor 2: leave frequency
    $leaves = $conn->query("
        SELECT COUNT(*) as leave_count 
        FROM leaves 
        WHERE employee_id = $employee_id 
        AND start_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
        AND status = 'approved'
    ")->fetch_assoc();
    
    if ($leaves['leave_count'] > 5) {
        $risk_score += 20;
        $factors[] = 'frequent leaves (' . $leaves['leave_count'] . ' in 3 months)';
    } elseif ($leaves['leave_count'] > 3) {
        $risk_score += 10;
        $factors[] = 'moderate leave usage';
    }
    
    // factor 3: sentiment of recent feedback
    $feedback = $conn->query("
        SELECT feedback_text, rating 
        FROM feedback 
        WHERE employee_id = $employee_id 
        ORDER BY created_at DESC LIMIT 3
    ")->fetch_all(MYSQLI_ASSOC);
    
    if (count($feedback) > 0) {
        $avg_rating = array_sum(array_column($feedback, 'rating')) / count($feedback);
        $negative_count = 0;
        foreach ($feedback as $f) {
            if (analyzeSentiment($f['feedback_text']) == 'negative') $negative_count++;
        }
        
        if ($avg_rating < 3) {
            $risk_score += 25;
            $factors[] = 'low satisfaction rating (' . round($avg_rating, 1) . '/5)';
        }
        
        if ($negative_count >= 2) {
            $risk_score += 15;
            $factors[] = 'negative feedback patterns';
        }
    }
    
    // factor 4: tenure (new employees or very long tenure might be at risk)
    $employee = $conn->query("SELECT date_of_joining FROM employees WHERE id = $employee_id")->fetch_assoc();
    if ($employee) {
        $tenure_months = (strtotime('now') - strtotime($employee['date_of_joining'])) / (30 * 24 * 60 * 60);
        if ($tenure_months < 6) {
            $risk_score += 10;
            $factors[] = 'new employee (< 6 months)';
        } elseif ($tenure_months > 36) {
            $risk_score += 5;
            $factors[] = 'long tenure (> 3 years) - check growth opportunities';
        }
    }
    
    // determine risk level
    if ($risk_score >= 60) $level = 'high';
    elseif ($risk_score >= 30) $level = 'medium';
    else $level = 'low';
    
    return [
        'score' => min(100, $risk_score),
        'level' => $level,
        'factors' => $factors
    ];
}

/**
 * Calculate Indian payroll with statutory deductions
 * PF, ESI, Professional Tax
 */
function calculatePayroll($basic_salary) {
    // HRA: 50% of basic for non-metro, 40% for metro (Pune is non-metro for HRA)
    $hra = $basic_salary * 0.40;
    
    // Conveyance: fixed ₹1600
    $conveyance = 1600;
    
    // Medical Allowance: fixed ₹1250
    $medical = 1250;
    
    // Special Allowance: remaining to make it look realistic
    $special = max(0, ($basic_salary * 0.10));
    
    $gross = $basic_salary + $hra + $conveyance + $medical + $special;
    
    // PF: 12% of basic (employee contribution)
    $pf = min($basic_salary * 0.12, 1800); // max PF is 1800
    
    // ESI: 0.75% of gross if gross < 21000
    $esi = ($gross <= 21000) ? round($gross * 0.0075, 2) : 0;
    
    // Professional Tax: ₹200/month if basic > 25000
    $pt = ($basic_salary > 25000) ? 200 : 0;
    
    // TDS: rough calculation based on old regime
    $annual_gross = $gross * 12;
    $annual_80c = 150000; // assume full 80C investment
    $taxable = $annual_gross - 50000 - $annual_80c; // standard deduction + 80C
    
    if ($taxable <= 0) {
        $tds = 0;
    } elseif ($taxable <= 250000) {
        $tds = 0;
    } elseif ($taxable <= 500000) {
        $tds = ($taxable - 250000) * 0.05;
    } elseif ($taxable <= 1000000) {
        $tds = 12500 + ($taxable - 500000) * 0.20;
    } else {
        $tds = 112500 + ($taxable - 1000000) * 0.30;
    }
    $tds_monthly = round($tds / 12, 2);
    
    $total_deductions = $pf + $esi + $pt + $tds_monthly;
    $net_salary = $gross - $total_deductions;
    
    return [
        'basic' => $basic_salary,
        'hra' => $hra,
        'conveyance' => $conveyance,
        'medical' => $medical,
        'special' => $special,
        'gross' => $gross,
        'pf' => $pf,
        'esi' => $esi,
        'pt' => $pt,
        'tds' => $tds_monthly,
        'total_deductions' => $total_deductions,
        'net' => $net_salary
    ];
}
?>
