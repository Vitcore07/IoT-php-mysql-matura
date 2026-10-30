<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/Data.php';

class DataQuery
{
    private mysqli $conn;

    public function __construct(?mysqli $conn = null)
    {
        $this->conn = $conn ?? Database::getConnection();
    }

    public function create(?mysqli $conn = null): self 
    {
        return new self($conn);
    }

    public function insertData(Data $data): void
    {
        $st = $this->conn->prepare
        ("INSERT INTO data (timestamp, temp, humid, press, PPM) VALUES (?, ?, ?, ?, ?)");
        $temp = $data->getTimestamp();
        $humid = $data->getHumid();
        $press = $data->getPress();
        $ppm = $data->getPPM();
        $st->bind_param("dddd", $temp, $humid, $press, $ppm);
        $st->execute();
    }

    public function getLastData(int $limit = 30): array
    {
        $st = $this->conn->prepare("SELECT * FROM data ORDER BY ID DESC LIMIT ?");
        $st->bind_param("i", $limit);
        $st->execute();
        $result = $st->get_result();
        
        $dataList = [];
        while ($row = $result->fetch_assoc()) {
            $dataList[] = new Data(
                (int)$row['ID'],
                $row['timestamp'],
                (float)$row['temp'],
                (float)$row['humid'],
                (float)$row['press'],
                (float)$row['PPM']
            );
        }
        return $dataList;
    }
}