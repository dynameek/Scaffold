<?php defined('SCAFFOLD') or die();

abstract class DatabaseQueryBuilder implements DatabaseQueryBuilderInterface {

	protected function extract($table = null, $options = []) {

		// Argument shuffling
		if (is_array($table)) {
			$options = $table;

			if (isset($options['table'])) {
				$table = $options['table'];
			} else {
				$table = null;
			}
		}

		if (!is_null($table)) {
			$options['table'] = $table;
		}

		$args = [];
		$required = ['table'];
		$optional = [
		    'vals' => ['*'],
		    'conds' => [],
		    'group' => [],
		    'order' => [],
		    'having' => [],
		    'limit' => []
		];

		$keys = array_keys($options);

		foreach ($required as $req) {
		    if (!in_array($req, $keys)) {
		        throw new InvalidArgumentException('Missing ' . $req);
		    } else {
		    	$args[$req] = $options[$req];
		    }
		}

		foreach ($optional as $name => $value) {
		    if (in_array($name, $keys)) {
		        $value = $options[$name];
		    }

		    $args[$name] = $value;
		}

		return $args;
	}

}
