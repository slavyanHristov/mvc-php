<?php

namespace app\core\form;

class TextAreaField extends Field
{
    public function renderField(): string
    {
        return sprintf(
            '<textarea style="white-space: pre-wrap;" name="%s" class="form-control%s">%s</textarea>',
            $this->attribute,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->model->{$this->attribute},
        );
    }
}
