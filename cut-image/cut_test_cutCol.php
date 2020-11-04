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

	Actual:
		388,  < ~a bit too early
		690,
		990,
		1290,
		1310, < invalid
		1458, < invalid
		1590,
		1716, < invalid
		1890,
*/

/*
[column=1] colh = 1898 (x=10); dt=1
[y=216]: (r:47;g:42;b:48) (max:8;avg:4.3333333333333); candidate=-1 [okCount=0]
[y=217]: (r:53;g:42;b:50) (max:8;avg:3.6666666666667); candidate=216 [okCount=1]
rejected
[y=301]: (r:49;g:52;b:43) (max:7;avg:3.3333333333333); candidate=-1 [okCount=0]
rejected
[y=316]: (r:59;g:54;b:50) (max:9;avg:4.3333333333333); candidate=-1 [okCount=0]
[y=317]: (r:41;g:57;b:47) (max:9;avg:6.3333333333333); candidate=316 [okCount=1]
rejected
[y=322]: (r:56;g:51;b:48) (max:6;avg:3); candidate=-1 [okCount=0]
rejected
[y=388]: (r:50;g:42;b:40) (max:10;avg:6); candidate=-1 [okCount=0]
[y=389]: (r:56;g:51;b:48) (max:6;avg:3); candidate=388 [okCount=1]
[y=390]: (r:54;g:50;b:49) (max:4;avg:1.6666666666667); candidate=388 [okCount=2]
[y=391]: (r:49;g:47;b:48) (max:3;avg:2); candidate=388 [okCount=3]
[y=392]: (r:50;g:50;b:50) (max:0;avg:0); candidate=388 [okCount=4]
accepted: 388
[y=422]: (r:52;g:58;b:48) (max:8;avg:4); candidate=-1 [okCount=0]
rejected
[y=432]: (r:48;g:57;b:40) (max:10;avg:6.3333333333333); candidate=-1 [okCount=0]
rejected
[y=469]: (r:46;g:57;b:41) (max:9;avg:6.6666666666667); candidate=-1 [okCount=0]
[y=470]: (r:41;g:51;b:40) (max:10;avg:6.6666666666667); candidate=469 [okCount=1]
rejected
[y=486]: (r:55;g:48;b:42) (max:8;avg:5); candidate=-1 [okCount=0]
rejected
[y=490]: (r:56;g:52;b:41) (max:9;avg:5.6666666666667); candidate=-1 [okCount=0]
rejected
[y=493]: (r:50;g:50;b:42) (max:8;avg:2.6666666666667); candidate=-1 [okCount=0]
rejected
[y=538]: (r:40;g:48;b:59) (max:10;avg:7); candidate=-1 [okCount=0]
rejected
[y=575]: (r:46;g:48;b:60) (max:10;avg:5.3333333333333); candidate=-1 [okCount=0]
rejected
[y=584]: (r:41;g:42;b:60) (max:10;avg:9); candidate=-1 [okCount=0]
rejected
[y=690]: (r:48;g:49;b:51) (max:2;avg:1.3333333333333); candidate=-1 [okCount=0]
[y=691]: (r:51;g:51;b:49) (max:1;avg:1); candidate=690 [okCount=1]
[y=692]: (r:49;g:49;b:47) (max:3;avg:1.6666666666667); candidate=690 [okCount=2]
[y=693]: (r:53;g:53;b:51) (max:3;avg:2.3333333333333); candidate=690 [okCount=3]
[y=694]: (r:50;g:50;b:52) (max:2;avg:0.66666666666667); candidate=690 [okCount=4]
accepted: 690
[y=834]: (r:52;g:58;b:48) (max:8;avg:4); candidate=-1 [okCount=0]
rejected
[y=990]: (r:49;g:50;b:44) (max:6;avg:2.3333333333333); candidate=-1 [okCount=0]
[y=991]: (r:48;g:49;b:51) (max:2;avg:1.3333333333333); candidate=990 [okCount=1]
[y=992]: (r:50;g:50;b:50) (max:0;avg:0); candidate=990 [okCount=2]
[y=993]: (r:50;g:50;b:50) (max:0;avg:0); candidate=990 [okCount=3]
[y=994]: (r:50;g:50;b:50) (max:0;avg:0); candidate=990 [okCount=4]
accepted: 990
[y=1290]: (r:48;g:50;b:49) (max:2;avg:1); candidate=-1 [okCount=0]
[y=1291]: (r:48;g:50;b:49) (max:2;avg:1); candidate=1290 [okCount=1]
[y=1292]: (r:52;g:52;b:52) (max:2;avg:2); candidate=1290 [okCount=2]
[y=1293]: (r:51;g:51;b:51) (max:1;avg:1); candidate=1290 [okCount=3]
[y=1294]: (r:51;g:49;b:50) (max:1;avg:0.66666666666667); candidate=1290 [okCount=4]
accepted: 1290
[y=1308]: (r:55;g:58;b:51) (max:8;avg:4.6666666666667); candidate=-1 [okCount=0]
rejected
[y=1310]: (r:52;g:50;b:51) (max:2;avg:1); candidate=-1 [okCount=0]
[y=1311]: (r:49;g:50;b:55) (max:5;avg:2); candidate=1310 [okCount=1]
[y=1312]: (r:59;g:59;b:59) (max:9;avg:9); candidate=1310 [okCount=2]
[y=1313]: (r:59;g:59;b:59) (max:9;avg:9); candidate=1310 [okCount=3]
[y=1314]: (r:47;g:47;b:45) (max:5;avg:3.6666666666667); candidate=1310 [okCount=4]
accepted: 1310
[y=1434]: (r:54;g:54;b:46) (max:4;avg:4); candidate=-1 [okCount=0]
rejected
[y=1447]: (r:40;g:50;b:41) (max:10;avg:6.3333333333333); candidate=-1 [okCount=0]
rejected
[y=1458]: (r:47;g:49;b:48) (max:3;avg:2); candidate=-1 [okCount=0]
[y=1459]: (r:49;g:51;b:50) (max:1;avg:0.66666666666667); candidate=1458 [okCount=1]
[y=1460]: (r:55;g:57;b:56) (max:7;avg:6); candidate=1458 [okCount=2]
[y=1461]: (r:57;g:59;b:56) (max:9;avg:7.3333333333333); candidate=1458 [okCount=3]
[y=1462]: (r:51;g:53;b:50) (max:3;avg:1.3333333333333); candidate=1458 [okCount=4]
accepted: 1458
[y=1562]: (r:49;g:50;b:52) (max:2;avg:1); candidate=-1 [okCount=0]
[y=1563]: (r:51;g:52;b:54) (max:4;avg:2.3333333333333); candidate=1562 [okCount=1]
[y=1564]: (r:55;g:56;b:58) (max:8;avg:6.3333333333333); candidate=1562 [okCount=2]
rejected
[y=1584]: (r:50;g:48;b:53) (max:3;avg:1.6666666666667); candidate=-1 [okCount=0]
rejected
[y=1590]: (r:46;g:46;b:46) (max:4;avg:4); candidate=-1 [okCount=0]
[y=1591]: (r:51;g:51;b:51) (max:1;avg:1); candidate=1590 [okCount=1]
[y=1592]: (r:50;g:50;b:50) (max:0;avg:0); candidate=1590 [okCount=2]
[y=1593]: (r:50;g:50;b:50) (max:0;avg:0); candidate=1590 [okCount=3]
[y=1594]: (r:50;g:50;b:50) (max:0;avg:0); candidate=1590 [okCount=4]
accepted: 1590
[y=1670]: (r:58;g:55;b:50) (max:8;avg:4.3333333333333); candidate=-1 [okCount=0]
[y=1671]: (r:54;g:50;b:47) (max:4;avg:2.3333333333333); candidate=1670 [okCount=1]
[y=1672]: (r:54;g:46;b:44) (max:6;avg:4.6666666666667); candidate=1670 [okCount=2]
rejected
[y=1678]: (r:53;g:43;b:41) (max:9;avg:6.3333333333333); candidate=-1 [okCount=0]
[y=1679]: (r:59;g:49;b:47) (max:9;avg:4.3333333333333); candidate=1678 [okCount=1]
[y=1680]: (r:54;g:47;b:41) (max:9;avg:5.3333333333333); candidate=1678 [okCount=2]
rejected
[y=1686]: (r:58;g:53;b:49) (max:8;avg:4); candidate=-1 [okCount=0]
rejected
[y=1688]: (r:60;g:59;b:57) (max:10;avg:8.6666666666667); candidate=-1 [okCount=0]
rejected
[y=1716]: (r:59;g:55;b:52) (max:9;avg:5.3333333333333); candidate=-1 [okCount=0]
[y=1717]: (r:56;g:51;b:47) (max:6;avg:3.3333333333333); candidate=1716 [okCount=1]
[y=1718]: (r:58;g:51;b:45) (max:8;avg:4.6666666666667); candidate=1716 [okCount=2]
[y=1719]: (r:56;g:49;b:41) (max:9;avg:5.3333333333333); candidate=1716 [okCount=3]
[y=1720]: (r:52;g:47;b:43) (max:7;avg:4); candidate=1716 [okCount=4]
accepted: 1716
[y=1731]: (r:49;g:44;b:40) (max:10;avg:5.6666666666667); candidate=1716 [okCount=0]
rejected
[y=1733]: (r:53;g:46;b:40) (max:10;avg:5.6666666666667); candidate=-1 [okCount=0]
[y=1734]: (r:54;g:47;b:41) (max:9;avg:5.3333333333333); candidate=1733 [okCount=1]
rejected
[y=1753]: (r:59;g:55;b:44) (max:9;avg:6.6666666666667); candidate=-1 [okCount=0]
rejected
[y=1758]: (r:43;g:45;b:42) (max:8;avg:6.6666666666667); candidate=-1 [okCount=0]
rejected
[y=1760]: (r:55;g:47;b:44) (max:6;avg:4.6666666666667); candidate=-1 [okCount=0]
rejected
[y=1890]: (r:53;g:53;b:53) (max:3;avg:3); candidate=-1 [okCount=0]
[y=1891]: (r:49;g:53;b:52) (max:3;avg:2); candidate=1890 [okCount=1]
[y=1892]: (r:53;g:49;b:48) (max:3;avg:2); candidate=1890 [okCount=2]
[y=1893]: (r:49;g:53;b:56) (max:6;avg:3.3333333333333); candidate=1890 [okCount=3]
[y=1894]: (r:49;g:50;b:44) (max:6;avg:2.3333333333333); candidate=1890 [okCount=4]
accepted: 1890

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