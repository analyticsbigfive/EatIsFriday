<?php
/**
 * Archive Activities Template
 * 
 * @package EatIsFamily
 */

get_header();
?>

<div class="archive-header">
    <h1>All Activities</h1>
    <p>Discover our culinary experiences and workshops</p>
</div>

<div class="activities-grid">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
    ?>
        <article id="activity-<?php the_ID(); ?>" <?php post_class('activity-card'); ?>>
            <?php if (has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium'); ?>
                </a>
            <?php endif; ?>
            
            <div class="activity-content">
                <h2 class="activity-title">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>
                
                <div class="activity-excerpt">
                    <?php echo esc_html(get_post_meta(get_the_ID(), 'description', true)); ?>
                </div>
                
                <div class="activity-details">
                    <?php if ($price = get_post_meta(get_the_ID(), 'price', true)) : ?>
                        <span class="price"><?php echo esc_html($price); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($date = get_post_meta(get_the_ID(), 'activity_date', true)) : ?>
                        <span class="date"><?php echo esc_html(date('M j, Y', strtotime($date))); ?></span>
                    <?php endif; ?>
                </div>
                
                <a href="<?php the_permalink(); ?>" class="read-more">Learn More →</a>
            </div>
        </article>
    <?php
        endwhile;
        
        the_posts_pagination();
    else :
        echo '<p>No activities found.</p>';
    endif;
    ?>
</div>

<?php
get_footer();
?>
