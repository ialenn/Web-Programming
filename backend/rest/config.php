<?php

class Config {

  public static function DB_HOST() {
    return getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
  }

  public static function DB_PORT() {
    return getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: 3306;
  }

  public static function DB_NAME() {
    return getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: getenv('MYSQLDATABASE');
  }

  public static function DB_USER() {
    return getenv('DB_USER') ?: getenv('MYSQLUSER');
  }

  public static function DB_PASSWORD() {
    return getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD');
  }

  public static function JWT_SECRET() {
    return getenv('JWT_SECRET') ?: 'dev-secret';
  }
}

class Database {
  private static $connection = null;

  public static function connect() {
    if (self::$connection === null) {
      try {
        self::$connection = new PDO(
          "mysql:host=" . Config::DB_HOST() .
          ";port=" . Config::DB_PORT() .
          ";dbname=" . Config::DB_NAME() .
          ";charset=utf8mb4",
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