<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/DataQuery.php';

class Data
{
    public function __construct(
        private int $ID = 0,
        private string $timestamp = '',
        private float $temp,
        private float $humid = 0.0,
        private float $press = 0.0,
        private float $PPM = 0.0
    ) {}

    public function getID(): int
    {
        return $this->ID;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function getTemp(): float
    {
        return $this->temp;
    }

    public function getHumid(): float
    {
        return $this->humid;
    }

    public function getPress(): float
    {
        return $this->press;
    }

    public function getPPM(): float
    {
        return $this->PPM;
    }

    public function valid(): string {
        if ($this->temp < -50 || $this->temp > 50) {
            return "Temperature must be between -50 and 50 degrees Celsius.";
        }
        if ($this->humid < 0 || $this->humid > 100) {
            return "Humidity must be between 0 and 100 percent.";
        }
        if ($this->press < 300 || $this->press > 1100) {
            return "Pressure must be between 300 and 1100 hPa.";
        }
        if ($this->PPM < 0 || $this->PPM > 5000) {
            return "CO₂ concentration must be between 0 and 5000 ppm.";
        }
        return "";
    }

    public function save(?mysqli $conn = null): bool
    {
        $err = $this->valid();
        if ($err !== "") {
            throw new Exception($err);
        }
        try {
            DataQuery::create($conn)->insertData($this);
        } catch (Exception $e) {
            throw new Exception("Failed to save data: " . $e->getMessage());
        }

        return "";
    }
}