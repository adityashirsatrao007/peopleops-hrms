<?php require_once 'includes/header.php';
require_once 'includes/ai_helpers.php';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $text = sanitize($_POST['feedback_text']);
    $rating = intval($_POST['rating']);
    $category = sanitize($_POST['category']);
    $emp_id = intval($_POST['employee_id']);
    
    // analyze sentiment before saving
    $sentiment = analyzeSentiment($text);
    
    $conn->query("INSERT INTO feedback (employee_id, feedback_text, rating, category, sentiment) 
                  VALUES ($emp_id, '$text', $rating, '$category', '$sentiment')");
    
    setMessage('success', 'Feedback submitted! Sentiment detected: ' . ucfirst($sentiment));
    redirect('feedback.php');
}

// Get feedback stats
$allFeedback = $conn->query("
    SELECT f.*, e.first_name, e.last_name, e.emp_id
    FROM feedback f
    JOIN employees e ON f.employee_id=e.id
    ORDER BY f.created_at DESC
");

$avgRating = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM feedback")->fetch_assoc();
?>

<h2>Employee Feedback</h2>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Submit Feedback Form -->
    <div class="card">
        <h2>Submit Feedback</h2>
        <p style="color:#666; font-size:13px;">AI will analyze the sentiment of your feedback automatically</p>
        
        <form method="POST">
            <div class="form-group">
                <label>Employee</label>
                <select name="employee_id" class="form-control" required>
                    <?php 
                    $emps = $conn->query("SELECT id, emp_id, first_name, last_name FROM employees WHERE status='active' ORDER BY emp_id");
                    while ($emp = $emps->fetch_assoc()): ?>
                        <option value="<?= $emp['id'] ?>"><?= $emp['emp_id'] ?> - <?= $emp['first_name'] ?> <?= $emp['last_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="work">Work</option>
                    <option value="management">Management</option>
                    <option value="culture">Culture</option>
                    <option value="compensation">Compensation</option>
                    <option value="growth">Growth</option>
                </select>
            </div>
            <div class="form-group">
                <label>Rating (1-5)</label>
                <select name="rating" class="form-control" required>
                    <option value="1">1 - Very Poor</option>
                    <option value="2">2 - Poor</option>
                    <option value="3">3 - Average</option>
                    <option value="4">4 - Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
            </div>
            <div class="form-group">
                <label>Your Feedback</label>
                <textarea name="feedback_text" class="form-control" rows="4" required 
                          placeholder="Share your thoughts about work environment, management, growth opportunities..."></textarea>
            </div>
            <button type="submit" name="submit_feedback" class="btn btn-success">Submit Feedback</button>
        </form>
    </div>
    
    <!-- Feedback Stats -->
    <div class="card" style="background:#f8f9fa;">
        <h2>Feedback Overview</h2>
        <div style="text-align:center; margin:20px 0;">
            <h3 style="font-size:48px; margin:0; color:#333;"><?= round($avgRating['avg_rating'] ?? 0, 1) ?></h3>
            <p style="color:#666;">Average Rating (out of 5)</p>
            <p style="color:#888; font-size:12px;">based on <?= $avgRating['total'] ?> responses</p>
        </div>
        
        <div style="margin-top:20px;">
            <h3 style="font-size:14px; margin-bottom:10px;">How Sentiment Analysis Works</h3>
            <div style="font-size:13px;">
                <p>positive words: great, good, love, supportive, helpful, growth</p>
                <p>negative words: bad, poor, toxic, frustrated, outdated, slow</p>
                <p style="margin-top:10px; color:#888;">
                    if more positive words found → positive sentiment<br>
                    if more negative words found → negative sentiment<br>
                    otherwise → neutral
                </p>
            </div>
        </div>
    </div>
</div>

<!-- All Feedback -->
<div class="card" style="margin-top: 20px;">
    <h2>All Feedback</h2>
    
    <?php if ($allFeedback->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Category</th>
                <th>Rating</th>
                <th>Feedback</th>
                <th>Sentiment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fb = $allFeedback->fetch_assoc()): ?>
            <tr>
                <td><?= $fb['emp_id'] ?> - <?= $fb['first_name'] ?></td>
                <td style="text-transform:capitalize;"><?= $fb['category'] ?></td>
                <td>
                    <span style="color: <?= $fb['rating'] >= 4 ? '#2d8e47' : ($fb['rating'] >= 3 ? '#fbbc04' : '#ea4335') ?>;">
                        <?= $fb['rating'] ?>/5
                    </span>
                </td>
                <td style="max-width:300px;"><?= htmlspecialchars(mb_substr($fb['feedback_text'], 0, 100)) ?>...</td>
                <td>
                    <span style="background: <?= $fb['sentiment']=='positive' ? '#e8f5e9' : ($fb['sentiment']=='negative' ? '#fce4ec' : '#f8f9fa') ?>; 
                         padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= ucfirst($fb['sentiment'] ?? 'neutral') ?>
                    </span>
                </td>
                <td><?= date('d M', strtotime($fb['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#888;">No feedback submitted yet.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
