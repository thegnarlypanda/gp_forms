<?php
    use Respect\Validation\Validator as Validator;
    use Respect\Validation\Exceptions\NestedValidationException;

    class Gp_forms_Rest {

        public function __construct() {
            $this->namespace = "gp_forms/v1";

            if (file_exists(realpath(dirname(__FILE__)).'/../../../../../vendor/autoload.php')) {
                require_once realpath(dirname(__FILE__)).'/../../../../../vendor/autoload.php';
            }
            else {
                require_once realpath(dirname(__FILE__)).'../../vendor/autoload.php';
            }
            
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
                        $tmpErrors = $form['errors'];
                        $errorMessage = $form['mainError'];

                        if ( isset( $form['files'] ) ) {
                            $mimetypes = $form['files'];
                        } else {
                            $mimetypes = false;
                        }

                        if ( isset( $form['filesize'] ) ) {
                            $filesize = $form['filesize'];
                        } else {
                            $filesize = false;
                        }

                        if ( isset( $form['successMessage'] ) ) {
                            $successMessage = $form['successMessage'];
                        } else {
                            $successMessage = 'Form submitted successfully.';
                        }

                        if ( isset( $form['email'] ) && !empty( $form['email'] ) ) {
                            $notificationEmail = $form['email'];
                        } else {
                            $notificationEmail = get_option( 'admin_email' );
                        }

                        if ( isset( $form['from'] ) && !empty( $form['from']['email'] ) && !empty( $form['from']['name'] ) ) {
                            $from = array(
                                'email' => $form['from']['email'],
                                'name' => $form['from']['name']
                            );
                        } else {
                            $from = array(
                                'email' => get_option( 'admin_email' ),
                                'name' => get_bloginfo( 'name' )
                            );
                        }
                    }
                }

                $formData = filter_input_array(
                    INPUT_POST,
                    $tmpFields
                );

                try {
                    $tmpValidation->assert($formData);

                    if ( isset( $_FILES ) ) {
                        $files = $this->process_files( $_FILES, $mimetypes, $filesize );
                    } else {
                        $files = null;
                    }

                    $this->process_data( $formData, $tmpFields, $formSlug );
                    $this->notify_admin( $formData, $formName, $notificationEmail, $from, $files );

                    return array(
                        'hasErrors' => false,
                        'successMessage' => $successMessage
                    ); 
                } catch ( NestedValidationException $ex ) {

                    $errorKey = array_keys( $tmpErrors );
                    $errors = $ex->findMessages( $errorKey );
                    $errors = $this->format_error_messages( $errors );

                    return array (
                        'hasErrors' => true,
                        'errors' => $errors,
                        'mainError' => $errorMessage
                    );
                }
            }
        }

        public function process_files( $files, $mimetypes, $filesize ) {
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            $keys = array_keys( $files );

            for ( $i = 0; $i < count($keys); $i++ ) {
                $file = $files[$keys[$i]];              // Current File
                $correctMime = false;                   // Flag for mime
                $correctSize = false;                   // Flag for size
                $uploadedFiles = array();               // Our uploaded files

                if ( $mimetypes ) {
                    foreach ( $mimetypes as $mimetype ) {
                        if ( Validator::mimetype( $mimetype )->validate( $file['tmp_name'] ) ) {
                            $correctMime = true;
                            break;
                        }
                    }
                }
                
                if ( $filesize ) {
                    if ( Validator::size(null, $filesize)->validate( $file['tmp_name'] ) ) {
                        $correctSize = true;
                    }
                }

                if ( $correctMime && $correctSize ) {   // If these be true, upload the file bruv
                    $uploaded = wp_handle_upload( $file, array( 'test_form' => false ) );

                    if ( $uploaded && !isset( $uploaded['error'] ) ) {
                        $uploadedFiles[] = $uploaded['file'];
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            return $uploadedFiles;
        }

        public function clean_up_files( $files ) {
            foreach ( $files as $file ) {
                unlink( $file );
            }
        }

        public function process_data( $data, $fields, $formSlug ) {

            global $wpdb;
            $table_name = $wpdb->prefix . "gp_forms_entires";
            $keys = array_keys( $data );      

            $post_title = $data[$keys[0]];  // Set Post Title to first field
            if (isset($data['subject']) && !empty($data['subject'])) $post_title .= " - " . $data['subject'];

            $new_id = wp_insert_post( array( 'post_type' => 'gp_forms', 'post_title' => $post_title ) );

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

        public function notify_admin( $data, $formName, $notificationEmail, $from, $files ) {
            add_filter( 'wp_mail_content_type', array( $this, "set_to_html" ) );
            
            $headers[] = "From: " . $from['name'] .  " <" . $from['email'] . ">";

            $subject = "Website Form Submission - " . $formName;
            if (isset($data['subject']) && !empty($data['subject'])) $subject .= " - " . $data['subject'];

            $body = "";
            $keys = array_keys( $data );

            for ( $i = 0; $i < count($keys); $i++ ) {
                $body .=  "<strong>" . $keys[$i] . ":</strong> " . nl2br($data[$keys[$i]]) . "<br>";
            }

            wp_mail( $notificationEmail, $subject, $body, $headers, $files );

            remove_filter( 'wp_mail_content_type', array( $this, "set_to_html" ) );

            if ( $files ) {
                $this->clean_up_files( $files );
            }
        }

        public function set_to_html() {
            return "text/html";
        }

        public function format_error_messages($errors) {

            $errorsFormatted = array();

            foreach ($errors as $key => $message) {
                $pos = strpos($message, $key);
                if ($pos !== false) {
                    // Replace first occurrence of field name in string with 'this field'.
                    $message = substr_replace($message, 'this field', $pos, strlen($key));
                }
                $errorsFormatted[$key] = ucfirst($message);
            }

            return $errorsFormatted;
        }

    }