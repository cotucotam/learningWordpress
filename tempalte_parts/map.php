<?php 
            $location = get_field('map_location'); // Lấy dữ liệu từ ACF

            if ($location){ 
                // Lấy ID bài post để tạo ID duy nhất cho bản đồ
                $post_id = get_the_ID();
                $map_id = "map-" . $post_id;
                // Lấy latitude (vĩ độ)
                preg_match('/data-map-lat="([\d\.]+)"/', $location, $lat_match);
                $lat = $lat_match[1] ?? null;

                // Lấy longitude (kinh độ)
                preg_match('/data-map-lng="([\d\.]+)"/', $location, $lng_match);
                $lng = $lng_match[1] ?? null;

               // Bước 1: Trích xuất chuỗi JSON từ thuộc tính data-map-markers
                preg_match('/data-map-markers="(.*?)"/', $location, $matches);
                $json_string = html_entity_decode($matches[1] ?? '');

                // Bước 2: Giải mã JSON thành mảng PHP
                $markers = json_decode($json_string, true);

                // Bước 3: Lấy địa chỉ từ dữ liệu JSON
                if (!empty($markers) && isset($markers[0]['label'])) {
                    $address = $markers[0]['label']; // Địa chỉ chính
                } elseif (!empty($markers) && isset($markers[0]['default_label'])) {
                    $address = $markers[0]['default_label']; // Địa chỉ dự phòng
                }

            ?>
                <div id="<?php echo $map_id; ?>" style="width: 100%; height: 400px;">
                    
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var map = L.map('<?php echo $map_id; ?>').setView([<?php echo trim($lat); ?>, <?php echo trim($lng); ?>], 15);
                        L.tileLayer('http://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                            attribution: '&copy; Google'
                        }).addTo(map);
                        L.marker([<?php echo trim($lat); ?>, <?php echo trim($lng); ?>]).addTo(map)
                        .bindPopup('<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3> <?php echo $address; ?>')
                        .openPopup();
                    });
                    
                </script>
                

<?php 
wp_reset_postdata();
} 