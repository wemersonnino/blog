<?php namespace MasterPopups\Includes;

use Xbox\Includes\CSS;

class Player {
    private $google_api_key = 'AIzaSyCXXembGADcoCgo0-H5LzkWuCxLK2XVVjA';
    public $video_url = '';
    public $provider = '';
    public $image = '';
    public $id = null;
    public $player = '';
    public $is_youtube = false;
    public $is_vimeo = false;
    public $is_dailymotion = false;
    public $is_html5 = false;
    public $is_playlist = false;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $url = '', $lazy_load = false, $parameters = array(), $css = array() ){
        $this->video_url = trim( $url );
        $this->id = $this->get_id( $url );
        $this->provider = $this->get_player_provider( $url );
        $this->image = $this->get_image( $url );
        $this->player = $this->get_player( $url, $lazy_load, $parameters, $css );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el id de un video. Usado de forma privada una sola vez en el constructor.
    |---------------------------------------------------------------------------------------------------
    */
    private function get_id( $url = '' ){
        if( $this->id = $this->is_youtube_url( $url ) ){
            $this->is_youtube = true;
        } else if( $this->id = $this->is_vimeo_url( $url ) ){
            $this->is_vimeo = true;
        } else if( $this->id = $this->is_dailymotion_url( $url ) ){
            $this->is_dailymotion = true;
        } else if( $this->is_html5_player( $url ) ){
            $this->is_html5 = true;
            $this->id = false;//HTML no tiene ID de video
        } else {
            $this->id = false;
        }
        return $this->id;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el id de un video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_video_id( $url = '' ){
        $video_id = '';
        if( $this->is_youtube_url( $url ) ){
            $video_id = $this->get_youtube_id( $url );
        } else if( $this->is_vimeo_url( $url ) ){
            $video_id = $this->get_vimeo_id( $url );
        } else if( $this->is_dailymotion_url( $url ) ){
            $video_id = $this->get_dailymotion_id( $url );
        }
        return $video_id;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el proveedor del video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_player_provider( $url = '' ){
        $provider = '';
        if( $this->is_youtube_url( $url ) ){
            $provider = 'youtube';
        } else if( $this->is_vimeo_url( $url ) ){
            $provider = 'vimeo';
        } else if( $this->is_dailymotion_url( $url ) ){
            $provider = 'dailymotion';
        } else if( $this->is_html5_player( $url ) ){
            $provider = 'html5';
        }
        return $provider;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene una imagen de un video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_image( $url = '' ){
        $image = '';
        if( $this->is_youtube_url( $url ) ){
            $image = $this->get_youtube_image( $this->get_youtube_id( $url ) );
        } else if( $this->is_vimeo_url( $url ) ){
            $image = $this->get_vimeo_image( $this->get_vimeo_id( $url ) );
        } else if( $this->is_dailymotion_url( $url ) ){
            $image = $this->get_dailymotion_image( $this->get_dailymotion_id( $url ) );
        }
        return $image;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el iframe de un video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_player( $url, $lazy_load = false, $parameters = array(), $css = array() ){
        $iframe = '';
        if( $this->is_youtube_url( $url ) ){
            $iframe = $this->get_youtube_player( $url, $lazy_load, $parameters, $css );
        } else if( $this->is_vimeo_url( $url ) ){
            $iframe = $this->get_vimeo_player( $url, $lazy_load, $parameters, $css );
        } else if( $this->is_dailymotion_url( $url ) ){
            $iframe = $this->get_dailymotion_player( $url, $lazy_load, $parameters, $css );
        } else if( $this->is_soundcloud_url( $url ) ){
            $iframe = $this->get_soundcloud_player( $url, $lazy_load, $parameters, $css );
        }
        return $iframe;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get youtube video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_youtube_player( $url, $lazy_load = false, $parameters = array(), $css = array() ){
        $unique_id = $this->random_player_id();
        $defaults = array(
            'origin' => get_home_url(),
            'version' => '3',
            'enablejsapi' => '1',
            'html5' => '1',
            'wmode' => 'opaque',
            'theme' => 'dark',
            'modestbranding' => '1',
            'hd' => '1',
            'rel' => '0',
            'showinfo' => '0',
            'start' => '0',
            'volume' => '100',
            'loop' => '0',
            'autoplay' => '0',
        );

        $player_id = $this->get_youtube_id( $url );

        if( $this->is_playlist ){
            $defaults['list'] = $player_id;
        }

        $parameters = wp_parse_args( $parameters, $defaults );
        $parameters = http_build_query( $parameters );

        if( $this->is_playlist ){
            $player_url = "//www.youtube.com/embed/videoseries?{$parameters}";
        } else {
            $player_url = "//www.youtube.com/embed/{$player_id}?{$parameters}";
        }

        $src = $lazy_load ? 'about:blank' : $player_url;
        $style = new CSS();
        $style = ! empty( $css ) ? "style='{$style->build_css( $css )}'" : '';
        $iframe = "<iframe id='{$unique_id}' src='{$src}' data-src='{$player_url}' frameborder='0' allowfullscreen {$style}></iframe>";
        return $iframe;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get vimeo video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_vimeo_player( $url, $lazy_load = false, $parameters = array(), $css = array() ){
        $unique_id = $this->random_player_id();
        $defaults = array(
            'player_id' => $unique_id,
            'api' => '1',
            'byline' => '0',
            'portrait' => '0',
            'badge' => '0',
            'title' => '0',
            'autoplay' => '0',
        );
        $parameters = wp_parse_args( $parameters, $defaults );
        $parameters = http_build_query( $parameters );
        $player_id = $this->get_vimeo_id( $url );
        $player_url = "//player.vimeo.com/video/{$player_id}?{$parameters}";
        $src = $lazy_load ? 'about:blank' : $player_url;
        $style = new CSS();
        $style = ! empty( $css ) ? "style='{$style->build_css( $css )}'" : '';

        $iframe = "<iframe id='{$unique_id}' src='{$src}' data-src='{$player_url}' frameborder='0' allowfullscreen {$style}></iframe>";
        return $iframe;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get daylimotion video
    |---------------------------------------------------------------------------------------------------
    */
    public function get_dailymotion_player( $url, $lazy_load = false, $parameters = array(), $css = array() ){
        $unique_id = $this->random_player_id();
        $defaults = array(
            'sharing-enable' => '0',
            'ui-logo' => '0',
            'autoplay' => '0',
        );
        $parameters = wp_parse_args( $parameters, $defaults );
        $parameters = http_build_query( $parameters );
        $player_id = $this->get_dailymotion_id( $url );
        $player_url = "//www.dailymotion.com/embed/video/{$player_id}?{$parameters}";
        $src = $lazy_load ? 'about:blank' : $player_url;
        $style = new CSS();
        $style = ! empty( $css ) ? "style='{$style->build_css( $css )}'" : '';

        $iframe = "<iframe id='{$unique_id}' src='{$src}' data-src='{$player_url}' frameborder='0' allowfullscreen {$style}></iframe>";
        return $iframe;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Get soundcloud player
    |---------------------------------------------------------------------------------------------------
    */
    public function get_soundcloud_player( $url, $lazy_load = false, $parameters = array(), $css = array() ){
        $iframe = '';
        $parameters = "url=$url&format=js&iframe=true";
        $args = array();
        if( $args['autoplay'] == 1 ){
            $parameters .= "&auto_play=true";
        }
        //Get the JSON data of song details with embed code from SoundCloud oEmbed
        //https://developers.soundcloud.com/docs/api/reference#oembed
        $data = file_get_contents( "http://soundcloud.com/oembed?$parameters" );
        if( $data !== false ){
            //Clean the Json to decode, remove: ( and );
            $decode_iframe = substr( $data, 1, -2 );
            //json decode to convert it as an array
            $json_obj = json_decode( $decode_iframe, true );
            if( isset( $json_obj['html'] ) ){
                $iframe = $json_obj['html'];
            }
        }
        return $iframe;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si una url es un video de youtube
    |---------------------------------------------------------------------------------------------------
    */
    public function is_youtube_url( $url ){
        return $this->get_youtube_id( $url );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si una url es un video de vimeo
    |---------------------------------------------------------------------------------------------------
    */
    public function is_vimeo_url( $url ){
        return $this->get_vimeo_id( $url );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si una url es un video de dailymotion
    |---------------------------------------------------------------------------------------------------
    */
    public function is_dailymotion_url( $url ){
        return $this->get_dailymotion_id( $url );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si una url es un audio de soundcloud
    |---------------------------------------------------------------------------------------------------
    */
    public function is_soundcloud_url( $url ){
        $pattern = '/^https?:\/\/(soundcloud.com|snd.sc)\/(.*)$/';
        $result = preg_match( $pattern, $url, $matches );
        if( $result ){
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si es un video html5
    |---------------------------------------------------------------------------------------------------
    */
    public function is_html5_player( $url = '' ){
        $extension = Functions::get_file_extension( $url );
        if( $extension && in_array( $extension, array( 'mp4', 'webm', 'ogv', 'ogg', 'vp8' ) ) ){
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el id de un video de youtube
    |---------------------------------------------------------------------------------------------------
    */
    public function get_youtube_id( $url = '' ){
        if( $this->is_youtube && $this->video_url == trim( $url ) && $this->id ){
            return $this->id;
        }
        $pattern =
            '~(?#!js YouTubeId Rev:20160125_1800)
	    # Match non-linked youtube URL in the wild. (Rev:20130823)
	    https?://          # Required scheme. Either http or https.
	    (?:[0-9A-Z-]+\.)?  # Optional subdomain.
	    (?:                # Group host alternatives.
	      youtu\.be/       # Either youtu.be,
	    | youtube          # or youtube.com or
	      (?:-nocookie)?   # youtube-nocookie.com
	      \.com            # followed by
	      \S*?             # Allow anything up to VIDEO ID,
	      [^\w\s-]         # but char before ID is non-ID char.
	    )                  # End host alternatives.
	    ([\w-]{11})        # $1: VIDEO ID is exactly 11 chars.
	    (?=[^\w-]|$)       # Assert next char is non-ID or EOS.
	    (?!                # Assert URL is not pre-linked.
	      [?=&+%\w.-]*     # Allow URL (query) remainder.
	      (?:              # Group pre-linked alternatives.
	        [\'"][^<>]*>   # Either inside a start tag,
	      | </a>           # or inside <a> element text contents.
	      )                # End recognized pre-linked alts.
	    )                  # End negative lookahead assertion.
	    [?=&+%\w.-]*       # Consume any URL (query) remainder.
	    ~ix';

        //Support for Video Playlist
        $result = preg_match( '/(\/playlist\?|\/embed\/videoseries\?)list=(([^\/])+?)\/?$/', $url, $matches );
        if( $result && isset( $matches[2] ) ){
            $this->is_playlist = true;
            return $matches[2];
        }

        //Support video
        $result = preg_match( $pattern, $url, $matches );
        if( $result && isset( $matches[1] ) ){
            $this->is_playlist = false;
            return $matches[1];
        }

        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el id de un video de vimeo
    |---------------------------------------------------------------------------------------------------
    */
    public function get_vimeo_id( $url ){
        if( $this->is_vimeo && $this->video_url == trim( $url ) && $this->id ){
            return $this->id;
        }
        $pattern = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';
        $result = preg_match( $pattern, $url, $matches );
        if( $result && isset( $matches[5] ) ){
            return $matches[5];
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene el id de un video de dailymotion
    |---------------------------------------------------------------------------------------------------
    */
    public function get_dailymotion_id( $url ){
        if( $this->is_dailymotion && $this->video_url == trim( $url ) && $this->id ){
            return $this->id;
        }
        $pattern = '!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!';
        $result = preg_match( $pattern, $url, $matches );
        if( $result ){
            if( isset( $matches[6] ) ){
                return $matches[6];
            }
            if( isset( $matches[4] ) ){
                return $matches[4];
            }
            return $matches[2];
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene la imagen de un video de youtube
    |---------------------------------------------------------------------------------------------------
    */
    public function get_youtube_image( $player_id ){
        $player_id = trim( $player_id );
        if( empty( $player_id ) ){
            return '';
        }
        if( $this->is_playlist ){
            $data = file_get_contents( "https://www.googleapis.com/youtube/v3/playlistItems?key={$this->google_api_key}&part=snippet&playlistId={$player_id}" );
            if( $data !== false ){
                $data = json_decode( $data, true );
                if( isset( $data['items'][0]['snippet']['thumbnails']['maxres']['url'] ) ){
                    return $data['items'][0]['snippet']['thumbnails']['maxres']['url'];
                }
            }
        }
        return "//img.youtube.com/vi/$player_id/maxresdefault.jpg";
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene la imagen de un video de Vimeo
    |---------------------------------------------------------------------------------------------------
    */
    public function get_vimeo_image( $player_id ){
        $player_id = trim( $player_id );
        if( empty( $player_id ) ){
            return '';
        }
        //$data = file_get_contents( "http://vimeo.com/api/v2/video/{$player_id}.php" );//Genera warning cuando $payer_id no existe. failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found
        $irondev = new IronDev( "http://vimeo.com/api/v2/video/{$player_id}.php" );
        $data = $irondev->get( '' );
        if( $irondev->success() ){
            if( $data !== false ){
                if( is_serialized( $data ) ){
                    $data = unserialize( $data );
                }
                if( isset( $data[0]['thumbnail_large'] ) ){
                    return $data[0]['thumbnail_large'];
                }
            }
        }
        return '';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Obtiene la imagen de un video de dailymotion
    |---------------------------------------------------------------------------------------------------
    */
    public function get_dailymotion_image( $player_id ){
        $player_id = trim( $player_id );
        if( empty( $player_id ) ){
            return '';
        }
        $data = file_get_contents( "https://api.dailymotion.com/video/{$player_id}?fields=thumbnail_720_url" );
        if( $data !== false ){
            $data = json_decode( $data, true );
            if( isset( $data['thumbnail_720_url'] ) ){
                return $data['thumbnail_720_url'];
            }
        }
        return '';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Id aleatorio para cada video
    |---------------------------------------------------------------------------------------------------
    */
    public function random_player_id( $length = 10, $numbers = true ){
        return Functions::random_string( $length, $numbers );
    }

}
