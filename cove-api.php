<?php
/*
Plugin Name: COVE API
Plugin URI:
Description: Implement basic access to the COVE API V1
Author: WNET
Version: 1.0
Author URI: http://www.wnet.org
*/

require_once(WP_PLUGIN_DIR . '/pbs-coveapi-wp-plugin/cove-api-auth.php');
require_once(WP_PLUGIN_DIR . '/pbs-coveapi-wp-plugin/cove-api-settings.php');

    //set the special key
    $options = get_option('cove_api_settings');
    $api_id = $options['api_id'];
    $api_secret = $options['api_secret'];
    $cache_ttl = $options['cache_ttl'];
    $default_producer = $options['default_producer'];
    $default_program = $options['default_program'];
    $show_dashboard_widget = $options['show_dashboard_widget'];

function cove_get_programs ($request_params) {

    global $api_id, $api_secret, $cache_ttl, $default_producer;

    $request_endpoint = "http://api.pbs.org/cove/v1/programs/";

    if (!isset($request_params)) {
        $request_params = array(
            'filter_producer__name' => $default_producer,
            'fields' => 'associated_images',
            'order_by' => 'title'
        );
    }

    $request_query = str_replace('%2C',',',http_build_query($request_params, '', '&')); // too much encoding was done by php

    $request_url = $request_endpoint."?".$request_query;

    $transient_key = 'cove_api'.md5($request_url);

    $cache = get_site_transient($transient_key);

    if (isset($cache->{'results'})) {
        return $cache;
    }

    $requestor = new COVE_API_Request($api_id,$api_secret);

    $json = $requestor->make_request($request_url);

    if ($json == FALSE)
    {
        $data = json_decode('{"status": "Request failed."}');
        return false;
    }

    $data = json_decode($json);

    $data->{'status'} = 'Last Updated: '.current_time('mysql');

    $cache_stat = set_site_transient($transient_key, $data, $cache_ttl);

    return $data;

}

function cove_get_videos ($request_params) {

    global $api_id, $api_secret, $cache_ttl, $default_program;

    $request_endpoint = "http://api.pbs.org/cove/v1/videos/";

    if (!isset($request_params)) {
        $request_params = array(
            'filter_program' => $default_program,
            'limit_stop' => '10',
            'fields' => 'associated_images,mediafiles',
            'order_by' => '-available_datetime'
        );
    }

    $request_query = str_replace('%2C',',',http_build_query($request_params, '', '&')); // too much encoding was done by php

    $request_url = $request_endpoint."?".$request_query;

    $transient_key = 'cove_api'.md5($request_url);

    $cache = get_site_transient($transient_key);

    if (isset($cache->{'results'})) {
        return $cache;
    }

    $requestor = new COVE_API_Request($api_id,$api_secret);


    $json = $requestor->make_request($request_url);

    if ($json == FALSE)
    {
        $data = json_decode('{"status": "Request failed."}');
        return false;
    }

    $data = json_decode($json);

    $data->{'status'} = 'Last Updated: '.current_time('mysql');

    $cache_stat = set_site_transient($transient_key, $data, $cache_ttl);

    return $data;

}

function cove_default_producer_display () {

    global $default_producer;

    $request_params = array(
        'fields' => 'associated_images',
        'order_by' => 'title'
        );
 
    if ($default_producer) {
        $request_params['filter_producer__name'] = $default_producer;
    }

    $data = cove_get_programs($request_params);

    echo '<p><strong>COVE Programs</strong> '.$data->{'status'}.'</p>';

    if ($data) {

    echo '<ul class="widefat">';

    foreach ($data->{'results'} as $programidx => $program)
    {
        $program_thumb_url = '';
        foreach ($program->associated_images as $imageidx => $image)
        {
            $type = $image->type;
            $usage_type = $type->usage_type;

            if ($usage_type == 'iPhone-Small')
            {
                $program_thumb_url = sprintf('<img src="%s" />',$image->url);
            } else {
                $program_thumb_url = '&nbsp;';
            }
        }
        $pieces = '';
        $pieces = explode('/', $program->producer->resource_uri);
        $cove_producer_id = $pieces[4];
        $pieces = '';
        $pieces = explode('/', $program->resource_uri);
        $cove_program_id = $pieces[4];

        printf('<li><table><tr><td valign="top"><small><strong>%s</strong>',$program->title);
        printf('<br />Producer ID: <strong>%s</strong> Program ID: <strong>%s</strong>',
                $cove_producer_id,
                $cove_program_id);
        printf('<br />%s',$program->short_description);
        printf('<br /><a href="%s">Website</a> NOLA Root: <strong>%s</strong></small>',
                $program->website,
                $program->nola_root);
        printf('</td><td style="width:144px;">%s</td></tr></table></li>',$program_thumb_url);


    }
    echo '</ul>';

    }

}

