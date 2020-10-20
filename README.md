More info on cuting images in cut-image\README.md.

## Quick guide.

1. Put `raw.jpg` in the folder.
2. Check height of largest column (without top bar).
3. Run in powershell: `.\cut-image\cut.ps1 -h 3900` (if you skip height parameter, then it will default to 4000 which should be fine for most puzzles).
4. Put images and html in a new sub-folder of: `\\\Secure FTP\nux@nazwa.pl\f.enux.pl\ingress\fs-puzzle\`
5. Insert url to small images sub-folder in "Columns Image Base:" field. The URL should end with a slash (`\`).
