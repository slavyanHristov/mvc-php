<?php

namespace app\core\form;

use app\core\Model;

abstract class Field
{
    public Model $model;
    public string $attribute;

    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    abstract public function renderField(): string;

    public function __toString()
    {
        return sprintf(
            '
        <div class="mb-3">
            <label class="form-label">%s</label>
            %s
            <div class="invalid-feedback">
                %s
            </div>
        </div>
        ',
            $this->model->labels()[$this->attribute] ?? $this->attribute,
            $this->renderField(),
            $this->model->getFirstError($this->attribute)
        );
    }
}
