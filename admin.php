<?php
// admin.php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "website_projects";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
$login_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $admin_username = $conn->real_escape_string($_POST['username']);
    $admin_password = $conn->real_escape_string($_POST['password']);
    
    // In a real application, use proper password hashing and secure credentials
    $valid_username = "khaled";
    $valid_password = "khaled2003";
    
    if ($admin_username === $valid_username && $admin_password === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $login_error = "Invalid username or password";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Check if admin is logged in
$logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Get all project requests
$requests = [];
$visitor_stats = [];
$page_views = [];
$referrers = [];
if ($logged_in) {
    // Project requests
    $result = $conn->query("SELECT * FROM project_requests ORDER BY submission_date DESC");
    if ($result) {
        $requests = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Visitor statistics
    $total_visitors = $conn->query("SELECT COUNT(*) as count FROM site_visits")->fetch_assoc()['count'];
    $today_visitors = $conn->query("SELECT COUNT(*) as count FROM site_visits WHERE DATE(visit_time) = CURDATE()")->fetch_assoc()['count'];
    $unique_visitors = $conn->query("SELECT COUNT(DISTINCT ip_address) as count FROM site_visits")->fetch_assoc()['count'];
    
    // Get popular pages
    $page_views_result = $conn->query("SELECT page_url, COUNT(*) as views FROM site_visits GROUP BY page_url ORDER BY views DESC LIMIT 5");
    if ($page_views_result) {
        $page_views = $page_views_result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get referrers
    $referrers_result = $conn->query("SELECT referrer, COUNT(*) as count FROM site_visits WHERE referrer != '' GROUP BY referrer ORDER BY count DESC LIMIT 5");
    if ($referrers_result) {
        $referrers = $referrers_result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get visitor data for chart (last 7 days)
    $visitor_data_result = $conn->query("SELECT DATE(visit_time) as date, COUNT(*) as visits FROM site_visits WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(visit_time) ORDER BY date");
    if ($visitor_data_result) {
        $visitor_stats = $visitor_data_result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Project request statistics
    $total_requests = $conn->query("SELECT COUNT(*) as count FROM project_requests")->fetch_assoc()['count'];
    $recent_requests = $conn->query("SELECT COUNT(*) as count FROM project_requests WHERE submission_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
}

// Delete request
if ($logged_in && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM project_requests WHERE id = $id");
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hello Worlders - Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0c023d;
            color: white;
            margin: 0;
            padding: 0;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #2c1b85;
        }
        .logout-btn {
            background-color: #ff4081;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #e73370;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background-color: #1a0f5e;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card h3 {
            margin-top: 0;
        }
        .stat-card .number {
            font-size: 2.5em;
            font-weight: bold;
            margin: 10px 0;
            color: #ff4081;
        }
        .chart-container {
            background-color: #1a0f5e;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #2c1b85;
        }
        th {
            background-color: #1a0f5e;
        }
        tr:hover {
            background-color: #1a0f5e;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .view-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            background-color: #1a0f5e;
            padding: 30px;
            border-radius: 10px;
        }
        .login-form h2 {
            text-align: center;
            margin-top: 0;
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #ff4081;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-form button:hover {
            background-color: #e73370;
        }
        .error {
            color: #f44336;
            text-align: center;
            margin: 10px 0;
        }
        .request-details {
            background-color: #1a0f5e;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #ff79c6;
            text-decoration: none;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
        .analytics-section {
            margin: 30px 0;
        }
        .analytics-section h2 {
            border-bottom: 1px solid #2c1b85;
            padding-bottom: 10px;
        }
        .popular-pages, .top-referrers {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .analytics-card {
            background-color: #1a0f5e;
            padding: 15px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php if (!$logged_in): ?>
    <div class="login-form">
        <h2>Admin Login</h2>
        <?php if ($login_error): ?>
            <div class="error"><?php echo $login_error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
    <?php else: ?>
    <div class="admin-container">
        <div class="header">
            <h1>Hello Worlders - Admin Panel</h1>
            <a href="admin.php?logout=1" class="logout-btn">Logout</a>
        </div>
        
        <?php if (!isset($_GET['view'])): ?>
        <div class="analytics-section">
            <h2>Website Analytics</h2>
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Visitors</h3>
                    <div class="number"><?php echo $total_visitors; ?></div>
                    <p>All-time visitors</p>
                </div>
                <div class="stat-card">
                    <h3>Today's Visitors</h3>
                    <div class="number"><?php echo $today_visitors; ?></div>
                    <p>Visitors today</p>
                </div>
                <div class="stat-card">
                    <h3>Unique Visitors</h3>
                    <div class="number"><?php echo $unique_visitors; ?></div>
                    <p>Distinct IP addresses</p>
                </div>
            </div>
            
            <div class="chart-container">
                <canvas id="visitorChart"></canvas>
                <script>
                    const ctx = document.getElementById('visitorChart').getContext('2d');
                    const visitorChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [<?php foreach($visitor_stats as $stat) { echo "'" . date('M j', strtotime($stat['date'])) . "',"; } ?>],
                            datasets: [{
                                label: 'Visitors',
                                data: [<?php foreach($visitor_stats as $stat) { echo $stat['visits'] . ","; } ?>],
                                backgroundColor: 'rgba(255, 64, 129, 0.2)',
                                borderColor: 'rgba(255, 64, 129, 1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    ticks: {
                                        color: 'white'
                                    }
                                },
                                x: {
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    ticks: {
                                        color: 'white'
                                    }
                                }
                            }
                        }
                    });
                </script>
            </div>
            
            <div class="popular-pages">
                <div class="analytics-card">
                    <h3>Most Popular Pages</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($page_views as $page): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($page['page_url']); ?></td>
                                <td><?php echo $page['views']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="analytics-card">
                    <h3>Top Referrers</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referrers as $referrer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($referrer['referrer']); ?></td>
                                <td><?php echo $referrer['count']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="analytics-section">
            <h2>Project Requests</h2>
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Requests</h3>
                    <div class="number"><?php echo $total_requests; ?></div>
                    <p>All project requests</p>
                </div>
                <div class="stat-card">
                    <h3>Recent Requests</h3>
                    <div class="number"><?php echo $recent_requests; ?></div>
                    <p>Last 7 days</p>
                </div>
                <div class="stat-card">
                    <h3>Projects Live</h3>
                    <div class="number">3</div>
                    <p>Completed projects</p>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Project Idea</th>
                        <th>Submission Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo htmlspecialchars($request['name']); ?></td>
                        <td><?php echo htmlspecialchars($request['email']); ?></td>
                        <td><?php echo htmlspecialchars($request['project_idea']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($request['submission_date'])); ?></td>
                        <td>
                            <a href="admin.php?view=<?php echo $request['id']; ?>" class="action-btn view-btn">View</a>
                            <a href="admin.php?delete=<?php echo $request['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: 
            $request_id = intval($_GET['view']);
            $request_details = $conn->query("SELECT * FROM project_requests WHERE id = $request_id")->fetch_assoc();
            if ($request_details):
        ?>
        <a href="admin.php" class="back-btn">&larr; Back to all requests</a>
        <div class="request-details">
            <h2>Request Details</h2>
            <p><strong>ID:</strong> <?php echo $request_details['id']; ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($request_details['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($request_details['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($request_details['phone']); ?></p>
            <p><strong>Project Idea:</strong> <?php echo htmlspecialchars($request_details['project_idea']); ?></p>
            <p><strong>Description:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($request_details['description'])); ?></p>
            <p><strong>Submitted on:</strong> <?php echo date('M d, Y H:i', strtotime($request_details['submission_date'])); ?></p>
        </div>
        <?php endif; endif; ?>
    </div>
    <?php endif; ?>
</body>
</html>