<html>
  <head>
        <script src="https://unpkg.com/htmx.org@1.7.0"></script>
  </head>
  
  <body>

<!--

we'd rather use photon.ride2go.com instead of komoot but because of an unknown
reason realted to CORS headers ride2go does not work but komoot does

-->

<h3> 
  Search location
  <span class="htmx-indicator"> 
    <img src="/img/bars.svg"/> Searching... 
   </span> 
</h3>
<input class="form-control" type="search" 
       name="q" placeholder="Begin Typing To Search Users..." 
       hx-get="https://photon.komoot.io/api" 
       hx-trigger="keyup changed delay:500ms, search" 
       hx-target="#search-results" 
       hx-indicator=".htmx-indicator">

<table class="table">
    <thead>
    <tr>
    </tr>
    </thead>
    <tbody id="search-results">
    </tbody>
</table>

</body>