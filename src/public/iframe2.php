<html>
<head>
    <title>iframe</title>
    <link rel="stylesheet" type="text/css" href="/css/style.min.css">
    <meta name="iframe" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div id="iframe-container">
    <?php
    use page\TripList;

    require_once "../core/Autoload.php";

    $filedata = file_get_contents('../data/json_data.json');
    $details = json_decode($filedata); ?>
    <?php
    TripList::render($details);
    ?>
</div>
</body>
</html>
