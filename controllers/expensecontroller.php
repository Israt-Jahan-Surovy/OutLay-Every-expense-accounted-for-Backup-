<?php
session_start();

require_once __DIR__ . "/../models/expenseModel.php";
//require_once __DIR__ . "/../models/budgetModel.php";

// Auth Guard Check
if (!isset($_SESSION["user_id"])) {
    header("Location: ../views/login.php");
    exit();
}

$user_id   = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"] ?? "User";
$user_role = $_SESSION["user_role"] ?? "Employee";

// ==========================================
// 1. DASHBOARD DATA AGGREGATION
// ==========================================
$current_month = date("Y-m");

// Fetch Assigned Budget
$my_budget = 0.00;
/*if (function_exists("getUserBudget")) {
    $budget_data = getUserBudget($user_id);
    if ($budget_data && isset($budget_data["budget_amount"])) {
        $my_budget = (float)$budget_data["budget_amount"];
    }
}*/

// Fetch Approved Total Spent
$spent = (float)getApprovedTotalByUser($user_id, $current_month);

// Fetch Pending Count
$pending_count = 0;
$pending_result = getExpensesByUser($user_id, "Pending");
if ($pending_result) {
    $pending_count = mysqli_num_rows($pending_result);
}

// Calculate Remaining Budget
$remaining = $my_budget - $spent;

// Fetch Recent Expenses
$recent_result = getExpensesByUser($user_id, "All");


// ==========================================
// 2. CREATE EXPENSE (POST)
// ==========================================
if (isset($_POST["add_expense"])) {
    $expense_title       = trim($_POST["expense_title"]);
    $expense_amount      = $_POST["expense_amount"];
    $expense_date        = $_POST["expense_date"];
    $expense_description = trim($_POST["expense_description"]);
    $category_id         = $_POST["category_id"];

    if (
        empty($expense_title) ||
        empty($expense_amount) ||
        $expense_amount <= 0 ||
        empty($expense_date) ||
        empty($category_id)
    ) {
        header("Location: ../views/employee/addExpense.php?error=" .
               urlencode("Please fill all required fields correctly"));
        exit();
    }

    $result = addExpense(
        $expense_title,
        $expense_amount,
        $expense_date,
        $expense_description,
        $user_id,
        $category_id
    );

    if ($result) {
        header("Location: ../views/employee/expenses.php?success=" .
               urlencode("Expense Added Successfully"));
    } else {
        header("Location: ../views/employee/addExpense.php?error=" .
               urlencode("Failed to Add Expense"));
    }
    exit();
}


// ==========================================
// 3. UPDATE EXPENSE (POST)
// ==========================================
if (isset($_POST["update_expense"])) {
    $expense_id          = $_POST["expense_id"];
    $expense_title       = trim($_POST["expense_title"]);
    $expense_amount      = $_POST["expense_amount"];
    $expense_date        = $_POST["expense_date"];
    $expense_description = trim($_POST["expense_description"]);
    $category_id         = $_POST["category_id"];

    $result = updateExpense(
        $expense_id,
        $expense_title,
        $expense_amount,
        $expense_date,
        $expense_description,
        $category_id,
        $user_id
    );

    if ($result) {
        header("Location: ../views/employee/expenses.php?success=" .
               urlencode("Expense Updated Successfully"));
    } else {
        header("Location: ../views/employee/expenses.php?error=" .
               urlencode("Only your pending expenses can be edited"));
    }
    exit();
}


// ==========================================
// 4. DELETE EXPENSE (GET)
// ==========================================
if (isset($_GET["delete"])) {
    $expense_id = $_GET["delete"];

    $result = deleteExpense($expense_id, $user_id);

    if ($result) {
        header("Location: ../views/employee/expenses.php?success=" .
               urlencode("Expense Deleted Successfully"));
    } else {
        header("Location: ../views/employee/expenses.php?error=" .
               urlencode("Only your pending expenses can be deleted"));
    }
    exit();
}
?>