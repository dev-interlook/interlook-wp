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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=DM Sans">

    <script type="text/javascript" async="" src="https://www.google-analytics.com/analytics.js"></script>

    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-144098545-1"></script>
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>

    <?php wp_head();?>

    <style>
        .logo-nav {
            position: absolute;
        }
        @media (max-width: 991px) {
            .logo-nav {
                position: relative;
            }
        }

        .table-borderless {
            border: none;
            border-collapse: unset;
        }
        
        .table-borderless > tbody > tr > td,
        .table-borderless > tbody > tr > th,
        .table-borderless > tfoot > tr > td,
        .table-borderless > tfoot > tr > th,
        .table-borderless > thead > tr > td,
        .table-borderless > thead > tr > th {
            border: none;
        }

        .monogram-logo-width {
            width: 50%;
        }
        .display-only-desktop {
            display: block;
        }
        .display-only-phone {
            display: none;
        }

        /* Override the css class plugin CF7 for input radio box */
        .wpcf7-list-item {
            display: block;
            margin: 0 0 0 1em;
        }
        span.wpcf7-list-item-label {
            display: inline;
            text-decoration: none;
            color: inherit;
        }
        
        @media screen and (max-width: 991px) {
            .monogram-logo-width {
                width: 50%;
            }

            /* Override the css class plugin CF7 for input next button */
            .fieldset-cf7mls .cf7mls_next {
                float: none;
            }
        }
        @media (max-width: 767px) {
            .monogram-logo-width {
                width: 50%;
            }
            .display-only-desktop {
                display: none;
            }
            .display-only-phone {
                display: block;
            }
        }
        @media (max-width: 480px) {
            .sfm-floating-menu.top-right,
            .sfm-floating-menu.bottom-right,
            .sfm-floating-menu.middle-right {
                right: 25px;
            }
        }

        .monogram-logo-banner {
            width: 30% !important;
            margin: auto;
        }
        @media (max-width: 480px) {
            .monogram-logo-banner {
                width: 50% !important;
            }
        }
    </style>
</head>



<body>

    <!-- Progress scroll totop -->

    <!-- <div class="progress-wrap cursor-pointer">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div> -->

    <!-- Navbar -->

    <nav class="navbar navbar-expand-lg">
        <div class="row" style="width: 100%; margin: 0;">
            <!-- Logo -->
            <div class="col-lg-2 col-md-12 logo-nav">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="icon-bar"><i class="ti-line-double"></i></span>
                </button>
                <?php if ( get_field( 'logo', 'option' ) ) { ?>
                <span class="text-center" style="width: 70%;">
                    <a class="logo" href="<?php bloginfo( 'url' ); ?>"> <img src="<?php the_field( 'logo', 'option' ); ?>" alt=""> </a>
                </span>
                <?php } ?>
            </div>

            <!-- Navbar links -->
            <div class="col-lg-12 col-md-12 text-center" style="padding: 0;">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto">
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
        </div>
    </nav>