# Generate a Plugin Queueworker Drupal 8
Using this module you can generate the scaffolding for a Queueworker plugin.
## How to use

You can run the command using `drupal generate:plugin:queue` or using the alias command `drupal gpqueue`
Also you can add parameters in the command, here is an example:

    drupal generate:plugin:queue  \
        --module="modulename"  \
        --class="PluginClassName"  \
        --plugin-id="plugin_class_name"  \
        --cron-time="30"  \
        --label="Example QueueWorker"
