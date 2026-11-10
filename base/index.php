<?php
require_once __DIR__ . '/../models/Data.php';

$rows = DataQuery::create()->getLastData(60);
$latest = $rows[0] ?? null;
$recent = array_slice($rows, 0, 10);
$rows = array_reverse($rows);

$labels = array_map(fn($d) => substr($d->getTimestamp(), 11, 5), $rows);
$temps = array_map(fn($d) => $d->getTemp(), $rows);
$humids = array_map(fn($d) => $d->getHumid(), $rows);
$presss = array_map(fn($d) => $d->getPress(), $rows);
$ppms = array_map(fn($d) => $d->getPPM(), $rows);

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Statistics dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <meta http-equiv="refresh" content="30"> 
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">IoT Monitor Kvality Ovzdušia</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm font-semibold text-gray-500 uppercase">CO₂ / Kvalita Ovzdušia</span>
                <div class="text-3xl font-extrabold text-emerald-600 mt-2">
                    <?= $latest ? '≈ ' . e(number_format($latest->getPPM(), 0)) . ' ppm' : 'N/A' ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm font-semibold text-gray-500 uppercase">Teplota</span>
                <div class="text-3xl font-extrabold text-blue-600 mt-2">
                    <?= $latest ? '≈ ' . e(number_format($latest->getTemp(), 1)) . ' °C' : 'N/A' ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm font-semibold text-gray-500 uppercase">Vlhkosť</span>
                <div class="text-3xl font-extrabold text-indigo-600 mt-2">
                    <?= $latest ? '≈ ' . e(number_format($latest->getHumid(), 1)) . ' %' : 'N/A' ?>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <span class="text-sm font-semibold text-gray-500 uppercase">Tlak</span>
                <div class="text-3xl font-extrabold text-purple-600 mt-2">
                    <?= $latest ? '≈ ' . e(number_format($latest->getPress(), 1)) . ' hPa' : 'N/A' ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Graf CO₂</div>
            <div class="p-6">
                <canvas id="chartPpm" width="400" height="200"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Graf teploty</div>
            <div class="p-6">
                <canvas id="chartTemp" width="400" height="200"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Graf vlhkosti</div>
            <div class="p-6">
                <canvas id="chartHumid" width="400" height="200"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Graf tlaku</div>
            <div class="p-6">
                <canvas id="chartPress" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Posledné merania</div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b">
                        <th class="px-6 py-3">Čas</th>
                        <th class="px-6 py-3">CO₂</th>
                        <th class="px-6 py-3">Teplota</th>
                        <th class="px-6 py-3">Vlhkosť</th>
                        <th class="px-6 py-3">Tlak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php foreach ($recent as $data): ?>
                        <tr>
                            <td class="px-6 py-4"><?= e($data->getTimestamp()) ?></td>
                            <td class="px-6 py-4"><?= e(number_format($data->getPPM(), 0)) ?> ppm</td>
                            <td class="px-6 py-4"><?= e(number_format($data->getTemp(), 1)) ?> °C</td>
                            <td class="px-6 py-4"><?= e(number_format($data->getHumid(), 1)) ?> %</td>
                            <td class="px-6 py-4"><?= e(number_format($data->getPress(), 1)) ?> hPa</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        const labels = <?= json_encode($labels) ?>;

        function createChart(canvasId, data, borderColor, bgColor, chartLabel) {
            new Chart(document.getElementById(canvasId), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: chartLabel,
                        data: data,
                        borderColor: borderColor,
                        backgroundColor: bgColor,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    animation: false,
                    plugins : {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxTicksLimit: 6
                            }
                        }
                    }
                }
            });
        }
        createChart('chartPpm', <?= json_encode($ppms) ?>, 'rgba(16, 185, 129, 1)', 'rgba(16, 185, 129, 0.2)', 'CO₂');
        createChart('chartTemp', <?= json_encode($temps) ?>, 'rgba(59, 130, 246, 1)', 'rgba(59, 130, 246, 0.2)', 'Teplota');
        createChart('chartHumid', <?= json_encode($humids) ?>, 'rgba(99, 102, 241, 1)', 'rgba(99, 102, 241, 0.2)', 'Vlhkosť');
        createChart('chartPress', <?= json_encode($presss) ?>, 'rgba(139, 92, 246, 1)', 'rgba(139, 92, 246, 0.2)', 'Tlak');
    </script>
</body>
</html>