<?php
/**
 * Single Job Template
 * 
 * @package EatIsFamily
 */

get_header();

while (have_posts()) : the_post();
?>

<article id="job-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        
        <?php if (has_post_thumbnail()) : ?>
            <div class="featured-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
    </header>
    
    <div class="job-meta">
        <?php if ($department = get_post_meta(get_the_ID(), 'department', true)) : ?>
            <div class="job-department">
                <strong>Department:</strong> <?php echo esc_html($department); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($job_type = get_post_meta(get_the_ID(), 'job_type', true)) : ?>
            <div class="job-type">
                <strong>Type:</strong> <?php echo esc_html($job_type); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($salary = get_post_meta(get_the_ID(), 'salary', true)) : ?>
            <div class="job-salary">
                <strong>Salary:</strong> <?php echo esc_html($salary); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($venue_id = get_post_meta(get_the_ID(), 'venue_id', true)) : ?>
            <div class="job-venue">
                <strong>Venue:</strong> <?php echo esc_html($venue_id); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
    
    <?php 
    $requirements = json_decode(get_post_meta(get_the_ID(), 'requirements', true), true);
    if ($requirements && is_array($requirements)) : 
    ?>
        <div class="job-requirements">
            <h3>Requirements</h3>
            <ul>
                <?php foreach ($requirements as $requirement) : ?>
                    <li><?php echo esc_html($requirement); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php 
    $benefits = json_decode(get_post_meta(get_the_ID(), 'benefits', true), true);
    if ($benefits && is_array($benefits)) : 
    ?>
        <div class="job-benefits">
            <h3>Benefits</h3>
            <ul>
                <?php foreach ($benefits as $benefit) : ?>
                    <li><?php echo esc_html($benefit); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="job-apply">
        <a href="<?php echo esc_url(home_url('/apply-jobs')); ?>" class="button">Apply Now</a>
    </div>
</article>

<?php
endwhile;

get_footer();
?>
