<?php
session_start();
require_once '../config/db.php';

$total_users = 0;
$total_assets = 0;
$total_wallets = 0;

if ($conn) {
    // Fetch total number of users
    $res = $conn->query('SELECT COUNT(*) AS total FROM users');
    $total_users = $res ? $res->fetch_assoc()['total'] : 0;

    // Fetch total number of assets
    $res = $conn->query('SELECT COUNT(*) AS total FROM user_assets');
    $total_assets = $res ? $res->fetch_assoc()['total'] : 0;

    // Fetch total number of wallets
    $res = $conn->query('SELECT COUNT(*) AS total FROM wallets');
    $total_wallets = $res ? $res->fetch_assoc()['total'] : 0;

    // Fetch total number of email imports
    $res = $conn->query('SELECT COUNT(*) AS total FROM wallet_imports');

    // Fetch total number of email imports
    $total_email_imports = $res ? $res->fetch_assoc()['total'] : 0;
}
if ($conn) {
    $get_wallet_email = $conn->query("SELECT email FROM wallet_imports");
    $wallet_emails = [];
    while ($row = $get_wallet_email->fetch_assoc()) {
        $wallet_emails[] = $row['email'];
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | QFS Self Custody Ledger</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" />
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <style>
        .page-wrapper {
            background-color: #f4f6f9;
        }

        .card-body {
            background-color: #fff;
        }
    </style>
</head>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="./index.php" class="text-nowrap logo-img">
                        <img src="./assets/images/logos/logo.svg" style="width: auto; height: 30px;" alt="" />
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                            <span class="hide-menu">Home</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="./index.php" aria-expanded="false">
                                <iconify-icon icon="solar:atom-line-duotone"></iconify-icon>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <!-- EMAIL CONNECTED -->
                        <li class="nav-small-cap">
                            <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                            <span class="hide-menu">User</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="email_imports.php" aria-expanded="false">
                                <iconify-icon icon="solar:shield-keyhole-minimalistic-bold"></iconify-icon>
                                <span class="hide-menu">Wallet Email</span>
                            </a>
                        </li>
                        <!-- EMAIL CONNECTED -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="email_users.php" aria-expanded="false">
                                <iconify-icon icon="solar:dialog-bold"></iconify-icon>
                                <span class="hide-menu">Email Users</span>
                            </a>
                        </li>
                        <!-- EMAIL CONNECTED -->
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="users.php" aria-expanded="false">
                                <iconify-icon icon="solar:user-bold"></iconify-icon>
                                <span class="hide-menu">Users</span>
                            </a>
                        </li>

                         <!-- EMAIL CONNECTED -->
                        <li class="nav-small-cap">
                            <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                            <span class="hide-menu">Settings</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link primary-hover-bg" href="email_imports.php" aria-expanded="false">
                                <iconify-icon icon="solar:settings-bold"></iconify-icon>
                                <span class="hide-menu">Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- Sidebar End -->

        <!-- Main wrapper -->
        <div class="body-wrapper">
            <!-- Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <img src="./assets/images/profile/user1.jpg" alt="" width="35" height="35"
                                        class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-mail fs-6"></i>
                                            <p class="mb-0 fs-3">My Account</p>
                                        </a>
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-list-check fs-6"></i>
                                            <p class="mb-0 fs-3">My Task</p>
                                        </a>
                                        <a href="logout.php"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Header End -->

            <div class="container-fluid">
                <!-- Row 1: Inject live stats -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <h5 class="card-title fw-semibold">Total Users</h5>
                                        <h4 class="fw-bold mb-0 text-primary"><?php echo $total_users; ?></h4>
                                    </div>
                                    <div class="bg-light-primary rounded-circle p-2">
                                        <i class="ti ti-user fs-6 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <h5 class="card-title fw-semibold">Total Assets</h5>
                                        <h4 class="fw-bold mb-0 text-info"><?php echo $total_assets; ?></h4>
                                    </div>
                                    <div class="bg-light-info rounded-circle p-2">
                                        <i class="ti ti-coin fs-6 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <h5 class="card-title fw-semibold">Total Wallets</h5>
                                        <h4 class="fw-bold mb-0 text-success"><?php echo $total_wallets; ?></h4>
                                    </div>
                                    <div class="bg-light-success rounded-circle p-2">
                                        <i class="ti ti-wallet fs-6 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <h5 class="card-title fw-semibold">Manage Users</h5>
                                        <h4 class="fw-bold mb-0 text-primary"><?php echo $total_users; ?></h4>
                                    </div>
                                    <div class="bg-light-primary rounded-circle p-2">
                                        <i class="ti ti-user fs-6 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <h5 class="card-title fw-semibold">Wallet connected & Emails</h5>
                                        <a href="email_imports.php"></a>
                                        <h4 class="fw-bold mb-0 text-info"><?php echo $total_email_imports; ?></h4>
                                    </div>
                                    <div class="bg-light-info rounded-circle p-2">
                                        <i class="ti ti-coin fs-6 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>

    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

</body>

</html>