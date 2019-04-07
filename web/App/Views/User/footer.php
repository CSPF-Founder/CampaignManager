<?php
use App\Auth;
use Core\View;
?>
    <!-- footer content -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b><?php View::securePrint(\App\Config::APP_TITLE); ?></b> 1.0
        </div>
        <strong>Copyright &copy; <?php echo date("Y"); ?></strong>
    </footer>
    <!-- /footer content -->
    </div>
</div>

<!-- Bootstrap -->
<script src="/resources/vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- data tables -->
<script src="/resources/vendor/datatables/js/jquery.dataTables.min.js?v=1.10.18"></script>
<script src="/resources/vendor/datatables/js/dataTables.bootstrap.min.js?v=1.10.18"></script>

<!-- Custom Theme Scripts -->
<script src="/resources/js/custom.js"></script>
<script src="/resources/vendor/iCheck/icheck.min.js"></script>
<!-- FastClick -->
<script src="/resources/vendor/fastclick/lib/fastclick.js"></script>

<!-- custom stylesheet -->
<link href="/resources/css/style.css?v=1.0.0" rel="stylesheet">

<script>
    function findBootstrapEnvironment() {
        var envs = ['xs', 'sm', 'md', 'lg'];

        var $el = $('<div>');
        $el.appendTo($('body'));

        for (var i = envs.length - 1; i >= 0; i--) {
            var env = envs[i];

            $el.addClass('hidden-'+env);
            if ($el.is(':hidden')) {
                $el.remove();
                return env;
            }
        }
    }
    <?php if(Auth::user()->hasRole('admin')): ?>
    $(document).ready(function(){
        if(findBootstrapEnvironment() !== 'xs'){
            $("#menu_toggle").click();
        }
    });
    <?php endif; ?>
</script>
</body>
</html>