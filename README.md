More info on cuting images in cut-image\README.md.

## Quick guide.

1. Put passcode's `raw.jpg` in the `cut-image` folder.
2. Run (PHP 5.5+): `php ./_cut.php`.
3. Put column images in sub-folder of:
	```
	\\\Secure FTP\nux@nazwa.pl\f.enux.pl\ingress\fs-puzzle\
	```
4. Copy [A Puzzle A Day #TPL](https://docs.google.com/spreadsheets/d/1Js5tlD7yPFcJAPxgboq4IcakCWzjpl3JQyeEFeXnObc/edit#gid=1760313402) to a new sheet.
5. Insert url to small images sub-folder in "Columns Image Base:" field.

Note that the base URL should end with a slash (`\`).

You can check if the cut was accurate by comparing raw with images in:
http://localhost/ingress/puzzle-day/puzzle.php
