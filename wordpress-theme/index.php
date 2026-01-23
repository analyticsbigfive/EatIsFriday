<?php
/**
 * The main template file
 * 
 * @package EatIsFamily
 */

get_header();
?>

<main id="main-content" class="site-main">
    <div class="container">
        <h1>Eat Is Family - API Documentation</h1>
        
        <section class="api-docs">
            <h2>Available API Endpoints</h2>
            
            <div class="endpoint">
                <h3>Activities</h3>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/activities')); ?></code>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/activities/{slug}')); ?></code>
            </div>
            
            <div class="endpoint">
                <h3>Blog Posts</h3>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/blog-posts')); ?></code>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/blog-posts/{slug}')); ?></code>
            </div>
            
            <div class="endpoint">
                <h3>Events</h3>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/events')); ?></code>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/events/{id}')); ?></code>
            </div>
            
            <div class="endpoint">
                <h3>Jobs</h3>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/jobs')); ?></code>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/jobs/{slug}')); ?></code>
            </div>
            
            <div class="endpoint">
                <h3>Venues</h3>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/venues')); ?></code>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/venues/{id}')); ?></code>
            </div>
            
            <div class="endpoint">
                <h3>Site Content</h3>
                <code>GET <?php echo esc_url(rest_url('eatisfamily/v1/site-content')); ?></code>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();
?>
