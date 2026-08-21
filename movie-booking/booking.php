<?php require 'includes/functions.php';require_login();$id=(int)($_GET['show']??0);$s=db()->prepare('SELECT s.*,m.title,m.poster,t.name theater,sc.name screen FROM shows s JOIN movies m ON m.id=s.movie_id JOIN screens sc ON sc.id=s.screen_id JOIN theaters t ON t.id=sc.theater_id WHERE s.id=?');$s->execute([$id]);$show=$s->fetch();if(!$show)exit('Show not found');$pageTitle='Select seats';require 'includes/header.php';?>
<div class="container py-5"><div class="row g-4"><div class="col-lg-8"><h2><?=e($show['title'])?></h2><p class="text-secondary"><?=e($show['theater'])?> · <?=e($show['screen'])?> · <?=date('D d M, g:i A',strtotime($show['show_date'].' '.$show['show_time']))?></p><div class="screen">SCREEN</div><div id="seats" data-show="<?=$id?>"></div><div class="d-flex justify-content-center gap-3 mt-4 small"><span><i class="fa fa-square text-success"></i> Available</span><span><i class="fa fa-square text-warning"></i> Selected</span><span><i class="fa fa-square text-secondary"></i> Booked</span></div></div><div class="col-lg-4"><div class="booking-panel"><h4>Booking summary</h4><p id="selected" class="text-secondary">No seats selected</p><div class="d-flex justify-content-between"><span>Price / seat</span><span>₹<?=$show['ticket_price']?></span></div><hr><div class="d-flex justify-content-between fs-4 fw-bold"><span>Total</span><span id="total">₹0</span></div><form method="post" action="checkout.php" id="bookForm"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="show_id" value="<?=$id?>"><input type="hidden" name="seats" id="seatInput"><button disabled id="continue" class="btn btn-gradient w-100 mt-3">Continue</button></form></div></div></div></div>
<script>
const price = <?=$show['ticket_price']?>, seatsBox = document.getElementById('seats');
const selectedText = document.getElementById('selected'), seatInput = document.getElementById('seatInput');
const total = document.getElementById('total'), continueButton = document.getElementById('continue');
fetch('ajax/seats.php?show=<?=$id?>')
  .then(response => { if (!response.ok) throw new Error(); return response.json(); })
  .then(data => {
    let html = '';
    'ABCDEFGHIJ'.split('').forEach(row => {
      html += '<div class="seat-row"><span class="row-label">' + row + '</span>';
      for (let number = 1; number <= 12; number++) {
        const seat = row + number;
        const state = data.booked.includes(seat) ? 'booked' : 'available';
        html += '<button type="button" class="seat ' + state + '" data-seat="' + seat + '">' + number + '</button>';
      }
      html += '</div>';
    });
    seatsBox.innerHTML = html;
  })
  .catch(() => seatsBox.innerHTML = '<div class="alert alert-danger">Unable to load seats. Confirm Apache and MySQL are running, then refresh.</div>');

seatsBox.addEventListener('click', event => {
  const button = event.target.closest('.seat.available, .seat.selected');
  if (!button) return;
  button.classList.toggle('selected'); button.classList.toggle('available');
  const selected = [...document.querySelectorAll('.seat.selected')].map(item => item.dataset.seat);
  if (selected.length > 10) {
    button.classList.toggle('selected'); button.classList.toggle('available');
    alert('Maximum 10 seats per booking'); return;
  }
  selectedText.textContent = selected.length ? selected.join(', ') : 'No seats selected';
  seatInput.value = selected.join(', ');
  total.textContent = '₹' + (selected.length * price);
  continueButton.disabled = !selected.length;
});
</script><?php require 'includes/footer.php'; ?>
