<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}
else {
delete_option( 's3_access_key' );
delete_option( 's3_secret_key' );
delete_option( 's3_bucket_name' );
}
 ?>
