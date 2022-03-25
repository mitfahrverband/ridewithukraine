<html>
  <head>
    <title>iframe</title>
    <link rel="stylesheet" type="text/css" href="iframe.css">
    <meta name="iframe" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
    <header>
    <h1 class="headertext"><img src="https://avatars.githubusercontent.com/u/77687247?s=200&v=4" class="mitfahrverband-logo">Powered by Mitfahrverband</h1>
    </header>
    <div id="iframe-container">
    <?php 
      echo '<table id="result-container-card">';
        $filedata = file_get_contents('./data/json_data.json');
        $details = json_decode($filedata, true);?>
        <?php
        foreach ($details as $element) {
          echo '<tr>';

          echo '<td id="departTime">';
          echo($element['departTime']);
          echo '</td>';

          echo '<td id="dateTime">';
          echo "24.03.2022";
          echo '</td>';


          echo '<td id="origin">';
          echo "<div id='trip_path_div'>";
          echo ($element['stops'][0]['address']);
          echo "</div>";
          echo '</td>';
          echo '<td id="destination">';
          echo "<div id='trip_path_div'>";
          echo end($element['stops'])['address'];
          echo "</div>";
          echo '</td>';
        

          $links = $element['deeplink'];

          $mifaz_word = "mifaz";

          if(strpos($links, $mifaz_word) !== false){
            echo '<td id="logo">';
            echo '<img src="https://ride2go.com/img/mifaz_logo.png" class="mifaz-logo">';
            echo '</td>';
        } else{
          echo '<td id="logo">';
          echo '<img src="https://ride2go.com/img/r2g_favicon.png" class="ride2go-logo">';
          echo '</td>';
        }

          echo '<td id="link">';
          echo "<a id='button1' href='$links'target='_blank' >Show</a>";
          echo '</td>';
          }

          echo '</tr>';
          echo '</table>';
          ?>
      </div>
  </body>
</html>