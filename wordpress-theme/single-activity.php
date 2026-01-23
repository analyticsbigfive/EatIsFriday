<?php
/**
 * Single Activity Template
 * 
 * @package EatIsFamily
 */

get_header();

while (have_posts()) : the_post();
?>

<article id="activity-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        
        <?php if (has_post_thumbnail()) : ?>
            <div class="featured-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
    </header>
    
    <div class="activity-meta">
        <?php if ($date = get_post_meta(get_the_ID(), 'activity_date', true)) : ?>
            <div class="activity-date">
                <strong>Date:</strong> <?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($date))); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($location = get_post_meta(get_the_ID(), 'location', true)) : ?>
            <div class="activity-location">
                <strong>Location:</strong> <?php echo esc_html($location); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($price = get_post_meta(get_the_ID(), 'price', true)) : ?>
            <div class="activity-price">
                <strong>Price:</strong> <?php echo esc_html($price); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($duration = get_post_meta(get_the_ID(), 'duration', true)) : ?>
            <div class="activity-duration">
                <strong>Duration:</strong> <?php echo esc_html($duration); ?>
            </div>
        <?php endif; ?>
        
        <?php 
        $capacity = get_post_meta(get_the_ID(), 'capacity', true);
        $available = get_post_meta(get_the_ID(), 'available_spots', true);
        if ($capacity && $available) : 
        ?>
            <div class="activity-spots">
                <strong>Available Spots:</strong> <?php echo esc_html($available); ?> / <?php echo esc_html($capacity); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
</article>

<?php
endwhile;

get_footer();
?>
