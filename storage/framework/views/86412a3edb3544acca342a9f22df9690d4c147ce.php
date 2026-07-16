<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo $__env->yieldContent('title', __('front.page_title')); ?></title>
<meta name="description" content="<?php echo $__env->yieldContent('meta_description', __('front.meta_description')); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<link href="<?php echo e(asset('assets_front/css/style.css')); ?>" rel="stylesheet">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="<?php echo e(app()->getLocale() === 'en' ? 'lang-en' : 'lang-ar'); ?>">

<!-- ======= NAV ======= -->
<?php echo $__env->make('front.includes.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- ======= CONTENT ======= -->
<?php echo $__env->yieldContent('content'); ?>

<!-- ======= FOOTER ======= -->
<?php echo $__env->make('front.includes.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script src="<?php echo e(asset('assets_front/js/general.js')); ?>"></script>
<?php echo $__env->yieldPushContent('data'); ?>
<script src="<?php echo e(asset('assets_front/js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\noor_jordan_new_backend\resources\views/layouts/front.blade.php ENDPATH**/ ?>