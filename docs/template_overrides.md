# Overriding default MagentaServer templates

MagentaServer makes it reasonably easy to override any of the templates
(including the "partial" templates - the ones in `templates/includes/`)
that are used by the application.

This is controlled by the `SITE_TEMPLATEDIR` environment variable.

## Step-by-step

0. Create a directory somewhere your web server has permission to read -
    the developers use `custom/templates/` within the cloned repository
	(as the `custom/` directory is already in Git's ignore file)
0. In your MagentaServer environment variables, set the `SITE_TEMPLATEDIR`
    variable to the path to this new directory (note that if you use a
	relative path here, it is relative from the `public/` directory, so
	use `../custom/templates` or some such)
0. Copy the template you wish to override into from the `templates/`
    directory of the MagentaServer installation into this directory, taking
    care to keep the same directory structure.
0. Modify the template as you see fit.

Changes to templates may not show up immediately:

- In `production` mode, you will have to remove the contents of the `cache/`
    directory of the MagentaServer installation every time you make a change
    to a template;
- In `development` mode, any changes to templates will be reflected on the
    MagentaServer installation immediately.

## Example

```shell
% cd /var/www/magentaserver
% mkdir -p custom/templates
% cp templates/index.html custom/templates/index.html
% $EDITOR custom/templates/index.html # edit your template
% echo 'SITE_TEMPLATEDIR="../custom/templates"' >> .env
```
