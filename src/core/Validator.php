<?php
class Validator {
    public static function validate($data, $rules) {
        foreach ($rules as $field => $fieldRules) {
            $ruleArray = explode("|", $fieldRules);

            foreach ($ruleArray as $rule) {
                if ($rule === 'required' && !isset($data[$field])) {
                    echo ''. $field .''. $rule .'';
                    return false;
                }

                if ($rule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    echo ''. $field .''. $rule .'';
                    echo $data[$field];
                    return false;
                }

                if ($rule === 'min' && strlen($data[$field]) < 6) {
                    echo ''. $field .''. $rule .'';
                    return false;
                }

                if ($rule === 'max' && strlen($data[$field]) > 255) {
                    echo ''. $field .''. $rule .'';
                    return false;
                }

                if ($rule === 'numeric' && !is_numeric($data[$field])) {
                    echo ''. $field .''. $rule .'';
                    return false;
                }
                if ($rule === 'positive' && (!is_numeric($data[$field]) ||intval($data['field'])>0)) {
                    echo ''. $field .''. $rule .'';
                    return false;
                }

                if ($rule === 'gender' && !($data[$field] =="F" || $data[$field] =="M" || $data[$field] =="N" ))  {
                    echo ''. $field .''. $rule .'';
                    return false;
                }
                
                if ($rule === 'int' && ! is_int($data[$field]))  {
                    echo ''. $field .''. $rule .'';
                    return false;
                }
                
                if ($rule === 'datetime') {
                    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $data[$field]);
                    if (!$dt || $dt->format('Y-m-d H:i:s') !== $data[$field]) {
                        echo ''. $field .''. $rule .'';
                        return false;
                    }
                }
                if ($rule === 'date') {
                    $dt = DateTime::createFromFormat('Y-m-d', $data[$field]);
                    if (!$dt || $dt->format('Y-m-d') !== $data[$field]) {
                        echo ''. $field .''. $rule .''; 
                        return false;
                    }
                }
            }
        }
        return true;
    }
}