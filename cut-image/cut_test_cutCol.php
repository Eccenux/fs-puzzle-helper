<?php
/*
	Column cutting test.

	Expected cuts:
		390
		690
		990
		1290
		1590
		1890

	Actual with distance=10:
		388,  < ~a bit too early
		690,
		990,
		1290,
		1310, < invalid
		1458, < invalid
		1590,
		1716, < invalid
		1890,
		
	Actual with AVG check:
		389,  < still 1px too early, but good enough
		690,
		990,
		1290,
		1590,
		1890,
*/

////
// Simple distance check (invalid result)
/*
[column=1] colh = 1898 (x=10); dt=1
rejected: 216 [okCount=2]
rejected: 301 [okCount=1]
rejected: 316 [okCount=2]
rejected: 322 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=388] (r:50;g:42;b:40) (max:10;avg:6);
[okCount=1] candidate=388 [y=389] (r:56;g:51;b:48) (max:6;avg:3);
[okCount=2] candidate=388 [y=390] (r:54;g:50;b:49) (max:4;avg:1.6666666666667);
[okCount=3] candidate=388 [y=391] (r:49;g:47;b:48) (max:3;avg:2);
[okCount=4] candidate=388 [y=392] (r:50;g:50;b:50) (max:0;avg:0);
accepted: 388
.
.
rejected: 422 [okCount=1]
rejected: 432 [okCount=1]
rejected: 469 [okCount=2]
rejected: 486 [okCount=1]
rejected: 490 [okCount=1]
rejected: 493 [okCount=1]
rejected: 538 [okCount=1]
rejected: 575 [okCount=1]
rejected: 584 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=690] (r:48;g:49;b:51) (max:2;avg:1.3333333333333);
[okCount=1] candidate=690 [y=691] (r:51;g:51;b:49) (max:1;avg:1);
[okCount=2] candidate=690 [y=692] (r:49;g:49;b:47) (max:3;avg:1.6666666666667);
[okCount=3] candidate=690 [y=693] (r:53;g:53;b:51) (max:3;avg:2.3333333333333);
[okCount=4] candidate=690 [y=694] (r:50;g:50;b:52) (max:2;avg:0.66666666666667);
accepted: 690
.
.
rejected: 834 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=990] (r:49;g:50;b:44) (max:6;avg:2.3333333333333);
[okCount=1] candidate=990 [y=991] (r:48;g:49;b:51) (max:2;avg:1.3333333333333);
[okCount=2] candidate=990 [y=992] (r:50;g:50;b:50) (max:0;avg:0);
[okCount=3] candidate=990 [y=993] (r:50;g:50;b:50) (max:0;avg:0);
[okCount=4] candidate=990 [y=994] (r:50;g:50;b:50) (max:0;avg:0);
accepted: 990
.
.

.
.
[okCount=0] candidate=-1 [y=1290] (r:48;g:50;b:49) (max:2;avg:1);
[okCount=1] candidate=1290 [y=1291] (r:48;g:50;b:49) (max:2;avg:1);
[okCount=2] candidate=1290 [y=1292] (r:52;g:52;b:52) (max:2;avg:2);
[okCount=3] candidate=1290 [y=1293] (r:51;g:51;b:51) (max:1;avg:1);
[okCount=4] candidate=1290 [y=1294] (r:51;g:49;b:50) (max:1;avg:0.66666666666667);
accepted: 1290
.
.
rejected: 1308 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=1310] (r:52;g:50;b:51) (max:2;avg:1);
[okCount=1] candidate=1310 [y=1311] (r:49;g:50;b:55) (max:5;avg:2);
[okCount=2] candidate=1310 [y=1312] (r:59;g:59;b:59) (max:9;avg:9);
[okCount=3] candidate=1310 [y=1313] (r:59;g:59;b:59) (max:9;avg:9);
[okCount=4] candidate=1310 [y=1314] (r:47;g:47;b:45) (max:5;avg:3.6666666666667);
accepted: 1310
.
.
rejected: 1434 [okCount=1]
rejected: 1447 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=1458] (r:47;g:49;b:48) (max:3;avg:2);
[okCount=1] candidate=1458 [y=1459] (r:49;g:51;b:50) (max:1;avg:0.66666666666667);
[okCount=2] candidate=1458 [y=1460] (r:55;g:57;b:56) (max:7;avg:6);
[okCount=3] candidate=1458 [y=1461] (r:57;g:59;b:56) (max:9;avg:7.3333333333333);
[okCount=4] candidate=1458 [y=1462] (r:51;g:53;b:50) (max:3;avg:1.3333333333333);
accepted: 1458
.
.
rejected: 1562 [okCount=3]
rejected: 1584 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=1590] (r:46;g:46;b:46) (max:4;avg:4);
[okCount=1] candidate=1590 [y=1591] (r:51;g:51;b:51) (max:1;avg:1);
[okCount=2] candidate=1590 [y=1592] (r:50;g:50;b:50) (max:0;avg:0);
[okCount=3] candidate=1590 [y=1593] (r:50;g:50;b:50) (max:0;avg:0);
[okCount=4] candidate=1590 [y=1594] (r:50;g:50;b:50) (max:0;avg:0);
accepted: 1590
.
.
rejected: 1670 [okCount=3]
rejected: 1678 [okCount=3]
rejected: 1686 [okCount=1]
rejected: 1688 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=1716] (r:59;g:55;b:52) (max:9;avg:5.3333333333333);
[okCount=1] candidate=1716 [y=1717] (r:56;g:51;b:47) (max:6;avg:3.3333333333333);
[okCount=2] candidate=1716 [y=1718] (r:58;g:51;b:45) (max:8;avg:4.6666666666667);
[okCount=3] candidate=1716 [y=1719] (r:56;g:49;b:41) (max:9;avg:5.3333333333333);
[okCount=4] candidate=1716 [y=1720] (r:52;g:47;b:43) (max:7;avg:4);
accepted: 1716
.
.
rejected: 1731 [okCount=1]
rejected: 1733 [okCount=2]
rejected: 1753 [okCount=1]
rejected: 1758 [okCount=1]
rejected: 1760 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=1890] (r:53;g:53;b:53) (max:3;avg:3);
[okCount=1] candidate=1890 [y=1891] (r:49;g:53;b:52) (max:3;avg:2);
[okCount=2] candidate=1890 [y=1892] (r:53;g:49;b:48) (max:3;avg:2);
[okCount=3] candidate=1890 [y=1893] (r:49;g:53;b:56) (max:6;avg:3.3333333333333);
[okCount=4] candidate=1890 [y=1894] (r:49;g:50;b:44) (max:6;avg:2.3333333333333);
accepted: 1890
.
.
array (
  0 => 388,
  1 => 690,
  2 => 990,
  3 => 1290,
  4 => 1310,
  5 => 1458,
  6 => 1590,
  7 => 1716,
  8 => 1890,
)

*/


