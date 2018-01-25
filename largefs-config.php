<?php
/*
Plugin Name: LargeFS Config
Plugin URI:   https://github.com/jayhill90
Description:  A plugin designed for creating an S3 bucket for use with WP Engine's LargeFS system
Version:      0.1.0
Author:       Jay Hill
Author URI:   https://github.com/jayhill90/
*/

require 'vendor/autoload.php';

// Hook Into Wordpress and call largefs_menu function
add_action( 'admin_menu', 'largefs_menu' );
add_action( 'admin_init', 'register_largefs_settings' );
// Add's the option page into Settings section of wp-admin
function largefs_menu() {
  add_options_page( 'LargeFS Options', 'LargeFS Config', 'manage_options', 'largefs-config', 'largefs_options' );
}
function register_largefs_settings() {
  register_setting( 'largefs-option-group', 's3_bucket_name' );
  register_setting( 'largefs-option-group', 's3_access_key' );
  register_setting( 'largefs-option-group', 's3_secret_key' );
}
function makeBucket() {
  // Grab the Access and Secret keys from the database and bucket name.
  $s3_access_key = get_option( 's3_access_key' );
  $s3_secret_key = get_option( 's3_secret_key' );
  $bucket_name = get_option( 's3_bucket_name' );

  // Grab the bucket policy and replace bucket name.
  $policy = file_get_contents( plugin_dir_path(__DIR__) . "largefs-config/inc/bucket_policy.json" );
  $bucket_policy = str_replace( '{bucketName}', $bucket_name, $policy );

  // If we've got the keys time to do the needful
  if ( get_option('s3_access_key') && get_option('s3_secret_key' )) {
    // Args to pass into S3 for region and credentials. This assumes the admin
    // has generated credentials using an IAM user.
    $args = [
      'version' => 'latest',
      'region' => 'us-east-1',
      'credentials' => [
        'key' => $s3_access_key, 'secret' => $s3_secret_key
      ],
  ];
    $s3 = new Aws\S3\S3Client($args); //Make our s3 client connection
    // Create the Bucket & enable Static Hosting
    if( !$s3->doesBucketExist( $bucket_name ) ) {
     $s3->createBucket([
        'ACL' => 'public-read',
        'Bucket' => $bucket_name
       ]);
     echo "Made bucket " . $bucket_name . "\n";
     echo "Please contact Support for the validation file to upload to the root of your bucket\n";
     echo "You can now uninstall this plugin.\n";
   }
   $s3->putBucketWebsite([
        'Bucket' => $bucket_name,
        'ContentMD5' => '',
        'WebsiteConfiguration' => [
          'ErrorDocument' => [ 'Key' => 'error.html' ],
          'IndexDocument' => [ 'Suffix' => 'index.html' ]
        ]
      ]);
    // Setup Bucket Policy per https://wpengine.com/support/configuring-largefs-store-transfer-unlimited-data/
    $s3 ->putBucketPolicy([
      'Bucket' => $bucket_name,
      'ConfirmRemoveSelfBucketAccess' => false,
      'Policy' => $bucket_policy,
    ]);
  }
}

// Renders out Options Page.
function largefs_options() {
  add_action( 'largefs_config' , 'makeBucket' );
  if (!current_user_can( 'manage_options' )) {
    wp_die(__( "You don't have access." ));
  }?>
  <div class="wrap">
  <h1>LargeFS Configuration</h1>
  <p>Please enter your S3 Access Key and Secret Key and ensure you are not using your root user for AWS' keys.<br>
    If you need help setting up an IAM User to utilize for this see the AWS Documentation <a href="https://docs.aws.amazon.com/IAM/latest/UserGuide/id_users_create.html" target="_blank">here</a><br />
    Once you click save, an S3 bucket will be created, static website hosting enabled, and your bucket policy configured.</p>
  <form method="post" action="options.php">
  <?php
    settings_fields( 'largefs-option-group' );
    do_settings_sections( 'largefs-option-group' );
  ?>
  Bucket Name:
  <input type="text" name="s3_bucket_name" value="<?php echo esc_attr( get_option( 's3_bucket_name') ); ?>" /><br/>
  Region:
  <select name="s3_region" value="<?php echo esc_attr( get_option( 's3_region' ) ); ?>" />
    <option value="us-east-1">us-east-1</option>
    <option value="us-west-1">us-west-1</option>
  </select><br />
  Access Key:
  <input type="text" name="s3_access_key" value="<?php echo esc_attr( get_option( 's3_access_key') ); ?>" /><br/>
  Secret Key:
  <input type="password" name="s3_secret_key" value="<?php echo esc_attr( get_option( 's3_secret_key' ) ); ?>" /><br />
  <?php submit_button(); ?>
  </form>
  <?php do_action( 'largefs_config' ); ?>
</div><?php
}
?>
