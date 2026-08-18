<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link href="<?php echo site_url(); ?>assets/img/logo/voltix_logo.png" rel="shortcut icon" type="image/png">
    <meta name="author" content="Voltix Tech">
    <meta name="description" content="NextGen Electrical, Smart IoT, Switchgear & Solar Lighting Supplies">
    <title><?php echo htmlspecialchars($siteName ?? $SiteName ?? 'Voltix Tech - Electrical Supplies'); ?></title>
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo site_url(); ?>fonts/Linearicons/Font/demo-files/demo.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/bootstrap4/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/owl-carousel/assets/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/slick/slick/slick.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/lightGallery/dist/css/lightgallery.min.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/jquery-bar-rating/dist/themes/fontawesome-stars.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>plugins/noUiSlider/nouislider.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>css/style.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>css/home-1.css">
    <link rel="stylesheet" href="<?php echo site_url(); ?>css/tech-theme.css">
<?php if (!empty($system_contactUs)) { ?>
<a href="https://api.whatsapp.com/send?phone=<?php echo preg_replace('/[^0-9]/', '', $system_contactUs); ?>&text=Hello%20Voltix%20Tech" class="float" target="_blank" aria-label="Chat on WhatsApp">
<i class="fa fa-whatsapp my-float"></i>
</a>
<?php } ?>