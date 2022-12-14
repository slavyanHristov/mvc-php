<?php

namespace app\core;

abstract class Model
{
    protected const RULE_REQUIRED = 'required';
    protected const RULE_EMAIL = 'email';
    protected const RULE_MIN = 'min';
    protected const RULE_MAX = 'max';
    protected const RULE_MATCH = 'match';
    protected const RULE_UNIQUE = 'unique';

    // holds associative array for error messages if validation fails
    private array $errors = [];

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
    // all classes who extend Model must implement this method
    abstract protected function rules(): array;

    public function labels(): array
    {
        return [];
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};   // value of class member attribute

            // loop through the rules of an attribute
            foreach ($rules as $rule) {
                $ruleName = $rule;

                // if the rule is of type array
                if (is_array($ruleName)) {
                    //extract the rule name constant
                    $ruleName = $rule[0];
                }
                /**
                 * if the given rule is 'REQUIRED' and value is not set
                 *  call addErrorForRule()
                 */
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }

                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }

                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }

                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }

                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    // className hold the class name string
                    $className = $rule['class'];
                    // gets the attribute which the unique rule to be applied
                    $uniqueAttr = $rule['attribute'] ?? $attribute;

                    // Gets the table name of the model
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
            }
        }

        // returns true if there are no errors
        return empty($this->errors);
    }

    private function addErrorForRule(string $attribute, string $rule, array $params = [])
    {
        // assigns the error message for the specific rule
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            // replaces where the placeholder matches the {$key} with the value in message
            $message = str_replace("{{$key}}", $value, $message);
        }
        // pushes the message to the assoc array to the specific attribute(key value of the array)
        $this->errors[$attribute][] = $message;
    }

    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    private function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must have a valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists',
        ];
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
}
