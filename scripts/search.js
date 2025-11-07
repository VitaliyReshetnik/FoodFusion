// === Пошук елементів ===
const form = document.getElementById('searchForm');
const recipesBox = document.getElementById('recipes');
const overlay = document.querySelector('.overlay');
const popup = document.getElementById('filterPopup');
const filterBtn = document.getElementById('filterBtn');
const closeBtn = document.getElementById('closeFilters');
const resetBtn = document.getElementById('resetBtn');

// === Відкриття / закриття popup ===
filterBtn.onclick = () => {
  popup.classList.add('active');
  overlay.classList.add('active');
};
closeBtn.onclick = overlay.onclick = () => {
  popup.classList.remove('active');
  overlay.classList.remove('active');
};

// === Новий подвійний повзунок ===
function updateSliderRange(minId, maxId, trackId, minLabel, maxLabel) {
  const minSlider = document.getElementById(minId);
  const maxSlider = document.getElementById(maxId);
  const rangeTrack = document.getElementById(trackId);
  const minOutput = document.getElementById(minLabel);
  const maxOutput = document.getElementById(maxLabel);
  const maxVal = parseInt(maxSlider.max);

  function updateTrack() {
    let minVal = parseInt(minSlider.value);
    let maxValNow = parseInt(maxSlider.value);

    if (minVal > maxValNow - 1) minSlider.value = maxValNow - 1;
    if (maxValNow < minVal + 1) maxSlider.value = minVal + 1;

    const percent1 = (minSlider.value / maxVal) * 100;
    const percent2 = (maxSlider.value / maxVal) * 100;
    rangeTrack.style.left = percent1 + "%";
    rangeTrack.style.width = (percent2 - percent1) + "%";

    minOutput.textContent = minSlider.value;
    maxOutput.textContent = maxSlider.value;
  }

  minSlider.addEventListener("input", updateTrack);
  maxSlider.addEventListener("input", updateTrack);
  updateTrack();
}

updateSliderRange("timeMin", "timeMax", "timeTrack", "timeMinVal", "timeMaxVal");
updateSliderRange("ingMin", "ingMax", "ingTrack", "ingMinVal", "ingMaxVal");

// === AJAX-завантаження рецептів ===
async function loadRecipes() {
  const fd = new FormData(form);
  fd.append('ajax', '1');
  const params = new URLSearchParams(fd);
  try {
    const res = await fetch('search.php?' + params.toString());
    const html = await res.text();
    recipesBox.innerHTML = html;
  } catch (e) {
    recipesBox.innerHTML = "<p style='text-align:center;color:red;'>Помилка завантаження 😢</p>";
    console.error(e);
  }
}

// === Подія “Знайти” ===
form.addEventListener('submit', (e) => {
  e.preventDefault();
  loadRecipes();
  popup.classList.remove('active');
  overlay.classList.remove('active');
});

// === Скидання ===
resetBtn.onclick = () => {
  form.reset();
  updateSliderRange("timeMin", "timeMax", "timeTrack", "timeMinVal", "timeMaxVal");
  updateSliderRange("ingMin", "ingMax", "ingTrack", "ingMinVal", "ingMaxVal");
  loadRecipes();
};

// === Початкове завантаження ===
document.addEventListener('DOMContentLoaded', loadRecipes);
