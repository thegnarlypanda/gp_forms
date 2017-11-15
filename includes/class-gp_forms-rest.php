<?php
    use Respect\Validation\Validator as Validator;
    use Respect\Validation\Exceptions\NestedValidationException;

    class Gp_forms_Rest {

        public function __construct() {
            $this->namespace = "gp_forms/v1";

            // require_once __DIR__ . '/vendor/autoload.php';
            require_once realpath(dirname(__FILE__)).'../../../../../vendor/autoload.php';
            
            include get_template_directory() . '/gp_forms/forms.php';

            $this->forms = $forms;

            add_action( 'rest_api_init', [$this, 'register_routes'] );
        }

        public function register_routes() {
            register_rest_route( $this->namespace , '/submit/(?P<form>\S+)', array(
                'methods' => 'POST',
                'callback' => array( $this, 'form_submit' )
            ));
        }

        public function form_submit( $request ) {
            if ( !check_ajax_referer( 'wp_rest', '_wpnonce' ) ) {
                return 'lol';
            } else {

                foreach ( $this->forms as $form ) {
                    if ( $form['form_slug'] == $request['form'] ) {
                        $formName = $form['form_name'];
                        $formSlug = $form['form_slug'];
                        $tmpFields = $form['fields'];
                        $tmpValidation = $form['validation'];
                        // print_r("form exist");
                    }
                }

                // $emailValidator = Validator::email();

                $formData = filter_input_array(
                    INPUT_POST,
                    $tmpFields
                );

                try {
                    $tmpValidation->assert($formData); 
                    $this->process_data( $formData, $tmpFields, $formSlug );
                    $this->notify_admin( $formData, $formName );

                    return array(
                        'hasErrors' => false
                    ); 
                } catch ( NestedValidationException $ex ) {
                    // print_r($ex->getMessages());
                    return array (
                        'hasErrors' => true,
                        'errors' => $ex->getMessages()
                    );
                }
                

                // print_r($request);
                // return $request;


                // try {
                //     $emailValidator->assert($_POST['email']);
                // } catch (NestedValidationException $ex) {
                //     $errors = $ex->getFullMessage();
                //     return $errors;
                // }
            }
        }

        public function process_data( $data, $fields, $formSlug ) {

            global $wpdb;
            $table_name = $wpdb->prefix . "gp_forms_entires";
            $keys = array_keys( $data );      

            $new_id = wp_insert_post( array( 'post_type' => 'gp_forms', 'post_title' => $data[$keys[0]] ) );

            wp_set_object_terms( $new_id, $formSlug, 'form' );

            if ( $new_id !== 0 ) {
                for ( $i = 0; $i < count($keys); $i++ ) {
                    $wpdb->insert( $table_name, array(
                        'form_id' => $formSlug,
                        'entry_id' => $new_id,
                        'field' => $keys[$i],
                        'value' => $data[$keys[$i]]
                    ) );
                }

                return true;
            }

            return false;
        }

        public function notify_admin( $data, $formName ) {
            add_filter( 'wp_mail_content_type', array( $this, "set_to_html" ) );
            
            $headers[] = "From: Website <herrow@giantpeach.agency>";

            $body = "";
            $keys = array_keys( $data );

            for ( $i = 0; $i < count($keys); $i++ ) {
                $body .=  "<strong>" . $keys[$i] . ":</strong> " . $data[$keys[$i]] . "<br>";
            }

            wp_mail( get_option( 'admin_email' ), "Form Entry - " . $formName, $body );

            remove_filter( 'wp_mail_content_type', array( $this, "set_to_html" ) );
        }

        public function set_to_html() {
            return "text/html";
        }

    }