$("form")[0].onsubmit = async e => {
  e.preventDefault();
  let fields = e.target.elements;
  let coordsStart = fields['departureLocation'].location.geometry.coordinates;
  let coordsEnd = fields['destination'].location.geometry.coordinates;
  let time = fields['departureTime'].value.split('T');
  let trunc = (num) => {
    let places = 10;
    return Math.trunc(num * Math.pow(10, places)) / Math.pow(10, places);
  }
  let data = {
    stops: [
      {
        address: fields['departureLocation'].value,
        coordinates: {lat: trunc(coordsStart[1]), lon: trunc(coordsStart[0])}
      },
      {
        address: fields['destination'].value,
        coordinates: {lat: trunc(coordsEnd[1]), lon: trunc(coordsEnd[0])}
      },
    ],
    departDate: time[0],
    departTime: time[1],
  };
  if (fields['mode'].value === 'driving') {
    data.seats = parseInt(fields['seats']?.value);
    data.email = fields['mail']?.value;
    data.phoneNumber = fields['phone']?.value;
    data.acceptTerms = true;
  }
  $("body, html").toggleClass("overflow-hidden");
  let $sendingModal = $("#sending-modal");
  $sendingModal.toggleClass('hidden');
  let response = await fetch('/create-trip.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  });
  $sendingModal.toggleClass('hidden');
  if (response.ok) {
    $("#success-modal").toggleClass('hidden');
  } else {
    $("#error-modal").toggleClass('hidden');
  }
};
