<?php
include dirname(__FILE__).'/_wrapper-open.tpl.php';
include dirname(__FILE__).'/label.tpl.php';
?>

<?php
if ($this->required) $this->attributes['required'] = 'required';
?>

<div class="form-input">

<textarea name="<?php echo $this->name; ?>" id="<?php echo $this->key; ?>" class="form-textarea <?php echo implode(' ', $this->f_classes); ?>" <?php $this->render_attributes(); ?>><?php echo htmlspecialchars($this->default_value); ?></textarea>

</div><!-- // form input -->

<?php
include dirname(__FILE__).'/_wrapper-close.tpl.php';
?>
