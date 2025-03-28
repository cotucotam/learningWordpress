<?php
get_header();
pageBanner(array(
    'title' => 'Our Campuses',
    'subtitle' =>'We have serveral conveniently located campuses'
))
?>


<div class="container container--narrow page-section">
    <ul class="link-list min-list">

        <?php 
        while(have_posts()){

            the_post(); ?>
            <li><a href="<?php the_permalink();?>"><?php the_title(); ?></a></li>
            
  
        <?php 
            get_template_part('tempalte_parts\map');
        }
        echo paginate_links();
        ?>
    </ul>
</div>

<?php
get_footer();
?>