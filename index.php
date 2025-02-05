
<?php
// Tạo WP_Query để lấy bài viết theo ngày giảm dần
$args = array(
    'post_type'      => 'post',      // Lấy bài viết (post type là 'post')
    'posts_per_page' => -1,          // Hiển thị tất cả bài viết
    'orderby'        => 'date',      // Sắp xếp theo ngày
    'order'          => 'DESC',      // Theo thứ tự giảm dần
);
$query = new WP_Query($args);

if ($query->have_posts()){
    while ($query->have_posts()) {
     $query->the_post(); ?>
        <h2>
            <?php the_title(); ?>
        </h2>
        <div>
            <span>By <?php the_author(); ?></span>
            <span><?php the_date(); ?></span>
        </div>
        <div>
            <?php the_excerpt(); // Hiển thị tóm tắt nội dung ?>
        </div>

    <?php }
} ?>


