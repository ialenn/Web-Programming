<?php
class Database {
  private static $host = 'localhost';
  private static $dbName = 'ems';
  private static $username = 'root';
  private static $password = '';
  private static $connection = null;

  public static function connect() {
    if (self::$connection === null) {
      try {
        self::$connection = new PDO(
          "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8mb4",
          self::$username,
          self::$password,
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false // safer prepared statements
          ]
        );
      } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
      }
    }
    return self::$connection;
  }
}
?>