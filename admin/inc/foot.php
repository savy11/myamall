
<script type="text/javascript" src="<?php echo $fn->permalink('assets/js/script.js'); ?>"></script>
<?php
echo $fn->script;
if ($fn->session('er')) {
 ?>
 <script type="text/javascript">
  $(function () {
   app.show_msg('<?php echo $fn->session('er', 'title'); ?>', '<?php echo $fn->replace_sql($fn->session('er', 'message')); ?>', '<?php echo $fn->session('er', 'type'); ?>');
  });
 </script>
 <?php
 unset($_SESSION['er']);
}
?> 
</body>
</html>