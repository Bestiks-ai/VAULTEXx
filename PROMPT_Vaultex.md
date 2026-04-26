# ПРОМТ ДЛЯ ИИ: ПОЛНЫЙ САЙТ КРИПТОВАЛЮТНОГО СЕРВИСА «VAULTEX»

> Используй этот документ как полное техническое задание.
> Создай весь код полностью — PHP, CSS, JS, SQL, конфиги.
> Ничего не пропускай. Никаких заглушек кроме явно отмеченных.

---

## НАЗВАНИЕ И БРЕНДИНГ

**Бренд:** `Vaultex`
**Слоган RU:** «Ваши ключи. Ваше будущее.»
**Слоган EN:** "Your keys. Your future."
**Домен-пример:** vaultex.io

Почему Vaultex: одинаково читается по-русски и по-английски, Vault = хранилище (надёжность), -ex = exchange (биржа). Запоминается мгновенно.

---

## ТЕХНИЧЕСКИЙ СТЕК

- **Backend:** PHP 8.2+, паттерн MVC, Composer
- **БД:** MySQL 8.0 (PDO, prepared statements)
- **Шаблонизатор:** Twig 3.x
- **CSS:** чистый CSS3 (CSS Variables, Grid, Flexbox) — никаких Bootstrap
- **JS:** Vanilla JS ES6+ — никакого jQuery
- **Карусель:** Splide.js (подключить через CDN)
- **Иконки:** Phosphor Icons (SVG inline) — **эмодзи полностью запрещены везде**
- **Шрифты:** Google Fonts — `Sora` (заголовки) + `DM Sans` (текст)
- **i18n:** JSON-файлы локализации `/lang/ru.json` и `/lang/en.json`, переключение через сессию
- **SEO:** полные meta-теги, Open Graph, Twitter Card, Schema.org JSON-LD, sitemap.xml, robots.txt
- **Безопасность:** CSRF-токены на всех формах, bcrypt пароли, rate limiting, session fingerprint, XSS-фильтрация, SQL только через PDO

---

## ДИЗАЙН-КОНЦЕПЦИЯ

**Стиль:** Dark Luxury Fintech
Тёмный, строгий, премиальный. Никакого «корпоративного синего». Никаких фиолетовых градиентов на белом. Никаких stock-фото. Никаких эмодзи.

### CSS-переменные (обязательно использовать везде)

```css
:root {
  --bg-primary:      #0A0D14;
  --bg-secondary:    #0F1520;
  --bg-card:         #141B2D;
  --bg-card-hover:   #1A2338;
  --border:          #1E2D45;
  --border-light:    #243352;
  --accent-blue:     #3D7EFF;
  --accent-blue-dim: #1A3D80;
  --accent-gold:     #F0B429;
  --accent-gold-dim: #7A5C14;
  --accent-green:    #22C55E;
  --accent-red:      #EF4444;
  --text-primary:    #F1F5F9;
  --text-secondary:  #94A3B8;
  --text-muted:      #475569;
  --shadow-card:     0 4px 24px rgba(0,0,0,0.4);
  --shadow-glow:     0 0 40px rgba(61,126,255,0.15);
  --radius-sm:       8px;
  --radius-md:       14px;
  --radius-lg:       20px;
  --radius-xl:       28px;
  --sidebar-width:   260px;
  --bottom-nav-h:    68px;
  --transition:      0.2s cubic-bezier(0.4,0,0.2,1);
}
```

### Типографика

| Элемент | Шрифт | Размер | Weight | Прочее |
|---------|-------|--------|--------|--------|
| H1 | Sora | 52–64px | 700 | letter-spacing: -1.5px |
| H2 | Sora | 36–42px | 600 | letter-spacing: -0.8px |
| H3 | Sora | 22–28px | 600 | letter-spacing: -0.4px |
| H4 | Sora | 18px | 600 | |
| Body | DM Sans | 16px | 400 | line-height: 1.7 |
| Label | DM Sans | 12px | 500 | uppercase, letter-spacing: 1.5px |
| Mono | JetBrains Mono | 14px | 400 | для адресов/seed |

---

## ДВУЯЗЫЧНОСТЬ (i18n)

### Реализация

Создай класс `Lang`:
```php
class Lang {
    private static array $strings = [];
    private static string $current = 'ru';

    public static function load(string $locale): void {
        self::$current = $locale;
        $file = ROOT . "/lang/{$locale}.json";
        self::$strings = json_decode(file_get_contents($file), true);
    }

    public static function get(string $key, array $replace = []): string {
        $str = self::$strings[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $str = str_replace(':' . $k, $v, $str);
        }
        return $str;
    }
}
// Использование в Twig: {{ lang('dashboard.balance') }}
```

### Переключатель языка

Переключатель языка размещается:
- На десктопе: в нижней части левого sidebar (над ссылками соцсетей)
- На мобильных: в бургер-меню (которое открывается сверху), **не** в нижней панели навигации

Вид переключателя:
```html
<div class="lang-switcher">
  <button class="lang-btn <?= $lang === 'ru' ? 'active' : '' ?>" data-lang="ru">
    <svg><!-- флаг РФ SVG --></svg> RU
  </button>
  <button class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" data-lang="en">
    <svg><!-- флаг UK SVG --></svg> EN
  </button>
</div>
```

