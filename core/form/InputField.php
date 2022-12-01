<?php

namespace app\core\form;

use app\core\Model;

class InputField extends Field
{
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';

    public string $type;

    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function renderField(): string
    {
        return sprintf(
            '<input type="%s" name="%s" value="%s" class="form-control%s">',
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
        );
    }

    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;

        return $this;
    }
    public function numberField()
    {
        $this->type = self::TYPE_NUMBER;

        return $this;
    }
}
