<?php
class Validator {
    public static function validate($data, $rules) {
        foreach ($rules as $field => $fieldRules) {
            $ruleArray = explode("|", $fieldRules);

            foreach ($ruleArray as $rule) {
                if ($rule === 'required' && !isset($data[$field])) {
                    echo $data[$field];
                    return false;
                }

                if ($rule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    echo $data[$field];
                    return false;
                }

                if ($rule === 'min' && strlen($data[$field]) < 6) {
                    echo $data[$field];
                    return false;
                }

                if ($rule === 'max' && strlen($data[$field]) > 255) {
                    echo $data[$field];
                    return false;
                }

                if ($rule === 'numeric' && !is_numeric($data[$field])) {
                    echo $data[$field];
                    return false;
                }
                if ($rule === 'positive' && (!is_numeric($data[$field]) ||intval($data['field'])>0)) {
                    echo $data[$field];
                    return false;
                }

                if ($rule === 'gender' && !($data[$field] =="F" || $data[$field] =="M" || $data[$field] =="N" ))  {
                    echo $data[$field];
                    return false;
                }
            }
        }
        return true;
    }
}