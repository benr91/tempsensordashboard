<?php
// MySQL connection settings
$servername = "localhost";
$username = "ben";
$password = "password";
$database = "ben";

// Handle CSV export
if (isset($_GET['export_csv']) && $_GET['export_csv'] == '1') {
    $step = isset($_GET['step']) ? intval($_GET['step']) : 30;
    $range = isset($_GET['range']) ? $_GET['range'] : 'all';
    if ($step <= 0) $step = 1;

    $whereClause = '';
    switch ($range) {
        case '1h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 1 HOUR"; break;
        case '6h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 6 HOUR"; break;
        case '12h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 12 HOUR"; break;
        case '24h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 1 DAY"; break;
        case '7d': $whereClause = "WHERE timedate >= NOW() - INTERVAL 7 DAY"; break;
        default: $whereClause = ''; break;
    }

    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM temp2 $whereClause ORDER BY ID ASC";
    $result = $conn->query($sql);

    $data = [];
    $counter = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($counter % $step === 0) {
                $data[] = $row;
            }
            $counter++;
        }
    }
    $conn->close();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="temperature_export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'temp1', 'temp2', 'temp3', 'timedate']);
    foreach ($data as $row) {
        fputcsv($output, [$row['ID'], $row['temp1'], $row['temp2'], $row['temp3'], $row['timedate']]);
    }
    fclose($output);
    exit();
}

// Main page
$step = isset($_GET['step']) ? intval($_GET['step']) : 30;
$range = isset($_GET['range']) ? $_GET['range'] : 'all';
$chartLib = isset($_GET['chart']) ? $_GET['chart'] : 'chartist';
if ($step <= 0) $step = 1;

$whereClause = '';
switch ($range) {
    case '1h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 1 HOUR"; break;
    case '6h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 6 HOUR"; break;
    case '12h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 12 HOUR"; break;
    case '24h': $whereClause = "WHERE timedate >= NOW() - INTERVAL 1 DAY"; break;
    case '7d': $whereClause = "WHERE timedate >= NOW() - INTERVAL 7 DAY"; break;
    default: $whereClause = ''; break;
}

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM temp2 $whereClause ORDER BY ID ASC";
$result = $conn->query($sql);

$data = [];
$counter = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($counter % $step === 0) {
            $data[] = $row;
        }
        $counter++;
    }
}

function calcStats($data, $key) {
    $values = array_column($data, $key);
    $values = array_map('floatval', $values);
    if (count($values) === 0) {
        return ['min' => 0, 'max' => 0, 'avg' => 0];
    }
    return [
        'min' => min($values),
        'max' => max($values),
        'avg' => round(array_sum($values) / count($values), 2)
    ];
}

$stats = [
    'temp1' => calcStats($data, 'temp1'),
    'temp2' => calcStats($data, 'temp2'),
    'temp3' => calcStats($data, 'temp3')
];

$conn->close();