function cove_default_videos_display () {

    global $default_program;

    $request_params = array(
        'filter_program' => $default_program,
        'order_by' => '-airdate',
        'fields' => 'associated_images,mediafiles,categories',
        'limit_stop' => '15'
        );

    $data = cove_get_latest_videos($request_params);

    echo '<p>Program ID: '.$default_program.' '.$data['status'].'</p>';

    if ($data) {

        echo '<ul class="widefat">';
        foreach ($data['results'] as $cove_latest_video) {
            echo '<li><table style="width:100%;"><tr><td>';
            printf('<small><strong>%s</strong> ',$cove_latest_video['title']);
            printf('<br />%s',$cove_latest_video['short_description']);
            printf('<br />Media ID: <strong>%s</strong> Post ID: <strong>%s</strong> Length: <strong>%s</strong>',
                       $cove_latest_video['tp_media_object_id'],
                       $cove_latest_video['legacy_external_ref'],
                       $cove_latest_video['media_length']);
            printf('<br />Air Date: <strong>%s</strong> Available: <strong>%s</strong></small>',
                       substr($cove_latest_video['airdate'], 0, 10),
                       $cove_latest_video['available_datetime']);

            printf('</td><td style="width:142px;"><small>Type: <strong>%s</strong></small>',$cove_latest_video['type']);
            if ($cove_latest_video['cove_default_thumb']) {
                printf('<img src="%s" width="142" height="60"/>',$cove_latest_video['cove_default_thumb']);
            } else {
                echo '&nbsp;';
            }
            if ($cove_latest_video['partner_player_thumb'] || $cove_latest_video['sd16x9_thumb']) { echo '<br />'; }
            if ($cove_latest_video['partner_player_thumb']) {
                printf('<small><a href="%s"> (512x288)</a></small>',
                        $cove_latest_video['partner_player_thumb']);
            } else {
                echo '&nbsp;';
            }
            if ($cove_latest_video['sd16x9_thumb']) {
                printf('<small><a href="%s"> (640x360)</a></small>',
                        $cove_latest_video['sd16x9_thumb']);
            } else {
                echo '&nbsp;';
            }
            echo '</td></tr></table></li>';

        }

    echo '</ul>';

    }

}

