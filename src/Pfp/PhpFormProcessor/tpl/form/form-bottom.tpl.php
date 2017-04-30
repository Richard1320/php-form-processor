<?php
if (isset($action_html['prefix'])) echo $action_html['prefix'];
?>
<div class="form-actions">
  <?php if (isset($action_html['buttons'])) echo $action_html['buttons']; ?>
  <input name="<?php echo $submit_name; ?>" type="submit" value="<?php echo $submit_value; ?>" class="form-submit" />
</div><!-- // form actions -->
<?php
if (isset($action_html['suffix'])) echo $action_html['suffix'];
?>
