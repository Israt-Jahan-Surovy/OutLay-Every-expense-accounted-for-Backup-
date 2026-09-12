<?php
session_start();
require_once '../../models/dbConnect.php';

$conn = dbConnection();

if (!$conn) {
    die("Database connection failed.");
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 4; 

// Determine active filter tab
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : 'All';

// Build secure prepared query based on filter
if (in_array($status_filter, ['Pending', 'Approved', 'Rejected'])) {
    $sql = "
        SELECT e.expense_id, e.expense_date, e.expense_title, c.category_name, e.expense_amount, e.expense_status 
        FROM expensetable e
        LEFT JOIN categorytable c ON e.category_id = c.category_id
        WHERE e.user_id = ? AND e.expense_status = ?
        ORDER BY e.expense_date DESC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $status_filter);
} else {
    $sql = "
        SELECT e.expense_id, e.expense_date, e.expense_title, c.category_name, e.expense_amount, e.expense_status 
        FROM expensetable e
        LEFT JOIN categorytable c ON e.category_id = c.category_id
        WHERE e.user_id = ?
        ORDER BY e.expense_date DESC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
}

mysqli_stmt_execute($stmt);
$expenses_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Expenses - Expense Tracker</title>
    <!-- Updated Absolute Paths -->
    <link rel="stylesheet" href="/M1_expense_budget_system_fixed/views/css/style.css">
    <link rel="stylesheet" href="/M1_expense_budget_system_fixed/views/css/employee.css">
    <script src="../js/expense.js"></script>
    <style>
        .page-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: normal;
        }

        .filter-tabs {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filter-tab {
            color: #ffffff;
            text-decoration: none;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .filter-tab.active {
            background-color: #121620;
            color: #ffffff;
            border: 1px solid #283042;
        }

        .filter-tab:hover:not(.active) {
            color: #e5c185;
        }

        .action-link {
            color: #4169e1;
            text-decoration: none;
        }

        .action-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Render Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content Area with ID matching sidebar offset -->
    <div id="main-content">
        
        <div class="page-header">
            <h1 class="page-title">My expenses</h1>
            
            <div class="filter-tabs">
                <a href="expenses.php?status=All" class="filter-tab <?php echo ($status_filter === 'All') ? 'active' : ''; ?>">All</a>
                <a href="expenses.php?status=Pending" class="filter-tab <?php echo ($status_filter === 'Pending') ? 'active' : ''; ?>">Pending</a>
                <a href="expenses.php?status=Approved" class="filter-tab <?php echo ($status_filter === 'Approved') ? 'active' : ''; ?>">Approved</a>
                <a href="expenses.php?status=Rejected" class="filter-tab <?php echo ($status_filter === 'Rejected') ? 'active' : ''; ?>">Rejected</a>
            </div>
        </div>

        <table class="expense-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($expenses_result && mysqli_num_rows($expenses_result) > 0): ?>
                    <?php while ($expense = mysqli_fetch_assoc($expenses_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                            <td><?php echo htmlspecialchars($expense['expense_title']); ?></td>
                            <td><?php echo htmlspecialchars($expense['category_name'] ?? 'Uncategorized'); ?></td>
                            <td><?php echo number_format($expense['expense_amount']); ?> Tk</td>
                            <td class="status-<?php echo strtolower($expense['expense_status']); ?>">
                                <?php echo htmlspecialchars($expense['expense_status']); ?>
                            </td>
                            <td>
                                <a href="viewExpense.php?id=<?php echo $expense['expense_id']; ?>" class="action-link">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #a0a8b9; padding: 20px;">
                            No expenses found for this filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</body>
</html>

<?php 
mysqli_stmt_close($stmt);
mysqli_close($conn); 
?>