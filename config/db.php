<?php

// Check for full database connection URL (standard on Railway, Render, etc.)
if ($db_url = getenv("MYSQL_URL") ?: getenv("DATABASE_URL")) {
    $parsed = parse_url($db_url);
    $host     = $parsed["host"] ?? "localhost";
    $username = $parsed["user"] ?? "root";
    $password = $parsed["pass"] ?? "";
    $database = ltrim($parsed["path"] ?? "warrantyvault", "/");
    $port     = isset($parsed["port"]) ? (int)$parsed["port"] : 3306;
} else {
    // Individual environment variables or default to local XAMPP
    $host     = getenv("DB_HOST") ?: "localhost";
    $username = getenv("DB_USER") ?: "root";
    $password = getenv("DB_PASSWORD") !== false ? getenv("DB_PASSWORD") : "";
    $database = getenv("DB_NAME") ?: "warrantyvault";
    $port     = getenv("DB_PORT") ? (int)getenv("DB_PORT") : 3306;
}

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($host, $username, $password, $database, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("
    <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif; max-width: 600px; margin: 60px auto; padding: 24px; border: 1px solid #fecaca; background: #fef2f2; border-radius: 12px; color: #991b1b;'>
        <h2 style='margin-top:0; color:#dc2626;'>Database Connection Error</h2>
        <p><strong>Could not connect to MySQL database:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <hr style='border:0; border-top:1px solid #fecaca; margin:16px 0;'>
        <p><strong>How to fix:</strong></p>
        <ol style='padding-left: 20px; line-height: 1.6;'>
            <li>If running locally: Ensure <strong>MySQL</strong> is started in your XAMPP Control Panel.</li>
            <li>If running on the cloud (Render / Railway): Ensure your database service is active and the environment variables (<code>DB_HOST</code> or <code>MYSQL_URL</code>) are configured.</li>
            <li>Ensure the database named <code>warrantyvault</code> exists.</li>
        </ol>
    </div>");
}
?>