=== Roost Web Push for Safari ===
Contributors: noticesoftware, danstever
Tags: safari, push, push notifications, web push notifications, Mavericks, mobile, web push, roost, roost.me, roost_me, Post, plugin, admin, posts, page, links, widget, ajax, social, wordpress, dashboard, news, notifications, services
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: 2.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Drive traffic to your website with Safari Mavericks push notifications and Roost.

== Description ==

[Roost Web Push for Safari](http://roost.me/) provides direct engagement with your readers by sending push notifications to their web browser or mobile device.

Push Plugin Features:

* **Safari Web Push** - Take advantage of web push (Desktop Push Notifications) on OS X Mavericks.
* **Detailed Analytics** - Real-Time stats straight within your WordPress dashboard.
* **Auto Notifications** - We take the work out of it. Automatically send notifications to your readers when creating a new post.
* **Scheduled Post Notifications** - Not posting now? Don't worry. Your alerts will go out when your content posts.
* **Optional mobile push solution** - Fully supported solution to send push not only to desktop, but mobile devices.

**Free** Roost account included. No setup fees, no surprises, and no limitations on your site visitors.

[vimeo http://vimeo.com/88533265]

== Installation ==

= From your WordPress dashboard =
1. Visit 'Plugins > Add New'
2. Search for 'Roost'
3. Activate Roost from your Plugins page
4. (a) Create your Free Roost for Bloggers account - (Click on "sign up for free") OR (b) Log In an Existing Account - Click "Log In" and enter your username and password

= From WordPress.org =
1. Download Roost For Bloggers Wordpress Plugin.
2. Upload the 'Roost' directory to your '/wp-content/plugins/' directory.
3. Activate Roost For Bloggers from your Plugins page.
4. (a) Create your Free Roost for Bloggers account - (Click on "sign up for free") OR (b) Log In an Existing Account - Click "Log In" and enter your username and password

= Using Features =

**Safari Web Push**
To use Safari Web Push, simply activate the *Roost for Bloggers* plugin. When your site is viewed in a push-enabled browser, the browser will prompt for permission. No additional setup is needed.

**Auto Push**
Auto Push is what makes this plugin stellar. When you create a new post, your post's title, link, and featured image (if one is attached) will be sent to all of your subscribed readers. That's it. Really. You don't have to do anything else. (It works just like magic.) When enabled, an auto push override check box is also placed on your post's admin page. If for some reason you do not want a notification to go out for that post, just check the box when publishing.

**Send Manual Alert**
You can send a manual alert to all subscribed users by entering your message text in the "Notification Text" box and a link in the "Notification Link" box.

**Activate all Roost Features**
When checked, your site will automatically receive the newest (and coolest) features from Roost as we roll them out. (Like Chrome Web Push. *cough*)

**Roost Analytics & Roost JS**
Detailed metrics are provided in the dashboard. These metrics include subscribers, notifications sent, average time on site, and total page views.

**Mobile Push Support**
To enable mobile push support, check the box. The Roost Header bar, will be inserted onto your page.

== Frequently Asked Questions ==

= What does Roost For Bloggers cost? =
**It's free!** No setup fees, no surprises, no limitations on your site visitors or subscribers.

= Free? Really? What's the catch? =
No catch! We believe in providing a great service and making it accessible to everyone. So... It's free. (Up to 1,000,000 messages. If you hit that... We'll need to talk.)

= Is it really this easy to use Safari Web Push on my site? =
Yup! Cool right?

= Do I need and Apple Developer Account or Google Play Developer account? =
Nope. We've got you covered.

= Do you support Google Chrome or Firefox for desktop push? =
Almost. We will be releasing support for Google Chrome very soon. Firefox will follow. To take advantage of them as we include support, make sure to have *Activate all Roost Features* checked.

= If I'm using Mobile Push Support, Why do subscribers have to download the Roost mobile app? =
In order for a device to receive a true push notification, a native mobile app must be installed. That's where Roost comes in. We are there to provide that *native bridge.* We do not pull readers into our app, but push them back to your website. Notifications also stay in the Roost app, which makes them able to be viewed at any time.

= Do my readers / subscribers have to create an account with Roost? =
Nope. We have a patent-pending *zero-configuration* installation process for the Roost app. When a person hits your subscription link, the are sent to the Roost page on their device's app store, and prompted for install. Once installed and opened, a person is sent directly back to the page they were viewing on your site.


== Screenshots ==

1. Log in to your Roost Account.
2. Roost plugin from the Wordpress dashboard showing analytics, manual push, and settings.

== Changelog ==

= 2.0.4 =
* Changed hard limit to soft character limit on manual notifications
* Restricted notifications to a "post" post type only
* Added settings link on plugin page
* Roost stats now cached to improve admin page performance
* Fixed bug that caused resending notifications when using "Quick Edit" to modify posts
* Removed shortcodes
* Code cleanup

= 2.0.3 =
* Scheduled Notifications can now Trigger Alerts
* Fixed Stats Error on First Login
* Character Count on Manual Notifications

= 2.0.2 =
* Corrected Total Time On Site calculation
* Fixed additional bug with character encoding
* Adjusted styling on admin pages

= 2.0.1 =
* Fixed bug that prevented notifications from sending

= 2.0 =
* Automatically activate Safari Web Push with plugin
* Simplified interface
* Pulling All-Time stats for your Roost account into Wordpress Dashboard
* Added a Roost Push override on post pages to prevent notification
* Simplified install process - Plugin no longer creates it's own table
* Added support for featured images from posts (For new Roost app - Soon in Apple App Store and Google Play)
* Settings now all controlled from server
* Added Roost Bar shortcode
* Fixed character encoding issue on auto push notifications

= 1.1 =
* Added support for Safari Web Push (Desktop Push Notifications) on OS X Mavericks
* Prefixed functions to avoid conflicts
* Updated settings to be removed on uninstall instead of deactivation
* Included Roost JS on every page via wp_footer() - (Needed for Roost analytics such as page views and time on site)

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 2.0.4 =
* Limited Auto Push to 'post' types only. Fixes situation with other plugin's custom post types triggering notifications.

= 2.0.3 =
* Now with support for Scheduled Notifications! Bam! Oh, and some bugfixes and other cool stuff.

= 2.0.2 =
* Corrected Total Time On Site calculation
* Fixed additional bug with character encoding
* Adjusted styling on admin pages

= 2.0.1 =
* Fixed bug that prevented notifications from sending

= 2.0 =
* Bug fixes and analytics. Simplified interface and design update to match Wordpress 3.8 design.
