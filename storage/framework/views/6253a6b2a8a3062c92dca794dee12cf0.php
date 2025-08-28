<?php $__env->startSection('body'); ?>

    <body class="login">
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo $__env->make('../layout/components/dark-mode-switcher', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('../layout/components/main-color-switcher', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- BEGIN: JS Assets-->
        <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
        <!-- END: JS Assets-->

        <?php echo $__env->yieldContent('script'); ?>
    </body>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('../layout/base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/arch/Downloads/astroway-astrology-consultation-app-with-php-backend-audiovideo-calls-chat-with-live-streaming (2)/Backend-PHP/Astroway-Backend-PHP-master/resources/views////layout/login.blade.php ENDPATH**/ ?>