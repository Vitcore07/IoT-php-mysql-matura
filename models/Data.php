<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/DataQuery.php';

class Data
{
    public function __construct(
        private float $temp = 0.0,
        private float $humid = 0.0,
        private float $press = 0.0,
        private float $ppm = 0.0,
        private int $id = 0,
        private string $time = ''
    ) {}

    public function getID(): int
    {
        return $this->id;
    }

    public function getTimestamp(): string
    {
        return $this->time;
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
        return $this->ppm;
    }

    public function valid(): string {
        if ($this->temp < -40 || $this->temp > 85) {
            return "Temperature must be between -40 and 85 degrees Celsius.";
        }
        if ($this->humid < 0 || $this->humid > 100) {
            return "Humidity must be between 0 and 100 percent.";
        }
        if ($this->press < 300 || $this->press > 1100) {
            return "Pressure must be between 300 and 1100 hPa.";
        }
        if ($this->ppm < 0 || $this->ppm > 5000) {
            return "CO₂ concentration must be between 0 and 5000 ppm.";
        }
        return "";
    }

    public function save(?mysqli $conn = null): string
    {
        $err = $this->valid();
        if ($err !== "") {
            return $err;
        }
        try {
            DataQuery::create($conn)->insertData($this);
        } catch (mysqli_sql_exception $e) {
            return "Failed to save data: " . $e->getMessage();
        }

        return "";
    }
}