=== Kitz Pro Builder ===
Tags: Divi Theme, Divi Builder Plugin
Requires at least: 6.12.25
Tested up to: 6.2.2
Requires PHP: 5.2.4
Stable tag: 6.12.24
License: GPL v2
== Description ==

#### Kitz Builder helps you build Divi sites ten times faster with cloud storage and drop-in features.
       
== Installation ==
* **Build WordPress sites ten times faster with Kitz Pro Builder.** 
* **Step 1**: Navigate to any page that has Divi enabled, and click “Kitz” on the topbar..
* **Step 2**: You will now enter the builder.
* **Step 3**: On the sidebar, choose “Sections” or “Layouts”. You can also add additional Divi-enabled pages inside the builder.
* **Step 4**: Save the page upon exit, and modify the page via the standard Divi builder.

* **Step 5**: In free version, You can connect your dropbox & save your layouts and sections in dropbox which you will get in the builder.


== Support == 
* If any problem occurs, please contact us at support@enspyredigital.com

## How to use

1. First Activate Plugin.
2. Then Click on " Kitz Pro " menu. Then connect your dropbox with your account.


== Changelog ==
= 6.12.25 (6th May, 2023) =
* Bug fixes.
* Initital release.
* Meet new functionality Drop Box
* Users can save layouts and sections to their dropbox and retrieve them from dropbox.
* User can save section on their Kitz Pro Builder, Just need to click on save icon
* Checked compatibility with wordpress 6.2.2


== Upgrade Notice ==
= Upgrade your old version to 6.12.25


== Other Notes ==

= Minimum requirements for Kitz Pro =
*   WordPress 3.3+
*   PHP 5.x
*   MySQL 5.x

If any problem occurs, please contact us at https://app.divikitz.com/, Here you need to login and you can chat with us directly.



== Divi Kitz Website ==

* Here is the main website where I am using API to connect with this website. Here is the URL: https://app.divikitz.com/login. The website admin needs to login here with a subscription and create a token.

* Website admin can use their email and password to get "Sections" and "Layout" with API's.

* define('kitz_apiurl', "https://app.divikitz.com/"); Here is the main website I defined here main website URL "kitz_apiurl".

* I used this function to Connect/Disconnect https://app.divikitz.com/ website by API: wp_remote_get(kitz_apiurl . 'wp-json/kitzdropbox/connect');


== Drop Box ==
* wp_remote_post('https://content.dropboxapi.com/2/files/upload', $args) I used this function to upload JSON files. Admin can upload their DIVI JSON file with this function. This function will save their .json file into Dropbox by this API.

* wp_remote_post('https://api.dropboxapi.com/2/files/create_folder_v2', $args_section_folder); I used this function for creating a folder in dropbox. Admin can upload their JSON file in the created folder.

* wp_remote_post('https://api.dropboxapi.com/2/files/list_folder', $args_listfolder); I used this function to retrieve all folders from the Dropbox.

* wp_remote_post('https://api.dropbox.com/oauth2/token', $args); I used this function to create a token by this API.

* wp_remote_post('https://content.dropboxapi.com/2/files/download', $dropbox_download_args); I used this function to download All . Json file in the wp plugin directory.