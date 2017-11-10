<?php
    use Respect\Validation\Validator as Validator;
    use Respect\Validation\Exceptions\NestedValidationException;

    class Gp_forms_Rest {

        public static $halp = "plzhal";

        public function __construct() {
            $this->namespace = "gp_forms/v1";

            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

            add_action( 'rest_api_init', [$this, 'register_routes'] );
        }

        public function register_routes() {
            register_rest_route( $this->namespace , '/submit', array(
                'methods' => 'POST',
                'callback' => array( $this, 'form_submit' )
            ));
        }

        public function form_submit( $request ) {
            if ( !check_ajax_referer( 'wp_rest', '_wpnonce' ) ) {
                return 'lol';
            } else {
                // return $_POST;
                echo $_POST['email'];
                $emailValidator = Validator::email();

                $formData = filter_input_array(
                    INPUT_POST,
                    array(
                        'name' => FILTER_SANITIZE_STRING,
                        'email' => FILTER_SANITIZE_STRING,
                        'message' => FILTER_SANITIZE_STRING,
                        'linkedin' => FILTER_SANITIZE_STRING,
                        'tel' => FILTER_VALIDATE_INT,
                        'twitter' => FILTER_SANITIZE_STRING,
                        'url' => FILTER_SANITIZE_STRING
                    )
                );
                print_r($request);
                return $request;


                // try {
                //     $emailValidator->assert($_POST['email']);
                // } catch (NestedValidationException $ex) {
                //     $errors = $ex->getFullMessage();
                //     return $errors;
                // }
            }
        }

        public function process_data( $data ) {
            return $data;
        }

    }
