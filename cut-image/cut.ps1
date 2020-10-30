<#
	Cut puzzle image to columns.

	1. Put `raw.jpg` in the folder.
	2. Check height of larget column.
	3. Set $h as checked.
	4. Run ps1 in powershell.
#>

##
# Run-time settings
##
# about params syntax: https://stackoverflow.com/a/2157625/333296
param (
	# height of largest column without top bar
	# (do not have to be very accurate)
	[int]$h = 4000,
	
	# number of columns
	[int]$cols = 19,

	# top bar height (sometimes 15)
	[int]$top = 100
)

##
# Settings
##
$irfan="C:\Program Files (x86)\IrfanView\i_view32.exe"
# Note! This settings need to be very acurate. Especially widths.
$w=490		# width of the widest image
$gap=10		# gap between columns

$sourceImage="$PSScriptRoot\raw.jpg"
$outputPath="$PSScriptRoot\..\img-auto-cut"

#Write-Output "[DEBUG] current dir: $PSScriptRoot"

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
	$parmeters="$base /convert=$outputPath\col_$colNumber.jpg"
	Write-Output "$parmeters"
	& "$irfan" "$parmeters"

	# ~half size
	$resize="/resize_short=200 /aspectratio /resample"
	$parmeters="$base $resize /convert=$outputPath\small\col_$colNumber.jpg"
	& "$irfan" "$parmeters"
}

# single, full size image
$fullW=($cols - 0) * $totalW
$base="$sourceImage /crop=(0,$top,$fullW,$h)"
$parmeters="$base /convert=$outputPath\all.jpg"
Write-Output "$parmeters"
& "$irfan" "$parmeters"
