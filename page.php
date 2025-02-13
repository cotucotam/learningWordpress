<?php
get_header();
// Tạo WP_Query để lấy bài viết theo ngày giảm dần
// Lấy đường dẫn hiện tại (path) từ URL
$current_url = $_SERVER['REQUEST_URI']; // Trả về chuỗi URL sau tên miền
$path = trim($current_url, '/');        // Loại bỏ dấu '/' ở đầu và cuối
$path_parts = explode('/', $path);      // Tách path thành các phần
$slug = end($path_parts);               // Lấy slug cuối cùng

// Truy vấn page dựa trên slug
$args = array(
    'post_type' => 'page',
    'name'      => $slug // Sử dụng slug từ URL hiện tại
);

$query = new WP_Query($args);

if ($query->have_posts()){
    while ($query->have_posts()) {
     $query->the_post(); ?>
        <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg')?>)"></div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php the_title();?></h1>
                <div class="page-banner__intro">
                <p>REPLACE ME LATER</p>
                </div>
            </div>
            </div>

            <div class="container container--narrow page-section">
                <?php 
                    $theParent = wp_get_post_parent_id(get_the_ID());
                    if($theParent){
                        ?>
                        <div class="metabox metabox--position-up metabox--with-home-link">
                        <p>
                        <a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParent) ?></a> <span class="metabox__main"><?php echo the_title();?></span>
                        </p>
                        <?php
                    }
                ?>
            </div>
            
            <?php 
            $testArray = get_pages(array(
                'child_of' => get_the_ID()
            ));

            if ($theParent or $testArray){
                ?>
                <div class="page-links">
                    <h2 class="page-links__title"><a href="<?php echo get_permalink($theParent)?>"><?php echo get_the_title(($theParent)) ?></a></h2>
                    <ul class="min-list">
                        <?php 
                            if($theParent){
                                $findChildrenOf = $theParent;
                            } else{
                                $findChildrenOf = get_the_ID();
                            }

                            wp_list_pages(array(
                                'title_li' => NULL,
                                'child_of' => $findChildrenOf,
                                'sort_column' => 'menu_order'
                            ));

                        ?>
                    </ul>
                </div>
            <?php } ?>
            <div class="generic-content">
               <?php the_content(); ?>
            </div>
        </div>
    <?php }

get_footer();
} ?>