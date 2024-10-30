=== Community Ads ===
Contributors: oucel
Donate link: http://omerucel.com/gunluk/2009/07/24/community-ads
Tags: community,ads,adsense
Requires at least: 2.8.0
Tested up to: 2.8
Stable tag: trunk

Yazarların gönderdiği içeriklerde kendi reklamlarının görüntülenmesini sağlamaya yarayan bir eklenti.

== Description ==

Yazarların gönderdiği içeriklerde kendi reklamlarının görüntülenmesini sağlamaya yarayan bir eklenti.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `ads.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php if (function_exists('community_ads_show_ad')) community_ads_show_ad(get_the_author_meta('ID')); ?>` in your templates
