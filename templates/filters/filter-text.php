<?php
$params = $_GET;
$params['tab'] = $type['id'];
$query = http_build_query($params);
$url = '/' . 'project/index.php' . '?' . $query;
$tab = filter_input(INPUT_GET, 'tab');
if ($tab === $type['id']) {
    $button_active = 'filters__button--active';
} else {
    $button_active = '';
}
?>
<li class="popular__filters-item filters__item">
    <a class="filters__button filters__button--text <?= $button_active?>" href="<?= $url ?>">
        <span class="visually-hidden"><?= $type['name']?></span>
        <svg class="filters__icon" width="20" height="21">
            <use xlink:href="#icon-filter-text"></use>
        </svg>
    </a>
</li>
