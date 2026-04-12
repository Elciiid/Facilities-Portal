<?php
/**
 * PdoSessionHandler - Database-backed session handler for stateless environments like Vercel.
 * Stores session data in PostgreSQL to persist across serverless invocations.
 */
class PdoSessionHandler implements SessionHandlerInterface
{
    private $pdo;
    private $table = 'fcl_app_sessions'; // Prepended with fcl_ as requested

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string|false
    {
        try {
            $stmt = $this->pdo->prepare("SELECT data FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['data'] : '';
        } catch (PDOException $e) {
            return '';
        }
    }

    public function write($id, $data): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table} (id, data, timestamp)
                VALUES (?, ?, ?)
                ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data, timestamp = EXCLUDED.timestamp
            ");
            return $stmt->execute([$id, $data, time()]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function destroy($id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function gc($maxlifetime): int|false
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE timestamp < ?");
            $stmt->execute([time() - $maxlifetime]);
            return (int)$stmt->rowCount();
        } catch (PDOException $e) {
            return false;
        }
    }
}
