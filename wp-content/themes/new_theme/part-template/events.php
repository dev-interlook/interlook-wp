<?php



/**



 Template Name: events



 */



?>
<?php get_header();?>
<!-- Content -->
<div class="content-wrapper">
    <!-- Lines -->
    <section class="content-lines-wrapper">
        <div class="content-lines-inner">
            <div class="content-lines"></div>
        </div>
    </section>
    <!-- Events -->
    <section>
        <div class="container mt-60">
            <div class="events-header">
                <h1>Events</h1>
                <p class="events-subtitle">Don't miss out! Find all the latest information on our events and exhibitions – from innovative showcases to engaging workshops.</p>
            </div>
            <div class="events-grid">
            <?php if ( have_rows( 'event_list' ) ) : ?>
                <?php while ( have_rows( 'event_list' ) ) : the_row(); ?>
                    <?php
                    $event_name = get_sub_field( 'event_name' );
                    $start_date = get_sub_field( 'start_date' );
                    $end_date = get_sub_field( 'end_date' );
                    $event_link_url = get_sub_field( 'event_link' );
                    $event_image = get_sub_field( 'event_image' ); // Add this field in WordPress admin

                    // Display event name, start date, and end date
                    echo '<a href="' . esc_url($event_link_url) . '" class="event-card" target="_blank">';
                    if ($event_image) {
                        echo '<div class="event-image"><img src="' . esc_url($event_image['url']) . '" alt="' . esc_attr($event_name) . '"></div>';
                    }
                    echo '<div class="event-content">';
                    if ($event_name) {
                        echo '<h3 class="event-title">' . esc_html($event_name) . '</h3>';
                    }
                    if ($start_date) {
                        echo '<div class="event-date"><span class="date-label">Start:</span> ' . esc_html($start_date) . '</div>';
                    }
                    if ($end_date) {
                        echo '<div class="event-date"><span class="date-label">End:</span> ' . esc_html($end_date) . '</div>';
                    }
                    echo '</div>'; // Close event-content
                    echo '</a>'; // Close event-card
                    ?>
                <?php endwhile; ?>
            <?php else : ?>
                <?php // no rows found ?>
            <?php endif; ?>
            </div> <!-- Close events-grid -->
        </div>
    </section>
    <style>
        @media (min-width: 1440px) {
            .container {
                max-width: 1400px;
            }
        }
        .events-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            background: #f4f4f4;
            border-radius: 8px;
            padding: 30px;
        }
        .events-header h1 {
            font-size: 2.5em;
            margin: 0;
            color: #333;
        }
        .events-subtitle {
            font-size: 1.1em;
            color: #666;
            max-width: 600px;
            margin: 0;
            text-align: right;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, calc((100% - 90px) / 4)), 1fr));
            gap: 30px;
            padding: 20px 0;
        }
        .event-card {
            display: block;
            text-decoration: none;
            color: inherit;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .event-image {
            width: 100%;
            padding-top: 100%;
            position: relative;
            overflow: hidden;
        }
        .event-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .event-content {
            padding: 20px;
        }
        .event-title {
            font-size: 1.4em;
            margin-bottom: 15px;
            color: #333;
        }
        .event-date {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 8px;
        }
        .date-label {
            font-weight: bold;
            color: #333;
        }
        .event-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
        }
        .event-link {
            display: inline-block;
            padding: 8px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .event-link:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            .events-header h1 {
                font-size: 2em;
            }
        }
    </style>
    <?php get_footer();?>