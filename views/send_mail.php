<?php
$this->setTitle('Send Mail');
?>

<h1>Send Mail</h1>

<?php $form = \app\core\form\Form::begin("", "post"); ?>
<?php echo $form->field($model, 'subject'); ?>
<?php echo $form->textArea($model, 'body'); ?>
<?php echo $form->field($model, 'recipient'); ?>
<button type="submit" class="btn btn-primary">Submit</button>
<?php \app\core\form\Form::end(); ?>

<br>