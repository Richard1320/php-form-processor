<?php
if (!empty($this->label)) {
  ?>
  <div class="form-label">
  <?php
  // Don't wrap with label tag for items with multiple keys
  if ($this->type == 'radio' || $this->type == 'checkbox' || $this->multiple) {
    echo $this->label;
  } else {
    ?>
    <label for="<?php echo $this->key; ?>"><?php echo $this->label; ?></label>
    <?php
  }
  ?>
  </div><!-- // form label -->
  <?php
}
?>
