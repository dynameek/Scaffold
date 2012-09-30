<?php defined('SCAFFOLD') or die();

/**
 * Validate data
 *
 * @author Nathaniel Higgins
 */
class Validate {

    /**
     * Holds the rules that we are to validate against.
     */
    public $_rules;

    /**
     * Our default checks
     */
    private $checks = ['empty', 'email', 'alphanumeric', 'regex', 'is_regex', 'equal'];

    /**
     * Checks can be prepended with some of these modifiers
     */
    private $modifiers = ['not'];

    /**
     * Test statuses
     */
    const TEST_FAILED = 1;
    const INVALID_DATA = 2;

    /**
     * Global rule
     */
    const GLOBAL_RULE = null;

    /**
     * Set rules from instantiation
     */
    public function __construct($name = null, $value = null) {
        if ($name) $this->set($name, $value);
    }

    /**
     * Set rules
     */
    public function set($name, $value = null) {
        $rules = $this->args($name, $value);

        foreach ($rules as $k => $v) {
            if (!isset($this->_rules[$k])) $this->_rules[$k] = [];
            $this->_rules[$k] = array_merge($this->_rules[$k], $v);
        }

        return $this;
    }

    /**
     * Set global rules
     */
    public function set_global($rules) {
        return $this->set(Validate::GLOBAL_RULE, $rules);
    }

    /**
     * Argument shuffling
     */
    public function args($name, $value = null) {
        $rules = [];
        if (is_null($value) && (is_string($name) || !is_hash($name))) {
            $value = is_string($name) ? [$name] : $name;
            $name = false;
            $rules[$name] = $value;
        }

        if ((is_null($name) || is_string($name)) && (is_string($value) || !is_hash($value))) {
            $value = is_string($value) ? [$value] : $value;
            if (is_null($name)) $name = false;
            $rules[$name] = $value;
        } else if (is_array($name) && is_null($value)) {
            foreach ($name as $k => $v) {
                if ($k === '') $k = null;
                $rules = array_merge($rules, $this->args($k, $v));
            }
        }

        return $rules;
    }


    /**
     * Test data against our rules
     *
     * Will only test on a hash.
     */
    public function test($data) {
        $errors = [];

        if (!is_hash($data)) {
            $errors[] = ['errors' => [
                'type' => VALIDATE::INVALID_DATA
            ]];
        } else {

            $globals = isset($this->_rules[false]) ? $this->_rules[false] : false;

            foreach ($data as $key => $value) {

                if (isset($this->_rules[$key]) || $globals) {
                    $rules = isset($this->_rules[$key]) ? $this->_rules[$key] : [];

                    if ($globals) {
                        $rules = array_merge($this->_rules[false], $rules);
                    }

                    $info = [
                        'name' => $key,
                        'tests' => $rules,
                        'value' => $value,
                        'errors' => []
                    ];
                    $results = [];

                    foreach ($rules as $original_rule) {
                        $rule = $original_rule;
                        $mods = [];

                        if (is_callable($rule)) {
                            $result = $rule($value);
                            $rule = 'custom';
                        } else if (is_string($rule)) {
                            if ($this->check_is_regex($rule)) {
                                $rule = 'regex';
                            } else if (strpos($rule, '_')) {
                                $parts = explode('_', $rule);
                                $last = end($parts);
                                reset($parts);

                                foreach ($parts as $part) {
                                    if ($last === $part || !in_array($part, $this->modifiers)) break;
                                    $mods[] = $part;
                                }

                                if (count($mods) > 0) {
                                    $rule = $last;
                                }
                            }
                        }

                        if (in_array($rule, $this->checks)) {
                            $funcname = 'check_' . $rule;
                            $result = $this->$funcname($value, $original_rule);
                        }

                        if (!isset($result)) {
                            $result = $this->check_equal($value, $rule);
                            $rule = 'equal';
                        }

                        foreach ($mods as $mod) {
                            $funcname = 'modifier_' . $mod;
                            $result = $this->$funcname($result, $original_rule);
                        }

                        $rule_pref = implode('_', $mods);
                        if ($rule_pref != '') $rule_pref .= '_';
                        $rule = $rule_pref . $rule;

                        $results[] = [
                            'result' => $result,
                            'rule' => $rule,
                            'value' => $value
                        ];
                    }

                    foreach ($results as $result) {
                        if (!$result['result']) {
                            $result['type'] = Validate::TEST_FAILED;
                            $info['errors'][] = $result;
                        }
                    }

                    if (count($info['errors']) > 0) {
                        $errors[] = $info;
                    }
                }
            }
        }

        if (count($errors) > 0) {
            throw new ExceptionValidate($errors);
        }

        return true;
    }

    /**
     * Empty Test
     */
    public function check_empty($value) {
        return !$value || $value == '';
    }

    /**
     * Alphanumeric test
     */
    public function check_alphanumeric($value) {
        return ctype_alnum($value);
    }

    /**
     * Email test
     */
    public function check_email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    /**
     * Is Regex test
     */
    public function check_is_regex($value) {
        return @preg_match($value, '') !== false;
    }

    /**
     * Regex match test
     */
    public function check_regex($value, $rule) {
        return preg_match_all($rule, $value, $matches) === strlen($value);
    }

    /**
     * Equal test
     */
    public function check_equal($value, $rule) {
        return $value == $rule ? true : false;
    }

    /**
     * Not modifier
     */
    public function modifier_not($value) {
        return !$value;
    }
}