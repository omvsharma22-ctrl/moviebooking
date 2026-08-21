<?php
$pageTitle = 'Book your next movie';
require 'includes/header.php';

$q = trim($_GET['q'] ?? '');
$stmt = db()->prepare('SELECT * FROM movies WHERE title LIKE ? ORDER BY status, release_date DESC LIMIT 10');
$stmt->execute(['%'.$q.'%']);
$movies = $stmt->fetchAll();
$now = array_filter($movies, fn($movie) => $movie['status'] === 'now_showing');
?>
<section class="hero">
    <div class="container py-5">
        <span class="badge bg-danger mb-3">NOW SHOWING</span>
        <h1>Feel every<br><span class="text-warning">frame.</span></h1>
        <p class="lead col-md-5 text-light-emphasis">Discover blockbuster stories on the biggest screen in town.</p>
        <a href="#movies" class="btn btn-gradient btn-lg">Explore movies <i class="fa fa-arrow-right ms-2"></i></a>
    </div>
</section>

<section class="container py-5" id="movies">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">Now playing</h2>
        <form class="d-flex" method="get">
            <input class="form-control me-2" name="q" value="<?=e($q)?>" placeholder="Search films">
            <button class="btn btn-outline-light" aria-label="Search"><i class="fa fa-search"></i></button>
        </form>
    </div>

    <?php if (!$now): ?>
        <div class="alert alert-warning border-0">No movies are in the database yet. Import <code>database/movie_booking.sql</code> into phpMyAdmin’s <strong>movie_booking</strong> database, then refresh this page.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($now as $m): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <article class="movie-card">
                        <img src="<?=e(movie_poster($m))?>" alt="<?=e($m['title'])?> poster" loading="lazy">
                        <div class="p-3">
                            <span class="small text-warning"><i class="fa fa-star"></i> 4.7</span>
                            <h5 class="mt-1 mb-1"><?=e($m['title'])?></h5>
                            <p class="small text-secondary mb-3"><?=e($m['genre'])?> · <?=e($m['duration'])?></p>
                            <a class="btn btn-sm btn-gradient w-100" href="/movie-booking/movie.php?id=<?=$m['id']?>">View &amp; book</a>
                        </div>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>

<section class="container py-3" id="upcoming">
    <h2 class="section-title mb-4">Coming soon</h2>
    <div class="row g-4">
        <?php foreach ($movies as $m): if ($m['status'] !== 'upcoming') continue; ?>
            <div class="col-md-4">
                <div class="movie-card d-flex">
                    <img class="w-50" src="<?=e(movie_poster($m))?>" alt="<?=e($m['title'])?> poster" loading="lazy">
                    <div class="p-3">
                        <h5><?=e($m['title'])?></h5>
                        <p class="small">Releases <?=date('d M Y', strtotime($m['release_date']))?></p>
                        <strong class="countdown" data-date="<?=$m['release_date']?>"></strong>
                        <a class="btn btn-sm btn-outline-warning mt-3 d-block" href="/movie-booking/movie.php?id=<?=$m['id']?>">Notify me</a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>
<?php require 'includes/footer.php'; ?>
