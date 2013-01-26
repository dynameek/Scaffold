<?php defined('SCAFFOLD') or die();

/**
 * Database drivers should extend this abstract class.
 *
 * This is to allow us to share some common functionality.
 */
abstract class DatabaseDriver implements DatabaseDriverInterface {

	const SELECT = 1;
	const INSERT = 2;
	const UPDATE = 3;

    public function __construct(DatabaseQueryBuilder $builder, $config) {
        $this->builder = $builder;
        $this->config = $config;
        $this->connect();
    }
}