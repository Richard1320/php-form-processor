<?php
include dirname(__FILE__).'/_wrapper-open.tpl.php';
include dirname(__FILE__).'/label.tpl.php';
?>

<?php
if ($this->required) $this->attributes['required'] = 'required';
if ($this->multiple) $this->attributes['multiple'] = 'multiple';
?>

<div class="form-input">

<select name="<?php echo $this->name; ?>" id="<?php echo $this->key; ?>" class="form-select <?php echo implode(' ', $this->f_classes); ?>" <?php $this->render_attributes(); ?>>
<?php
foreach($this->deep as $deep_key => $deep_value) {
  $selected = ($deep_key == $this->default_value) ? 'selected' : '';

  ?>
  <option value="<?php echo $deep_key; ?>" <?php echo $selected; ?>><?php echo $deep_value; ?></option>
  <?php
}
?>
</select>
</div><!-- // form input -->

<?php
include dirname(__FILE__).'/_wrapper-close.tpl.php';
?>
