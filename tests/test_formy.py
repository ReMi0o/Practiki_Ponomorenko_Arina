"""Автотест форм регистрации и входа «Мастерской идей» (МДК.05.03, задание 3).

Автоматизировано 16 из 38 проверок отчёта: 12 проверок регистрации и 4 проверки входа.
Остальные проверки (без JavaScript, CSRF, cookie, права доступа и др.) выполнены вручную
и записаны в Результаты_проверок.xlsx. Скриншоты автотест не делает.

Подготовка (из папки Код_проекта, на свежей базе с тестовыми данными):
    php database/seed.php
    php -S localhost:8000 -t public public/index.php
Запуск (из корня репозитория, во втором терминале):
    pip install pytest playwright && python -m playwright install chromium
    pytest tests/test_formy.py -v
Адрес сайта можно изменить переменной окружения BASE_URL.
Ожидаемый итог на свежей базе: 13 passed, 3 failed (дефекты R13, R17, R21).
"""
import os
import time

import pytest
from playwright.sync_api import sync_playwright

BASE = os.environ.get('BASE_URL', 'http://localhost:8000').rstrip('/')
STAMP = time.strftime('%H%M%S')
GOOD = dict(password='Proba2026', password2='Proba2026', fio='Тестова Ольга Игоревна',
            phone='+7(999)123-45-67', email='olga.test@mail.ru')


def uniq(n):
    return f'auto_{STAMP}_{n}'


# (ID, данные, должна ли пройти регистрация)
REGISTRATION = [
    ('R1 верные данные', dict(GOOD, login=uniq(1)), True),
    ('R3 логин 3 символа', dict(GOOD, login='ab1'), False),
    ('R5 логин 21 символ', dict(GOOD, login='b' * 21), False),
    ('R6 логин кириллицей', dict(GOOD, login='ольга_т'), False),
    ('R8 логин занят', dict(GOOD, login='kotova_m'), False),
    ('R9 занятый логин другим регистром', dict(GOOD, login='KOTOVA_M'), False),
    ('R11 пароль 7 символов', dict(GOOD, login=uniq(2), password='abcde1g', password2='abcde1g'), False),
    ('R12 пароль без цифр', dict(GOOD, login=uniq(3), password='abcdefgh', password2='abcdefgh'), False),
    ('R13 пароль из пробелов', dict(GOOD, login=uniq(4), password='       1', password2='       1'), False),
    ('R17 ФИО из одной буквы', dict(GOOD, login=uniq(5), fio='Я'), False),
    ('R18 телефон с 8 вместо +7', dict(GOOD, login=uniq(6), phone='8(999)123-45-67'), False),
    ('R21 email уже занят', dict(GOOD, login=uniq(7), email='kotova.m@yandex.ru'), False),
]
LOGIN = [
    ('L1 вход участника', 'leila_h', 'Uchastnik1', True),
    ('L2 вход администратора', 'admin', 'Master2026', True),
    ('L4 неверный пароль', 'leila_h', 'Uchastnik2', False),
    ("L6 SQL-инъекция", "admin' --", 'x', False),
]


@pytest.fixture(scope='module')
def browser():
    with sync_playwright() as p:
        b = p.chromium.launch()
        yield b
        b.close()


@pytest.fixture
def page(browser):
    ctx = browser.new_context()
    yield ctx.new_page()
    ctx.close()


@pytest.mark.parametrize('name, data, should_pass', REGISTRATION, ids=[r[0] for r in REGISTRATION])
def test_registration(page, name, data, should_pass):
    page.goto(BASE + '/register')
    for field, value in data.items():
        page.fill(f'[name={field}]', value)
    page.click('button[type=submit]')
    page.wait_for_load_state()
    registered = page.url.endswith('/login')
    errors = [e.strip() for e in page.locator('.field__error').all_text_contents() if e.strip()]
    assert registered == should_pass, f'зарегистрирован={registered}, ошибки: {errors}'


@pytest.mark.parametrize('name, login, password, should_pass', LOGIN, ids=[r[0] for r in LOGIN])
def test_login(page, name, login, password, should_pass):
    page.goto(BASE + '/login')
    page.fill('#login', login)
    page.fill('#password', password)
    page.click('button[type=submit]')
    page.wait_for_load_state()
    assert (not page.url.endswith('/login')) == should_pass
