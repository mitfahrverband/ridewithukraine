function Autocomplete() {
}

$('input[oninput^=Autocomplete]').forEach((input) => {
  input.setAttribute('autocomplete', 'off'); // prevent browser autocomplete
});

Autocomplete.onclick = (btn) => {
  navigator.geolocation.getCurrentPosition(position => {
    let lat = position.coords.latitude;
    let lon = position.coords.longitude;
    let url = encodeURI(`https://photon.komoot.io/reverse/?limit=1&lon=${lon}&lat=${lat}`);
    fetch(url).then(r => r.json()).then((r) => {
      let data = r.features[0];
      let input = btn.parentElement.querySelector('input');
      input.location = data;
      input.value = Autocomplete.getText(data);
    });
  });
};

Autocomplete.oninput = (input) => {
  clearTimeout(this.delay);
  this.delay = setTimeout(() => Autocomplete.updateValues(input), 500);
};

Autocomplete.updateValues = (input) => {
  fetch('https://photon.komoot.io/api/?limit=5&q=' + input.value).then(r => r.json()).then((r) => {
    if (input.div) input.div.innerHTML = '';
    else {
      input.div = document.createElement("div")
      input.after(input.div);
    }
    let entries = [];
    r.features.forEach((entry) => {
      let text = Autocomplete.getText(entry);

      if (entries.includes(text)) return;
      entries.push(text);

      let p = document.createElement("p");
      p.innerText = text;
      p.onclick = () => {
        input.location = entry;
        input.value = text;
        input.div.innerHTML = '';
      };
      input.div.appendChild(p);
    });
  });
}

Autocomplete.getText = (entry) => {
  let {street, housenumber, postcode, city, countrycode, name} = entry.properties;
  if (!city) city = name;
  return [street, housenumber, postcode, city, countrycode]
    .filter((e) => e).join(', ');
}
