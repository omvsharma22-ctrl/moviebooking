<?php
require_once __DIR__.'/../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function is_logged_in(): bool { return !empty($_SESSION['user']); }
function is_admin(): bool { return !empty($_SESSION['admin']); }
function redirect(string $path) { header('Location: '.$path); exit; }
function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) { $_SESSION['flash'][$key] = $message; return null; }
    $message = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $message;
}
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24)); return $_SESSION['csrf']; }
function check_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid form token.'); } }
function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'] ?? '/movie-booking/index.php';
        flash('error','Please sign in to choose your seats.');
        redirect('/movie-booking/login.php');
    }
}
function movie_poster(array $movie): string {
    $seedPosters = [
        1 => 'assets/images/posters/avatar-fire-and-ash.svg',
        2 => 'assets/images/posters/superman.svg',
        3 => 'assets/images/posters/jurassic-world-rebirth.svg',
        4 => 'assets/images/posters/mission-impossible-final-reckoning.svg',
        5 => 'assets/images/posters/fantastic-four.svg',
        6 => 'assets/images/posters/f1.svg',
        7 => 'assets/images/posters/ballerina.svg',
        8 => 'assets/images/posters/elio.svg',
        9 => 'assets/images/posters/how-to-train-your-dragon.svg',
        10 => 'assets/images/posters/the-bad-guys-2.svg',
    ];

    $poster = $movie['poster'] ?? '';
    if ($poster && $poster !== 'assets/images/cinema-poster.svg') return $poster;

    return $seedPosters[(int) ($movie['id'] ?? 0)] ?? 'assets/images/cinema-poster.svg';
}
