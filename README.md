FS Puzzle Helper is a single-user solver for FS Puzzles. It is also a cutter for the puzzle images.

## How it looks like

<table>
<tr>
  <th valign="top">Puzzle form</th>
  <td> <img alt="screen" src="https://user-images.githubusercontent.com/1045235/109427708-ed4a1080-79f3-11eb-9e2d-1e61f89e8df6.png"/></td>
  <td>In the "FS puzzle" field you copy stuff you would put in normal sheet. So title, nickname, url (separated by tabs).<br><br>

To quickly copy data straight from IITC use [iitc-plugin-fs-puzzle-helper](https://github.com/Eccenux/iitc-plugin-fs-puzzle-helper).

Same thing for Ingress Mission editor: [ingress-mission-fs-puzzle-helper](https://github.com/Eccenux/ingress-mission-fs-puzzle-helper).
</td>
</tr>
<tr>
  <th valign="top">Character map</th>
  <td> <img alt="screen" src="https://user-images.githubusercontent.com/1045235/109428082-a6f5b100-79f5-11eb-83e6-59b873e8ecbf.png"/></td>
  <td>Green dot-marker indicates a starting point (first portal you found in the column). Red, dotted line is shown when you have a missing portal. Purple, solid line is when you have two adjected portals.<br><br>
    
Fun fact: this is an actual map. You can zoom in and out, but it's a side effect, not a feature really 🙂.
   </td>
</tr>
</table>

Whole page looks like in the below image. Columns 1 through 11 are collapsed. You can collapse columns by clicking on the column number (header). Green portals are those that are done. They are marked as done automatically (when you fill the FS puzzle field).
![screen](https://user-images.githubusercontent.com/1045235/109427993-33ec3a80-79f5-11eb-9eb7-f199588d91b8.png)

## Requirements

You will need a server with PHP. You can use Apache HTTPD distributed with you Linux or use some installer like the one from Apache Lounge (and PHP from php.net). Or you can use a bundle like XAMPP.

You will need PHP 5.5+. Any version of Apache server should be fine (must only be able to run your PHP).

## Multi-user usage

You can still use automatic cutter for multi-user events (like FS). Just cut the image and use my [Puzzle - #TPL](https://docs.google.com/spreadsheets/d/1Js5tlD7yPFcJAPxgboq4IcakCWzjpl3JQyeEFeXnObc/edit#gid=1760313402).

Note that for images to work you will need to put column images on some *external* server. External meaning a server that can be accessed from the Internet. This is because Google Sheet will need to be able to access images.

Remember to modify "Columns Image Base" field in the sheet. It must point to your server, not mine 🙂.

## Setup

If you have a server with PHP you can probably use it. Do note that you still need to change some settings.

1. Install the server (and PHP).
2. Download fs-puzzle-helper package from Github (you can download a zip).
3. On your server, in `htdocs` folder, unzip the downloaded package. I re
4. Edit `cut-image\__settings.bat` and enter your PHP path (if on Windows).
5. Edit `php.ini` (to open php.ini with Xampp panel you can use Config button for Apache).
6. In `php.ini` find `;extension=gd`. Remove `;` before `extension=gd`. Save changes.
7. Start your Apache server (if you haven't already).

Now to make sure cutting works:

1. Create `cut-image\input\` folder.
2. Save passcode's puzzle image in the `cut-image\input\` folder. 
3. Run: `_cut.bat` (or if you are on Linux: `php ./_cut.php`).

If that works you should see a progress for cutting columns and some info.
If you see errors then either your PHP is too old or you didn't enable gd extension. 

## Using the helper

1. Save passcode's puzzle image in the `cut-image\input\` folder.
2. Run: `_cut.bat`.
3. Open your puzzle site: http://localhost/fs-puzzle-helper/puzzle.php

I recommend using my Google Sheet for saving solutions: [Micro-Puzzle #TPL](https://docs.google.com/spreadsheets/d/111gE09r7AqnhXfsuNouOssruunuRt3rTXw7Nt42zpVU/edit#gid=1662443983) (you will need to copy it). But it is not required -- you can just use the puzzle site.

You can check if the cut was accurate by comparing raw with images in:
http://localhost/ingress/puzzle-day/puzzle.php


## Tips and tricks

1. If there is master-code image below input image then remove it before cutting.
2. If some rows were not cut use [MosaicSvgEditor](https://github.com/Eccenux/MosaicSvgEditor) to fix that.

You can find some extra notes in [Cutter readme](cut-image/README.md).
