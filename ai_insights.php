<?php require_once 'includes/header.php';
require_once 'includes/ai_helpers.php';

// Get all active employees
$employees = $conn->query("
    SELECT e.*, d.name as department_name
    FROM employees e
    LEFT JOIN departments d ON e.department_id=d.id
    WHERE e.status='active'
    ORDER BY e.emp_id
");

// Calculate attrition risk for each employee
$employeeRisks = [];
while ($emp = $employees->fetch_assoc()) {
    $risk = calculateAttritionRisk($conn, $emp['id']);
    $employeeRisks[] = [
        'employee' => $emp,
        'risk' => $risk
    ];
}

// Sort by risk score descending
usort($employeeRisks, function($a, $b) {
    return $b['risk']['score'] - $a['risk']['score'];
});

// Department-wise stats
$deptStats = $conn->query("
    SELECT d.name as department,
           COUNT(e.id) as headcount,
           AVG(e.basic_salary) as avg_salary,
           (SELECT COUNT(*) FROM attendance a 
            JOIN employees emp ON a.employee_id=emp.id 
            WHERE emp.department_id=d.id AND a.status='absent'
            AND a.attendance_date >= NOW() - INTERVAL '30 days') as total_absences
    FROM departments d
    LEFT JOIN employees e ON d.id=e.department_id AND e.status='active'
    GROUP BY d.id, d.name
    HAVING headcount > 0
    ORDER BY headcount DESC
");

// Sentiment summary
$allFeedback = $conn->query("SELECT feedback_text, rating, category FROM feedback");
$sentimentSummary = ['positive' => 0, 'negative' => 0, 'neutral' => 0];
$categoryRatings = [];
while ($f = $allFeedback->fetch_assoc()) {
    $s = analyzeSentiment($f['feedback_text']);
    $sentimentSummary[$s]++;
    if (!isset($categoryRatings[$f['category']])) {
        $categoryRatings[$f['category']] = ['total' => 0, 'count' => 0];
    }
    $categoryRatings[$f['category']]['total'] += $f['rating'];
    $categoryRatings[$f['category']]['count']++;
}

// High risk employees count
$highRisk = count(array_filter($employeeRisks, function($r) { return $r['risk']['level'] == 'high'; }));
$mediumRisk = count(array_filter($employeeRisks, function($r) { return $r['risk']['level'] == 'medium'; }));
?>

<h2>AI Insights & Analytics</h2>
<p style="color:#666; margin-bottom: 20px;">HR analytics powered by rule-based AI algorithms</p>

<!-- Risk Overview -->
<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid #ea4335;">
        <h3 style="color:#ea4335;"><?= $highRisk ?></h3>
        <p>High Risk</p>
    </div>
    <div class="stat-card" style="border-left: 4px solid #fbbc04;">
        <h3 style="color:#fbbc04;"><?= $mediumRisk ?></h3>
        <p>Medium Risk</p>
    </div>
    <div class="stat-card" style="border-left: 4px solid #2d8e47;">
        <h3 style="color:#2d8e47;"><?= count($employeeRisks) - $highRisk - $mediumRisk ?></h3>
        <p>Low Risk</p>
    </div>
    <div class="stat-card" style="border-left: 4px solid #4285f4;">
        <h3><?= $sentimentSummary['positive'] ?></h3>
        <p>Positive Feedback</p>
    </div>
</div>

<!-- Attrition Risk List -->
<div class="card" style="border-left: 4px solid #ea4335;">
    <h2 style="color: #ea4335;">Attrition Risk Analysis</h2>
    <p style="color:#666; font-size:13px;">AI-scored risk based on attendance patterns, leave frequency, feedback sentiment, and tenure</p>
    
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Risk Score</th>
                <th>Risk Level</th>
                <th>Key Factors</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employeeRisks as $item): ?>
            <tr>
                <td>
                    <strong><?= $item['employee']['emp_id'] ?></strong><br>
                    <?= $item['employee']['first_name'] ?> <?= $item['employee']['last_name'] ?>
                </td>
                <td><?= $item['employee']['department_name'] ?></td>
                <td>
                    <div style="background:#e0e0e0; border-radius:4px; height:20px; width:100px;">
                        <div style="background: <?= $item['risk']['score'] >= 60 ? '#ea4335' : ($item['risk']['score'] >= 30 ? '#fbbc04' : '#2d8e47') ?>; 
                             height:100%; border-radius:4px; width:<?= min(100, $item['risk']['score']) ?>%;"></div>
                    </div>
                    <small><?= $item['risk']['score'] ?>/100</small>
                </td>
                <td>
                    <span style="background: <?= $item['risk']['level']=='high' ? '#ea4335' : ($item['risk']['level']=='medium' ? '#fbbc04' : '#2d8e47') ?>; 
                         color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= ucfirst($item['risk']['level']) ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($item['risk']['factors'])): ?>
                        <ul style="margin:0; padding-left:15px; font-size:12px;">
                            <?php foreach ($item['risk']['factors'] as $factor): ?>
                                <li><?= $factor ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <small style="color:#888;">No risk factors detected</small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Sentiment Analysis -->
