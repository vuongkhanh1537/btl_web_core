<?php
class Validator {
    public static function validate($data, $rules) {
        foreach ($rules as $field => $fieldRules) {
            $ruleArray = explode("|", $fieldRules);

            foreach ($ruleArray as $rule) {
                if ($rule === 'required' && !isset($data[$field])) {
                    return false;
                }

                if ($rule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    return false;
                }

                if ($rule === 'min' && strlen($data[$field]) < 6) {
                    return false;
                }

                if ($rule === 'max' && strlen($data[$field]) > 255) {
                    return false;
                }

                if ($rule === 'numeric' && !is_numeric($data[$field])) {
                    return false;
                }
            }
        }
        return true;
    }
}