Переключение — POST-запрос на `/lang/set`, сохраняет в `$_SESSION['lang']`, редирект обратно.

### Структура lang/ru.json (пример ключей)

```json
{
  "nav.dashboard": "Главная",
  "nav.wallet": "Кошелёк",
  "nav.deposit": "Пополнить",
  "nav.withdraw": "Вывод",
  "nav.p2p": "P2P",
  "nav.history": "История",
  "nav.settings": "Настройки",
  "nav.logout": "Выйти",
  "nav.language": "Язык",
  "dashboard.balance": "Общий баланс",
  "dashboard.deposit": "Пополнить",
  "dashboard.withdraw": "Вывести",
  "deposit.title": "Пополнение кошелька",
  "withdraw.title": "Вывод средств",
  "seed.title": "Сид-фраза",
  "seed.warning": "Никогда не передавайте сид-фразу третьим лицам",
  "p2p.soon": "Раздел в разработке",
  "2fa.stub": "Двухфакторная аутентификация",
  "footer.rights": "Все права защищены"
}
```

---

## НАВИГАЦИЯ — АРХИТЕКТУРА

### Правило адаптивности

```
Экран ≥ 1024px  →  левый sidebar (фиксированный, 260px)
Экран < 1024px  →  нижняя панель (5 иконок) + бургер-меню сверху
```

### Левый Sidebar (десктоп)

```
┌─────────────────────────────┐
│  [LOGO] Vaultex             │
│─────────────────────────────│
│  [icon] Главная             │  ← активный пункт подсвечен accent-blue
│  [icon] Кошелёк             │
│  [icon] Пополнить           │
│  [icon] Вывод               │
│  [icon] P2P        [SOON]   │
│  [icon] История             │
│─────────────────────────────│
│  [icon] Настройки           │
│  [icon] Безопасность        │
│─────────────────────────────│
│  🌐 [RU] [EN]               │  ← переключатель языка
│─────────────────────────────│
│  [TG] [TW] [DC]             │  ← соцсети SVG-иконки
│  © 2025 Vaultex             │
└─────────────────────────────┘
```

CSS sidebar:
```css
.sidebar {
  position: fixed;
  left: 0; top: 0; bottom: 0;
  width: var(--sidebar-width);
  background: var(--bg-secondary);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  z-index: 100;
  overflow-y: auto;
  scrollbar-width: none;
}
.main-content {
  margin-left: var(--sidebar-width);
  min-height: 100vh;
}
@media (max-width: 1023px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; padding-bottom: var(--bottom-nav-h); }
}
```

### Нижняя панель (мобильные, max-width: 1023px)

Только 5 главных кнопок. Всё остальное — в бургер-меню.

```
┌────────────────────────────────────┐
│ [🏠]   [💼]   [➕]   [📤]   [☰]  │
│  Главн  Кошел  Депоз  Вывод  Ещё  │
└────────────────────────────────────┘
```

CSS:
```css
.bottom-nav {
  display: none;
  position: fixed;
  bottom: 0; left: 0; right: 0;
  height: var(--bottom-nav-h);
  background: var(--bg-secondary);
  border-top: 1px solid var(--border);
  z-index: 200;
}
@media (max-width: 1023px) {
  .bottom-nav { display: flex; align-items: center; justify-content: space-around; }
}
.bottom-nav__item {
  display: flex; flex-direction: column; align-items: center;
  gap: 4px; font-size: 11px; color: var(--text-muted);
  padding: 8px 12px; border-radius: var(--radius-sm);
  transition: color var(--transition);
  text-decoration: none;
}
.bottom-nav__item.active,
.bottom-nav__item:hover { color: var(--accent-blue); }
.bottom-nav__item svg { width: 22px; height: 22px; }
```

### Бургер-меню (мобильные)

Открывается сдвигом сверху вниз (slide-down). Содержит:
- Профиль пользователя (аватар, email)
- Полный список навигации
- Переключатель языка RU / EN
- Ссылки на соцсети
- Кнопка «Выйти»

```css
.mobile-menu {
  position: fixed;
  top: 0; left: 0; right: 0;
  background: var(--bg-card);
  transform: translateY(-100%);
  transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
  z-index: 300;
  border-bottom: 1px solid var(--border);
  border-radius: 0 0 var(--radius-xl) var(--radius-xl);
  padding: 24px 20px;
}
.mobile-menu.open { transform: translateY(0); }
```

---

## РОУТИНГ И СТРАНИЦЫ

### Файловая структура

