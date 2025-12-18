<?php

class Config {

  public static function DB_HOST() {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    if ($host === 'localhost') {
      $host = '127.0.0.1';
    }
    return $host;
  }

  public static function DB_PORT() {
    return getenv('DB_PORT') ?: '3306';
  }

  public static function DB_NAME() {
    return getenv('DB_NAME') ?: 'ems';
  }

  public static function DB_USER() {
    return getenv('DB_USER') ?: 'root';
  }

  public static function DB_PASSWORD() {
    return getenv('DB_PASSWORD') ?: '';
  }

  public static function JWT_SECRET() {
    return getenv('JWT_SECRET') ?: '12345';
  }
}

class Database {
  private static $connection = null;

  public static function connect() {
    if (self::$connection === null) {
      try {
        $dsn =
          "mysql:host=" . Config::DB_HOST() .
          ";port=" . Config::DB_PORT() .
          ";dbname=" . Config::DB_NAME() .
          ";charset=utf8mb4";

        self::$connection = new PDO(
          $dsn,
          Config::DB_USER(),
          Config::DB_PASSWORD(),
          array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
          )
        );
      } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
      }
    }
    return self::$connection;
  }
}
?>