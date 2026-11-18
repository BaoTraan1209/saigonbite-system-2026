<?php
namespace App\Repositories;
use App\Core\Database;
class DeliveryRepository {
    public function createForOrder(int $orderId): int { $s=Database::connection()->prepare("INSERT INTO deliveries(order_id,status) VALUES(?,'WAITING_PICKUP') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");$s->execute([$orderId]);return (int)Database::connection()->lastInsertId(); }
    public function update(int $orderId,array $d): void {
        $s=Database::connection()->prepare('UPDATE deliveries SET shipper_name=?,shipper_phone=?,status=?,failure_reason=?,cod_collected=?,delivered_at=IF(?="DELIVERED",NOW(),delivered_at) WHERE order_id=?');
        $s->execute([$d['shipper_name']??null,$d['shipper_phone']??null,$d['status'],$d['failure_reason']??null,$d['cod_collected']??0,$d['status'],$orderId]);
    }
    public function findByOrder(int $id): ?array { $s=Database::connection()->prepare('SELECT * FROM deliveries WHERE order_id=?');$s->execute([$id]);return $s->fetch()?:null; }
}
