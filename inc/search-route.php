<?php
    add_action('rest_api_init', 'universityRegisterSearch');

    function universityRegisterSearch(){
        register_rest_route('university/v1', 'search', array(
            'method' => WP_REST_SERVER::READABLE,
            'callback' => 'universitySearchResults'
        ));
    }

    function universitySearchResults($data){
        $mainQuery = new WP_Query(array(
            'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
            's' => sanitize_text_field($data['term'])
        ));
        
        $results = array(
            'generalInfo' => array(),
            'professors' => array(),
            'programs' => array(),
            'events' => array(),
            'campuses' => array(),
        );

        while($mainQuery->have_posts()){
            $mainQuery->the_post();
            if(get_post_type() == 'post' OR get_post_type() == 'page'){
                array_push($results['generalInfo'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'postType' => get_post_type(),
                    'authorName' => get_the_author()
                ));
            }

            if(get_post_type() == 'professor'){
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));
            }
            if(get_post_type() == 'program'){
                $relatedCampuses = get_field('related_campus');
                if($relatedCampuses){
                    foreach($relatedCampuses as $campus){
                        array_push($results['campuses'],array(
                            'title' => get_the_title($campus),
                            'permalink' => get_the_permalink($campus)
                        ));
                    }
                }
                array_push($results['programs'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'id' => get_the_id()
                ));
            }
            if(get_post_type() == 'event'){
                $eventDate = get_post_meta(get_the_ID(), 'event-date', true); // Get event-date field
                // Convert the event date to a DateTime object
                $eventDateObj = DateTime::createFromFormat('F jS, Y', $eventDate);
  
                if ($eventDateObj) {
                    $eventMonth = $eventDateObj->format('F'); // Get full month name (e.g., "July")
                    $eventDay   = $eventDateObj->format('j'); // Get day number (e.g., "1")
                } else {
                    $eventMonth = 'Unknown';
                    $eventDay   = 'Unknown';
                }

                $description = null;
                if (has_excerpt()){
                    $description = get_the_excerpt();
                    } else{
                    $description = wp_trim_words(get_the_content(),18);
                    }
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventMonth,
                    'day' =>$eventDay,
                    'description' =>$description
                ));
                
            }
            if(get_post_type() == 'campus'){
                array_push($results['campuses'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink()
                ));
            }

        }
        if($results['programs']){
            $programsMetaQuery = array('relation' => 'OR');
            foreach($results['programs'] as $item){
                array_push($programsMetaQuery,array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . $item['id'] . '"'
                ));
            }
            $programRelationshipQuery = new WP_Query(array(
                'post_type' => array('professor', 'event') ,
                'meta_query' => $programsMetaQuery
            ));

            while($programRelationshipQuery->have_posts()){
                $programRelationshipQuery->the_post();

                if(get_post_type() == 'event'){
                    $eventDate = get_post_meta(get_the_ID(), 'event-date', true); // Get event-date field
                    // Convert the event date to a DateTime object
                    $eventDateObj = DateTime::createFromFormat('F jS, Y', $eventDate);
      
                    if ($eventDateObj) {
                        $eventMonth = $eventDateObj->format('F'); // Get full month name (e.g., "July")
                        $eventDay   = $eventDateObj->format('j'); // Get day number (e.g., "1")
                    } else {
                        $eventMonth = 'Unknown';
                        $eventDay   = 'Unknown';
                    }
    
                    $description = null;
                    if (has_excerpt()){
                        $description = get_the_excerpt();
                        } else{
                        $description = wp_trim_words(get_the_content(),18);
                        }
                    array_push($results['events'], array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'month' => $eventMonth,
                        'day' =>$eventDay,
                        'description' =>$description
                    ));
                    
                }

                if(get_post_type() == 'professor'){
                    array_push($results['professors'],array(
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                    ));
                }
            }
            $results['professor'] = array_values(array_unique($results['professors'],SORT_REGULAR));
            $results['events'] = array_values(array_unique($results['events'],SORT_REGULAR));
        }

        return $results;
    }
?>