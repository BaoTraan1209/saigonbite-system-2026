<?php
namespace App\Repositories;
use App\Core\Database;
class ReportRepository {
    public function sales(string $from,string $to): array { $s=Database::connection()->prepare("SELECT DATE(created_at) report_date,COUNT(*) orders_count,SUM(total) revenue FROM orders WHERE status NOT IN ('CANCELLED','REJECTED') AND DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY report_date");$s->execute([$from,$to]);return $s->fetchAll(); }
    public function hubWorkload(): array { return (new HubRepository())->all(false); }
    public function deliveryPerformance(string $from,string $to): array { $s=Database::connection()->prepare("SELECT d.status,COUNT(*) total, SUM(d.cod_collected) cod_collected FROM deliveries d JOIN orders o ON o.id=d.order_id WHERE DATE(o.created_at) BETWEEN ? AND ? GROUP BY d.status");$s->execute([$from,$to]);return $s->fetchAll(); }
}
