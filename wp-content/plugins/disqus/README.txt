Disqus WordPress Plugin
Version 1.03 (October 30, 2007)
http://disqus.com/

* Notes

 - KNOWN ISSUE: For WordPress 2.0.x, the Disqus management page does not show
                up for the comments link.
                
                To get manage the settings of this plugin,, go to the Plugins
                page and find the link "Click Here to Manage Settings" for the
                Disqus Comment System.

* Installation

 - Unpack archive to this archive to the 'wp-content/plugins/' directory inside
   of WordPress

    - Maintain the directory structure of the archive (all extracted files
      should exist in 'wp-content/plugins/disqus/'

 - From your blog administration, click on Comments to change settings (see note
   above for WordPress 2.0.x users)

    - Enter the subdomain of the forum you created from Disqus for the forum URL.
   
    - Choose an option for which posts to replace.

* Template tags

 - Display recent comments (Optional)

    - If you wish to display recent comments in your template, you may use the
      provided disqus_recent_comments() template_tag.
      
      disqus_recent_comments($num_comments = 5, $display_message = false)
      
      Where num_comments is the number of comments to display, and
      display_message allows you to choose whether or not to display an excerpt
      of the comment.

* Support

 - Visit our forum at http://disqus.disqus.com/ for help.
