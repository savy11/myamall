</div>
<script type="text/javascript" src="<?php echo $fn->permalink('assets/js/vendor/jquery.js'); ?>" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php echo $fn->permalink('resources/vendor/bootstrap/bootstrap.min.js'); ?>" crossorigin="anonymous" defer></script>
<script type="text/javascript" src="<?php echo $fn->permalink("resources/vendor/moment/moment.js"); ?>" crossorigin="anonymous" defer></script>
<script type="text/javascript" src="<?php echo $fn->permalink("resources/vendor/common.js"); ?>" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php echo $fn->permalink('assets/js/main.js'); ?>" crossorigin="anonymous" defer></script>
<script type="text/javascript" src="<?php echo $fn->permalink("assets/js/custom.js"); ?>" crossorigin="anonymous" defer></script>
<?php
 echo $fn->script;
 if ($fn->session('er')) {
  ?>
     <script type="text/javascript">
         $(function () {
             app.show_msg('<?php echo $fn->session('er', 'title'); ?>', '<?php echo $fn->session('er', 'message'); ?>', '<?php echo $fn->session('er', 'type'); ?>');
         });
     </script>
  <?php
  unset($_SESSION['er']);
 }
?>

</body>
</html>