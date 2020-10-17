# Usage

## Cut images to columns

1. Put `raw.jpg` in the folder.
2. Check height of largest column.
3. Set `$h` in `cut.ps1` script.
4. Run ps1 in powershell.

## Prepare spreadsheet

1. Put images in sub-folder of:
	```
	\\\Secure FTP\nux@nazwa.pl\f.enux.pl\ingress\fs-puzzle\
	```

2. Copy [A Puzzle A Day #TPL](https://docs.google.com/spreadsheets/d/1Js5tlD7yPFcJAPxgboq4IcakCWzjpl3JQyeEFeXnObc/edit#gid=1760313402) to new sheet.

3. Insert url to that sub-folder in "Columns Image Base:".

# Notes

Notes on typical image size and column dimensions.

## top bar
* height: 100px
* total: 10k x 5k

## 1st col
0px - 490px

## gap
10 px

## 2nd col
500px + 490px