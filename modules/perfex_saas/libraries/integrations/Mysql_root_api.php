<?php

/**
 * This class manages database connections and operations using PDO.
 */
class Mysql_root_api
{
    public $host;
    public $username;
    public $password;
    public $port;
    public $prefix;
    private $conn;

    /**
     * Initializes the database credentials.
     * 
     * @param string $username The database username.
     * @param string $password The database password.
     * @param string $host     The host name or IP address.
     * @param int    $port     The port number.
     * @param string $prefix   The database name and user prefix;
     */
    public function init($username, $password, $host = 'localhost', $port = 3306, $prefix = '')
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->prefix = $prefix;
        $this->connect();
        return $this;
    }

    public function addPrefix($text)
    {
        if (empty($this->prefix)) return $text;

        $text = str_starts_with($text, $this->prefix) ? $text : $this->prefix . $text;
        return $text;
    }

    /**
     * Establishes a database connection.
     * 
     * @return PDO|null Returns a PDO instance on success, or null on failure.
     */
    private function connect($reconnect = false)
    {
        if ($this->conn && !$reconnect) return $this->conn;

        $dsn = "mysql:host={$this->host};port={$this->port}";

        $this->conn = new PDO(
            $dsn,
            $this->username,
            $this->password,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'"
            ]
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->conn;
    }

    /**
     * Tests the database connection by creating and deleting a temporary database.
     * 
     * @return bool Returns true if the connection and operation were successful, false otherwise.
     */
    public function testConnection($testUserCreation = true)
    {
        $this->connect();

        if ($this->conn !== null) {

            // Generate a random database name
            $testDbName = $this->addPrefix('test_' . mt_rand());

            try {
                // Delete db if exist
                $this->deleteDatabase($testDbName);
            } catch (\Throwable $th) {
            }

            // Create the test database
            $this->createDatabase($testDbName);

            // Confirm the database creation
            $databaseCreated = $this->conn->query("SHOW DATABASES LIKE '{$testDbName}'")->rowCount() > 0;

            // Delete the test database
            $databaseDeleted = $this->deleteDatabase($testDbName);

            if ($testUserCreation) {
                try {
                    $this->deleteDatabaseUser($testDbName);
                } catch (\Throwable $th) {
                }
                $databaseCreated = $this->createDatabaseUser($testDbName, bin2hex(random_bytes(8)));
                $databaseCreated = $databaseCreated && $this->assignUserToDatabase($testDbName, $testDbName);
                $databaseDeleted = $this->deleteDatabaseUser($testDbName);
            }

            return $databaseCreated && $databaseDeleted;
        }

        return false;
    }

    /**
     * Creates a new database.
     * 
     * @param string $dbName The name of the new database to be created.
     * 
     * @return bool Returns true on successful creation, otherwise false.
     */
    public function createDatabase($dbName)
    {

        $dbName = $this->addPrefix($dbName);
        $sql = "CREATE DATABASE IF NOT EXISTS $dbName";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Deletes an existing database.
     * 
     * @param string $dbName The name of the database to be deleted.
     * 
     * @return bool Returns true on successful deletion, otherwise false.
     */
    public function deleteDatabase($dbName)
    {
        $dbName = $this->addPrefix($dbName);
        $sql = "DROP DATABASE IF EXISTS $dbName";
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Create a database user.
     *
     * @param string $username The username of the new database user.
     * @param string $password The password for the new database user.
     *
     * @return bool True if the user creation is successful, otherwise false.
     */
    public function createDatabaseUser($username, $password)
    {
        $username = $this->addPrefix($username);
        $pdo = $this->connect();
        $stmt = $pdo->prepare("CREATE USER '$username'@'%' IDENTIFIED BY '$password'");
        return $stmt->execute();
    }

    /**
     * Assign a user to a specific database.
     *
     * @param string $username     The username of the database user.
     * @param string $databaseName The name of the database.
     *
     * @return bool True if the assignment is successful, otherwise false.
     */
    public function assignUserToDatabase($username, $databaseName)
    {
        $username = $this->addPrefix($username);
        $databaseName = $this->addPrefix($databaseName);

        $pdo = $this->connect();
        $stmt = $pdo->prepare("GRANT ALL PRIVILEGES ON $databaseName.* TO '$username'@'%'");
        return $stmt->execute();
    }

    /**
     * Delete a database user.
     *
     * @param string $username The username of the database user to be deleted.
     *
     * @return bool True if the user deletion is successful, otherwise false.
     */
    public function deleteDatabaseUser($username)
    {
        $username = $this->addPrefix($username);
        $pdo = $this->connect();
        $stmt = $pdo->prepare("DROP USER '$username'@'%'");
        return $stmt->execute();
    }
}