```
/vaultex
├── /app
│   ├── /Controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── WalletController.php
│   │   ├── DepositController.php
│   │   ├── WithdrawController.php
│   │   ├── SeedController.php
│   │   ├── P2PController.php
│   │   ├── HistoryController.php
│   │   ├── SettingsController.php
│   │   ├── LangController.php
│   │   └── AdminController.php
│   ├── /Models
│   │   ├── User.php
│   │   ├── Wallet.php
│   │   ├── Transaction.php
│   │   ├── Banner.php
│   │   └── Setting.php
│   ├── /Middleware
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── CsrfMiddleware.php
│   └── /Core
│       ├── Router.php
│       ├── DB.php
│       ├── Lang.php
│       └── Session.php
├── /lang
│   ├── ru.json
│   └── en.json
├── /public
│   ├── index.php
│   ├── /css
│   │   ├── main.css
│   │   ├── sidebar.css
│   │   ├── cards.css
│   │   └── forms.css
│   ├── /js
│   │   ├── main.js
│   │   ├── carousel.js
│   │   └── lang-switch.js
│   └── /assets
│       ├── /images    ← см. README_IMAGES.md
│       └── /icons     ← SVG-иконки
├── /views
│   ├── /layouts
│   │   ├── base.twig
│   │   ├── auth.twig
│   │   └── admin.twig
│   ├── /partials
│   │   ├── sidebar.twig
│   │   ├── bottom-nav.twig
│   │   ├── mobile-menu.twig
│   │   └── flash.twig
│   ├── home.twig
│   ├── login.twig
│   ├── register.twig
│   ├── dashboard.twig
│   ├── wallet.twig
│   ├── deposit.twig
│   ├── withdraw.twig
│   ├── seed.twig
│   ├── p2p.twig
│   ├── history.twig
│   ├── settings.twig
│   └── /admin
│       ├── index.twig
│       ├── users.twig
│       ├── transactions.twig
│       ├── banners.twig
│       ├── wallets.twig
│       └── settings.twig
├── /migrations
│   └── 001_init.sql
├── composer.json
├── .htaccess
└── .env.example
```

---

## ГЛАВНАЯ СТРАНИЦА (лендинг) — публичная

### SEO-разметка

```html
<title>Vaultex — Криптовалютный кошелёк и обменник | Crypto Wallet & Exchange</title>
<meta name="description" content="Vaultex — безопасный криптовалютный сервис. Пополняйте, выводите, храните криптовалюту. Secure crypto wallet and exchange platform.">
<meta name="keywords" content="криптокошелёк, обменник криптовалюты, crypto wallet, bitcoin, ethereum, vaultex">
<link rel="canonical" href="https://vaultex.io/">

<!-- Open Graph -->
<meta property="og:title" content="Vaultex — Your keys. Your future.">
<meta property="og:description" content="Безопасный криптовалютный кошелёк нового поколения">
<meta property="og:image" content="/assets/images/og-cover.jpg">
<meta property="og:type" content="website">

<!-- Schema.org -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Vaultex",
  "url": "https://vaultex.io",
  "logo": "https://vaultex.io/assets/images/logo.svg",
  "sameAs": ["https://t.me/vaultex", "https://twitter.com/vaultex"]
}
</script>
```

### Структура секций лендинга

#### HERO (первый экран)

```html
<section class="hero" aria-label="Главный экран">
  <div class="hero__bg"><!-- animated grid lines SVG background --></div>
  <div class="hero__content">
    <span class="hero__label"><!-- label: "Безопасно · Быстро · Надёжно" --></span>
    <h1 class="hero__title">
      Криптовалюта<br>
      <span class="hero__title--accent">без компромиссов</span>
    </h1>
    <p class="hero__desc">
      Храните, отправляйте и получайте криптовалюту в одном месте.
      Полный контроль над вашими активами.
    </p>
    <div class="hero__actions">
      <a href="/register" class="btn btn--primary btn--lg">Начать бесплатно</a>
      <a href="#features" class="btn btn--ghost btn--lg">Узнать больше</a>
    </div>
    <div class="hero__stats">
      <!-- 3 цифры: пользователей / транзакций / стран -->
      <!-- берутся из БД через AdminController -->
    </div>
  </div>
  <div class="hero__visual">
    <!-- Изображение: 3D-рендер криптокошелька. Описание в README_IMAGES.md -->
    <img src="/assets/images/hero-wallet-3d.png" alt="Vaultex Crypto Wallet" width="600" height="500">
  </div>
</section>
```

#### КАРУСЕЛЬ БАННЕРОВ

```php
// BannerController подгружает активные баннеры из БД
// Adminka позволяет: добавить/удалить/переупорядочить/вкл-выкл баннер
```

```html
<section class="banners" aria-label="Акции и новости">
  <div class="splide" id="banner-carousel" aria-label="Vaultex баннеры">
    <div class="splide__track">
      <ul class="splide__list">
        <?php foreach ($banners as $b): ?>
        <li class="splide__slide">
          <a href="<?= $b['link'] ?>" class="banner-card">
            <img src="/assets/images/banners/<?= $b['image'] ?>"
                 alt="<?= htmlspecialchars($b['alt_text']) ?>"
                 width="1200" height="400" loading="lazy">
            <div class="banner-card__text">
              <h2><?= htmlspecialchars($b['title']) ?></h2>
              <p><?= htmlspecialchars($b['subtitle']) ?></p>
            </div>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="splide__arrows">...</div>
    <ul class="splide__pagination"></ul>
  </div>
</section>
```

```js
// carousel.js
const splide = new Splide('#banner-carousel', {
  type: 'loop',
  autoplay: true,
  interval: 5000,
  pauseOnHover: true,
  rewind: true,
  gap: '1.5rem',
  pagination: true,
  arrows: true,
  breakpoints: {
    768: { gap: '1rem' }
  }
});
splide.mount();
```

