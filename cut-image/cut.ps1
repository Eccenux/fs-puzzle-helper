<#
	Cut puzzle image to columns.

	1. Put `raw.jpg` in the folder.
	2. Check height of larget column.
	3. Set $h as checked.
	4. Run ps1 in powershell.
#>

##
# Settings
##
$irfan="C:\Program Files (x86)\IrfanView\i_view32.exe"
$h=4000		# height of largest column (for smaller images)
$top=100	# top bar height
$w=490		# width of the widest image
$gap=10		# gap between columns
$cols=19	# number of columns
$sourceImage="raw.jpg"

##
# Internal settings
##
# total width of a column
$totalW = $w + $gap

# 1st col (test)
# & "$irfan" "raw.jpg /crop=(0,$top,$w,$h) /convert=.\out\col_1.jpg"

##
# cut to columns
#mkdir -Force .\out\small
for($colNumber = 1; $colNumber -lt $cols + 1; $colNumber++)
{
	$startX=($colNumber - 1) * $totalW
	$base="$sourceImage /crop=($startX,$top,$w,$h)"
	
	# full size
	$parmeters="$base /convert=.\out\col_$colNumber.jpg"
	Write-Output "$parmeters"
	& "$irfan" "$parmeters"

	# ~half size
	$resize="/resize_short=200 /aspectratio /resample"
	$parmeters="$base $resize /convert=.\out\small\col_$colNumber.jpg"
	& "$irfan" "$parmeters"
}

# single, full size image
$fullW=($cols - 0) * $totalW
$base="$sourceImage /crop=(0,$top,$fullW,$h)"
$parmeters="$base /convert=.\out\all.jpg"
Write-Output "$parmeters"
& "$irfan" "$parmeters"