////
// Fixed, with AVG check
/*
[column=1] colh = 1898 (x=10); dt=1
rejected: 216 [okCount=2]
rejected: 301 [okCount=1]
rejected: 322 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=389] (r:56;g:51;b:48) (max:6;avg:3);
[okCount=1] candidate=389 [y=390] (r:54;g:50;b:49) (max:4;avg:1.6666666666667);
[okCount=2] candidate=389 [y=391] (r:49;g:47;b:48) (max:3;avg:2);
[okCount=3] candidate=389 [y=392] (r:50;g:50;b:50) (max:0;avg:0);
accepted: 389
.
.
rejected: 422 [okCount=1]
rejected: 486 [okCount=1]
rejected: 493 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=690] (r:48;g:49;b:51) (max:2;avg:1.3333333333333);
[okCount=1] candidate=690 [y=691] (r:51;g:51;b:49) (max:1;avg:1);
[okCount=2] candidate=690 [y=692] (r:49;g:49;b:47) (max:3;avg:1.6666666666667);
[okCount=3] candidate=690 [y=693] (r:53;g:53;b:51) (max:3;avg:2.3333333333333);
accepted: 690
.
.
rejected: 834 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=990] (r:49;g:50;b:44) (max:6;avg:2.3333333333333);
[okCount=1] candidate=990 [y=991] (r:48;g:49;b:51) (max:2;avg:1.3333333333333);
[okCount=2] candidate=990 [y=992] (r:50;g:50;b:50) (max:0;avg:0);
[okCount=3] candidate=990 [y=993] (r:50;g:50;b:50) (max:0;avg:0);
accepted: 990
.
.

.
.
[okCount=0] candidate=-1 [y=1290] (r:48;g:50;b:49) (max:2;avg:1);
[okCount=1] candidate=1290 [y=1291] (r:48;g:50;b:49) (max:2;avg:1);
[okCount=2] candidate=1290 [y=1292] (r:52;g:52;b:52) (max:2;avg:2);
[okCount=3] candidate=1290 [y=1293] (r:51;g:51;b:51) (max:1;avg:1);
accepted: 1290
.
.
rejected: 1308 [okCount=1]
rejected: 1310 [okCount=2]
rejected: 1314 [okCount=2]
rejected: 1434 [okCount=1]
rejected: 1458 [okCount=3]
rejected: 1462 [okCount=2]
rejected: 1562 [okCount=3]
rejected: 1584 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=1590] (r:46;g:46;b:46) (max:4;avg:4);
[okCount=1] candidate=1590 [y=1591] (r:51;g:51;b:51) (max:1;avg:1);
[okCount=2] candidate=1590 [y=1592] (r:50;g:50;b:50) (max:0;avg:0);
[okCount=3] candidate=1590 [y=1593] (r:50;g:50;b:50) (max:0;avg:0);
accepted: 1590
.
.
rejected: 1670 [okCount=3]
rejected: 1686 [okCount=1]
rejected: 1717 [okCount=2]
rejected: 1720 [okCount=5]
rejected: 1727 [okCount=3]
rejected: 1758 [okCount=1]
rejected: 1760 [okCount=1]

.
.
[okCount=0] candidate=-1 [y=1890] (r:53;g:53;b:53) (max:3;avg:3);
[okCount=1] candidate=1890 [y=1891] (r:49;g:53;b:52) (max:3;avg:2);
[okCount=2] candidate=1890 [y=1892] (r:53;g:49;b:48) (max:3;avg:2);
[okCount=3] candidate=1890 [y=1893] (r:49;g:53;b:56) (max:6;avg:3.3333333333333);
[okCount=4] candidate=1890 [y=1894] (r:49;g:50;b:44) (max:6;avg:2.3333333333333);
accepted: 1890
.
.
array (
  0 => 389,
  1 => 690,
  2 => 990,
  3 => 1290,
  4 => 1590,
  5 => 1890,
)
[column=1] total dt=61
*/