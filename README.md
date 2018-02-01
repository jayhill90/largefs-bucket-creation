### WP Engine LargeFS Configuration 
This WordPress plugin is designed for use with WP Engine. Create an IAM user with S3 Admin access, install the plugin and input the keys and the name of your bucket into the plugin to create an S3 bucket, enable Static Hosting, and applies WP Engine's Bucket Policy to utilize their unlimited storage option with your S3 bucket.

### Configuration Steps
You will need an Amazon Web Services account, which allows for some free services for the first year. There is no charge for IAM users, however there are charges associated with storage using the S3 service. For more information regarding the Free Tier of AWS see https://aws.amazon.com/free/ After the first year S3 data storage is pretty inexpensive. If your site gets a lot of bandwidth usage while using S3 then additional charges may apply.

To get started sign in to the AWS console at https://console.aws.amazon.com and navigate to the IAM service. https://console.aws.amazon.com/iam/home/
On the left sidebar navigate to the Users page and click Add User. 
Enter any User name that you wish. These do not need to be globally unique as they are applied to your account.
Check the box for Programmatic access to generate an Access Key ID and Secret Access key then click Next: Permissions.
On the next page click "Attach existing policies directly" and at the bottom section in the Search bar type in 's3'.
Check the box for AmazonS3FullAccess then click Next: Review.
On the next page, if everything looks good clikc Create User.
You can download the CSV file that contains your Access and Secret keys needed to configure your bucket or just copy and paste them from the screen.

Install the plugin and activate it, then navigate to "Settings -> LargeFS Config" in the WordPress Dashboard.
Choose a bucket name. This must be DNS compliant and globally unique.

The rules for DNS-compliant bucket names are as follows:
Bucket names must be at least 3 and no more than 63 characters long.
Bucket names must be a series of one or more labels. Adjacent labels are separated by a single period (.). Bucket names can contain lowercase letters, numbers, and hyphens. Each label must start and end with a lowercase letter or a number.
Bucket names must not be formatted as an IP address (for example, 192.168.5.4).

Input your Access Key and Secret Key into the appropriate fields then click "Make Bucket".

Contact WP Engine support with to provide them with your bucket name and for the final steps in setting up LargeFS. 


