<?php
get_header();
?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg')?>)"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title">
            <?php
                if(is_category()){
                    single_cat_title();
                } 
                else if(is_author()){
                    echo 'Post by '; the_author();
                }
                else{
                  post_type_archive_title();
                }
            ?>
        </h1>
        <div class="page-banner__intro">
        <p>
          <?php if (get_option('event_archive_description')) : ?>
            <div class="archive-description">
                <?php echo get_option('event_archive_description'); ?>
            </div>
          <?php endif; ?>
        </p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
<?php 

  while(have_posts()){

    the_post(); 
    $event_date = get_post_meta(get_the_ID(), 'event-date', true);
    $event_type = get_post_meta(get_the_ID(), 'event-type', true);
    ?>
    <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
    <div class="metabox">
      <p>Posted by <?php the_author_posts_link();?> on <?php echo $event_date;  ?> type <?php echo $event_type ?></p>
    </div>
    <div class="generic-content">
      <?php
        the_excerpt();
      ?>
      <p><a href="<?php the_permalink();?>" class="btn btn--blue">Continue reading &raquo;</a></p>
      
    </div>
<?php   
  }
  echo paginate_links();
  ?>

</div>

<?php
get_footer();
?>