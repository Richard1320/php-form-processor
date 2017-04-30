<?php
if (!empty($this->errors)) {
  ?>
  <div class="error-wrapper">
  <?php
  foreach($this->errors as $value) {
    ?>
    <p class="error">Error: <?php echo $value; ?><p>
    <?php
  } // end error loop
  ?>
  </div><!-- // error wrapper -->
  <?php
} // end error check
?>
