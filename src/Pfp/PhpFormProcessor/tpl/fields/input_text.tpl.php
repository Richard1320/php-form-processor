<?php
include dirname(__FILE__).'/_wrapper-open.tpl.php';
include dirname(__FILE__).'/label.tpl.php';
?>

<?php
if ($this->maxlength) $this->attributes['maxlength'] = (int)$this->maxlength;
if ($this->required) $this->attributes['required'] = 'required';

// Name attribute has to be an array for multiple values to work
$name = ($this->multiple) ? $this->name .'[]' : $this->name;

// cast default value as an array to loop potentially multiple input elements
$default_value = (array)$this->default_value;
?>

<div class="form-input">
  <?php
  $i = 0;
  foreach($default_value as $key => $value) {
    ?>
    <input
    type="<?php echo $this->type; ?>"
    name="<?php echo $name; ?>"
    id="<?php echo $this->key; if ($i != 0) echo '_'. $i; ?>"
    class="<?php echo implode(' ', $this->f_classes); ?>"
    value="<?php echo htmlspecialchars($value); ?>"
    <?php $this->render_attributes(); ?>
    />
    <?php
    $i++;
  } // end multiple loop
  ?>
</div><!-- // form input -->

<?php
include dirname(__FILE__).'/_wrapper-close.tpl.php';
?>
