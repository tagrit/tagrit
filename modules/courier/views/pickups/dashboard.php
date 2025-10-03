<div class="card-container row">
    <div class="card col-xs-6 col-sm-3 bg-green">
        <div class="icon"><i  style="color:blueviolet"  class="fa fa-question" aria-hidden="true"></i></div>
        <div class="label">Pending</div>
        <div class="count"><?= $status_counts['pending'] ?></div>
    </div>
    <div class="card col-xs-6 col-sm-3 bg-blue">
        <div class="icon"><i style="color:blueviolet" class="fa fa-truck" aria-hidden="true"></i></div>
        <div class="label">Picked Up</div>
        <div class="count"><?= $status_counts['picked_up'] ?></div>
    </div>
    <div class="card col-xs-6 col-sm-3 bg-blue">
        <div class="icon"><i style="color:blueviolet" class="fa fa-handshake" aria-hidden="true"></i></div>
        <div class="label">Delivered</div>
        <div class="count"><?= $status_counts['delivered'] ?></div>
    </div>
</div>