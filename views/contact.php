<?php $this->setTitle('Contact'); ?>
<h1>Contact us</h1>

<?php $form = \app\core\form\Form::begin('', 'post'); ?>
<?php echo $form->field($model, 'subject'); ?>
<?php echo $form->field($model, 'email'); ?>
<?php echo $form->textArea($model, 'body'); ?>
<button type="submit" class="btn btn-primary">Submit</button>
<?php $form = \app\core\form\Form::end() ?>