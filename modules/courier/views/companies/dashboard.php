<div class="card-container row">
    <div class="card col-xs-6 col-sm-3 bg-blue">
        <div class="icon"><i style="color:blueviolet" class="fa fa-bullseye" aria-hidden="true"></i></div>
        <div class="label">Total Companies</div>
        <div class="count"><?= array_sum($type_counts) ?></div> <!-- Total count of all companies -->
    </div>
    <div class="card col-xs-6 col-sm-3 bg-green">
        <div class="icon"><i style="color:orange" class="fa fa-building" aria-hidden="true"></i></div>
        <div class="label">Internal Companies</div>
        <div class="count"><?= $type_counts['internal'] ?></div> <!-- Count of internal companies -->
    </div>
    <div class="card col-xs-6 col-sm-3 bg-orange">
        <div class="icon"><i style="color:green" class="fa fa-calculator" aria-hidden="true"></i></div>
        <div class="label">External Companies</div>
        <div class="count"><?= $type_counts['third_party'] ?></div> <!-- Count of external companies -->
    </div>
</div>
