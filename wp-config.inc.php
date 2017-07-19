<?php

abstract class WPConfigDefaults {

  const Plugins = [
    'amazon-s3-and-cloudfront/wordpress-s3.php',
    'amazon-web-services/amazon-web-services.php',
    'asse-channelizer/asse-channelizer.php',
    'asse-exporter/asse-exporter.php',
    'asse-feed/asse-feed.php',
    'asse-framework/asse-framework.php',
    'asse-helpers/asse-helpers.php',
    'asse-http/asse-http.php',
    'asse-importer/asse-importer.php',
    'asse-social/asse-social.php',
    'disable-wordpress-updates/disable-updates.php',
    'dynamic-featured-image/dynamic-featured-image.php',
    'featured-galleries/featured-galleries.php',
    'mashshare-floating-sidebar/mashshare-floating-sidebar.php',
    'mashshare-networks/mashshare-networks.php',
    'mashshare-select-and-share/mashshare-select-and-share.php',
    'mashshare-sharebar/mashshare-sharebar.php',
    'mashsharer/mashshare.php',
    'no-category-base-wpml/no-category-base-wpml.php',
    'shortcoder/shortcoder.php',
    'wp-category-permalink/wp-category-permalink.php',
    'wp-meta-tags/meta-tags.php'
  ];

  const Links = [
    'development' =>  [],
    'testing'     =>  [
      'Stylebook'  => 'https://be-stylebook.test.tortuga.cloud/wp/wp-admin/',
      'Techbook'   => 'https://be-techbook.test.tortuga.cloud/wp/wp-admin/',
      'Travelbook' => 'https://be-travelbook.test.tortuga.cloud/wp/wp-admin/',
      'Fitbook'    => 'https://be-fitbook.test.tortuga.cloud/wp/wp-admin/',
    ],
    'production'  => [
      'Stylebook'  => 'https://backend.stylebook.de/wp/wp-admin/',
      'Techbook'   => 'https://backend.techbook.de/wp/wp-admin/',
      'Travelbook' => 'https://backend.travelbook.de/wp/wp-admin/',
      'Fitbook'    => 'https://backend.fitbook.de/wp/wp-admin/',
    ]
  ];

}
