<?php
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}

loadLocalEnv(__DIR__ . '/.env');

function loadLocalEnv(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

class SupabaseResult
{
    public int $num_rows = 0;
    private array $rows;
    private int $position = 0;

    public function __construct(array $rows = [])
    {
        $this->rows = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc(): ?array
    {
        if ($this->position >= $this->num_rows) {
            return null;
        }

        return $this->rows[$this->position++];
    }

    public function fetch_all($mode = MYSQLI_ASSOC): array
    {
        return $this->rows;
    }

    public function data_seek(int $offset): bool
    {
        if ($offset < 0 || $offset > $this->num_rows) {
            return false;
        }

        $this->position = $offset;
        return true;
    }
}

class SupabaseStatement
{
    public int $insert_id = 0;
    public int $affected_rows = 0;
    public string $error = '';

    private SupabaseConnection $connection;
    private PDOStatement $statement;
    private array $params = [];
    private ?SupabaseResult $result = null;
    private ?string $returningColumn;

    public function __construct(SupabaseConnection $connection, PDOStatement $statement, ?string $returningColumn = null)
    {
        $this->connection = $connection;
        $this->statement = $statement;
        $this->returningColumn = $returningColumn;
    }

    public function bind_param(string $types, &...$values): bool
    {
        $this->params = [];
        foreach ($values as $value) {
            $this->params[] = $value;
        }
        return true;
    }

    public function execute(): bool
    {
        try {
            $ok = $this->statement->execute($this->params);
            $this->affected_rows = $this->statement->rowCount();
            $this->connection->affected_rows = $this->affected_rows;

            if ($this->statement->columnCount() > 0) {
                $rows = $this->statement->fetchAll(PDO::FETCH_ASSOC);
                $this->result = new SupabaseResult($rows);

                if ($this->returningColumn && isset($rows[0][$this->returningColumn])) {
                    $this->insert_id = (int) $rows[0][$this->returningColumn];
                    $this->connection->insert_id = $this->insert_id;
                }
            } else {
                $this->result = new SupabaseResult();
            }

            return $ok;
        } catch (Throwable $e) {
            $this->error = $e->getMessage();
            $this->connection->error = $this->error;
            return false;
        }
    }

    public function get_result(): SupabaseResult
    {
        return $this->result ?? new SupabaseResult();
    }

    public function close(): bool
    {
        return true;
    }
}

class SupabaseConnection
{
    public string $connect_error = '';
    public string $error = '';
    public int $insert_id = 0;
    public int $affected_rows = 0;

    private PDO $pdo;
    private array $primaryKeys = [
        'admin_forgot_tb' => 'id',
        'admin_list_login_tb' => 'ID',
        'admin_login_tb' => 'admin_id',
        'audit_logs' => 'id',
        'cart' => 'cart_id',
        'cart_items' => 'cart_items_id',
        'category' => 'category_id',
        'forgot_password_tb' => 'id',
        'inventory' => 'inventory_id',
        'login_tb' => 'login_id',
        'log_history_tb' => 'id',
        'notifadmin' => 'notif_id',
        'notifcustomer' => 'notif_id',
        'notifications' => 'notification_id',
        'products' => 'product_id',
        'purchase' => 'purchase_id',
        'purchase_items' => 'purchase_item_id',
        'registers_tb' => 'register_id',
        'reservations' => 'reservation_id',
        'reservation_items' => 'reservation_item_id',
        'walk_in' => 'walk_in_id',
        'walk_in_items' => 'walk_in_item_id',
    ];

    public function __construct()
    {
        $url = getenv('SUPABASE_DB_URL') ?: getenv('DATABASE_URL');

        if ($url) {
            $config = parse_url($url);
            $host = $config['host'] ?? '';
            $port = (int) ($config['port'] ?? 5432);
            $database = isset($config['path']) ? ltrim($config['path'], '/') : 'postgres';
            $user = isset($config['user']) ? rawurldecode($config['user']) : '';
            $password = isset($config['pass']) ? rawurldecode($config['pass']) : '';
        } else {
            $host = getenv('DB_HOST');
            $port = (int) (getenv('DB_PORT') ?: 5432);
            $database = getenv('DB_NAME') ?: 'postgres';
            $user = getenv('DB_USER') ?: 'postgres';
            $password = getenv('DB_PASSWORD');
        }

        $host = $this->normalizeSupabaseHost((string) $host);

        if (empty($host) || empty($password)) {
            die(
                "Connection failed: Missing Supabase database settings. " .
                "Set SUPABASE_DB_URL in Render environment variables or create a local .env file from .env.example."
            );
        }

        if (!in_array('pgsql', PDO::getAvailableDrivers(), true)) {
            die(
                "Connection failed: PHP PostgreSQL driver is not installed/enabled. " .
                "Enable pdo_pgsql in local XAMPP, or deploy to Render using this project's Dockerfile."
            );
        }

        try {
            $dsn = "pgsql:host={$host};port={$port};dbname={$database};sslmode=require";
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (Throwable $e) {
            $this->connect_error = $e->getMessage();
            $message = 'Connection failed: ' . $this->connect_error;

            if (str_starts_with($host, 'db.') && str_contains($host, '.supabase.co')) {
                $message .= ' Use the Supabase Session Pooler connection string instead of the Direct connection string. Direct Supabase database hosts often require IPv6.';
            }

            die($message);
        }
    }

    public function prepare(string $sql)
    {
        try {
            [$sql, $returningColumn] = $this->prepareSql($sql);
            return new SupabaseStatement($this, $this->pdo->prepare($sql), $returningColumn);
        } catch (Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function query(string $sql)
    {
        try {
            [$sql, $returningColumn] = $this->prepareSql($sql);
            $stmt = $this->pdo->query($sql);
            $this->affected_rows = $stmt->rowCount();

            if ($stmt->columnCount() > 0) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($returningColumn && isset($rows[0][$returningColumn])) {
                    $this->insert_id = (int) $rows[0][$returningColumn];
                }
                return new SupabaseResult($rows);
            }

            return true;
        } catch (Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function real_escape_string(string $value): string
    {
        return substr($this->pdo->quote($value), 1, -1);
    }

    public function begin_transaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    public function close(): bool
    {
        return true;
    }

    public function set_charset(string $charset): bool
    {
        return true;
    }

    private function normalizeSupabaseHost(string $host): string
    {
        $host = trim($host);

        if (str_starts_with($host, 'https://') || str_starts_with($host, 'http://')) {
            $parsedHost = parse_url($host, PHP_URL_HOST);
            if (is_string($parsedHost) && $parsedHost !== '') {
                $host = $parsedHost;
            }
        }

        if (preg_match('/^([a-z0-9]+)\.supabase\.co$/i', $host, $match)) {
            return 'db.' . $match[1] . '.supabase.co';
        }

        return $host;
    }

    private function prepareSql(string $sql): array
    {
        $sql = $this->translateMysqlSyntax($sql);
        $sql = $this->quoteMixedCaseColumns($sql);
        $returningColumn = $this->detectReturningColumn($sql);

        if ($returningColumn && stripos($sql, ' returning ') === false) {
            $sql = rtrim($sql, " \t\n\r\0\x0B;") . ' RETURNING "' . $returningColumn . '"';
        }

        return [$sql, $returningColumn];
    }

    private function detectReturningColumn(string $sql): ?string
    {
        if (!preg_match('/^\s*INSERT\s+INTO\s+"?([a-zA-Z_][a-zA-Z0-9_]*)"?\s+/i', $sql, $match)) {
            return null;
        }

        return $this->primaryKeys[$match[1]] ?? null;
    }

    private function quoteMixedCaseColumns(string $sql): string
    {
        $columns = ['totalAmount', 'purchaseMethod', 'purchaseDate', 'login_TnD', 'Login_at', 'Email', 'Password', 'ID'];

        foreach ($columns as $column) {
            $sql = preg_replace('/(?<!")\b' . preg_quote($column, '/') . '\b(?!")/', '"' . $column . '"', $sql);
        }

        return $sql;
    }

    private function translateMysqlSyntax(string $sql): string
    {
        $sql = str_replace('`', '"', $sql);
        $sql = preg_replace('/\bIFNULL\s*\(/i', 'COALESCE(', $sql);
        $sql = preg_replace('/\bNOW\s*\(\s*\)/i', 'CURRENT_TIMESTAMP', $sql);
        $sql = preg_replace('/\bCURDATE\s*\(\s*\)/i', 'CURRENT_DATE', $sql);
        $sql = preg_replace('/DATE_ADD\s*\(\s*CURRENT_TIMESTAMP\s*,\s*INTERVAL\s+(\d+)\s+DAY\s*\)/i', "(CURRENT_TIMESTAMP + INTERVAL '$1 days')", $sql);
        $sql = preg_replace('/TIMESTAMPDIFF\s*\(\s*DAY\s*,\s*([^,]+?)\s*,\s*CURRENT_TIMESTAMP\s*\)\s*>=\s*(\d+)/i', "$1 <= CURRENT_TIMESTAMP - INTERVAL '$2 days'", $sql);
        $sql = preg_replace('/DATEDIFF\s*\(\s*([^,]+?)\s*,\s*CURRENT_DATE\s*\)\s*=\s*(\d+)/i', "$1 = CURRENT_DATE + INTERVAL '$2 days'", $sql);
        $sql = preg_replace('/\bLIMIT\s+\?/i', 'LIMIT CAST(? AS integer)', $sql);

        return $sql;
    }
}

$conn = new SupabaseConnection();
?>
