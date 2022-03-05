<?php
class Database
{
    // DB Params
    private $connection;

    // DB Connect
    public function connect()
    {
        $this->connection = null;

        $url = getenv('JAWSDB_URL');
        $dbParts = parse_url($url);

        $host = $dbParts['host'];
        $username = $dbParts['user'];
        $password = $dbParts['pass'];
        $dbName = ltrim($dbParts['path'], '/');

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
            exit("Unable to commit to the database");
        }

        return $this->connection;
    }
}
