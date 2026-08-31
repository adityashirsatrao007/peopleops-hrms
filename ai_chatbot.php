<?php require_once 'includes/header.php';
require_once 'includes/ai_helpers.php';

// Handle chat
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = sanitize($_POST['query']);
    $response = hrChatbot($query);
    
    // save to logs
    $conn->query("INSERT INTO chatbot_logs (user_query, bot_response) VALUES ('$query', '" . sanitize($response) . "')");
    
    $_SESSION['chat_history'][] = ['query' => $query, 'response' => $response];
}

$chat_history = $_SESSION['chat_history'] ?? [];
?>

<h2>HR Chatbot</h2>

<div class="card" style="border-left: 4px solid #4285f4;">
    <h2 style="color: #4285f4;">Ask PeopleOps Bot</h2>
    <p style="color:#666; font-size:13px;">AI-powered HR assistant - ask about leave, salary, attendance, policies</p>
    
    <!-- Chat Display -->
    <div id="chatBox" style="background:#f8f9fa; border-radius:8px; padding:20px; max-height:400px; overflow-y:auto; margin-bottom:15px;">
        <?php if (empty($chat_history)): ?>
        <div style="text-align:center; color:#888; padding:20px;">
            <p style="font-size:24px;">🤖</p>
            <p>Hello! I'm the PeopleOps HR assistant. How can I help you today?</p>
            <p style="font-size:12px; color:#aaa;">Try asking: "What is the leave policy?" or "How does PF work?"</p>
        </div>
        <?php else: ?>
            <?php foreach ($chat_history as $chat): ?>
            <div style="margin-bottom:15px;">
                <div style="text-align:right;">
                    <div style="display:inline-block; background:#4285f4; color:white; padding:8px 12px; border-radius:12px; max-width:70%; text-align:left;">
                        <?= htmlspecialchars($chat['query']) ?>
                    </div>
                </div>
                <div style="text-align:left; margin-top:8px;">
                    <div style="display:inline-block; background:white; border:1px solid #e0e0e0; padding:8px 12px; border-radius:12px; max-width:70%;">
                        🤖 <?= htmlspecialchars($chat['response']) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Chat Input -->
    <form method="POST" style="display:flex; gap:10px;">
        <input type="text" name="query" class="form-control" required 
               placeholder="Ask about leave, salary, attendance, HR policies..." autofocus>
        <button type="submit" class="btn btn-success">Send</button>
    </form>
</div>

<!-- Quick Questions -->
<div class="card">
    <h2>Quick Questions</h2>
    <p style="color:#666; font-size:13px;">click to ask</p>
    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;">
        <a href="?query=What+is+the+leave+policy" class="btn" style="background:#e8f5e9; color:#2d8e47;">Leave Policy</a>
        <a href="?query=How+does+PF+deduction+work" class="btn" style="background:#e3f2fd; color:#4285f4;">PF Deduction</a>
        <a href="?query=What+are+the+working+hours" class="btn" style="background:#fff3e0; color:#f57c00;">Working Hours</a>
        <a href="?query=When+is+salary+credited" class="btn" style="background:#f3e5f5; color:#9c27b0;">Salary Date</a>
        <a href="?query=How+to+apply+for+work+from+home" class="btn" style="background:#fce4ec; color:#e91e63;">WFH Policy</a>
        <a href="?query=What+is+the+appraisal+process" class="btn" style="background:#e0f2f1; color:#00897b;">Appraisal</a>
        <a href="?query=Tell+me+about+ESI+benefits" class="btn" style="background:#fbe9e7; color:#e64a19;">ESI Benefits</a>
        <a href="?query=Who+is+the+HR+contact" class="btn" style="background:#f1f8e9; color:#558b2f;">HR Contact</a>
    </div>
</div>

<!-- How it works -->
<div class="card" style="border-left: 4px solid #9c27b0;">
    <h2 style="color:#9c27b0;">How the Chatbot Works</h2>
    <p style="color:#666; font-size:13px;">this is a rule-based AI system, not a real ML model</p>
    <div style="font-size:13px; margin-top:10px;">
        <p><strong>1.</strong> You type a question</p>
        <p><strong>2.</strong> The bot scans for keywords (leave, salary, PF, attendance, etc.)</p>
        <p><strong>3.</strong> It matches keywords to predefined HR policy responses</p>
        <p><strong>4.</strong> If no match found, it gives a default "I don't understand" response</p>
        <p style="color:#888; margin-top:10px;"><strong>TODO:</strong> In future, could integrate with OpenAI API or train a proper NLP model</p>
    </div>
</div>

<script>
// auto scroll chat to bottom
var chatBox = document.getElementById('chatBox');
if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

<?php 
// handle quick question GET links
if (isset($_GET['query'])) {
    $_POST['query'] = $_GET['query'];
    // trigger the same logic
    $query = sanitize($_GET['query']);
    $response = hrChatbot($query);
    $conn->query("INSERT INTO chatbot_logs (user_query, bot_response) VALUES ('$query', '" . sanitize($response) . "')");
    $_SESSION['chat_history'][] = ['query' => $query, 'response' => $response];
    header("Location: ai_chatbot.php");
    exit();
}
?>

<?php require_once 'includes/footer.php'; ?>
