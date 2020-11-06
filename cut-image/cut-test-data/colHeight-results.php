<?php
/**
	colHeight testing (speed)
	
	dt = time in [ms]
*/

/*
Heights with 1 step

[column=1] colh = 1896 (x=10); dt=36
[column=2] colh = 1448 (x=310); dt=50
[column=3] colh = 1519 (x=610); dt=48
[column=4] colh = 1421 (x=910); dt=54
[column=5] colh = 2120 (x=1210); dt=28
[column=6] colh = 1592 (x=1510); dt=46
[column=7] colh = 2293 (x=1810); dt=23
[column=8] colh = 1749 (x=2110); dt=42
[column=9] colh = 1891 (x=2410); dt=36
[column=10] colh = 1600 (x=2710); dt=46
[column=11] colh = 1831 (x=3010); dt=39
[column=12] colh = 1752 (x=3310); dt=41
[column=13] colh = 2224 (x=3610); dt=25
[column=14] colh = 2128 (x=3910); dt=29
[column=15] colh = 1976 (x=4210); dt=34
[column=16] colh = 1519 (x=4510); dt=49
[column=17] colh = 1752 (x=4810); dt=41
[column=18] colh = 3000 (x=5110); dt=99
[column=19] colh = 3000 (x=5410); dt=96
*/

/*
Heights with adjustable step 200 > 100 > 50...

[column=1] colh = 1898 (x=10); dt=1
[column=2] colh = 1449 (x=310); dt=1
[column=3] colh = 1521 (x=610); dt=1
[column=4] colh = 1423 (x=910); dt=1
[column=5] colh = 2119 (x=1210); dt=1
[column=6] colh = 1591 (x=1510); dt=1
[column=7] colh = 2292 (x=1810); dt=1
[column=8] colh = 1751 (x=2110); dt=1
[column=9] colh = 1892 (x=2410); dt=1
[column=10] colh = 1601 (x=2710); dt=1
[column=11] colh = 1832 (x=3010); dt=1
[column=12] colh = 1753 (x=3310); dt=1
[column=13] colh = 2226 (x=3610); dt=1
[column=14] colh = 2129 (x=3910); dt=1
[column=15] colh = 1978 (x=4210); dt=1
[column=16] colh = 1521 (x=4510); dt=1
[column=17] colh = 1754 (x=4810); dt=1
[column=18] colh = 3000 (x=5110); dt=0
[column=19] colh = 3000 (x=5410); dt=0
*/