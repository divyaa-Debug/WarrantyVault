<?php

// Ensure session directory is writable in serverless environments like Vercel
if (session_status() === PHP_SESSION_NONE) {
    if (is_dir('/tmp') && is_writable('/tmp')) {
        @session_save_path('/tmp');
    }
}

// -------------------------------------------------------------
// SQLite Fallback Adapter for Serverless / Vercel Deployments
// -------------------------------------------------------------
class SQLiteResultWrapper {
    private $rows;
    private $cursor = 0;
    public $num_rows = 0;

    public function __construct(array $rows) {
        $this->rows = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc() {
        if ($this->cursor < $this->num_rows) {
            return $this->rows[$this->cursor++];
        }
        return null;
    }
}

class SQLiteStmtWrapper {
    private $pdo;
    private $stmt;
    private $params = [];
    private $lastResult = null;

    public function __construct(PDO $pdo, $sql) {
        $this->pdo = $pdo;
        $this->stmt = $pdo->prepare($sql);
    }

    public function bind_param($types, &...$args) {
        $this->params = [];
        foreach ($args as $arg) {
            $this->params[] = $arg;
        }
    }

    public function execute() {
        try {
            $success = $this->stmt->execute($this->params);
            if ($this->stmt->columnCount() > 0) {
                $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->lastResult = new SQLiteResultWrapper($rows);
            } else {
                $this->lastResult = new SQLiteResultWrapper([]);
            }
            return $success;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 || strpos($e->getMessage(), 'UNIQUE') !== false) {
                throw new mysqli_sql_exception("Duplicate entry: " . $e->getMessage(), 1062);
            }
            throw new mysqli_sql_exception($e->getMessage(), (int)$e->getCode());
        }
    }

    public function get_result() {
        return $this->lastResult;
    }
}

class SQLiteConnWrapper {
    private $pdo;
    public $connect_error = null;

    public function __construct($path) {
        $this->pdo = new PDO("sqlite:" . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->initSchema();
    }

    private function initSchema() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                product_name TEXT NOT NULL,
                category TEXT,
                brand TEXT,
                model TEXT,
                serial_number TEXT,
                purchase_date TEXT NOT NULL,
                warranty_duration INTEGER NOT NULL,
                expiry_date TEXT NOT NULL,
                price REAL,
                store_name TEXT,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            );
        ");

        // Pre-seed demo user if empty so visitors can immediately test
        $count = (int)$this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count === 0) {
            $hash = password_hash("password123", PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute(["Demo User", "demo@warrantyvault.com", $hash]);
            $userId = (int)$this->pdo->lastInsertId();

            $pStmt = $this->pdo->prepare("
                INSERT INTO products (user_id, product_name, category, brand, model, serial_number, purchase_date, warranty_duration, expiry_date, price, store_name, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $today = new DateTime();
            $pStmt->execute([
                $userId,
                "MacBook Pro 16",
                "Laptop",
                "Apple",
                "M3 Max",
                "SN-88392019",
                $today->format("Y-m-d"),
                2,
                (clone $today)->modify("+2 years")->format("Y-m-d"),
                2499.00,
                "Apple Store",
                "AppleCare+ Coverage"
            ]);
        }
    }

    public function prepare($sql) {
        return new SQLiteStmtWrapper($this->pdo, $sql);
    }

    public function query($sql) {
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return new SQLiteResultWrapper($rows);
    }

    public function set_charset($charset) {
        return true;
    }
}

// -------------------------------------------------------------
// Database Connection Resolution (MySQL First -> SQLite Fallback)
// -------------------------------------------------------------
$conn = null;

// Check for cloud database URL (standard on Railway, Render, etc.)
if ($db_url = getenv("MYSQL_URL") ?: getenv("DATABASE_URL")) {
    $parsed = parse_url($db_url);
    $host     = $parsed["host"] ?? "localhost";
    $username = $parsed["user"] ?? "root";
    $password = $parsed["pass"] ?? "";
    $database = ltrim($parsed["path"] ?? "warrantyvault", "/");
    $port     = isset($parsed["port"]) ? (int)$parsed["port"] : 3306;
} else {
    $host     = getenv("DB_HOST");
    $username = getenv("DB_USER") ?: "root";
    $password = getenv("DB_PASSWORD") !== false ? getenv("DB_PASSWORD") : "";
    $database = getenv("DB_NAME") ?: "warrantyvault";
    $port     = getenv("DB_PORT") ? (int)getenv("DB_PORT") : 3306;
}

// If running in local XAMPP on Windows without DB_HOST specified, default to localhost
$is_local_windows = (strtoupper(substr(PHP_OS, 0, 3)) === "WIN");
if (!$host) {
    $host = $is_local_windows ? "localhost" : "127.0.0.1";
}

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_init();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
    $conn->real_connect($host, $username, $password, $database, $port);
    $conn->set_charset("utf8mb4");
} catch (Throwable $e) {
    // If MySQL is unavailable (e.g. Vercel without cloud DB configured),
    // automatically fallback to SQLite in /tmp so the live site works with zero errors!
    if (class_exists("PDO") && in_array("sqlite", PDO::getAvailableDrivers())) {
        $sqlitePath = (is_dir('/tmp') && is_writable('/tmp'))
            ? '/tmp/warrantyvault.sqlite'
            : __DIR__ . '/warrantyvault.sqlite';
        $conn = new SQLiteConnWrapper($sqlitePath);
    } else {
        die("
        <div style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif; max-width: 600px; margin: 60px auto; padding: 24px; border: 1px solid #fecaca; background: #fef2f2; border-radius: 12px; color: #991b1b;'>
            <h2 style='margin-top:0; color:#dc2626;'>Database Connection Error</h2>
            <p><strong>Could not connect to MySQL database:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <hr style='border:0; border-top:1px solid #fecaca; margin:16px 0;'>
            <p><strong>How to fix:</strong></p>
            <ol style='padding-left: 20px; line-height: 1.6;'>
                <li>If running locally: Ensure <strong>MySQL</strong> is started in your XAMPP Control Panel.</li>
                <li>If running on the cloud (Vercel / Render): Add your <code>DB_HOST</code>, <code>DB_USER</code>, <code>DB_PASSWORD</code> in Environment Variables.</li>
                <li>Ensure the database named <code>warrantyvault</code> exists.</li>
            </ol>
        </div>");
    }
}
?>