echo '<!DOCTYPE html><html><head><title>Temperature Dashboard</title>';
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist@0.11.4/dist/chartist.min.css">';
echo '<style>
    body { font-family: Arial, sans-serif; padding: 20px; background-color: #fff; color: #000; transition: background-color 0.3s, color 0.3s; }
    body.dark-mode { background-color: #121212; color: #eee; }
    .ct-chart, #rgraphCanvas, #anychartContainer, #chartjsCanvas, #canvasjsContainer { height: 500px; margin-top: 30px; }
    .stats { margin-top: 10px; }
    .stats span { display: inline-block; margin-right: 20px; }
    form { margin-bottom: 20px; }
    h2 { margin-bottom: 5px; }
    button, select, input[type=submit] { font-size: 16px; margin-right: 10px; }
</style>';
echo '</head><body>';

echo '<h2>Temperature Readings</h2>';

echo '<form method="GET" id="filterForm">';
echo 'Sample every: <select name="step" onchange="document.getElementById(\'filterForm\').submit()">';
foreach ([1,10,20,30,60,120] as $s) {
    $sel = $s == $step ? 'selected' : '';
    echo "<option value=\"$s\" $sel>Every $s row(s)</option>";
}
echo '</select> ';

echo '| Date range: <select name="range" onchange="document.getElementById(\'filterForm\').submit()">';
$options = ['1h'=>'Last 1 hour','6h'=>'Last 6 hours','12h'=>'Last 12 hours','24h'=>'Last 24 hours','7d'=>'Last 7 days','all'=>'All Time'];
foreach ($options as $key=>$label) {
    $sel = $key==$range ? 'selected' : '';
    echo "<option value=\"$key\" $sel>$label</option>";
}
echo '</select> ';

echo '| Chart library: <select name="chart" onchange="document.getElementById(\'filterForm\').submit()">';
$chartOptions = ['chartist'=>'Chartist.js','chartjs'=>'Chart.js','rgraph'=>'RGraph','anychart'=>'AnyChart','canvasjs'=>'CanvasJS'];
foreach ($chartOptions as $key=>$label) {
    $sel = $key==$chartLib ? 'selected' : '';
    echo "<option value=\"$key\" $sel>$label</option>";
}
echo '</select> ';

echo '<input type="submit" value="Update">';
echo '</form>';

// CSV export button
echo '<form method="GET" action="" style="display:inline">';
echo '<input type="hidden" name="step" value="'.htmlspecialchars($step).'">';
echo '<input type="hidden" name="range" value="'.htmlspecialchars($range).'">';
echo '<input type="hidden" name="chart" value="'.htmlspecialchars($chartLib).'">';
echo '<input type="hidden" name="export_csv" value="1">';
echo '<button type="submit">Export CSV</button>';
echo '</form>';

// Dark mode toggle
echo '<button id="modeToggle" style="margin-left: 20px;">Toggle Light/Dark Mode</button>';

// Stats
echo '<div class="stats">';
foreach ($stats as $label=>$vals) {
    echo "<strong>$label:</strong> ";
    echo "<span>Min: {$vals['min']}C</span>";
    echo "<span>Max: {$vals['max']}C</span>";
    echo "<span>Avg: {$vals['avg']}C</span><br>";
}
echo '</div>';

// Chart containers - only one visible at a time
echo '<div id="chartistContainer" class="ct-chart" style="display:'.($chartLib==='chartist'?'block':'none').'"></div>';
echo '<canvas id="chartjsCanvas" style="display:'.($chartLib==='chartjs'?'block':'none').'; width:100%; height:500px;"></canvas>';
echo '<canvas id="rgraphCanvas" width="800" height="500" style="display:'.($chartLib==='rgraph'?'block':'none').'; border:1px solid #ccc;"></canvas>';
echo '<div id="anychartContainer" style="display:'.($chartLib==='anychart'?'block':'none').'; width:100%; height:500px;"></div>';
echo '<div id="canvasjsContainer" style="display:'.($chartLib==='canvasjs'?'block':'none').'; width:100%; height:500px;"></div>';

// Scripts
echo '
<script src="https://cdn.jsdelivr.net/npm/chartist@0.11.4/dist/chartist.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/rgraph@6.05/RGraph.common.core.js"></script>
<script src="https://cdn.jsdelivr.net/npm/rgraph@6.05/RGraph.line.js"></script>
<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-base.min.js"></script>
<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-line.min.js"></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

<script>
const rawData = '.json_encode($data).';

const labels = rawData.map(e=>e.timedate);
const temp1 = rawData.map(e=>parseFloat(e.temp1));
const temp2 = rawData.map(e=>parseFloat(e.temp2));
const temp3 = rawData.map(e=>parseFloat(e.temp3));

const chartLib = "'.$chartLib.'";

function renderChartist(){
    new Chartist.Line("#chartistContainer", {
        labels: labels,
        series: [temp1, temp2, temp3]
    },{
        fullWidth:true,
        chartPadding:{right:40},
        axisX:{
            showGrid:false,
            labelInterpolationFnc:function(value,index){
                return index % Math.ceil(labels.length/10) === 0 ? value : null;
            }
        },
        axisY:{
            labelInterpolationFnc:function(value){
                return value + "C";
            }
        }
    });
}

function renderChartJS(){
    const ctx = document.getElementById("chartjsCanvas").getContext("2d");
    if (window.chartjsInstance) window.chartjsInstance.destroy();
    window.chartjsInstance = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                { label: "Temp1", data: temp1, borderColor: "red", fill:false, tension:0.3 },
                { label: "Temp2", data: temp2, borderColor: "green", fill:false, tension:0.3 },
                { label: "Temp3", data: temp3, borderColor: "blue", fill:false, tension:0.3 }
            ]
        },
        options: {
            responsive:true,
            scales: {
                y: { beginAtZero:false, ticks: { callback: val => val + "C" } }
            }
        }
    });
}

