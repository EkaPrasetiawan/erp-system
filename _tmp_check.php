<?php
$k = new mysqli('localhost','root','','kingdom_system');
echo "=== STRUCTURE rombongan_detail ===\n";
$r = $k->query('DESCRIBE rombongan_detail');
while ($x = $r->fetch_assoc()) { echo $x['Field'].' | '.$x['Type']."\n"; }

echo "\n=== SAMPLE point=1 (FK Awal) ===\n";
$r = $k->query('SELECT fasilitas_id, fasilitas_name, group_fasilitas, qty, price, price_vend, point FROM rombongan_detail WHERE del_status=0 AND point=1 LIMIT 8');
while ($x = $r->fetch_assoc()) { print_r($x); }

echo "\n=== SAMPLE point=0 (FK Final) ===\n";
$r = $k->query('SELECT fasilitas_id, fasilitas_name, group_fasilitas, qty, price, price_vend, point FROM rombongan_detail WHERE del_status=0 AND point=0 LIMIT 8');
while ($x = $r->fetch_assoc()) { print_r($x); }

echo "\n=== COUNT by point ===\n";
$r = $k->query('SELECT point, COUNT(*) c, SUM(price) sp, SUM(price_vend) spv FROM rombongan_detail WHERE del_status=0 GROUP BY point');
while ($x = $r->fetch_assoc()) { print_r($x); }