#### СЕКЦИЯ ВОЗМОЖНОСТЕЙ (Features)

```html
<section class="features" id="features">
  <h2>Всё что нужно для работы с криптовалютой</h2>
  <div class="features__grid">
    <!-- 6 карточек с SVG-иконками (Phosphor): -->
    <!-- 1. Безопасное хранение -->
    <!-- 2. Мгновенное пополнение -->
    <!-- 3. Быстрый вывод -->
    <!-- 4. История транзакций -->
    <!-- 5. Двухфакторная защита -->
    <!-- 6. P2P торговля (скоро) -->
  </div>
</section>
```

#### ПОДДЕРЖИВАЕМЫЕ МОНЕТЫ

```html
<section class="coins">
  <h2>Поддерживаемые активы</h2>
  <!-- Горизонтальная прокрутка с логотипами монет: BTC, ETH, USDT, BNB, SOL, USDC, TRX, MATIC -->
  <!-- Логотипы — SVG, берём из cryptoicons или официальных CDN -->
</section>
```

#### КАК ЭТО РАБОТАЕТ (Steps)

```html
<section class="how-it-works">
  <h2>Как начать за 3 минуты</h2>
  <!-- 3 шага с нумерацией, иконками и стрелками между ними -->
  <!-- 1. Создайте аккаунт -->
  <!-- 2. Получите кошелёк -->
  <!-- 3. Пополните и пользуйтесь -->
</section>
```

#### БЕЗОПАСНОСТЬ

```html
<section class="security">
  <h2>Безопасность на первом месте</h2>
  <!-- 4 пункта: шифрование, 2FA, мультиподпись, аудит -->
  <!-- Фоновое изображение: описание в README_IMAGES.md -->
</section>
```

#### ПРИЗЫВ К ДЕЙСТВИЮ (CTA)

```html
<section class="cta">
  <h2>Готовы начать?</h2>
  <p>Создайте аккаунт бесплатно за 2 минуты</p>
  <a href="/register" class="btn btn--primary btn--xl">Создать кошелёк</a>
</section>
```

#### ПОДВАЛ (Footer)

```html
<footer class="footer">
  <div class="footer__grid">
    <!-- Колонка 1: Лого + описание + соцсети -->
    <!-- Колонка 2: Продукт (ссылки) -->
    <!-- Колонка 3: Компания -->
    <!-- Колонка 4: Поддержка -->
  </div>
  <div class="footer__bottom">
    <p>© 2025 Vaultex. Все права защищены.</p>
    <nav>
      <a href="/privacy">Политика конфиденциальности</a>
      <a href="/terms">Условия использования</a>
    </nav>
  </div>
</footer>
```

### Социальные сети (SVG-иконки, без эмодзи)

```html
<div class="social-links">
  <a href="https://t.me/vaultex" class="social-link social-link--tg" aria-label="Telegram">
    <svg><!-- Telegram SVG icon --></svg>
    <span>Telegram</span>
  </a>
  <a href="https://twitter.com/vaultex" class="social-link social-link--tw" aria-label="Twitter">
    <svg><!-- X/Twitter SVG icon --></svg>
    <span>Twitter</span>
  </a>
  <a href="https://discord.gg/vaultex" class="social-link social-link--dc" aria-label="Discord">
    <svg><!-- Discord SVG icon --></svg>
    <span>Discord</span>
  </a>
</div>
```

---

## СТРАНИЦЫ АВТОРИЗАЦИИ

### /register — Регистрация

```html
<main class="auth-page">
  <!-- SEO: <title>Регистрация — Vaultex</title> -->
  <div class="auth-card">
    <div class="auth-card__logo">
      <img src="/assets/images/logo.svg" alt="Vaultex" width="140">
    </div>
    <h1>Создать аккаунт</h1>
    <p class="auth-card__sub">Уже есть аккаунт? <a href="/login">Войти</a></p>

    <form method="POST" action="/register" class="auth-form">
      <!-- CSRF token -->
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autocomplete="email">
      </div>
      <div class="form-group">
        <label for="username">Имя пользователя</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>
        <!-- strength indicator bar -->
      </div>
      <div class="form-group">
        <label for="password_confirm">Подтвердите пароль</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
      </div>
      <div class="form-group form-group--check">
        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms">Я принимаю <a href="/terms">условия использования</a></label>
      </div>
      <button type="submit" class="btn btn--primary btn--full">Создать аккаунт</button>
    </form>
  </div>
</main>
```

Логика PHP:
- Валидация email (filter_var), username (3–20 символов, [a-z0-9_-]), password (мин 8 символов)
- Хеширование: `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])`
- После регистрации → автоматически генерируется seed phrase (12 слов BIP39) и кошелёк
- Seed phrase хранится зашифрованной (AES-256) в БД, показывается ОДИН раз после регистрации
- Редирект на `/seed-phrase?first=1` для просмотра seed phrase

### /login — Вход

```html
<form method="POST" action="/login" class="auth-form">
  <!-- email + password -->
  <!-- remember me checkbox -->
  <!-- кнопка 2FA (заглушка): показывает плашку "Двухфакторная аутентификация будет доступна в ближайшее время" -->
  <!-- ссылка "Забыли пароль?" -->
</form>
```

