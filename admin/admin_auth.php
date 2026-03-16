<?php
session_start();
// Set timezone to ensure correct time display
date_default_timezone_set('Asia/Manila'); // Adjust timezone as needed

$admin_server_name = "10.2.0.9";
$connectionOptions = [
    "UID" => "sa",
    "PWD" => "S3rverDB02lrn25",
    "Database" => "LRNPH_OJT"
];

$admin_conn = sqlsrv_connect($admin_server_name, $connectionOptions);

if (!$admin_conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if (!empty($username) && !empty($password)) {
        // Query to get employee information from LRNPH_OJT database
        $query = "SELECT
                lu.username,
                lu.password,
                ml.FirstName + ' ' + ml.LastName as fullname,
                REPLACE(ml.Department, ' - LRN', '') as department,
                ml.PositionTitle,
                ml.EmployeeID
                FROM LRNPH.dbo.lrnph_users lu
                LEFT JOIN LRNPH_OJT.dbo.lrn_master_list ml
                    ON lu.username = ml.BiometricsID
                WHERE lu.username = ?";

        $params = array($username);
        $stmt = sqlsrv_query($admin_conn, $query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['employee_id'] = $row['EmployeeID'];
                $_SESSION['department'] = $row['department'];
                $_SESSION['position_title'] = $row['PositionTitle'];
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_login_time'] = time();

                // List of specifically authorized EmployeeIDs for admin access
                $authorized_employee_ids = [
                    '2012-00003',
                    '2013-00823'
                ];

                // Check if user has admin privileges
                // Either by specific EmployeeID OR by being in Information Technology Department
                $is_authorized_employee = in_array($row['EmployeeID'], $authorized_employee_ids);
                $is_it_department = (trim($row['department']) === 'Information Technology Department');

                if ($is_authorized_employee || $is_it_department) {
                    $_SESSION['is_admin'] = true;
                    $_SESSION['admin_role'] = 'admin';
                    header("Location: admin.php");
                } else {
                    // Not authorized for admin access
                    header("Location: admin_login.php?error=unauthorized");
                }
                exit();
            } else {
                header("Location: admin_login.php?error=invalid");
                exit();
            }
        } else {
            header("Location: admin_login.php?error=invalid");
            exit();
        }
    }
}
sqlsrv_close($admin_conn);
?>