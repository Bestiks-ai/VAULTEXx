# Vaultex - Cryptocurrency Wallet and Exchange

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Web server (Apache/Nginx)

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database
4. Run migrations: `mysql -u root -p vaultex < migrations/001_init.sql`
5. Configure web server to point to `/public` directory
6. Open in browser

## Features

- Multi-language support (RU/EN)
- Secure authentication with bcrypt
- CSRF protection
- Rate limiting
- Session fingerprinting
- XSS filtering
- PDO prepared statements

## Tech Stack

- Backend: PHP 8.2+ MVC
- Database: MySQL 8.0 (PDO)
- Template: Twig 3.x
- CSS: Pure CSS3 (Variables, Grid, Flexbox)
- JS: Vanilla ES6+
- Carousel: Splide.js
- Icons: Phosphor Icons (SVG inline)
- Fonts: Sora + DM Sans