function renderRGraph(){
    var line = new RGraph.Line({
        id: "rgraphCanvas",
        data: [temp1, temp2, temp3],
        options: {
            colors: ["red", "green", "blue"],
            gutterLeft: 50,
            gutterBottom: 50,
            labels: labels,
            tickmarks: "circle",
            spline: true,
            ymin: Math.min(...temp1, ...temp2, ...temp3) - 5,
            ymax: Math.max(...temp1, ...temp2, ...temp3) + 5,
            ylabelsCount: 10
        }
    }).draw();
}

function renderAnyChart(){
    anychart.onDocumentReady(function () {
        var chart = anychart.line();
        chart.title("Temperature Readings");
        chart.xAxis().title("Time");
        chart.yAxis().title("Temperature (C)");
        var series1 = chart.line(labels.map((v,i)=>[v, temp1[i]]));
        series1.name("Temp1");
        var series2 = chart.line(labels.map((v,i)=>[v, temp2[i]]));
        series2.name("Temp2");
        var series3 = chart.line(labels.map((v,i)=>[v, temp3[i]]));
        series3.name("Temp3");
        chart.legend(true);
        chart.container("anychartContainer");
        chart.draw();
    });
}

function renderCanvasJS(){
    var chart = new CanvasJS.Chart("canvasjsContainer", {
        animationEnabled: true,
        theme: "light2",
        title: {
            text: "Temperature Readings"
        },
        axisX: {
            labelAngle: -45,
            interval: Math.ceil(labels.length / 10),
            intervalType: "number",
            labelFormatter: function(e){
                return labels[e.value] || "";
            }
        },
        axisY: {
            title: "Temperature (C)"
        },
        data: [
            {
                type: "line",
                name: "Temp1",
                showInLegend: true,
                dataPoints: labels.map((v,i)=>({ y: temp1[i], label: v }))
            },
            {
                type: "line",
                name: "Temp2",
                showInLegend: true,
                dataPoints: labels.map((v,i)=>({ y: temp2[i], label: v }))
            },
            {
                type: "line",
                name: "Temp3",
                showInLegend: true,
                dataPoints: labels.map((v,i)=>({ y: temp3[i], label: v }))
            }
        ]
    });
    chart.render();
}

function renderSelectedChart(){
    if(chartLib === "chartist") renderChartist();
    else if(chartLib === "chartjs") renderChartJS();
    else if(chartLib === "rgraph") renderRGraph();
    else if(chartLib === "anychart") renderAnyChart();
    else if(chartLib === "canvasjs") renderCanvasJS();
}

renderSelectedChart();

// Dark mode toggle with persistence
if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("dark-mode");
}

document.getElementById("modeToggle").addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
    if (document.body.classList.contains("dark-mode")) {
        localStorage.setItem("darkMode", "enabled");
    } else {
        localStorage.setItem("darkMode", "disabled");
    }
});
</script>
';

echo '</body></html>';
?>
