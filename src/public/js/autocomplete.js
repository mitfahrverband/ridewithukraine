$('input[data-autocomplete]').forEach((input) => {
  let div;

  let getText = (entry) => {
    let {street, housenumber, postcode, city, countrycode, name} = entry.properties;
    return [street, housenumber, postcode, city ?? name, countrycode]
      .filter((e) => e).join(', ');
  };

  let updateValues = () => {
    fetch('https://photon.komoot.io/api/?limit=5&q=' + input.value).then(r => r.json()).then((r) => {
      if (div) div.innerHTML = '';
      else {
        div = document.createElement("div")
        input.after(div);
      }
      let entries = [];
      r.features.forEach((entry) => {
        let text = getText(entry);

        if (entries.includes(text)) return;
        entries.push(text);

        let p = document.createElement("p");
        p.innerText = text;
        p.onclick = () => {
          input.location = entry;
          input.value = text;
          div.innerHTML = '';
        };
        div.appendChild(p);
      });
    });
  };

  let delay;
  input.oninput = () => {
    clearTimeout(delay);
    delay = setTimeout(updateValues, 500);
  };

  input.parentElement.querySelectorAll('button').forEach(btn => {
    btn.onclick = () => {
      navigator.geolocation.getCurrentPosition(position => {
        let lat = position.coords.latitude;
        let lon = position.coords.longitude;
        let url = encodeURI(`https://photon.komoot.io/reverse/?limit=1&lon=${lon}&lat=${lat}`);
        fetch(url).then(r => r.json()).then((r) => {
          let data = r.features[0];
          input.location = data;
          input.value = getText(data);
        });
      });
    };
  });
});
