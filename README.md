# distributor-clone-fix-addon

Distributor Clone Fix add-on is for extending the [Distributor Plugin](https://distributorplugin.com/) functionality to fix subscriptions if spoke was cloned

## Requirements

* PHP 5.6+
* [WordPress](http://wordpress.org) 4.7+
* Distributor plug-in


## Plugin installation and usage

- After you've cloned spoke, you must create new external connection for that.
- Install `Distributor Clone Fix` add-on on both sides  ( `Hub` & `Spoke` ).
- For now this plugin working with post metas. So you have to add following meta to all posts which need to be fixed 
```
meta_key => dt_repair_post
meta_value => id of new connection created for clone
```
- Plugin uses wp cron for work, so all you need to do is set meta as mentioned in previous step
- Please note that this add-on was tested for single-site installation with external connections.
