# appsero-license-activator-cli
Activate licence for plugin managed by Appsero.

## WP Cli Command
```bash
wp appsero activate <lisence_key> --plugin_file=<plugin_file> --plugin_hash=<plugin_hash> --option_key=<optional_option_key>
```

## Example
```bash
# if you want to activate the license for the plugin "dokan-pro" with the license key "1234567890" and the plugin hash "plugin-hash" and the optional option key "optional_option_key"
wp appsero activate 1234567890 --plugin_file="dokan-pro/dokan-pro.php" --plugin_hash="plugin-hash" --option_key="optional_option_key"
```
### Plugin build and release
```bash
composer install
npm install
npm run release
```