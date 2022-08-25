<?php namespace MasterPopups\Includes;

use Xbox\Includes\CSS;

class Lista extends ListOptions {
    public $plugin = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $options = array() ){
        $this->plugin = Functions::get_plugin_instance();
        parent::__construct( $options );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas creadas
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_all_lists(){
        $plugin = Functions::get_plugin_instance();
        $items = array();
        $items[''] = __( '- Select your list -', 'masterpopups' );
        $lists = \XboxItems::posts_by_post_type( $plugin->post_types['lists'], array(
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ) );
        return Functions::nice_array_merge( $items, $lists );
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna una tabla con todos los contactos de una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function get_subscribers_list(){
        if( ! Functions::is_editing_post_type( $this->plugin->post_types['lists'] ) ){
            return '';
        }

        $return = '';
        $subscribers = (array) $this->option('subscribers');//Suscriptores guardados en Masterpopups
        $total = (int) $this->option('total-subscribers' );//Total tanto en MasterPopups como en servicios
        $subscribers = array_reverse( $subscribers );

        $total_text = sprintf( _n( '%s Subscriber', '%s Subscribers', $total, 'masterpopups' ), '<span>' . $total . '</span>' );
        $return .= "<div class='ampp-total-subscribers'><i class='xbox-icon xbox-icon-users'></i>$total_text</div>";

        if( Functions::is_empty( $subscribers ) ){
            return $return;
        }

        $return .= "<table class='ampp-table mpp-datatable mpp-table-subscribers' data-audience-id='" . $this->id . "'>";
        $return .= "<thead><tr>";
        $return .= "<th><i class='xbox-icon xbox-icon-envelope'></i> Email</th>";
        $return .= "<th>" . __( 'First name', 'masterpopups' ) . "</th>";
        $return .= "<th>" . __( 'Last name', 'masterpopups' ) . "</th>";
        $return .= "<th>" . __( 'Custom fields', 'masterpopups' ) . "</th>";
        $return .= "<th><i class='xbox-icon xbox-icon-calendar'></i> " . __( 'Registration date', 'masterpopups' ) . "</th>";
        $return .= "<th><input type='checkbox' class='ampp-checkbox-all-subscribers'><a href='#' class='ampp-delete-all-subscribers' title='Delete'><i class='xbox-icon xbox-icon-trash xbox-color-red'></i></a></th>";
        $return .= "</tr></thead><tbody>";

        foreach( $subscribers as $email => $data ){
            $return .= "<tr>";
            $return .= "<td data-email='$email'>$email</td>";
            $return .= "<td>{$data['first_name']}</td>";
            $return .= "<td>{$data['last_name']}</td>";
            $return .= "<td>";
            foreach( $data['custom_fields'] as $key => $value ){
                $return .= ! empty( $value ) ? "<strong>$key</strong>: $value<br>" : '';
            }
            $return .= "</td>";
            if( isset( $data['date'] ) ){
                $date = date( "m/d/Y h:i:s a", strtotime( $data['date'] ) );
                $return .= "<td data-sort='{$data['date']}'>$date</td>";
            } else{
                $return .= "<td>-</td>";
            }
            $return .= "<td><a href='#' class='ampp-delete-subscriber' title='Delete'><i class='xbox-icon xbox-icon-trash xbox-color-red'></i></a></td>";
            $return .= "</tr>";
        }
        $return .= "</tbody></table>";
        return $return;
    }





}
