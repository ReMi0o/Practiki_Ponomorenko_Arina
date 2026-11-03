const rules = {
  login:       v => /^[A-Za-z0-9_]{4,20}$/.test(v) || 'Логин — от 4 до 20 символов: латинские буквы, цифры, «_»',
  password:    v => (v.length >= 8 && /\d/.test(v)) || 'Пароль — не короче 8 символов и хотя бы одна цифра',
  password2:   v => v === document.getElementById('password')?.value || 'Пароли не совпадают',
  fio:         v => /^[А-ЯЁа-яё][А-ЯЁа-яё\s-]*$/.test(v.trim()) || 'ФИО пишется русскими буквами',
  phone:       v => /^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/.test(v) || 'Телефон в формате +7(XXX)XXX-XX-XX',
  email:       v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Некорректный адрес электронной почты',
  workshop_id: v => v !== '' || 'Выберите мастер-класс из списка',
  payment:     v => v !== '' || 'Выберите способ оплаты',
  room:        v => v !== '' || 'Выберите помещение',
  message:     v => v.trim().length >= 15 || 'Напишите хотя бы пару предложений (от 15 символов)',
};

function validate(input) {
  const rule = rules[input.name];
  if (!rule) return true;
  const result = rule(input.value);
  const field = input.closest('.field');
  const box = field?.querySelector('.field__error');
  const ok = result === true;
  field?.classList.toggle('field--bad', !ok);
  if (box) box.textContent = ok ? '' : result;
  return ok;
}

for (const form of document.querySelectorAll('form[data-check]')) {
  const inputs = form.querySelectorAll('input:not([type=hidden]):not([type=radio]), select, textarea');
  inputs.forEach(el => {
    el.addEventListener('blur', () => validate(el));
    el.addEventListener('input', () => el.closest('.field--bad') && validate(el));
  });
  form.addEventListener('submit', event => {
    const bad = [...inputs].filter(el => !validate(el));
    const stars = form.querySelector('.stars-pick');
    if (stars && !form.querySelector('[name=stars]:checked')) {
      stars.classList.add('field--bad');
      stars.querySelector('.field__error').textContent = 'Поставьте оценку от 1 до 5';
      bad.push(stars.querySelector('input'));
    }
    if (bad.length) {
      event.preventDefault();
      bad[0].focus();
    }
  });
}

const select = document.getElementById('workshop_id');
const seats = document.getElementById('seats');
async function showSeats() {
  if (!select || !seats) return;
  if (!select.value) { seats.textContent = ''; return; }
  const res = await fetch(`/api/workshops/${select.value}`);
  if (!res.ok) return;
  const data = await res.json();
  seats.textContent = data.free > 0 ? `Свободно мест: ${data.free} из ${data.seats}` : 'Мест нет';
  seats.animate?.([{ opacity: 0, transform: 'translateY(-4px)' }, { opacity: 1, transform: 'none' }], { duration: 250 });
  seats.classList.toggle('field__hint--warn', data.free <= 2);
}
select?.addEventListener('change', showSeats);
showSeats();
