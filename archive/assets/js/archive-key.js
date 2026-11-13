// Effects for the password boxes (all cosmetic).

var strengthNames = ['Unsealed', 'Fragile', 'Sound', 'Fortified', 'Vaulted'];

// A random two-character code like "A7"
function randomGlyph() {
  var hex = '0123456789ABCDEF';
  return hex.charAt(Math.floor(Math.random() * 16)) +
         hex.charAt(Math.floor(Math.random() * 16));
}

// Score the password from 0 to 4
function getStrength(password) {
  if (password.length == 0) { return 0; }
  if (password.length < 8) { return 1; }

  var score = 1;
  if (password.length >= 12) { score++; }
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) { score++; }
  if (/\d/.test(password) && /[^A-Za-z0-9]/.test(password)) { score++; }
  return score;
}

function setupKeyField(box) {
  var input    = box.querySelector('input');
  var toggle   = box.querySelector('[data-toggle]');
  var status   = box.querySelector('[data-status]');
  var cells    = box.querySelectorAll('[data-cell]');
  var meter    = box.querySelector('[data-meter]');
  var strength = box.querySelector('[data-strength]');

  // If this is the "confirm" box, find the first password box
  var firstId = box.getAttribute('data-confirm-for');
  var first = firstId ? document.getElementById(firstId) : null;

  var idleText = status.textContent;

  function update() {
    var password = input.value;
    var length = password.length;

    // Fill one cell for every character typed
    for (var i = 0; i < cells.length; i++) {
      if (i < length) {
        if (cells[i].textContent == '') {
          cells[i].textContent = randomGlyph();
        }
        cells[i].classList.add('is-filled');
      } else {
        cells[i].textContent = '';
        cells[i].classList.remove('is-filled');
      }
    }

    // Status text
    box.classList.remove('is-match', 'is-mismatch');
    if (length == 0) {
      status.textContent = idleText;
    } else if (first) {
      if (password == first.value) {
        status.textContent = 'Keys correspond';
        box.classList.add('is-match');
      } else {
        status.textContent = 'Keys do not correspond';
        box.classList.add('is-mismatch');
      }
    } else {
      status.textContent = length + ' glyphs entered';
    }

    // Strength meter (only on the register page)
    if (meter) {
      var level = getStrength(password);
      meter.setAttribute('data-level', level);
      strength.textContent = strengthNames[level];
    }
  }

  // Reveal / hide button
  toggle.addEventListener('click', function () {
    if (input.type == 'password') {
      input.type = 'text';
      toggle.textContent = 'Hide';
      box.classList.add('is-revealed');
    } else {
      input.type = 'password';
      toggle.textContent = 'Reveal';
      box.classList.remove('is-revealed');
    }
  });

  input.addEventListener('input', update);
  if (first) {
    first.addEventListener('input', update);
  }
  update();
}

var boxes = document.querySelectorAll('[data-keyfield]');
for (var i = 0; i < boxes.length; i++) {
  setupKeyField(boxes[i]);
}
