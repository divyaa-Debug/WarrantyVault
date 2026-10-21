<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "warrantyvault";

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("
    <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif; max-width: 600px; margin: 60px auto; padding: 24px; border: 1px solid #fecaca; background: #fef2f2; border-radius: 12px; color: #991b1b;'>
        <h2 style='margin-top:0; color:#dc2626;'>Database Connection Error</h2>
        <p><strong>Could not connect to MySQL database:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <hr style='border:0; border-top:1px solid #fecaca; margin:16px 0;'>
        <p><strong>How to fix:</strong></p>
        <ol style='padding-left: 20px; line-height: 1.6;'>
            <li>Open the <strong>XAMPP Control Panel</strong> (or run <code>C:\\xampp\\xampp-control.exe</code>).</li>
            <li>Click <strong>Start</strong> next to both <strong>Apache</strong> and <strong>MySQL</strong>.</li>
            <li>Ensure the database named <code>warrantyvault</code> exists.</li>
        </ol>
    </div>");
}
?>