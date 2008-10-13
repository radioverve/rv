This directory is used for caching remote images referenced by Wordpress posts.  Since Flash 9 has issues with loading
remote images directly, we simply access the images via proxy.  PHP loads the images and saves them here, and Featurific
(Flash 9) loads them locally (via the server which hosts your Wordpress installation) instead of remotely.