Логика PHP:
- `password_verify()` для проверки
- Session regeneration при успехе
- Rate limiting: не более 5 попыток за 15 минут по IP

---

## ЛИЧНЫЙ КАБИНЕТ

### /dashboard — Главная панели

```html
<!-- SEO: <title>Дашборд — Vaultex</title> -->
<div class="dashboard">
  <header class="page-header">
    <h1>Добро пожаловать, <?= $user['username'] ?></h1>
    <time class="page-header__date"><?= date('d F Y') ?></time>
  </header>

  <!-- Карточка общего баланса -->
  <div class="balance-card">
    <span class="balance-card__label">Общий баланс</span>
    <div class="balance-card__amount">
      <span class="balance-card__currency">$</span>
      <span class="balance-card__value" id="total-balance">0.00</span>
    </div>
    <div class="balance-card__actions">
      <a href="/deposit" class="btn btn--primary">Пополнить</a>
      <a href="/withdraw" class="btn btn--outline">Вывести</a>
    </div>
  </div>

  <!-- Мини-карточки активов (BTC, ETH, USDT...) -->
  <section class="assets-grid">
    <h2>Мои активы</h2>
    <div class="assets-grid__list">
      <!-- foreach wallet as asset -->
    </div>
  </section>

  <!-- Последние транзакции (5 штук) -->
  <section class="recent-tx">
    <h2>Последние операции</h2>
    <!-- список + кнопка "Все транзакции" -->
  </section>
</div>
```

### /wallet — Кошелёк

```html
<h1>Мой кошелёк</h1>
<!-- Tabs: BTC / ETH / USDT / BNB / ... -->
<!-- Для каждой монеты: -->
<!--   - Адрес (моноширинный шрифт, кнопка копировать + QR-код) -->
<!--   - Баланс -->
<!--   - Кнопки: Пополнить / Вывести -->
```

### /deposit — Пополнение

```html
<h1>Пополнение кошелька</h1>
<form method="POST" action="/deposit">
  <!-- Выбор монеты (select) -->
  <!-- Адрес кошелька (readonly, автоподставляется при выборе монеты через JS fetch) -->
  <!-- QR-код адреса (генерируется PHP: chillerlan/php-qrcode) -->
  <!-- Сумма (информативно) -->
  <!-- Кнопка "Скопировать адрес" -->
  <!-- Блок с инструкцией -->
</form>
```

### /withdraw — Вывод

```html
<h1>Вывод средств</h1>
<form method="POST" action="/withdraw">
  <!-- Выбор монеты -->
  <!-- Адрес получателя -->
  <!-- Сумма -->
  <!-- Доступный баланс (подгружается JS) -->
  <!-- Комиссия сети (берётся из настроек админки) -->
  <!-- Итого к получению -->
  <!-- Кнопка "Вывести" -->
  <!-- 2FA-заглушка: модальное окно с сообщением "2FA скоро будет активирована" -->
</form>
```

### /seed-phrase — Сид-фраза

**ВАЖНО:** максимальная безопасность.

```html
<h1>Ваша сид-фраза</h1>

<!-- Предупреждение (красный блок) -->
<div class="seed-warning" role="alert">
  <svg><!-- alert icon --></svg>
  <p>Никогда не передавайте сид-фразу третьим лицам. Запишите её и храните в безопасном месте.</p>
</div>

<!-- Seed phrase скрыта по умолчанию -->
<div class="seed-container">
  <div class="seed-blur" id="seed-blur">
    <!-- Blur overlay с кнопкой "Показать" -->
  </div>
  <div class="seed-grid">
    <!-- 12 пронумерованных слов в сетке 3×4 -->
    <!-- JetBrains Mono, крупный шрифт -->
  </div>
</div>

<!-- Кнопки: Скопировать / Скачать PDF -->
<!-- Чекбокс подтверждения: "Я сохранил сид-фразу" -->
```

PHP: seed-фраза расшифровывается из БД только при явном запросе, пишется в лог доступа.

### /p2p — P2P торговля (заглушка)

```html
<div class="coming-soon-page">
  <div class="coming-soon__badge">В разработке</div>
  <h1>P2P Торговля</h1>
  <p>
    Мы работаем над созданием безопасной P2P-площадки.
    Скоро вы сможете торговать напрямую с другими пользователями.
  </p>
  <!-- Animated countdown или просто "Coming Soon" -->
  <!-- Email-форма для уведомления: "Уведомить меня о запуске" -->
  <div class="coming-soon__features">
    <h2>Что будет доступно:</h2>
    <!-- 4 пункта: объявления, чат, эскроу, рейтинги -->
  </div>
</div>
```

SEO: `<meta name="robots" content="noindex">` для этой страницы.

### /history — История транзакций

