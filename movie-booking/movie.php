<?php
require 'includes/functions.php';
$id=(int)($_GET['id']??0);
if ($id > 0) {
    $s=db()->prepare('SELECT * FROM movies WHERE id=?');
    $s->execute([$id]);
} else {
    // Keep the page useful when it is opened directly as movie.php.
    $s=db()->query("SELECT * FROM movies WHERE status='now_showing' ORDER BY release_date DESC LIMIT 1");
}
$m=$s->fetch();
if(!$m){http_response_code(404);exit('Movie not found. Import database/movie_booking.sql and try again.');}
$id=(int)$m['id'];
$m['avg_rating']=0; $shows=[]; $reviews=[]; $dataWarning='';
try {
    $rating=db()->prepare('SELECT COALESCE(AVG(rating),0) FROM reviews WHERE movie_id=? AND approved=1');
    $rating->execute([$id]); $m['avg_rating']=$rating->fetchColumn();
    $showQuery=db()->prepare('SELECT s.*,t.name theater,sc.name screen FROM shows s JOIN screens sc ON sc.id=s.screen_id JOIN theaters t ON t.id=sc.theater_id WHERE s.movie_id=? AND s.show_date>=CURDATE() ORDER BY s.show_date,s.show_time');
    $showQuery->execute([$id]); $shows=$showQuery->fetchAll();
    $reviewQuery=db()->prepare('SELECT r.*,u.name FROM reviews r JOIN users u ON u.id=r.user_id WHERE r.movie_id=? AND r.approved=1 ORDER BY r.created_at DESC');
    $reviewQuery->execute([$id]); $reviews=$reviewQuery->fetchAll();
} catch (PDOException $e) { $dataWarning='Showtime and review data is not available yet. Import the complete database/movie_booking.sql file.'; }
$pageTitle=$m['title'];require 'includes/header.php';?>
<section class="container py-5"><div class="row g-5"><div class="col-md-4"><img class="poster-lg" src="<?=e(movie_poster($m))?>" alt=""></div><div class="col-md-8"><span class="badge badge-status"><?=e(str_replace('_',' ',$m['status']))?></span><h1 class="display-4 fw-bold mt-2"><?=e($m['title'])?></h1><div class="rating fs-5"><i class="fa fa-star"></i> <?=number_format($m['avg_rating']?:4.5,1)?> <span class="text-secondary fs-6">Audience rating</span></div><p class="lead mt-4"><?=e($m['description'])?></p><p><strong>Director:</strong> <?=e($m['director'])?><br><strong>Cast:</strong> <?=e($m['cast_members'])?><br><strong>Details:</strong> <?=e($m['language'])?> · <?=e($m['duration'])?> · <?=e($m['genre'])?></p><?php if($m['trailer_url']):?><button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#trailer"><i class="fa fa-play me-2"></i>Watch trailer</button><?php endif?></div></div></section>
<section class="container"><h3>Choose a showtime</h3><?php if($dataWarning): ?><div class="alert alert-warning"><?=e($dataWarning)?></div><?php elseif(!$shows): ?><div class="alert alert-info">No future showtimes have been created for this movie. Sign in as an admin and add one under <strong>Shows</strong>.</div><?php else: ?><div class="row g-3 mb-5"><?php foreach($shows as $sh):?><div class="col-md-4"><div class="booking-panel p-3"><strong><?=date('D, d M',strtotime($sh['show_date']))?></strong><h4 class="text-warning"><?=date('g:i A',strtotime($sh['show_time']))?></h4><small class="text-secondary"><?=e($sh['theater'])?> · <?=e($sh['screen'])?></small><a class="btn btn-gradient d-block mt-3" href="booking.php?show=<?=$sh['id']?>">Book ₹<?=number_format($sh['ticket_price'])?></a></div></div><?php endforeach?></div><?php endif ?><h3>Reviews</h3><?php foreach($reviews as $r):?><div class="border-bottom border-secondary-subtle py-3"><strong><?=e($r['name'])?></strong> <span class="rating">★★★★★</span><p class="mb-0 text-secondary"><?=e($r['review'])?></p></div><?php endforeach?></section>
<div class="modal fade" id="trailer"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content bg-dark"><div class="ratio ratio-16x9"><iframe src="<?=e($m['trailer_url'])?>" allowfullscreen></iframe></div></div></div></div><?php require 'includes/footer.php'; ?>
