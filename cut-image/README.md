# Cutting notes

Cutting the puzzle used to be much simpler when all columns were all of the same width. Later puzzle come with all sorts for width. Also internal alignment of images changed over time.

Note that you can use this cutter for puzzles at least since June 2020. At a very least you will be able to cut to columns.

## Auto-cut image

Main script is `_cut.php`. It takes latest JPEG image from  `./input` folder and cuts it.

The script should calculate everything (column count, widhts etc). It will create cells, columns, and `all.jpg`.

Problems:
1. If the input image has too low quality some cells will not be cut correctly.
2. If there is master-code image below input image then remove it before cutting.
3. If there is no gap between images then only cutting to columns will work.

The cutter generates some JSON files that are compatible with this SVG editor:
https://github.com/Eccenux/MosaicSvgEditor

SVG editor can cut any puzzle image. Even worst quality JPEG.

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

## Dimensions notes

Notes on typical image size and column dimensions.

### top bar
* height: 100px
* total: 10k x 5k

### 1st col
0px - 490px

### gap
10 px

### 2nd col
500px + 490px (when width is constant)
