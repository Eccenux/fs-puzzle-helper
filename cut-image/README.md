# Usage

## Cut images to columns

1. Put `raw.jpg` in the folder.
2. Check height of largest column (without top bar). It should not be larger then 4000 px or you will only see part of it.
3. Run `cut.ps1` in powershell.

You can also use parameters. If the height is larger then 4000 px, then you will have to provide it:
```
.\cut-image\cut.ps1 -h 4100
```
You can also provide a smaller height to see a bit bigger images:
```
.\cut-image\cut.ps1 -h 3900
```

You can also provide number of columns in a parameter:
```
.\cut-image\cut.ps1 -h 3900 -cols 3
```


Also note that you can run the script from any path. It always uses absolute paths anyway.

## Prepare spreadsheet

1. Put images in sub-folder of:
	```
	\\\Secure FTP\nux@nazwa.pl\f.enux.pl\ingress\fs-puzzle\
	```

2. Copy [A Puzzle A Day #TPL](https://docs.google.com/spreadsheets/d/1Js5tlD7yPFcJAPxgboq4IcakCWzjpl3JQyeEFeXnObc/edit#gid=1760313402) to new sheet.

3. Insert url to that sub-folder in "Columns Image Base:".

# Cutting notes

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