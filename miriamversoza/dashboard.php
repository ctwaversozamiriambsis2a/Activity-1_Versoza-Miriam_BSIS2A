<?php
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$sql = "SELECT
            COUNT(*) AS total_products,
            COALESCE(SUM(quantity), 0) AS total_stock,
            COALESCE(SUM(CASE WHEN quantity <= 5 THEN 1 ELSE 0 END), 0) AS low_stock,
            COALESCE(SUM(quantity * price), 0) AS inventory_value
        FROM products";

$result = $conn->query($sql);
$stats = $result->fetch_assoc();

$productQuery = "SELECT * FROM products ORDER BY id DESC LIMIT 5";
$productResult = $conn->query($productQuery);

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: #f7f4e9;
            color: #263b31;
            display: flex;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #536f59;
            color: #fffef8;
            padding: 25px 20px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: #f7f4e9;
            text-decoration: none;
            padding: 14px;
            margin-bottom: 10px;
            border-radius: 10px;
            transition: background .18s, transform .18s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #6b896f;
            transform: translateX(3px);
        }

        .main {
            margin-left: 240px;
            width: calc(100% - 240px);
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .topbar h1 {
            color: #263b31;
            letter-spacing: -.035em;
        }

        .welcome {
            color: #748276;
            margin-top: 8px;
        }

        .logout {
            background: #789d79;
            color: #fffef8;
            padding: 11px 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: transform .18s, background .18s;
        }

        .logout:hover { transform: translateY(-2px); background: #668b69; }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #fffef8;
            padding: 25px;
            border: 1px solid rgba(93,126,98,.12);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(54,79,57,.07);
            transition: transform .2s, box-shadow .2s;
        }

        .card:hover { transform: translateY(-3px); box-shadow: 0 13px 30px rgba(54,79,57,.11); }

        .card h3 {
            color: #748276;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .card h2 {
            color: #263b31;
            font-size: 28px;
        }

        .blue {
            border-left: 5px solid #a9c6a3;
        }

        .green {
            border-left: 5px solid #789d79;
        }

        .orange {
            border-left: 5px solid #dfc487;
        }

        .purple {
            border-left: 5px solid #c1afd1;
        }

        .section {
            background: #fffef8;
            padding: 25px;
            border: 1px solid rgba(93,126,98,.12);
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 8px 24px rgba(54,79,57,.07);
        }

        .section h2 {
            color: #263b31;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5e9df;
        }

        th {
            background: #f1f5ed;
            color: #526356;
        }

        .low {
            color: #a85e4c;
            font-weight: bold;
        }

        .normal {
            color: #527c58;
            font-weight: bold;
        }

        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .action-btn {
            background: #789d79;
            color: #fffef8;
            padding: 13px 20px;
            text-decoration: none;
            border-radius: 10px;
            transition: transform .18s, background .18s, box-shadow .18s;
        }

        .action-btn:hover {
            background: #668b69;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(91,131,94,.2);
        }

        @media(max-width: 1000px) {
            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 650px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            body {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 13px;
            }

            th, td {
                padding: 9px;
            }

            .sidebar h2 { margin-bottom: 18px; }
            .sidebar a { display: inline-block; margin: 4px 3px; padding: 10px 12px; }
            .section { overflow-x: auto; padding: 18px; }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <h2>Inventory System</h2>

        <a href="dashboard.php" class="active">Dashboard</a>

        <a href="products.php">Products</a>

        <a href="stock-in.php">Stock In</a>

        <a href="stock-out.php">Stock Out</a>

        <?php if ($role === 'admin'): ?>
            <a href="users.php">Manage Users</a>
        <?php endif; ?>

        <a href="logout.php">Logout</a>

    </div>


    <!-- MAIN CONTENT -->
    <div class="main">

        <!-- TOP BAR -->
        <div class="topbar">

            <div>
                <h1>Dashboard</h1>

                <p class="welcome">
                    Welcome back, <?= h($username) ?>!
                </p>

                <p class="welcome">
                    Account Role: <?= h($role) ?>
                </p>
            </div>

        </div>


        <!-- DASHBOARD CARDS -->
        <div class="cards">

            <div class="card blue">
                <h3>Total Products</h3>

                <h2>
                    <?= number_format($stats['total_products']) ?>
                </h2>
            </div>


            <div class="card green">
                <h3>Total Stock Items</h3>

                <h2>
                    <?= number_format($stats['total_stock']) ?>
                </h2>
            </div>


            <div class="card orange">
                <h3>Low Stock Products</h3>

                <h2>
                    <?= number_format($stats['low_stock']) ?>
                </h2>
            </div>


            <div class="card purple">
                <h3>Inventory Value</h3>

                <h2>
                    ₱<?= number_format($stats['inventory_value'], 2) ?>
                </h2>
            </div>

        </div>


        <!-- RECENT PRODUCTS -->
        <div class="section">

            <h2>Recent Products</h2>

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($productResult && $productResult->num_rows > 0): ?>

                        <?php while ($product = $productResult->fetch_assoc()): ?>

                            <tr>
                                <td><?= h($product['id']) ?></td>

                                <td><?= h($product['product_name']) ?></td>

                                <td><?= h($product['category']) ?></td>

                                <td><?= h($product['quantity']) ?></td>

                                <td>
                                    ₱<?= number_format((float)$product['price'], 2) ?>
                                </td>

                                <td>
                                    <?php if ($product['quantity'] <= 5): ?>

                                        <span class="low">Low Stock</span>

                                    <?php else: ?>

                                        <span class="normal">Available</span>

                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6">
                                No products available yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- QUICK ACTIONS -->
        <div class="section">

            <h2>Quick Actions</h2>

            <div class="actions">

                <a href="products.php" class="action-btn">
                    + Add Product
                </a>

                <a href="stock-in.php" class="action-btn">
                    + Stock In
                </a>

                <a href="stock-out.php" class="action-btn">
                    - Stock Out
                </a>

            </div>

        </div>

    </div>

</body>
</html>
