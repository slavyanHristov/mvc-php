<?php
$this->setTitle('Create ToDo');
?>

<h1>Create a ToDo</h1>

<?php $form = \app\core\form\Form::begin("", "post"); ?>
<?php echo $form->field($model, 'title'); ?>
<button type="submit" class="btn btn-primary">Submit</button>
<?php \app\core\form\Form::end(); ?>

<br>