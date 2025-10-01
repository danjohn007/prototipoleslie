<?php
/**
 * Clase Database
 * Gestiona la conexión a la base de datos usando PDO
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtiene la instancia única de Database (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtiene la conexión PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Ejecuta una consulta SELECT
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error en query: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecuta una consulta SELECT y devuelve un solo registro
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error en queryOne: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecuta una consulta INSERT, UPDATE o DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Error en execute: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el ID del último registro insertado
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Inicia una transacción
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Confirma una transacción
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Revierte una transacción
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
    
    /**
     * Previene la clonación del objeto
     */
    private function __clone() {}
    
    /**
     * Previene la deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
