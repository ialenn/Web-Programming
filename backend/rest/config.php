<?php

class Config {

  private static function env($key, $default = null) {
    $val = getenv($key);
    if ($val === false || $val === null || $val === '') {
      return $default;
    }
    return $val;
  }

  public static function DB_HOST() {
    return self::env('DB_HOST', 'localhost');
  }

  public static function DB_PORT() {
    return self::env('DB_PORT', '3306');
  }

  public static function DB_NAME() {
    return self::env('DB_NAME', 'ems');
  }

  public static function DB_USER() {
    return self::env('DB_USER', 'root');
  }

  public static function DB_PASSWORD() {
    return self::env('DB_PASSWORD', '');
  }

  public static function JWT_SECRET() {
    return self::env('JWT_SECRET', '12345');
  }
}

class Database {
  private static $connection = null;

  public static function connect() {
    if (self::$connection === null) {
      try {
        $dsn = "mysql:host=" . Config::DB_HOST() .
               ";port=" . Config::DB_PORT() .
               ";dbname=" . Config::DB_NAME() .
               ";charset=utf8mb4";

        self::$connection = new PDO(
          $dsn,
          Config::DB_USER(),
          Config::DB_PASSWORD(),
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
          ]
        );
      } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
      }
    }
    return self::$connection;
  }
}





