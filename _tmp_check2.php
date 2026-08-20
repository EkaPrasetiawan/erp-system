<?php
$k = new mysqli('localhost','root','','kingdom_system');
echo "=== FULL record point=0 (FK Final) for WK0226001 ===\n";
$r = $k->query("SELECT fasilitas_name, group_fasilitas, qty, price, price2, price_vend, cost1, cost2, amount, point FROM rombongan_detail WHERE fasilitas_id='WK0226001' AND del_status=0 AND point=0 ORDER BY group_fasilitas");
while ($x = $r->fetch_assoc()) {
    printf("%-22s | %-18s | qty=%-4s price=%-9s p2=%-9s pvend=%-9s c1=%-9s c2=%-9s amt=%-9s\n",
        $x['fasilitas_name'], $x['group_fasilitas'], $x['qty'], $x['price'], $x['price2'], $x['price_vend'], $x['cost1'], $x['cost2'], $x['amount']);
}

echo "\n=== FULL record point=1 (FK Awal) for WK0226001 ===\n";
$r = $k->query("SELECT fasilitas_name, group_fasilitas, qty, price, price2, price_vend, cost1, cost2, amount, point FROM rombongan_detail WHERE fasilitas_id='WK0226001' AND del_status=0 AND point=1 ORDER BY group_fasilitas");
while ($x = $r->fetch_assoc()) {
    printf("%-22s | %-18s | qty=%-4s price=%-9s p2=%-9s pvend=%-9s c1=%-9s c2=%-9s amt=%-9s\n",
        $x['fasilitas_name'], $x['group_fasilitas'], $x['qty'], $x['price'], $x['price2'], $x['price_vend'], $x['cost1'], $x['cost2'], $x['amount']);
}
