<?php
include dirname(__FILE__).'/_wrapper-open.tpl.php';
include dirname(__FILE__).'/label.tpl.php';
?>

<?php
// Name attribute has to be an array for multiple values to work
$name = ($this->type == 'checkbox') ? $this->name .'[]' : $this->name;

// cast default value as an array to loop potentially multiple input elements
$default_value = (is_array($this->default_value)) ? $this->default_value : array($this->default_value);
?>

<div class="form-input">
  <?php
  $i = 0;
  foreach($this->deep as $deep_key => $deep_value) {

    $selected = (in_array($deep_key, $default_value)) ? 'checked' : '';
    ?>
    <div class="form-item-deep form-item-deep-<?php echo $i; ?>">
      <input
        type="<?php echo $this->type; ?>"
        name="<?php echo $this->name; ?>"
        id="<?php echo $this->key; ?>_<?php echo $deep_key; ?>"
        value="<?php echo $deep_key; ?>"
        class="<?php echo implode(' ', $this->f_classes); ?>"
        <?php echo $selected; ?>
        <?php $this->render_attributes(); ?>
      />
      <label for="<?php echo $this->key; ?>_<?php echo $deep_key; ?>"><?php echo $deep_value; ?></label>
    </div><!-- // form item deep -->
    <?php

    $i++;
  }
  ?>
</div><!-- // form input -->

<?php
include dirname(__FILE__).'/_wrapper-close.tpl.php';
?>
