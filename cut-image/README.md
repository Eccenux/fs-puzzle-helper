# Usage

## Auto-cut image

1. Put `raw.jpg` in the folder.
2. Just run `php ./_cut.php` (PHP 5.5 or higher).

The script should calculate everything (column count, widhts etc). It will create cells, columns, and `all.jpg`.

## Alternative cut (ps)

PowerShell and IrfanView is required here.

Fallback alternative to PHP. Will NOT create cells though. Just columns and `all.jpg`.

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