<div class="card" style="border-left: 4px solid #9c27b0;">
    <h2 style="color: #9c27b0;">Feedback Sentiment Analysis</h2>
    <p style="color:#666; font-size:13px;">analyzing employee feedback using keyword-based sentiment detection</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <h3 style="font-size:14px;">Overall Sentiment</h3>
            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <div style="background:#e8f5e9; padding:15px; border-radius:8px; text-align:center; flex:1;">
                    <h3 style="margin:0; font-size:24px; color:#2d8e47;"><?= $sentimentSummary['positive'] ?></h3>
                    <p style="margin:5px 0 0; font-size:12px;">Positive</p>
                </div>
                <div style="background:#f8f9fa; padding:15px; border-radius:8px; text-align:center; flex:1;">
                    <h3 style="margin:0; font-size:24px; color:#666;"><?= $sentimentSummary['neutral'] ?></h3>
                    <p style="margin:5px 0 0; font-size:12px;">Neutral</p>
                </div>
                <div style="background:#fce4ec; padding:15px; border-radius:8px; text-align:center; flex:1;">
                    <h3 style="margin:0; font-size:24px; color:#ea4335;"><?= $sentimentSummary['negative'] ?></h3>
                    <p style="margin:5px 0 0; font-size:12px;">Negative</p>
                </div>
            </div>
        </div>
        
        <div>
            <h3 style="font-size:14px;">Category-wise Ratings</h3>
            <table style="margin-top:10px;">
                <thead><tr><th>Category</th><th>Avg Rating</th><th>Count</th></tr></thead>
                <tbody>
                    <?php foreach ($categoryRatings as $cat => $data): ?>
                    <tr>
                        <td style="text-transform:capitalize;"><?= $cat ?></td>
                        <td>
                            <?php $avg = $data['total'] / $data['count']; ?>
                            <span style="color: <?= $avg >= 4 ? '#2d8e47' : ($avg >= 3 ? '#fbbc04' : '#ea4335') ?>;">
                                <?= round($avg, 1) ?>/5
                            </span>
                        </td>
                        <td><?= $data['count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Department Performance -->
<div class="card">
    <h2>Department Overview</h2>
    <table>
        <thead>
            <tr><th>Department</th><th>Headcount</th><th>Avg Salary</th><th>Absences (30d)</th></tr>
        </thead>
        <tbody>
            <?php while ($dept = $deptStats->fetch_assoc()): ?>
            <tr>
                <td><strong><?= $dept['department'] ?></strong></td>
                <td><?= $dept['headcount'] ?></td>
                <td><?= formatCurrency($dept['avg_salary']) ?></td>
                <td>
                    <span style="color: <?= $dept['total_absences'] > 5 ? '#ea4335' : '#2d8e47' ?>;">
                        <?= $dept['total_absences'] ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- How the AI Works -->
<div class="card" style="border-left: 4px solid #4285f4;">
    <h2 style="color:#4285f0;">How the AI Works</h2>
    <div style="font-size:13px; margin-top:10px;">
        <p><strong>Attrition Risk Scoring:</strong> Combines 4 factors - attendance pattern (absence rate), leave frequency, feedback sentiment, and tenure. Each factor adds points to a 0-100 risk score.</p>
        <p style="margin-top:10px;"><strong>Sentiment Analysis:</strong> Keyword-based matching - positive words ("great", "love", "supportive") vs negative words ("bad", "toxic", "frustrated"). Simple but effective for HR feedback.</p>
        <p style="margin-top:10px; color:#888;"><strong>TODO:</strong> Could integrate with a proper NLP model for more accurate sentiment analysis. Also could add predictive analytics for hiring needs.</p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
