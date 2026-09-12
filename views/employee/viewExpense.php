<?php
session_start();
require_once '../../models/expenseModel.php';

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 4; 
$expense_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Pass $user_id to restrict access to owned expenses only
$expense = getExpenseDetailsById($expense_id, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Details - Expense Tracker</title>
    <link rel="stylesheet" href="/M1_expense_budget_system_fixed/views/css/style.css">
    <link rel="stylesheet" href="/M1_expense_budget_system_fixed/views/css/employee.css">
    <style>
        .details-container { max-width: 600px; }
        .details-title { font-size: 2rem; margin-bottom: 35px; color: #ffffff; }
        .details-grid { display: grid; grid-template-columns: 160px 1fr; row-gap: 20px; font-size: 1.05rem; }
        .details-label { color: #a0a8b9; }
        .details-value { color: #ffffff; }
        .back-link-container { margin-top: 50px; }
        .back-link { color: #ffffff; text-decoration: underline; }
        .back-link:hover { color: #e5c185; }
    </style>
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div id="main-content">
        <div class="details-container">
            <h1 class="details-title">Expense details</h1>

            <?php if ($expense): ?>
                <div class="details-grid">
                    <div class="details-label">Title</div>
                    <div class="details-value"><?php echo htmlspecialchars($expense['expense_title']); ?></div>

                    <div class="details-label">Category</div>
                    <div class="details-value"><?php echo htmlspecialchars($expense['category_name']); ?></div>

                    <div class="details-label">Amount</div>
                    <div class="details-value"><?php echo number_format($expense['expense_amount']); ?> Tk</div>

                    <div class="details-label">Date</div>
                    <div class="details-value"><?php echo htmlspecialchars($expense['expense_date']); ?></div>

                    <div class="details-label">Description</div>
                    <div class="details-value"><?php echo htmlspecialchars(!empty($expense['expense_description']) ? $expense['expense_description'] : 'N/A'); ?></div>

                    <div class="details-label">Status</div>
                    <div class="details-value status-<?php echo strtolower($expense['expense_status']); ?>">
                        <?php echo htmlspecialchars($expense['expense_status']); ?>
                    </div>

                    <?php if (!empty($expense['manager_name'])): ?>
                        <div class="details-label">Approved by</div>
                        <div class="details-value"><?php echo htmlspecialchars($expense['manager_name'] . ' (Manager)'); ?></div>

                        <div class="details-label">Approval date</div>
                        <div class="details-value"><?php echo htmlspecialchars($expense['approval_date'] ?? 'N/A'); ?></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p style="color: #ffffff;">Expense record not found or access denied.</p>
            <?php endif; ?>

            <div class="back-link-container">
                <a href="expenses.php" class="back-link">Back to my expenses</a>
            </div>
        </div>
    </div>

</body>
</html>