<?php
if (!empty($this->errors)) {
  ?>
  <div class="error-wrapper">
  <?php
  foreach($this->errors as $error) {
    ?>
    <p class="error">Error: <?php echo $error['message']; ?><p>
    <?php
  } // end error loop
  ?>
  </div><!-- // error wrapper -->
  <?php
} // end error check
?>