```html
<h1>История операций</h1>

<!-- Фильтры -->
<div class="history-filters">
  <!-- Тип: Все / Пополнение / Вывод -->
  <!-- Монета: Все / BTC / ETH / ... -->
  <!-- Период: 7д / 30д / 90д / Всё время -->
  <!-- Статус: Все / Успешно / В обработке / Ошибка -->
</div>

<!-- Таблица -->
<table class="tx-table">
  <thead>
    <tr>
      <th>Дата</th><th>Тип</th><th>Монета</th>
      <th>Сумма</th><th>Адрес</th><th>Статус</th><th>TxHash</th>
    </tr>
  </thead>
  <tbody>
    <!-- foreach transactions -->
    <!-- статус: зелёный badge / жёлтый / красный -->
  </tbody>
</table>

<!-- Пагинация -->
```

### /settings — Настройки профиля

```html
<h1>Настройки</h1>

<!-- Вкладки: Профиль / Безопасность / Уведомления -->

<!-- Профиль -->
<section>
  <h2>Личные данные</h2>
  <!-- Аватар (загрузка) -->
  <!-- Имя пользователя -->
  <!-- Email -->
  <!-- Кнопка сохранить -->
</section>

<!-- Безопасность -->
<section>
  <h2>Безопасность</h2>

  <!-- Смена пароля -->
  <h3>Изменить пароль</h3>
  <!-- текущий / новый / подтверждение -->

  <!-- 2FA — ЗАГЛУШКА -->
  <h3>Двухфакторная аутентификация</h3>
  <div class="stub-badge">Скоро</div>
  <p>Двухфакторная аутентификация находится в разработке и будет доступна в ближайшее время.</p>
  <button class="btn btn--outline" disabled>Подключить 2FA</button>
</section>

<!-- Уведомления -->
<section>
  <h2>Уведомления</h2>
  <!-- Toggles: Email при пополнении / Email при выводе / Новости -->
</section>
```

---

## БАЗА ДАННЫХ — СХЕМА

```sql
-- 001_init.sql

CREATE TABLE users (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username    VARCHAR(30) NOT NULL UNIQUE,
  email       VARCHAR(255) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,          -- bcrypt
  role        ENUM('user','admin') DEFAULT 'user',
  avatar      VARCHAR(255) DEFAULT NULL,
  is_active   TINYINT(1) DEFAULT 1,
  is_blocked  TINYINT(1) DEFAULT 0,
  email_verified TINYINT(1) DEFAULT 0,
  two_fa_enabled TINYINT(1) DEFAULT 0,        -- заглушка
  lang        ENUM('ru','en') DEFAULT 'ru',
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE wallets (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  coin        VARCHAR(10) NOT NULL,            -- 'BTC','ETH','USDT' etc
  address     VARCHAR(255) NOT NULL,
  balance     DECIMAL(20,8) DEFAULT 0.00000000,
  seed_phrase TEXT DEFAULT NULL,               -- AES-256 зашифровано
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uk_user_coin (user_id, coin)
);

CREATE TABLE transactions (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  wallet_id   INT UNSIGNED NOT NULL,
  type        ENUM('deposit','withdraw','internal') NOT NULL,
  coin        VARCHAR(10) NOT NULL,
  amount      DECIMAL(20,8) NOT NULL,
  fee         DECIMAL(20,8) DEFAULT 0,
  address_to  VARCHAR(255) DEFAULT NULL,
  address_from VARCHAR(255) DEFAULT NULL,
  tx_hash     VARCHAR(255) DEFAULT NULL,
  status      ENUM('pending','completed','failed','cancelled') DEFAULT 'pending',
  note        VARCHAR(500) DEFAULT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (wallet_id) REFERENCES wallets(id)
);

CREATE TABLE banners (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title_ru    VARCHAR(255) NOT NULL,
  title_en    VARCHAR(255) NOT NULL,
  subtitle_ru VARCHAR(500) DEFAULT NULL,
  subtitle_en VARCHAR(500) DEFAULT NULL,
  image       VARCHAR(255) NOT NULL,
  alt_ru      VARCHAR(255) DEFAULT NULL,
  alt_en      VARCHAR(255) DEFAULT NULL,
  link        VARCHAR(500) DEFAULT '#',
  sort_order  TINYINT UNSIGNED DEFAULT 0,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE site_settings (
  `key`       VARCHAR(100) PRIMARY KEY,
  `value`     TEXT,
  `type`      ENUM('text','number','boolean','json','color') DEFAULT 'text',
  `group`     VARCHAR(50) DEFAULT 'general',
  label       VARCHAR(255) DEFAULT NULL,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE withdrawal_fees (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  coin        VARCHAR(10) NOT NULL UNIQUE,
  fee_percent DECIMAL(5,2) DEFAULT 0.00,
  fee_fixed   DECIMAL(20,8) DEFAULT 0,
  min_amount  DECIMAL(20,8) DEFAULT 0,
  max_amount  DECIMAL(20,8) DEFAULT NULL,
  is_active   TINYINT(1) DEFAULT 1
);

CREATE TABLE security_logs (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED DEFAULT NULL,
  action      VARCHAR(100) NOT NULL,           -- 'login','seed_view','password_change'
  ip          VARCHAR(45) NOT NULL,
  user_agent  VARCHAR(500) DEFAULT NULL,
  success     TINYINT(1) DEFAULT 1,
  detail      TEXT DEFAULT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE rate_limits (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip          VARCHAR(45) NOT NULL,
  action      VARCHAR(100) NOT NULL,
  attempts    TINYINT UNSIGNED DEFAULT 1,
  first_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_action (ip, action)
);

-- Начальные настройки
INSERT INTO site_settings VALUES
('site_name','Vaultex','text','general','Название сайта',NOW()),
('site_description_ru','Безопасный криптовалютный кошелёк','text','general','Описание (RU)',NOW()),
('site_description_en','Secure crypto wallet and exchange','text','general','Описание (EN)',NOW()),
('maintenance_mode','0','boolean','general','Режим обслуживания',NOW()),
('registration_enabled','1','boolean','general','Регистрация открыта',NOW()),
('withdraw_enabled','1','boolean','finance','Вывод включён',NOW()),
('deposit_enabled','1','boolean','finance','Пополнение включено',NOW()),
('min_withdraw_usd','10','number','finance','Мин. сумма вывода $',NOW()),
('telegram_url','https://t.me/vaultex','text','social','Telegram',NOW()),
('twitter_url','https://twitter.com/vaultex','text','social','Twitter',NOW()),
('discord_url','https://discord.gg/vaultex','text','social','Discord',NOW()),
('stats_users','0','number','stats','Число пользователей (если ручное)',NOW()),
('stats_countries','50','number','stats','Число стран',NOW());
```

