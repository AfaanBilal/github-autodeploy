GitHub AutoDeploy
==============

Author: **Afaan Bilal ([@AfaanBilal](https://github.com/AfaanBilal))**   
Author URL: **[Google+][1]**

##### Project Page: [afaan.ml/github-autodeploy](https://afaan.ml/github-autodeploy)

## Introduction
**GitHub AutoDeploy** is a PHP script to easily deploy web app repositories on `git push` or 
any other hook. [I][1] wrote this when a relentless search for something which could do this proved 
fruitless. I needed to automatically deploy a web app to a shared server on which I had no SSH 
access, as is required to achieve this. The other way was to setup a git repo on the server and 
execute a `git pull` on each `git push` for the git server, but since it was a shared server, 
there was no `exec` or `shell_exec` for me either.  
So, I had to write it myself. 

## Features
- No SSH access required.
- No shell access required.
- Fully compatible with shared servers.
- Currently, only public repos are supported.

## Setup
1. Download [github-autodeploy.php](github-autodeploy.php).
2. Update the GitHub repo link by replacing `[USERNAME]` and `[REPO]`.
3. Set the deploy directory by replacing `[DEPLOY_DIR]` relative to the script.
4. Set your timezone.
5. Upload the script to your server.
6. On GitHub, [add a WebHook][2] to your repo for a `push` event (or any other) and
set it to the uploaded script.
7. You're done!

Now, whenever you `git push` to your github repo, it will be automatically deployed
to your web server!

## Adding a WebHook
*You must have administrative access to the repo for adding WebHooks*   
1. Go to your GitHub repo &raquo; `Settings` &raquo; `Webhooks & services`.
2. Click `Add webhook`.
3. Enter the URL of the github-autodeploy.php script in the `Payload URL` field.
4. Leave everything else as is, click `Add webhook`.
5. You're done!

## Contributing
All contributions are welcome. Please create an issue first for any feature request
or bug. Then fork the repository, create a branch and make any changes to fix the bug 
or add the feature and create a pull request. That's it!
Thanks!

## License
**GitHub AutoDeploy** is released under the MIT License.
Check out the full license [here](LICENSE).

[1]: https://google.com/+AfaanBilal "Afaan Bilal"
[2]: #adding-a-webhook "Adding a WebHook"
