<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <title><?php bloginfo( 'name' ); ?></title>

    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.png" />

    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/plugins.css"  />

    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style.css" />

    <script type="text/javascript" async="" src="https://www.google-analytics.com/analytics.js"></script>

    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-144098545-1"></script>

    <?php wp_head();?>

</head>



<body>

    <!-- Preloader -->

    <div id="preloader"></div> 

    <!-- Progress scroll totop -->

    <div class="progress-wrap cursor-pointer">

        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">

            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />

        </svg>

    </div>

    <!-- Navbar -->

    <nav class="navbar navbar-expand-lg">

        <div class="container">

        <!-- Logo -->

        <?php if ( get_field( 'logo', 'option' ) ) { ?>
        <a class="logo" href="<?php bloginfo( 'url' ); ?>"> <img src="<?php the_field( 'logo', 'option' ); ?>" alt=""> </a>
        <?php } ?>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span class="icon-bar"><i class="ti-line-double"></i></span> </button>

        <!-- Navbar links -->

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav ml-auto">

            <?php if ( have_rows( 'menu', 'option' ) ) : ?>
	<?php while ( have_rows( 'menu', 'option' ) ) : the_row(); ?>

                <li class="nav-item"><a class="nav-link" href="<?php the_sub_field( 'link' ); ?>" ><?php the_sub_field( 'title' ); ?></a></li>

            <?php endwhile; ?>
<?php else : ?>
	<?php // no rows found ?>
<?php endif; ?>    

            </ul>

        </div>

        </div>

    </nav>