---

## ПАНЕЛЬ АДМИНИСТРАТОРА

**URL:** `/admin`
**Доступ:** только пользователи с `role = 'admin'`
**Дизайн:** такой же тёмный, но с другим акцентом — золотой (`--accent-gold`) вместо синего для маркировки «admin zone»

### /admin — Дашборд

```html
<h1>Панель управления</h1>

<!-- 4 плитки статистики -->
<div class="admin-stats">
  <div class="stat-card">
    <h3>Пользователей</h3>
    <span><?= $stats['users'] ?></span>
    <small>+<?= $stats['new_today'] ?> сегодня</small>
  </div>
  <!-- Транзакций / Объём за 24ч / Активных сессий -->
</div>

<!-- График транзакций (7 дней) — canvas chart или SVG -->
<!-- Последние регистрации (таблица 10 строк) -->
<!-- Последние транзакции (таблица 10 строк) -->
<!-- Быстрые действия: создать баннер / заблокировать пользователя -->
```

### /admin/users — Управление пользователями

```
Функции:
- Список всех пользователей с поиском и фильтрацией (роль, статус, дата)
- Просмотр профиля: баланс по каждой монете, история транзакций
- Блокировка / разблокировка аккаунта
- Смена роли (user / admin)
- Ручное изменение баланса кошелька (с комментарием, пишется в security_logs)
- Сброс пароля (генерирует временный, отправляет на email)
- Просмотр логов безопасности пользователя
- Удаление аккаунта (soft delete)
```

### /admin/transactions — Управление транзакциями

```
Функции:
- Таблица всех транзакций (фильтр по типу, монете, статусу, дате, пользователю)
- Ручное изменение статуса транзакции
- Добавление TxHash вручную
- Экспорт в CSV
- Создание ручной транзакции (зачисление/списание с комментарием)
```

### /admin/banners — Управление баннерами

```
Функции:
- Список баннеров с превью
- Добавить баннер: загрузка изображения, заголовок RU/EN, ссылка, alt-текст
- Редактировать / удалить
- Drag-and-drop сортировка (JS sortable)
- Вкл/выкл без удаления
- Валидация: изображение 1200×400px, max 2MB, форматы jpg/png/webp
```

### /admin/wallets — Управление адресами кошельков

```
Функции:
- Список активных монет
- Добавить монету: символ, полное название, иконка, статус депозита/вывода
- Редактировать адрес генерации / настройки
- Настройка комиссий вывода (% + фиксированная) по каждой монете
- Мин./макс. лимиты на вывод
```

### /admin/settings — Глобальные настройки

```
Вкладки:
1. Основные
   - Название сайта
   - Описание RU / EN
   - Логотип (загрузить)
   - Favicon
   - Режим обслуживания (включить / выключить + текст)
   - Регистрация: открыта / закрыта
   - Вывод: включён / выключён + причина отключения

2. Финансы
   - Мин. сумма вывода
   - Мак. сумма вывода
   - Комиссии по умолчанию
   - Адреса горячих кошельков

3. SEO
   - Meta title (RU/EN)
   - Meta description (RU/EN)
   - OG-изображение
   - Robots.txt (textarea)
   - Яндекс.Вебмастер код
   - Google Search Console код

4. Социальные сети
   - Telegram URL
   - Twitter URL
   - Discord URL
   - Instagram URL (опционально)

5. Уведомления
   - SMTP-настройки (host, port, user, pass, from)
   - Тест отправки письма
   - Шаблон письма при выводе
   - Шаблон письма при регистрации

6. Безопасность
   - Время жизни сессии (минуты)
   - Макс. попыток входа
   - Период блокировки (минуты)
   - IP-белый список для /admin
   - Включить логирование входов
```

---

## КОМПОНЕНТЫ UI

### Кнопки

