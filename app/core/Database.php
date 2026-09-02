<?php
// /app/core/Database.php

class Database {
    private $driver = DB_DRIVER;
    private $host = DB_HOST;
    private $port = DB_PORT;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $db_name = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    // Add this property to track transaction value
    private $inTransaction = false;

    // Yg awal ada didalam model semua, pindahkan kesini saja
    public function __construct() {
        $dsn = "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->db_name}";
        // $this->pdo = new PDO($dsn, $user, $pass);
 
        // Option: digunakan utk mengoptimasi DB kita, agar koneksi tetap persisten
        $option = [
            // Jangan stringify blob, dan gunakan native prepares
            PDO::ATTR_STRINGIFY_FETCHES => false, // ⚠️ PENTING: Jangan stringify BLOB
            PDO::ATTR_EMULATE_PREPARES => false, // ⚠️ PENTING: Gunakan native prepares
            // Yg General
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // This is important for Transactions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $option);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Buat di-return ke root index.php khususnya buat Login & Registration Auth PHP
    public function getConnection() : PDO {
        return $this->dbh;
    }

    // ===== TRANSACTION METHODS =====

    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        if(!$this->inTransaction) {
            $this->dbh->beginTransaction();
            $this->inTransaction = true;
        }
    }

    /**
     * Commit a transaction
     */
    public function commit() {
        if($this->inTransaction) {
            $this->dbh->commit();
            $this->inTransaction = false;
        }
    }

    /**
     * Rollback a transaction
     */
    public function rollBack() {
        if($this->inTransaction) {
            $this->dbh->rollBack();
            $this->inTransaction = false;
        }
    }

    /**
     * Check if currently in a transaction
     */
    public function inTransaction() {
        return $this->inTransaction;
    }

    /**
     * Execute a transaction with automatic commit/rollback
     * Usage: $db->transaction(function() use ($db) {
     *      $db->query("INSERT ...");
     *      $db->execute();
     * }); 
     */
    public function transaction(callable $callback) {
        try {
            $this->beginTransaction();
            $callback($this);
            $this->commit();
            return true;
        } catch(Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    // ===== Query, Bind, Execute, Etc =====

    public function query($query) {  // querynya apapun nantinya
        // Yg isinya statement, diisi dgn handlernya (dbh), prepare, dan query
        $this->stmt = $this->dbh->prepare($query);
        // Disini querynya kita siapin, usernya maunya apa, apakah select, insert, update, delete.
    }

    // Selanjutnya kita jg perlu binding datanya, sp tau didalam querynya itu ada wherenya misalkan.  Lalu misalnya insert, insert itu valuesnya itu apa, kalau update itu ada set datanya apa, jadi istilahnya parameternya.
    public function bind($param, $value, $type = null) {
        if(is_null($type)) {
            switch(true) {
                case is_int($value) :
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value) :
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value) :
                    $type = PDO::PARAM_NULL;
                    break;
                default :
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Lalu kita execute
    public function execute() {
        try {
            return $this->stmt->execute(); // Return true/false
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    // Lalu disini kita tentukan, setelah dieksekusi hasilnya kalian pengen banyak atau cuman 1 saja datanya?

    // Kalau mau banyak
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    } // Ini kalau mau banyak, kyk contoh "SELECT * FROM user"

    // Kalau mau cuman satu
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    } // Ini adalah wrappernya.

    // Diatas ini semua bisa kita pakai untuk tabel manapun nantinya

    // Untuk menghitung ada berapa baris yg baru berubah didalam tabelnya (misal: ada tambah, ada ngepush, ada ubah nantinya) itu ada angkanya
    public function rowCount() { // rowCount ini punya kita
        return $this->stmt->rowCount(); //rowCount ini punya PDO
    }

    /** ===============
     * Get the Last inserted ID
     * Alternative with error handling
     * 
     * NOTE (Postgres-specific): unlike MySQL, PDO::lastInsertId() on
     * Postgres needs the sequence name for tables that don't use it
     * implicitly, e.g. lastInsertId('users_id_seq'). Pass $sequence when
     * you know the table's PK sequence name, or better, use
     * "INSERT ... RETURNING id" in your insert() and fetch it directly.
     */
    public function lastInsertID($sequence = null) {
        try {
            return $this->dbh->lastInsertID($sequence);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log("lastInsertID error: ", $this->error);
            return false;
        }
    }

    // ===== Convenience Methods =====

    /**
     * Get a single record by ID
     * @param string $table - Table name
     * @param int $id - Record ID
     * @return array|false
     */
    public function find($table, $id, $pk = 'id') { // param 2 dan 3 wajib sama persis
        $this->query("SELECT * FROM {$table} WHERE {$pk} = :id");
        $this->bind(':id', $id);
        return $this->single();
    } // Jika PK = 'id', maka cukup isi find('automotive_api.cars', $id), param 3 ga perlu isi.

    /**
     * Get all records from a table
     * @param string $table - Table name
     * @param string $order - ORDER BY clause
     * @return array
     */
    public function all($table, $order = 'id DESC') {
        $this->query("SELECT * FROM {$table} ORDER BY {$order}");
        return $this->resultSet();
    }

    /**
     * Insert a record
     * @param string $table - Table name
     * @param array $data - Associative array of column => value
     * @return bool|int - Last Insert ID or false
     */
    public function insert($table, $data, $pk = 'id') {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders}) RETURNING {$pk}";
        $this->query($sql);

        foreach($data as $key => $value) {
            $this->bind(":{$key}", $value);
        }

        $this->execute();
        $row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        // Using the RETURNING id, which is the standard/reliable way to get a new PK in Postgres.

        return $row ? $row[$pk] : false;

        // if($this->execute()) {
        //     return $this->lastInsertID();
        // }
        // return false;
    }
    
    /**
     * Update a record
     * @param string $table - Table Name
     * @param array $data - Associative array of column => value
     * @param int $id - Record ID
     * @return bool
     */
    public function update($table, $data, $id, $pk = 'id') {
        $set = [];
        foreach(array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $set = implode(", ", $set);

        $sql = "UPDATE {$table} SET {$set} WHERE {$pk} = :id";
        $this->query($sql);

        foreach($data as $key => $value) {
            $this->bind(':{$key}', $value);
        }
        $this->bind(':id', $id);
        
        return $this->execute();
    }

    /**
     * Delete a record
     * @param string $table - Table name
     * @param int $id - Record ID
     * @return bool
     */
    public function delete($table, $id, $pk = 'id') {
        $this->query("DELETE FROM {$table} WHERE {$pk} = :id");
        $this->bind(':id', $id);
        return $this->execute();
    }

    // ===== ERROR HANDLING =====

    public function getError() {
        return $this->error;
    }
        
    public function debugDumpParams() {
        return $this->stmt->debugDumpParams();
    }
}