function cove_get_latest_videos ( $args ) {

    if (!$args['order_by']) { $args['order_by'] = '-airdate';}
    if (!$args['fields']) { $args['fields'] = 'associated_images,mediafiles,categories';}
    if (!$args['limit_stop']) { $args['limit_stop'] = '10';}

    $request_params = array(
        'filter_title' => $args['filter_title'],
        'filter_program' => $args['filter_program'],
        'filter_availability_status' => $args['filter_availability_status'],
        'filter_record_last_updated_datetime__gt' => $args['filter_record_last_updated_datetime__gt'],
        'filter_type' => $args['filter_type'],
        'exclude_type' => $args['exclude_type'],
        'filter_tp_media_object_id' => $args['filter_tp_media_object_id'],
        'filter_mediafile_set__video_encoding__mime_type' => $args['filter_mediafile_set__video_encoding__mime_type'],
        'filter_categories__id' => $args['filter_categories__id'],
        'filter_producer_name' => $args['filter_producer_name'],
        'order_by' => $args['order_by'],
        'fields' => $args['fields'],
        'limit_start' => $args['limit_start'],
        'limit_stop' => $args['limit_stop']
        );
 
    $data = cove_get_videos(array_filter($request_params));

    $cove_latest_videos = array();

    if ($data) {

    $cove_latest_videos['status'] = $data->{'status'};

    foreach ($data->{'results'} as $videoidx => $video)
    {
        $media_length = '';
        foreach ($video->mediafiles as $mediaidx => $media)
        {
                if (in_array('Ingestion Complete',$media->ingestion_state) &&
                        'MPEG-4 500kbps' == $media->video_encoding->name) {
                        $media_length = cove_convert_milliseconds($media->length_mseconds);
                }
        }
        $cove_default_thumb = '';
        $partner_player_thumb = '';
        $stack_card_thumb = '';
        $ipad_small_thumb = '';
        $sd16x9_thumb = '';
        foreach ($video->associated_images as $imageidx => $image)
        {
            $type = $image->type;
            $usage_type = $type->usage_type;

            if ($usage_type == 'ThumbnailCOVEDefault')
            {
                if (strstr($image->url,'/cove-media')) {
                    $cove_default_thumb = $image->url;
                } elseif (empty($cove_default_thumb)) {
                    $cove_default_thumb = $image->url;
                }
            }
            if ($usage_type == 'PartnerPlayer')
            {
                $partner_player_thumb = $image->url;
            }
            if ($usage_type == 'COVEStackCard')
            {
                $stack_card_thumb = $image->url;
            }
            if ($usage_type == 'iPad-Small')
            {
                $ipad_small_thumb = $image->url;
            }
            if ($usage_type == 'SD16x9')
            {
                $sd16x9_thumb = $image->url;
            }
        }
        $video_item = array();
        $video_item['rating'] = $video->rating;
        $video_item['episode_url'] = $video->episode_url;
        $video_item['legacy_external_ref'] = $video->legacy_external_ref;
        $video_item['airdate'] = $video->airdate;
        $video_item['availability'] = $video->availability;
        $video_item['allow_embed'] = $video->allow_embed;
        $video_item['available_datetime'] = $video->available_datetime;
        $video_item['long_description'] = $video->long_description;
        $video_item['segment_stop_time'] = $video->segment_stop_time;
        $video_item['tp_media_object_id'] = $video->tp_media_object_id;
        $video_item['copyright'] = $video->copyright;
        $video_item['title'] = $video->title;
        $video_item['nola_episode'] = $video->nola_episode;
        $video_item['short_description'] = $video->short_description;
        $video_item['type'] = $video->type;
        $video_item['transcript_url'] = $video->transcript_url;
        $video_item['segment_start_time'] = $video->segment_start_time;
        $video_item['nola_root'] = $video->nola_root;
        $video_item['resource_uri'] = $video->resource_uri;
        $video_item['media_length'] = $media_length;
        $video_item['cove_default_thumb'] = $cove_default_thumb;
        $video_item['partner_player_thumb'] = $partner_player_thumb;
        $video_item['stack_card_thumb'] = $stack_card_thumb;
        $video_item['ipad_small_thumb'] = $ipad_small_thumb;
        $video_item['sd16x9_thumb'] = $sd16x9_thumb;
        $cove_latest_videos['results'][] = $video_item;

    }

    return $cove_latest_videos;

    } else {
        return false;
    }

}

function cove_add_dashboard_widget () {

    global $wp_meta_boxes, $show_dashboard_widget;

    if (is_admin()) {

        if ('Yes' == $show_dashboard_widget) {
            add_meta_box('cove_default_videos_display','Recent COVE Videos','cove_default_videos_display','dashboard','side','sorted');
        }

    }

}

add_action('wp_dashboard_setup', 'cove_add_dashboard_widget');

function cove_convert_milliseconds ($duration_mseconds) {

    $duration_seconds = $duration_mseconds / 1000;

    if ($duration_seconds > 3600) {

        $hours = intval($duration_seconds/3600);
        $remaining_seconds = ($duration_seconds % 3600);
        $minutes = intval($remaining_seconds / 60);
        $seconds = intval($remaining_seconds % 60);
        $media_length = sprintf("%'02u:%'02u:%'02u", $hours, $minutes, $seconds);

    } elseif ($duration_seconds > 60) {

        $minutes = intval($duration_seconds / 60);
        $seconds = intval($duration_seconds % 60);
        $media_length = sprintf("%'02u:%'02u", $minutes, $seconds);

    } else {

        $media_length = sprintf("%'02u:%'02u",0, intval($duration_seconds));
    }

    return $media_length;

}

?>