```css
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: var(--radius-md);
  font-family: 'DM Sans', sans-serif;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: all var(--transition);
  text-decoration: none;
  border: none;
}
.btn--primary {
  background: var(--accent-blue);
  color: #fff;
  box-shadow: 0 4px 20px rgba(61,126,255,0.35);
}
.btn--primary:hover {
  background: #5591ff;
  box-shadow: 0 6px 28px rgba(61,126,255,0.5);
  transform: translateY(-1px);
}
.btn--outline {
  background: transparent;
  border: 1.5px solid var(--border-light);
  color: var(--text-primary);
}
.btn--outline:hover {
  border-color: var(--accent-blue);
  color: var(--accent-blue);
}
.btn--ghost {
  background: rgba(255,255,255,0.06);
  color: var(--text-primary);
}
.btn--full { width: 100%; justify-content: center; }
.btn--lg { padding: 14px 32px; font-size: 16px; }
.btn--xl { padding: 18px 48px; font-size: 18px; }
```

### Карточки

```css
.card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 24px;
  transition: border-color var(--transition), transform var(--transition);
}
.card:hover {
  border-color: var(--border-light);
  transform: translateY(-2px);
}
```

### Формы

```css
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 20px;
}
.form-group label {
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  color: var(--text-secondary);
}
.form-group input,
.form-group select,
.form-group textarea {
  background: var(--bg-secondary);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  padding: 12px 16px;
  font-size: 15px;
  font-family: 'DM Sans', sans-serif;
  transition: border-color var(--transition);
  outline: none;
}
.form-group input:focus {
  border-color: var(--accent-blue);
  box-shadow: 0 0 0 3px rgba(61,126,255,0.15);
}
```

### Status badge

```css
.badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
}
.badge--success { background: rgba(34,197,94,0.15); color: var(--accent-green); }
.badge--pending { background: rgba(240,180,41,0.15); color: var(--accent-gold); }
.badge--error   { background: rgba(239,68,68,0.15);  color: var(--accent-red); }
.badge--soon    { background: rgba(61,126,255,0.12); color: var(--accent-blue); }
```

---

## SEO-ЧЕКЛИСТ

Каждая страница обязана иметь:

```html
<!-- В <head> каждой страницы -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{PAGE_TITLE} — Vaultex</title>
<meta name="description" content="{PAGE_DESCRIPTION}">
<link rel="canonical" href="{CANONICAL_URL}">
<meta property="og:title"       content="{PAGE_TITLE}">
<meta property="og:description" content="{PAGE_DESCRIPTION}">
<meta property="og:image"       content="/assets/images/og-cover.jpg">
<meta property="og:url"         content="{CANONICAL_URL}">
<meta property="og:type"        content="website">
<meta name="twitter:card"       content="summary_large_image">
<meta name="twitter:site"       content="@vaultex">
<link rel="icon" href="/assets/images/favicon.ico">
<link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">
```

Страничная иерархия H1→H2→H3 обязательна. Не более 1 H1 на странице. Все изображения — alt-тексты. Ленивая загрузка (`loading="lazy"`) для всего ниже первого экрана.

---

## БЕЗОПАСНОСТЬ — ТРЕБОВАНИЯ

```php
// CSRF защита — в каждой форме
<input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">

// В контроллере
if (!Session::verifyCsrf($_POST['csrf_token'])) {
    http_response_code(403);
    die('CSRF token invalid');
}

// Rate limiting (пример)
class RateLimit {
    public static function check(string $action, int $max = 5, int $window = 900): bool {
        // Проверяет rate_limits таблицу
        // Возвращает false если лимит превышен
    }
}

// Session fingerprint
Session::start();
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
if (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] !== $fingerprint) {
    Session::destroy(); // Возможный угон сессии
}
```

---

## .htaccess

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/index.php [QSA,L]

# Защита директорий
Options -Indexes

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options SAMEORIGIN
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy strict-origin-when-cross-origin
Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"

# Кеширование статики
<FilesMatch "\.(css|js|png|jpg|webp|svg|woff2)$">
  Header set Cache-Control "public, max-age=31536000, immutable"
</FilesMatch>
```

---

## composer.json

```json
{
  "require": {
    "php": ">=8.2",
    "twig/twig": "^3.0",
    "vlucas/phpdotenv": "^5.0",
    "chillerlan/php-qrcode": "^5.0",
    "phpmailer/phpmailer": "^6.0"
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    }
  }
}
```

---

## ФИНАЛЬНЫЕ ТРЕБОВАНИЯ К ИИ

1. Напиши **весь** PHP-код полностью, без «здесь будет логика»
2. Все SQL-запросы через PDO с prepared statements
3. Все формы с CSRF-токеном
4. Все тексты продублированы в `ru.json` и `en.json`
5. Все изображения заменены на `<img src="..." alt="...">` с корректными alt, размеры указаны
6. SVG-иконки встроены inline или подключены через Phosphor CDN
7. Никаких эмодзи ни в коде, ни в текстах
8. Адаптивность: sidebar для ≥1024px, bottom-nav + burger для <1024px
9. Переключатель языка — в sidebar (desktop) и в burger-menu (mobile)
10. robots.txt и sitemap.xml в корне public/
11. README_IMAGES.md (отдельный файл с описанием всех нужных изображений)
