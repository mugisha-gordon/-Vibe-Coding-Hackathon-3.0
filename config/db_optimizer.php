<?php
class DBOptimizer {
    private $conn;
    private $cache;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->cache = new Cache();
    }

    public function query($sql, $params = [], $cache_key = null, $cache_time = 3600) {
        // Try to get from cache first
        if ($cache_key && ($cached_result = $this->cache->get($cache_key))) {
            return $cached_result;
        }

        // Prepare and execute query
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch all rows
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Cache the result if cache key is provided
        if ($cache_key) {
            $this->cache->set($cache_key, $data);
        }

        return $data;
    }

    public function optimizeTables() {
        $tables = ['users', 'children', 'programs', 'staff', 'success_stories', 'child_programs', 'donations', 'volunteers'];
        foreach ($tables as $table) {
            $this->conn->query("OPTIMIZE TABLE $table");
            $this->conn->query("ANALYZE TABLE $table");
        }
    }

    public function addIndexes() {
        // Add indexes for frequently searched columns
        $indexes = [
            'users' => ['username', 'email'],
            'children' => ['first_name', 'last_name', 'status'],
            'programs' => ['name', 'status'],
            'staff' => ['email', 'status'],
            'success_stories' => ['title'],
            'donations' => ['donor_name', 'status'],
            'volunteers' => ['email', 'status']
        ];

        foreach ($indexes as $table => $columns) {
            foreach ($columns as $column) {
                $index_name = "idx_{$table}_{$column}";
                $this->conn->query("CREATE INDEX IF NOT EXISTS $index_name ON $table($column)");
            }
        }
    }
}
?> 